<?php
/**
 * Enhanced Movie Dashboard with Contact Management
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add custom admin menu
function movieflix_admin_menu() {
    add_menu_page(
        __('MovieFlix Dashboard', 'movieflix'),
        __('MovieFlix', 'movieflix'),
        'manage_options',
        'movieflix-dashboard',
        'movieflix_dashboard_page',
        'dashicons-video-alt3',
        6
    );
    
    add_submenu_page(
        'movieflix-dashboard',
        __('Movie Statistics', 'movieflix'),
        __('Statistics', 'movieflix'),
        'manage_options',
        'movieflix-stats',
        'movieflix_stats_page'
    );
    
    add_submenu_page(
        'movieflix-dashboard',
        __('Contact Messages', 'movieflix'),
        __('Contact Messages', 'movieflix'),
        'manage_options',
        'movieflix-contacts',
        'movieflix_contacts_page'
    );
    
    add_submenu_page(
        'movieflix-dashboard',
        __('Movie Comments', 'movieflix'),
        __('Comments', 'movieflix'),
        'manage_options',
        'movieflix-comments',
        'movieflix_comments_page'
    );
    
    add_submenu_page(
        'movieflix-dashboard',
        __('Theme Settings', 'movieflix'),
        __('Settings', 'movieflix'),
        'manage_options',
        'movieflix-settings',
        'movieflix_settings_page'
    );
}
add_action('admin_menu', 'movieflix_admin_menu');

// Enhanced Dashboard Page
function movieflix_dashboard_page() {
    // Security check
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'movieflix'));
    }
    
    $movie_count = wp_count_posts('movie');
    $total_movies = $movie_count->publish + $movie_count->draft;
    
    // Get recent movies with error handling
    $recent_movies = get_posts(array(
        'post_type' => 'movie',
        'posts_per_page' => 5,
        'post_status' => 'publish',
        'suppress_filters' => false
    ));
    
    // Get popular genres with error handling
    $popular_genres = get_terms(array(
        'taxonomy' => 'movie_genre',
        'orderby' => 'count',
        'order' => 'DESC',
        'number' => 5,
        'hide_empty' => true
    ));
    
    // Get total views
    global $wpdb;
    $total_views = $wpdb->get_var($wpdb->prepare("
        SELECT SUM(CAST(meta_value AS UNSIGNED)) 
        FROM {$wpdb->postmeta} 
        WHERE meta_key = %s
    ", '_movie_views'));
    $total_views = $total_views ? intval($total_views) : 0;
    
    // Get contact messages count
    $contact_table = $wpdb->prefix . 'movieflix_contacts';
    $total_contacts = $wpdb->get_var("SELECT COUNT(*) FROM $contact_table");
    $unread_contacts = $wpdb->get_var("SELECT COUNT(*) FROM $contact_table WHERE status = 'unread'");
    
    // Get comments count
    $comments_table = $wpdb->prefix . 'movieflix_comments';
    $total_comments = $wpdb->get_var("SELECT COUNT(*) FROM $comments_table");
    
    ?>
    <div class="wrap">
        <h1><?php _e('MovieFlix Dashboard', 'movieflix'); ?></h1>
        
        <div class="movieflix-dashboard">
            <div class="dashboard-widgets">
                <!-- Statistics Widget -->
                <div class="dashboard-widget">
                    <h3><?php _e('Movie Statistics', 'movieflix'); ?></h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo esc_html($movie_count->publish); ?></div>
                            <div class="stat-label"><?php _e('Published Movies', 'movieflix'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo esc_html($movie_count->draft); ?></div>
                            <div class="stat-label"><?php _e('Draft Movies', 'movieflix'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo esc_html($total_movies); ?></div>
                            <div class="stat-label"><?php _e('Total Movies', 'movieflix'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($total_views); ?></div>
                            <div class="stat-label"><?php _e('Total Views', 'movieflix'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact & Comments Stats -->
                <div class="dashboard-widget">
                    <h3><?php _e('Contact & Comments', 'movieflix'); ?></h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($total_contacts); ?></div>
                            <div class="stat-label"><?php _e('Total Contacts', 'movieflix'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" style="color: #e50914;"><?php echo number_format($unread_contacts); ?></div>
                            <div class="stat-label"><?php _e('Unread Messages', 'movieflix'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($total_comments); ?></div>
                            <div class="stat-label"><?php _e('Total Comments', 'movieflix'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($total_movies + $total_comments); ?></div>
                            <div class="stat-label"><?php _e('Total Content', 'movieflix'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Movies Widget -->
                <div class="dashboard-widget">
                    <h3><?php _e('Recent Movies', 'movieflix'); ?></h3>
                    <div class="recent-movies">
                        <?php if (!empty($recent_movies)) : ?>
                            <?php foreach ($recent_movies as $movie) : ?>
                                <div class="recent-movie-item">
                                    <div class="movie-thumb">
                                        <?php echo get_the_post_thumbnail($movie->ID, array(50, 75)); ?>
                                    </div>
                                    <div class="movie-details">
                                        <strong><?php echo esc_html($movie->post_title); ?></strong>
                                        <div class="movie-meta">
                                            <?php 
                                            $year = get_post_meta($movie->ID, '_movie_release_year', true);
                                            echo $year ? esc_html($year) : __('N/A', 'movieflix');
                                            ?>
                                        </div>
                                    </div>
                                    <div class="movie-actions">
                                        <a href="<?php echo esc_url(get_edit_post_link($movie->ID)); ?>" class="button">
                                            <?php _e('Edit', 'movieflix'); ?>
                                        </a>
                                        <a href="<?php echo esc_url(get_permalink($movie->ID)); ?>" class="button" target="_blank">
                                            <?php _e('View', 'movieflix'); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p><?php _e('No movies found.', 'movieflix'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Popular Genres Widget -->
                <div class="dashboard-widget">
                    <h3><?php _e('Popular Genres', 'movieflix'); ?></h3>
                    <div class="genre-list">
                        <?php if (!empty($popular_genres) && !is_wp_error($popular_genres)) : ?>
                            <?php foreach ($popular_genres as $genre) : ?>
                                <div class="genre-item">
                                    <span class="genre-name"><?php echo esc_html($genre->name); ?></span>
                                    <span class="genre-count">
                                        <?php printf(_n('%d movie', '%d movies', $genre->count, 'movieflix'), $genre->count); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p><?php _e('No genres found.', 'movieflix'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Actions Widget -->
                <div class="dashboard-widget">
                    <h3><?php _e('Quick Actions', 'movieflix'); ?></h3>
                    <div class="quick-actions">
                        <a href="<?php echo esc_url(admin_url('post-new.php?post_type=movie')); ?>" class="button button-primary">
                            <?php _e('Add New Movie', 'movieflix'); ?>
                        </a>
                        <a href="<?php echo esc_url(admin_url('edit.php?post_type=movie')); ?>" class="button">
                            <?php _e('Manage Movies', 'movieflix'); ?>
                        </a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=movieflix-contacts')); ?>" class="button">
                            <?php _e('Contact Messages', 'movieflix'); ?>
                            <?php if ($unread_contacts > 0) : ?>
                                <span class="unread-badge"><?php echo $unread_contacts; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=movieflix-comments')); ?>" class="button">
                            <?php _e('Movie Comments', 'movieflix'); ?>
                        </a>
                        <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=movie_genre&post_type=movie')); ?>" class="button">
                            <?php _e('Manage Genres', 'movieflix'); ?>
                        </a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=movieflix-settings')); ?>" class="button">
                            <?php _e('Theme Settings', 'movieflix'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .movieflix-dashboard {
        margin-top: 20px;
    }
    
    .dashboard-widgets {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .dashboard-widget {
        background: #fff;
        border: 1px solid #ccd0d4;
        border-radius: 4px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .dashboard-widget h3 {
        margin-top: 0;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        color: #23282d;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    
    .stat-item {
        text-align: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 4px;
        border: 1px solid #e9ecef;
    }
    
    .stat-number {
        font-size: 2em;
        font-weight: bold;
        color: #e50914;
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #666;
        font-size: 0.9em;
        font-weight: 500;
    }
    
    .recent-movie-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    
    .recent-movie-item:last-child {
        border-bottom: none;
    }
    
    .movie-thumb img {
        border-radius: 4px;
        max-width: 50px;
        height: auto;
    }
    
    .movie-details {
        flex: 1;
    }
    
    .movie-meta {
        color: #666;
        font-size: 0.9em;
        margin-top: 3px;
    }
    
    .movie-actions {
        display: flex;
        gap: 5px;
    }
    
    .genre-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    
    .genre-item:last-child {
        border-bottom: none;
    }
    
    .genre-name {
        font-weight: 500;
    }
    
    .genre-count {
        color: #666;
        font-size: 0.9em;
        background: #f0f0f1;
        padding: 2px 8px;
        border-radius: 12px;
    }
    
    .quick-actions {
        display: grid;
        gap: 10px;
    }
    
    .quick-actions .button {
        text-align: center;
        text-decoration: none;
        position: relative;
    }
    
    .unread-badge {
        background: #e50914;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 0.8em;
        margin-left: 5px;
    }
    </style>
    <?php
}

// Contact Messages Management Page
function movieflix_contacts_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'movieflix'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'movieflix_contacts';
    
    // Handle actions
    if (isset($_GET['action']) && isset($_GET['contact_id'])) {
        $contact_id = intval($_GET['contact_id']);
        $action = sanitize_text_field($_GET['action']);
        
        if ($action === 'mark_read') {
            $wpdb->update($table_name, array('status' => 'read'), array('id' => $contact_id));
            echo '<div class="notice notice-success"><p>Message marked as read.</p></div>';
        } elseif ($action === 'mark_unread') {
            $wpdb->update($table_name, array('status' => 'unread'), array('id' => $contact_id));
            echo '<div class="notice notice-success"><p>Message marked as unread.</p></div>';
        } elseif ($action === 'delete') {
            $wpdb->delete($table_name, array('id' => $contact_id));
            echo '<div class="notice notice-success"><p>Message deleted.</p></div>';
        }
    }
    
    // Get contacts
    $contacts = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
    
    ?>
    <div class="wrap">
        <h1><?php _e('Contact Messages', 'movieflix'); ?></h1>
        
        <?php if (!empty($contacts)) : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Name', 'movieflix'); ?></th>
                        <th><?php _e('Email', 'movieflix'); ?></th>
                        <th><?php _e('Subject', 'movieflix'); ?></th>
                        <th><?php _e('Message', 'movieflix'); ?></th>
                        <th><?php _e('Status', 'movieflix'); ?></th>
                        <th><?php _e('Date', 'movieflix'); ?></th>
                        <th><?php _e('Actions', 'movieflix'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact) : ?>
                        <tr class="<?php echo $contact->status === 'unread' ? 'unread-message' : ''; ?>">
                            <td><strong><?php echo esc_html($contact->name); ?></strong></td>
                            <td><?php echo esc_html($contact->email); ?></td>
                            <td><?php echo esc_html($contact->subject); ?></td>
                            <td>
                                <div class="message-preview">
                                    <?php echo esc_html(wp_trim_words($contact->message, 15)); ?>
                                </div>
                                <div class="full-message" style="display: none;">
                                    <?php echo nl2br(esc_html($contact->message)); ?>
                                </div>
                                <button class="button button-small toggle-message">Show Full</button>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo esc_attr($contact->status); ?>">
                                    <?php echo esc_html(ucfirst($contact->status)); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html(date('M j, Y g:i A', strtotime($contact->created_at))); ?></td>
                            <td>
                                <?php if ($contact->status === 'unread') : ?>
                                    <a href="?page=movieflix-contacts&action=mark_read&contact_id=<?php echo $contact->id; ?>" class="button button-small">Mark Read</a>
                                <?php else : ?>
                                    <a href="?page=movieflix-contacts&action=mark_unread&contact_id=<?php echo $contact->id; ?>" class="button button-small">Mark Unread</a>
                                <?php endif; ?>
                                <a href="mailto:<?php echo esc_attr($contact->email); ?>?subject=Re: <?php echo esc_attr($contact->subject); ?>" class="button button-small">Reply</a>
                                <a href="?page=movieflix-contacts&action=delete&contact_id=<?php echo $contact->id; ?>" class="button button-small button-link-delete" onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php _e('No contact messages found.', 'movieflix'); ?></p>
        <?php endif; ?>
    </div>
    
    <style>
    .unread-message {
        background-color: #fff3cd;
    }
    
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8em;
        font-weight: bold;
    }
    
    .status-unread {
        background: #dc3545;
        color: white;
    }
    
    .status-read {
        background: #28a745;
        color: white;
    }
    
    .message-preview {
        margin-bottom: 5px;
    }
    
    .full-message {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 4px;
        margin: 5px 0;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        $('.toggle-message').on('click', function() {
            var $button = $(this);
            var $preview = $button.siblings('.message-preview');
            var $full = $button.siblings('.full-message');
            
            if ($full.is(':visible')) {
                $full.hide();
                $preview.show();
                $button.text('Show Full');
            } else {
                $preview.hide();
                $full.show();
                $button.text('Show Less');
            }
        });
    });
    </script>
    <?php
}

// Movie Comments Management Page
function movieflix_comments_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'movieflix'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'movieflix_comments';
    
    // Handle actions
    if (isset($_GET['action']) && isset($_GET['comment_id'])) {
        $comment_id = intval($_GET['comment_id']);
        $action = sanitize_text_field($_GET['action']);
        
        if ($action === 'approve') {
            $wpdb->update($table_name, array('status' => 'approved'), array('id' => $comment_id));
            echo '<div class="notice notice-success"><p>Comment approved.</p></div>';
        } elseif ($action === 'unapprove') {
            $wpdb->update($table_name, array('status' => 'pending'), array('id' => $comment_id));
            echo '<div class="notice notice-success"><p>Comment unapproved.</p></div>';
        } elseif ($action === 'delete') {
            $wpdb->delete($table_name, array('id' => $comment_id));
            echo '<div class="notice notice-success"><p>Comment deleted.</p></div>';
        }
    }
    
    // Get comments with movie titles
    $comments = $wpdb->get_results("
        SELECT c.*, p.post_title as movie_title 
        FROM $table_name c 
        LEFT JOIN {$wpdb->posts} p ON c.movie_id = p.ID 
        ORDER BY c.created_at DESC
    ");
    
    ?>
    <div class="wrap">
        <h1><?php _e('Movie Comments', 'movieflix'); ?></h1>
        
        <?php if (!empty($comments)) : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Author', 'movieflix'); ?></th>
                        <th><?php _e('Movie', 'movieflix'); ?></th>
                        <th><?php _e('Comment', 'movieflix'); ?></th>
                        <th><?php _e('Status', 'movieflix'); ?></th>
                        <th><?php _e('Date', 'movieflix'); ?></th>
                        <th><?php _e('Actions', 'movieflix'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comments as $comment) : ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html($comment->author_name); ?></strong><br>
                                <small><?php echo esc_html($comment->author_email); ?></small>
                            </td>
                            <td>
                                <?php if ($comment->movie_title) : ?>
                                    <a href="<?php echo esc_url(get_permalink($comment->movie_id)); ?>" target="_blank">
                                        <?php echo esc_html($comment->movie_title); ?>
                                    </a>
                                <?php else : ?>
                                    <em>Movie not found</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="comment-content">
                                    <?php echo nl2br(esc_html(wp_trim_words($comment->comment_content, 20))); ?>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo esc_attr($comment->status); ?>">
                                    <?php echo esc_html(ucfirst($comment->status)); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html(date('M j, Y g:i A', strtotime($comment->created_at))); ?></td>
                            <td>
                                <?php if ($comment->status === 'approved') : ?>
                                    <a href="?page=movieflix-comments&action=unapprove&comment_id=<?php echo $comment->id; ?>" class="button button-small">Unapprove</a>
                                <?php else : ?>
                                    <a href="?page=movieflix-comments&action=approve&comment_id=<?php echo $comment->id; ?>" class="button button-small">Approve</a>
                                <?php endif; ?>
                                <a href="?page=movieflix-comments&action=delete&comment_id=<?php echo $comment->id; ?>" class="button button-small button-link-delete" onclick="return confirm('Are you sure you want to delete this comment?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php _e('No comments found.', 'movieflix'); ?></p>
        <?php endif; ?>
    </div>
    
    <style>
    .comment-content {
        max-width: 300px;
        word-wrap: break-word;
    }
    </style>
    <?php
}

// Enhanced Statistics Page
function movieflix_stats_page() {
    // Security check
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'movieflix'));
    }
    
    global $wpdb;
    
    // Get download statistics with error handling
    $download_stats = $wpdb->get_results($wpdb->prepare("
        SELECT meta_key, SUM(CAST(meta_value AS UNSIGNED)) as total_downloads 
        FROM {$wpdb->postmeta} 
        WHERE meta_key LIKE %s 
        GROUP BY meta_key
        ORDER BY total_downloads DESC
    ", '_download_count_%'));
    
    // Get most viewed movies
    $most_viewed = $wpdb->get_results($wpdb->prepare("
        SELECT p.ID, p.post_title, pm.meta_value as views
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE p.post_type = %s 
        AND p.post_status = %s
        AND pm.meta_key = %s
        ORDER BY CAST(pm.meta_value AS UNSIGNED) DESC
        LIMIT 10
    ", 'movie', 'publish', '_movie_views'));
    
    ?>
    <div class="wrap">
        <h1><?php _e('Movie Statistics', 'movieflix'); ?></h1>
        
        <div class="stats-container">
            <!-- Download Statistics -->
            <div class="stats-widget">
                <h3><?php _e('Download Statistics', 'movieflix'); ?></h3>
                <?php if (!empty($download_stats)) : ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Quality', 'movieflix'); ?></th>
                                <th><?php _e('Total Downloads', 'movieflix'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($download_stats as $stat) : ?>
                                <?php
                                $quality = str_replace('_download_count_', '', $stat->meta_key);
                                $quality = ucfirst($quality);
                                ?>
                                <tr>
                                    <td><?php echo esc_html($quality); ?></td>
                                    <td><?php echo number_format($stat->total_downloads); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p><?php _e('No download statistics available.', 'movieflix'); ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Most Viewed Movies -->
            <div class="stats-widget">
                <h3><?php _e('Most Viewed Movies', 'movieflix'); ?></h3>
                <?php if (!empty($most_viewed)) : ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Movie Title', 'movieflix'); ?></th>
                                <th><?php _e('Views', 'movieflix'); ?></th>
                                <th><?php _e('Actions', 'movieflix'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($most_viewed as $movie) : ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html($movie->post_title); ?></strong>
                                    </td>
                                    <td><?php echo number_format($movie->views); ?></td>
                                    <td>
                                        <a href="<?php echo esc_url(get_edit_post_link($movie->ID)); ?>" class="button button-small">
                                            <?php _e('Edit', 'movieflix'); ?>
                                        </a>
                                        <a href="<?php echo esc_url(get_permalink($movie->ID)); ?>" class="button button-small" target="_blank">
                                            <?php _e('View', 'movieflix'); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p><?php _e('No view statistics available.', 'movieflix'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <style>
        .stats-container {
            display: grid;
            gap: 20px;
            margin-top: 20px;
        }
        
        .stats-widget {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 20px;
        }
        
        .stats-widget h3 {
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .stats-widget table {
            margin-top: 15px;
        }
        </style>
    </div>
    <?php
}

// Enhanced Settings Page
function movieflix_settings_page() {
    // Security check
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'movieflix'));
    }
    
    // Handle form submission
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['movieflix_settings_nonce'], 'movieflix_save_settings')) {
        update_option('movieflix_site_title', sanitize_text_field($_POST['site_title']));
        update_option('movieflix_featured_movies', sanitize_text_field($_POST['featured_movies']));
        update_option('movieflix_primary_color', sanitize_hex_color($_POST['primary_color']));
        update_option('movieflix_movies_per_page', absint($_POST['movies_per_page']));
        
        echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'movieflix') . '</p></div>';
    }
    
    $site_title = get_option('movieflix_site_title', get_bloginfo('name'));
    $featured_movies = get_option('movieflix_featured_movies', '');
    $primary_color = get_option('movieflix_primary_color', '#e50914');
    $movies_per_page = get_option('movieflix_movies_per_page', 24);
    ?>
    <div class="wrap">
        <h1><?php _e('MovieFlix Settings', 'movieflix'); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('movieflix_save_settings', 'movieflix_settings_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Site Title', 'movieflix'); ?></th>
                    <td>
                        <input type="text" name="site_title" value="<?php echo esc_attr($site_title); ?>" class="regular-text" />
                        <p class="description"><?php _e('The title displayed in the header', 'movieflix'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Primary Color', 'movieflix'); ?></th>
                    <td>
                        <input type="color" name="primary_color" value="<?php echo esc_attr($primary_color); ?>" />
                        <p class="description"><?php _e('Main theme color', 'movieflix'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Movies Per Page', 'movieflix'); ?></th>
                    <td>
                        <input type="number" name="movies_per_page" value="<?php echo esc_attr($movies_per_page); ?>" min="1" max="100" />
                        <p class="description"><?php _e('Number of movies to display per page', 'movieflix'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}