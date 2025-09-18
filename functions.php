<?php
/**
 * MovieFlix Theme Functions
 * 
 * @package MovieFlix
 * @version 1.8.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function movieflix_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('custom-logo');
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('responsive-embeds');
    add_theme_support('automatic-feed-links');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'movieflix'),
    ));
    
    // Add image sizes
    add_image_size('movie-poster', 300, 450, true);
    add_image_size('movie-thumb', 150, 225, true);
    add_image_size('movie-backdrop', 1280, 720, true);
    
    // Set content width
    if (!isset($content_width)) {
        $content_width = 1200;
    }
}
add_action('after_setup_theme', 'movieflix_setup');

/**
 * Register Movie Post Type
 */
function movieflix_register_movie_post_type() {
    $labels = array(
        'name' => __('Movies', 'movieflix'),
        'singular_name' => __('Movie', 'movieflix'),
        'menu_name' => __('Movies', 'movieflix'),
        'add_new' => __('Add New Movie', 'movieflix'),
        'add_new_item' => __('Add New Movie', 'movieflix'),
        'edit_item' => __('Edit Movie', 'movieflix'),
        'new_item' => __('New Movie', 'movieflix'),
        'view_item' => __('View Movie', 'movieflix'),
        'search_items' => __('Search Movies', 'movieflix'),
        'not_found' => __('No movies found', 'movieflix'),
        'not_found_in_trash' => __('No movies found in trash', 'movieflix'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-video-alt3',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite' => array('slug' => 'movie'),
        'show_in_rest' => true,
    );

    register_post_type('movie', $args);
}
add_action('init', 'movieflix_register_movie_post_type');

/**
 * Register Movie Taxonomies
 */
function movieflix_register_taxonomies() {
    // Movie Genre
    register_taxonomy('movie_genre', 'movie', array(
        'labels' => array(
            'name' => __('Genres', 'movieflix'),
            'singular_name' => __('Genre', 'movieflix'),
        ),
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'genre'),
    ));

    // Movie Language
    register_taxonomy('movie_language', 'movie', array(
        'labels' => array(
            'name' => __('Languages', 'movieflix'),
            'singular_name' => __('Language', 'movieflix'),
        ),
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'language'),
    ));

    // Movie Quality
    register_taxonomy('movie_quality', 'movie', array(
        'labels' => array(
            'name' => __('Quality', 'movieflix'),
            'singular_name' => __('Quality', 'movieflix'),
        ),
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'quality'),
    ));

    // Movie Year
    register_taxonomy('movie_year', 'movie', array(
        'labels' => array(
            'name' => __('Release Years', 'movieflix'),
            'singular_name' => __('Release Year', 'movieflix'),
        ),
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'year'),
    ));
}
add_action('init', 'movieflix_register_taxonomies');

/**
 * Enqueue Scripts and Styles
 */
function movieflix_scripts() {
    // Enqueue styles
    wp_enqueue_style('movieflix-style', get_stylesheet_uri(), array(), '1.8.0');
    
    // Enqueue scripts
    wp_enqueue_script('jquery');
    
    wp_enqueue_script('movieflix-main', get_template_directory_uri() . '/js/movieflix.js', array('jquery'), '1.8.0', true);
    wp_enqueue_script('movieflix-menu-toggle', get_template_directory_uri() . '/js/menu-toggle.js', array(), '1.8.0', true);
    wp_enqueue_script('movieflix-back-to-top', get_template_directory_uri() . '/js/back-to-top.js', array(), '1.8.0', true);
    
    // Conditional scripts
    if (is_page() && get_the_ID() == get_option('movieflix_dynamic_page_contact')) {
        wp_enqueue_script('movieflix-contact-form', get_template_directory_uri() . '/js/contact-form.js', array('jquery'), '1.8.0', true);
    }
    
    if (is_singular('movie')) {
        wp_enqueue_script('movieflix-movie-comments', get_template_directory_uri() . '/js/movie-comments.js', array('jquery'), '1.8.0', true);
    }
    
    // Enqueue comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
    
    // Localize script for AJAX
    wp_localize_script('movieflix-main', 'movieflix_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('movieflix_nonce'),
        'home_url' => home_url(),
        'theme_url' => get_template_directory_uri(),
        'loading_text' => __('Loading...', 'movieflix'),
        'error_text' => __('Something went wrong. Please try again.', 'movieflix'),
        'movies_per_page' => movieflix_get_movies_per_page(),
    ));
}
add_action('wp_enqueue_scripts', 'movieflix_scripts');

/**
 * Add Movie Meta Boxes
 */
function movieflix_add_movie_meta_boxes() {
    add_meta_box(
        'movie_details',
        __('Movie Details', 'movieflix'),
        'movieflix_movie_details_callback',
        'movie',
        'normal',
        'high'
    );

    add_meta_box(
        'movie_media',
        __('Movie Media', 'movieflix'),
        'movieflix_movie_media_callback',
        'movie',
        'normal',
        'high'
    );

    add_meta_box(
        'movie_download_links',
        __('Download Links', 'movieflix'),
        'movieflix_movie_download_links_callback',
        'movie',
        'normal',
        'high'
    );

    add_meta_box(
        'movie_watch_links',
        __('Watch Links', 'movieflix'),
        'movieflix_movie_watch_links_callback',
        'movie',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'movieflix_add_movie_meta_boxes');

/**
 * Movie Details Meta Box Callback
 */
function movieflix_movie_details_callback($post) {
    wp_nonce_field('movieflix_save_movie_meta', 'movieflix_movie_meta_nonce');
    
    $release_year = get_post_meta($post->ID, '_movie_release_year', true);
    $duration = get_post_meta($post->ID, '_movie_duration', true);
    $imdb_rating = get_post_meta($post->ID, '_movie_imdb_rating', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="movie_release_year"><?php _e('Release Year', 'movieflix'); ?></label></th>
            <td><input type="number" id="movie_release_year" name="movie_release_year" value="<?php echo esc_attr($release_year); ?>" min="1900" max="<?php echo date('Y') + 5; ?>" /></td>
        </tr>
        <tr>
            <th><label for="movie_duration"><?php _e('Duration (minutes)', 'movieflix'); ?></label></th>
            <td><input type="number" id="movie_duration" name="movie_duration" value="<?php echo esc_attr($duration); ?>" min="1" /></td>
        </tr>
        <tr>
            <th><label for="movie_imdb_rating"><?php _e('IMDB Rating', 'movieflix'); ?></label></th>
            <td><input type="number" id="movie_imdb_rating" name="movie_imdb_rating" value="<?php echo esc_attr($imdb_rating); ?>" min="0" max="10" step="0.1" /></td>
        </tr>
    </table>
    <?php
}

/**
 * Movie Media Meta Box Callback
 */
function movieflix_movie_media_callback($post) {
    $trailer_url = get_post_meta($post->ID, '_movie_trailer_url', true);
    $screenshots = get_post_meta($post->ID, '_movie_screenshots', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="movie_trailer_url"><?php _e('Trailer URL (YouTube)', 'movieflix'); ?></label></th>
            <td><input type="url" id="movie_trailer_url" name="movie_trailer_url" value="<?php echo esc_attr($trailer_url); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="movie_screenshots"><?php _e('Screenshots (comma-separated URLs)', 'movieflix'); ?></label></th>
            <td><textarea id="movie_screenshots" name="movie_screenshots" rows="3" class="large-text"><?php echo esc_textarea($screenshots); ?></textarea></td>
        </tr>
    </table>
    <?php
}

/**
 * Movie Download Links Meta Box Callback
 */
function movieflix_movie_download_links_callback($post) {
    $download_480p = get_post_meta($post->ID, '_movie_download_480p', true);
    $download_720p = get_post_meta($post->ID, '_movie_download_720p', true);
    $download_1080p = get_post_meta($post->ID, '_movie_download_1080p', true);
    $download_4k = get_post_meta($post->ID, '_movie_download_4k', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="movie_download_480p"><?php _e('480p Download Link', 'movieflix'); ?></label></th>
            <td><input type="url" id="movie_download_480p" name="movie_download_480p" value="<?php echo esc_attr($download_480p); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="movie_download_720p"><?php _e('720p Download Link', 'movieflix'); ?></label></th>
            <td><input type="url" id="movie_download_720p" name="movie_download_720p" value="<?php echo esc_attr($download_720p); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="movie_download_1080p"><?php _e('1080p Download Link', 'movieflix'); ?></label></th>
            <td><input type="url" id="movie_download_1080p" name="movie_download_1080p" value="<?php echo esc_attr($download_1080p); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="movie_download_4k"><?php _e('4K Download Link', 'movieflix'); ?></label></th>
            <td><input type="url" id="movie_download_4k" name="movie_download_4k" value="<?php echo esc_attr($download_4k); ?>" class="regular-text" /></td>
        </tr>
    </table>
    <?php
}

/**
 * Movie Watch Links Meta Box Callback
 */
function movieflix_movie_watch_links_callback($post) {
    $watch_480p = get_post_meta($post->ID, '_movie_watch_480p', true);
    $watch_720p = get_post_meta($post->ID, '_movie_watch_720p', true);
    $watch_1080p = get_post_meta($post->ID, '_movie_watch_1080p', true);
    $watch_4k = get_post_meta($post->ID, '_movie_watch_4k', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="movie_watch_480p"><?php _e('480p Watch Link', 'movieflix'); ?></label></th>
            <td><input type="url" id="movie_watch_480p" name="movie_watch_480p" value="<?php echo esc_attr($watch_480p); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="movie_watch_720p"><?php _e('720p Watch Link', 'movieflix'); ?></label></th>
            <td><input type="url" id="movie_watch_720p" name="movie_watch_720p" value="<?php echo esc_attr($watch_720p); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="movie_watch_1080p"><?php _e('1080p Watch Link', 'movieflix'); ?></label></th>
            <td><input type="url" id="movie_watch_1080p" name="movie_watch_1080p" value="<?php echo esc_attr($watch_1080p); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="movie_watch_4k"><?php _e('4K Watch Link', 'movieflix'); ?></label></th>
            <td><input type="url" id="movie_watch_4k" name="movie_watch_4k" value="<?php echo esc_attr($watch_4k); ?>" class="regular-text" /></td>
        </tr>
    </table>
    <?php
}

/**
 * Save Movie Meta Data
 */
function movieflix_save_movie_meta($post_id) {
    if (!isset($_POST['movieflix_movie_meta_nonce']) || !wp_verify_nonce($_POST['movieflix_movie_meta_nonce'], 'movieflix_save_movie_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save movie details
    $fields = array(
        'movie_release_year' => '_movie_release_year',
        'movie_duration' => '_movie_duration',
        'movie_imdb_rating' => '_movie_imdb_rating',
        'movie_trailer_url' => '_movie_trailer_url',
        'movie_screenshots' => '_movie_screenshots',
        'movie_download_480p' => '_movie_download_480p',
        'movie_download_720p' => '_movie_download_720p',
        'movie_download_1080p' => '_movie_download_1080p',
        'movie_download_4k' => '_movie_download_4k',
        'movie_watch_480p' => '_movie_watch_480p',
        'movie_watch_720p' => '_movie_watch_720p',
        'movie_watch_1080p' => '_movie_watch_1080p',
        'movie_watch_4k' => '_movie_watch_4k',
    );

    foreach ($fields as $field => $meta_key) {
        if (isset($_POST[$field])) {
            $value = sanitize_text_field($_POST[$field]);
            if ($field === 'movie_screenshots') {
                $value = sanitize_textarea_field($_POST[$field]);
            } elseif (strpos($field, 'url') !== false || strpos($field, 'download') !== false || strpos($field, 'watch') !== false) {
                $value = esc_url_raw($_POST[$field]);
            }
            update_post_meta($post_id, $meta_key, $value);
        }
    }
}
add_action('save_post', 'movieflix_save_movie_meta');

/**
 * Track Movie Views
 */
function movieflix_track_movie_views() {
    if (is_singular('movie')) {
        global $post;
        $views = get_post_meta($post->ID, '_movie_views', true);
        $views = $views ? intval($views) + 1 : 1;
        update_post_meta($post->ID, '_movie_views', $views);
    }
}
add_action('wp_head', 'movieflix_track_movie_views');

/**
 * AJAX Search Handler
 */
function movieflix_ajax_search() {
    check_ajax_referer('movieflix_nonce', 'nonce');
    
    $search_term = sanitize_text_field($_POST['search_term']);
    
    if (empty($search_term)) {
        wp_send_json_error('Empty search term');
        return;
    }
    
    $movies = get_posts(array(
        'post_type' => 'movie',
        'posts_per_page' => 10,
        's' => $search_term,
        'post_status' => 'publish',
    ));
    
    $results = array();
    
    foreach ($movies as $movie) {
        $poster = get_the_post_thumbnail_url($movie->ID, 'movie-thumb');
        if (!$poster) {
            $poster = esc_url(get_template_directory_uri() . '/images/no-poster.jpg');
        }
        
        $year = get_post_meta($movie->ID, '_movie_release_year', true);
        $rating = get_post_meta($movie->ID, '_movie_imdb_rating', true);
        
        $results[] = array(
            'title' => $movie->post_title,
            'url' => get_permalink($movie->ID),
            'poster' => $poster,
            'year' => $year,
            'rating' => $rating,
        );
    }
    
    wp_send_json_success($results);
}
add_action('wp_ajax_movieflix_search', 'movieflix_ajax_search');
add_action('wp_ajax_nopriv_movieflix_search', 'movieflix_ajax_search');

/**
 * AJAX Advanced Filter Handler
 */
function movieflix_filter_movies_advanced() {
    check_ajax_referer('movieflix_nonce', 'nonce');
    
    $filters = isset($_POST['filters']) ? $_POST['filters'] : array();
    $category = sanitize_text_field($_POST['category']);
    $paged = intval($_POST['paged']);
    
    $args = array(
        'post_type' => 'movie',
        'posts_per_page' => movieflix_get_movies_per_page(),
        'paged' => $paged,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    $tax_query = array('relation' => 'AND');
    
    // Category filter
    if ($category && $category !== 'all') {
        $tax_query[] = array(
            'taxonomy' => 'movie_genre',
            'field' => 'slug',
            'terms' => $category,
        );
    }
    
    // Additional filters
    if (!empty($filters)) {
        foreach ($filters as $filter_type => $filter_value) {
            if (!empty($filter_value)) {
                $taxonomy = '';
                switch ($filter_type) {
                    case 'year':
                        $taxonomy = 'movie_year';
                        break;
                    case 'quality':
                        $taxonomy = 'movie_quality';
                        break;
                    case 'language':
                        $taxonomy = 'movie_language';
                        break;
                }
                
                if ($taxonomy) {
                    $tax_query[] = array(
                        'taxonomy' => $taxonomy,
                        'field' => 'slug',
                        'terms' => $filter_value,
                    );
                }
            }
        }
    }
    
    if (count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
    }
    
    $query = new WP_Query($args);
    
    $movies = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            $poster = get_the_post_thumbnail_url(get_the_ID(), 'movie-poster');
            if (!$poster) {
                $poster = esc_url(get_template_directory_uri() . '/images/no-poster.jpg');
            }
            
            $year = get_post_meta(get_the_ID(), '_movie_release_year', true);
            $rating = get_post_meta(get_the_ID(), '_movie_imdb_rating', true);
            $views = get_post_meta(get_the_ID(), '_movie_views', true);
            
            $qualities = get_the_terms(get_the_ID(), 'movie_quality');
            $quality_names = array();
            if ($qualities && !is_wp_error($qualities)) {
                foreach ($qualities as $quality) {
                    $quality_names[] = $quality->name;
                }
            }
            
            $movies[] = array(
                'title' => get_the_title(),
                'url' => get_permalink(),
                'poster' => $poster,
                'year' => $year,
                'rating' => $rating,
                'views' => $views,
                'quality' => $quality_names,
            );
        }
        wp_reset_postdata();
    }
    
    $response = array(
        'movies' => $movies,
        'max_pages' => $query->max_num_pages,
        'found_posts' => $query->found_posts,
    );
    
    wp_send_json_success($response);
}
add_action('wp_ajax_movieflix_filter_advanced', 'movieflix_filter_movies_advanced');
add_action('wp_ajax_nopriv_movieflix_filter_advanced', 'movieflix_filter_movies_advanced');

/**
 * AJAX Download Tracking
 */
function movieflix_track_download() {
    check_ajax_referer('movieflix_nonce', 'nonce');
    
    $movie_id = intval($_POST['movie_id']);
    $quality = sanitize_text_field($_POST['quality']);
    
    if ($movie_id && $quality) {
        $meta_key = '_download_count_' . $quality;
        $current_count = get_post_meta($movie_id, $meta_key, true);
        $new_count = $current_count ? intval($current_count) + 1 : 1;
        update_post_meta($movie_id, $meta_key, $new_count);
        
        wp_send_json_success(array('count' => $new_count));
    } else {
        wp_send_json_error('Invalid data');
    }
}
add_action('wp_ajax_movieflix_track_download', 'movieflix_track_download');
add_action('wp_ajax_nopriv_movieflix_track_download', 'movieflix_track_download');

/**
 * AJAX Watch Tracking
 */
function movieflix_track_watch() {
    check_ajax_referer('movieflix_nonce', 'nonce');
    
    $movie_id = intval($_POST['movie_id']);
    $quality = sanitize_text_field($_POST['quality']);
    
    if ($movie_id && $quality) {
        $meta_key = '_watch_count_' . $quality;
        $current_count = get_post_meta($movie_id, $meta_key, true);
        $new_count = $current_count ? intval($current_count) + 1 : 1;
        update_post_meta($movie_id, $meta_key, $new_count);
        
        wp_send_json_success(array('count' => $new_count));
    } else {
        wp_send_json_error('Invalid data');
    }
}
add_action('wp_ajax_movieflix_track_watch', 'movieflix_track_watch');
add_action('wp_ajax_nopriv_movieflix_track_watch', 'movieflix_track_watch');

/**
 * Contact Form Handler
 */
function movieflix_handle_contact_form() {
    check_ajax_referer('movieflix_contact_form', 'nonce');
    
    $name = sanitize_text_field($_POST['contact_name']);
    $email = sanitize_email($_POST['contact_email']);
    $subject = sanitize_text_field($_POST['contact_subject']);
    $message = sanitize_textarea_field($_POST['contact_message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        wp_send_json_error(array('message' => __('Please fill in all required fields.', 'movieflix')));
        return;
    }
    
    if (!is_email($email)) {
        wp_send_json_error(array('message' => __('Please enter a valid email address.', 'movieflix')));
        return;
    }
    
    if (strlen($message) < 10) {
        wp_send_json_error(array('message' => __('Message must be at least 10 characters long.', 'movieflix')));
        return;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'movieflix_contacts';
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'status' => 'unread',
            'created_at' => current_time('mysql'),
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s')
    );
    
    if ($result !== false) {
        wp_send_json_success(array('message' => __('Thank you for your message! We will get back to you soon.', 'movieflix')));
    } else {
        wp_send_json_error(array('message' => __('Failed to send message. Please try again later.', 'movieflix')));
    }
}
add_action('wp_ajax_movieflix_contact_form', 'movieflix_handle_contact_form');
add_action('wp_ajax_nopriv_movieflix_contact_form', 'movieflix_handle_contact_form');

/**
 * Movie Comments Handler
 */
function movieflix_submit_comment() {
    check_ajax_referer('movieflix_nonce', 'nonce');
    
    $movie_id = intval($_POST['movie_id']);
    $parent_id = intval($_POST['parent_id']);
    $author_name = sanitize_text_field($_POST['author_name']);
    $author_email = sanitize_email($_POST['author_email']);
    $comment_content = sanitize_textarea_field($_POST['comment_content']);
    
    if (empty($author_name) || empty($author_email) || empty($comment_content)) {
        wp_send_json_error(array('message' => __('Please fill in all required fields.', 'movieflix')));
        return;
    }
    
    if (!is_email($author_email)) {
        wp_send_json_error(array('message' => __('Please enter a valid email address.', 'movieflix')));
        return;
    }
    
    if (strlen($comment_content) < 5) {
        wp_send_json_error(array('message' => __('Comment must be at least 5 characters long.', 'movieflix')));
        return;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'movieflix_comments';
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'movie_id' => $movie_id,
            'parent_id' => $parent_id,
            'author_name' => $author_name,
            'author_email' => $author_email,
            'comment_content' => $comment_content,
            'status' => 'approved',
            'created_at' => current_time('mysql'),
        ),
        array('%d', '%d', '%s', '%s', '%s', '%s', '%s')
    );
    
    if ($result !== false) {
        wp_send_json_success(array('message' => __('Comment posted successfully!', 'movieflix')));
    } else {
        wp_send_json_error(array('message' => __('Failed to post comment. Please try again later.', 'movieflix')));
    }
}
add_action('wp_ajax_movieflix_submit_comment', 'movieflix_submit_comment');
add_action('wp_ajax_nopriv_movieflix_submit_comment', 'movieflix_submit_comment');

/**
 * Get Movie Comments
 */
function movieflix_get_movie_comments($movie_id, $parent_id = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'movieflix_comments';
    
    $comments = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE movie_id = %d AND parent_id = %d AND status = 'approved' ORDER BY created_at DESC",
        $movie_id,
        $parent_id
    ));
    
    return $comments;
}

/**
 * Get Related Movies
 */
function movieflix_get_related_movies($movie_id, $limit = 5) {
    $genres = get_the_terms($movie_id, 'movie_genre');
    
    if (!$genres || is_wp_error($genres)) {
        return new WP_Query(array(
            'post_type' => 'movie',
            'posts_per_page' => $limit,
            'post__not_in' => array($movie_id),
            'orderby' => 'rand',
        ));
    }
    
    $genre_ids = array();
    foreach ($genres as $genre) {
        $genre_ids[] = $genre->term_id;
    }
    
    return new WP_Query(array(
        'post_type' => 'movie',
        'posts_per_page' => $limit,
        'post__not_in' => array($movie_id),
        'tax_query' => array(
            array(
                'taxonomy' => 'movie_genre',
                'field' => 'term_id',
                'terms' => $genre_ids,
            ),
        ),
        'orderby' => 'rand',
    ));
}

/**
 * Get Movies Per Page Setting
 */
function movieflix_get_movies_per_page() {
    return get_theme_mod('movieflix_movies_per_page', 24);
}

/**
 * Create Database Tables
 */
function movieflix_create_tables() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // Contacts table
    $contacts_table = $wpdb->prefix . 'movieflix_contacts';
    $contacts_sql = "CREATE TABLE $contacts_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        subject varchar(200) NOT NULL,
        message text NOT NULL,
        status varchar(20) DEFAULT 'unread',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    // Comments table
    $comments_table = $wpdb->prefix . 'movieflix_comments';
    $comments_sql = "CREATE TABLE $comments_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        movie_id mediumint(9) NOT NULL,
        parent_id mediumint(9) DEFAULT 0,
        author_name varchar(100) NOT NULL,
        author_email varchar(100) NOT NULL,
        comment_content text NOT NULL,
        status varchar(20) DEFAULT 'approved',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY movie_id (movie_id),
        KEY parent_id (parent_id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($contacts_sql);
    dbDelta($comments_sql);
}
add_action('after_switch_theme', 'movieflix_create_tables');

/**
 * Theme Customizer
 */
function movieflix_customize_register($wp_customize) {
    // MovieFlix Theme Options Section
    $wp_customize->add_section('movieflix_theme_options', array(
        'title' => __('MovieFlix Theme Options', 'movieflix'),
        'priority' => 30,
    ));
    
    // Primary Color
    $wp_customize->add_setting('movieflix_primary_color', array(
        'default' => '#e50914',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'movieflix_primary_color', array(
        'label' => __('Primary Color', 'movieflix'),
        'section' => 'movieflix_theme_options',
        'settings' => 'movieflix_primary_color',
    )));
    
    // Movies Per Page
    $wp_customize->add_setting('movieflix_movies_per_page', array(
        'default' => 24,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('movieflix_movies_per_page', array(
        'label' => __('Movies Per Page', 'movieflix'),
        'section' => 'movieflix_theme_options',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 100,
        ),
    ));
    
    // Telegram Button URL
    $wp_customize->add_setting('movieflix_telegram_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('movieflix_telegram_url', array(
        'label' => __('Telegram Channel URL', 'movieflix'),
        'description' => __('Enter your Telegram channel URL to show a "Join Telegram" button in the menu', 'movieflix'),
        'section' => 'movieflix_theme_options',
        'type' => 'url',
    ));
    
    // Footer Text
    $wp_customize->add_setting('movieflix_footer_text', array(
        'default' => '© ' . date('Y') . ' MovieFlix. All rights reserved.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control('movieflix_footer_text', array(
        'label' => __('Footer Text', 'movieflix'),
        'section' => 'movieflix_theme_options',
        'type' => 'text',
    ));
    
    // Dynamic Pages Section
    $wp_customize->add_section('movieflix_dynamic_pages', array(
        'title' => __('Dynamic Pages Manager', 'movieflix'),
        'priority' => 35,
    ));
    
    // About Us Page
    $wp_customize->add_setting('movieflix_enable_about', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('movieflix_enable_about', array(
        'label' => __('Enable About Us Page', 'movieflix'),
        'section' => 'movieflix_dynamic_pages',
        'type' => 'checkbox',
    ));
    
    $wp_customize->add_setting('movieflix_about_content', array(
        'default' => 'Welcome to MovieFlix, your ultimate destination for the latest movies and entertainment.',
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control('movieflix_about_content', array(
        'label' => __('About Us Content', 'movieflix'),
        'section' => 'movieflix_dynamic_pages',
        'type' => 'textarea',
        'active_callback' => function() use ($wp_customize) {
            return $wp_customize->get_setting('movieflix_enable_about')->value();
        },
    ));
    
    // Contact Us Page
    $wp_customize->add_setting('movieflix_enable_contact', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('movieflix_enable_contact', array(
        'label' => __('Enable Contact Us Page', 'movieflix'),
        'section' => 'movieflix_dynamic_pages',
        'type' => 'checkbox',
    ));
    
    $wp_customize->add_setting('movieflix_contact_content', array(
        'default' => 'Get in touch with us for any questions or suggestions.',
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control('movieflix_contact_content', array(
        'label' => __('Contact Us Content', 'movieflix'),
        'section' => 'movieflix_dynamic_pages',
        'type' => 'textarea',
        'active_callback' => function() use ($wp_customize) {
            return $wp_customize->get_setting('movieflix_enable_contact')->value();
        },
    ));
    
    // Privacy Policy Page
    $wp_customize->add_setting('movieflix_enable_privacy', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('movieflix_enable_privacy', array(
        'label' => __('Enable Privacy Policy Page', 'movieflix'),
        'section' => 'movieflix_dynamic_pages',
        'type' => 'checkbox',
    ));
    
    $wp_customize->add_setting('movieflix_privacy_content', array(
        'default' => 'Your privacy is important to us. This privacy policy explains how we collect and use your information.',
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control('movieflix_privacy_content', array(
        'label' => __('Privacy Policy Content', 'movieflix'),
        'section' => 'movieflix_dynamic_pages',
        'type' => 'textarea',
        'active_callback' => function() use ($wp_customize) {
            return $wp_customize->get_setting('movieflix_enable_privacy')->value();
        },
    ));
}
add_action('customize_register', 'movieflix_customize_register');

/**
 * Customizer Live Preview
 */
function movieflix_customize_preview_js() {
    wp_enqueue_script('movieflix-customizer', get_template_directory_uri() . '/js/customizer.js', array('customize-preview'), '1.8.0', true);
}
add_action('customize_preview_init', 'movieflix_customize_preview_js');

/**
 * Replacement for deprecated get_page_by_title function
 */
function movieflix_get_page_by_title($page_title, $output = OBJECT, $post_type = 'page') {
    $query = new WP_Query(array(
        'post_type' => $post_type,
        'title' => $page_title,
        'post_status' => 'all',
        'numberposts' => 1,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
        'orderby' => 'post_date ID',
        'order' => 'ASC',
    ));
    
    if (!empty($query->posts)) {
        $page_got = $query->posts[0];
    } else {
        $page_got = null;
    }
    
    return $page_got;
}

/**
 * Create Dynamic Pages
 */
function movieflix_create_dynamic_pages() {
    // Create About page
    if (get_theme_mod('movieflix_enable_about', true)) {
        $about_page = movieflix_get_page_by_title('About Us');
        if (!$about_page) {
            $about_id = wp_insert_post(array(
                'post_title' => 'About Us',
                'post_content' => get_theme_mod('movieflix_about_content', 'Welcome to MovieFlix, your ultimate destination for the latest movies and entertainment.'),
                'post_status' => 'publish',
                'post_type' => 'page',
            ));
            update_option('movieflix_dynamic_page_about', $about_id);
        } else {
            update_option('movieflix_dynamic_page_about', $about_page->ID);
        }
    }
    
    // Create Contact page
    if (get_theme_mod('movieflix_enable_contact', true)) {
        $contact_page = movieflix_get_page_by_title('Contact Us');
        if (!$contact_page) {
            $contact_id = wp_insert_post(array(
                'post_title' => 'Contact Us',
                'post_content' => get_theme_mod('movieflix_contact_content', 'Get in touch with us for any questions or suggestions.'),
                'post_status' => 'publish',
                'post_type' => 'page',
            ));
            update_option('movieflix_dynamic_page_contact', $contact_id);
        } else {
            update_option('movieflix_dynamic_page_contact', $contact_page->ID);
        }
    }
    
    // Create Privacy Policy page
    if (get_theme_mod('movieflix_enable_privacy', true)) {
        $privacy_page = movieflix_get_page_by_title('Privacy Policy');
        if (!$privacy_page) {
            $privacy_id = wp_insert_post(array(
                'post_title' => 'Privacy Policy',
                'post_content' => get_theme_mod('movieflix_privacy_content', 'Your privacy is important to us. This privacy policy explains how we collect and use your information.'),
                'post_status' => 'publish',
                'post_type' => 'page',
            ));
            update_option('movieflix_dynamic_page_privacy', $privacy_id);
        } else {
            update_option('movieflix_dynamic_page_privacy', $privacy_page->ID);
        }
    }
}
add_action('after_switch_theme', 'movieflix_create_dynamic_pages');

/**
 * Add Dynamic Pages to Menu
 */
function movieflix_add_dynamic_pages_to_menu($items, $args) {
    if ($args->theme_location == 'primary') {
        $dynamic_items = '';
        
        // Add About page
        if (get_theme_mod('movieflix_enable_about', true)) {
            $about_id = get_option('movieflix_dynamic_page_about');
            if ($about_id) {
                $dynamic_items .= '<li class="menu-item"><a href="' . get_permalink($about_id) . '">About Us</a></li>';
            }
        }
        
        // Add Contact page
        if (get_theme_mod('movieflix_enable_contact', true)) {
            $contact_id = get_option('movieflix_dynamic_page_contact');
            if ($contact_id) {
                $dynamic_items .= '<li class="menu-item"><a href="' . get_permalink($contact_id) . '">Contact Us</a></li>';
            }
        }
        
        // Add Privacy Policy page
        if (get_theme_mod('movieflix_enable_privacy', true)) {
            $privacy_id = get_option('movieflix_dynamic_page_privacy');
            if ($privacy_id) {
                $dynamic_items .= '<li class="menu-item"><a href="' . get_permalink($privacy_id) . '">Privacy Policy</a></li>';
            }
        }
        
        // Add Telegram button
        $telegram_url = get_theme_mod('movieflix_telegram_url', '');
        if (!empty($telegram_url)) {
            $dynamic_items .= '<li class="menu-item telegram-menu-item"><a href="' . esc_url($telegram_url) . '" target="_blank" rel="noopener noreferrer" class="telegram-btn">📱 Join Telegram</a></li>';
        }
        
        $items .= $dynamic_items;
    }
    
    return $items;
}
add_filter('wp_nav_menu_items', 'movieflix_add_dynamic_pages_to_menu', 10, 2);

/**
 * Include Admin Dashboard
 */
require_once get_template_directory() . '/admin/movie-dashboard.php';

/**
 * Custom CSS for Primary Color
 */
function movieflix_custom_css() {
    $primary_color = get_theme_mod('movieflix_primary_color', '#e50914');
    
    if ($primary_color !== '#e50914') {
        echo '<style type="text/css">';
        echo ':root { --primary-color: ' . esc_attr($primary_color) . '; }';
        echo '</style>';
    }
}
add_action('wp_head', 'movieflix_custom_css');

/**
 * Flush Rewrite Rules on Theme Activation
 */
function movieflix_flush_rewrite_rules() {
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'movieflix_flush_rewrite_rules');

/**
 * Add Body Classes
 */
function movieflix_body_classes($classes) {
    if (is_singular('movie')) {
        $classes[] = 'single-movie';
    }
    
    if (is_post_type_archive('movie') || is_home()) {
        $classes[] = 'movie-archive';
    }
    
    return $classes;
}
add_filter('body_class', 'movieflix_body_classes');

/**
 * Modify Main Query for Movie Archive
 */
function movieflix_modify_main_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_home()) {
            $query->set('post_type', array('movie'));
            $query->set('posts_per_page', movieflix_get_movies_per_page());
        }
    }
}
add_action('pre_get_posts', 'movieflix_modify_main_query');

/**
 * Enhanced Search Query Modification
 */
function movieflix_modify_search_query($query) {
    if (!is_admin() && $query->is_main_query() && is_search()) {
        // Only search in movies
        $query->set('post_type', array('movie'));
        
        // Handle taxonomy filters
        $tax_query = array('relation' => 'AND');
        
        if (isset($_GET['genre']) && !empty($_GET['genre'])) {
            $tax_query[] = array(
                'taxonomy' => 'movie_genre',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['genre']),
            );
        }
        
        if (isset($_GET['year']) && !empty($_GET['year'])) {
            $tax_query[] = array(
                'taxonomy' => 'movie_year',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['year']),
            );
        }
        
        if (isset($_GET['quality']) && !empty($_GET['quality'])) {
            $tax_query[] = array(
                'taxonomy' => 'movie_quality',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['quality']),
            );
        }
        
        if (count($tax_query) > 1) {
            $query->set('tax_query', $tax_query);
        }
        
        // Handle sorting
        if (isset($_GET['orderby']) && !empty($_GET['orderby'])) {
            $orderby = sanitize_text_field($_GET['orderby']);
            
            switch ($orderby) {
                case 'title':
                    $query->set('orderby', 'title');
                    $query->set('order', 'ASC');
                    break;
                    
                case 'views':
                    $query->set('meta_key', '_movie_views');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
                    
                case 'rating':
                    $query->set('meta_key', '_movie_imdb_rating');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
                    
                default:
                    $query->set('orderby', 'date');
                    $query->set('order', 'DESC');
                    break;
            }
        } else {
            $query->set('orderby', 'date');
            $query->set('order', 'DESC');
        }
        
        // Set posts per page
        $query->set('posts_per_page', movieflix_get_movies_per_page());
        
        // Improve search relevance
        add_filter('posts_search', 'movieflix_improve_search_relevance', 10, 2);
    }
}
add_action('pre_get_posts', 'movieflix_modify_search_query');

/**
 * Improve Search Relevance
 */
function movieflix_improve_search_relevance($search, $wp_query) {
    if (!is_search() || empty($search)) {
        return $search;
    }
    
    global $wpdb;
    
    $search_term = $wp_query->get('s');
    if (empty($search_term)) {
        return $search;
    }
    
    // Search in title, content, and meta fields
    $search = '';
    $search_term = $wpdb->esc_like($search_term);
    
    $search .= " AND (";
    $search .= "({$wpdb->posts}.post_title LIKE '%{$search_term}%')";
    $search .= " OR ({$wpdb->posts}.post_content LIKE '%{$search_term}%')";
    $search .= " OR EXISTS (";
    $search .= "SELECT 1 FROM {$wpdb->postmeta} WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID";
    $search .= " AND {$wpdb->postmeta}.meta_value LIKE '%{$search_term}%'";
    $search .= ")";
    $search .= ")";
    
    return $search;
}

/**
 * Enhanced AJAX Search with Better Results
 */
function movieflix_ajax_search_enhanced() {
    check_ajax_referer('movieflix_nonce', 'nonce');
    
    $search_term = sanitize_text_field($_POST['search_term']);
    
    if (empty($search_term) || strlen($search_term) < 2) {
        wp_send_json_error('Search term too short');
        return;
    }
    
    // Enhanced search query
    $args = array(
        'post_type' => 'movie',
        'posts_per_page' => 10,
        's' => $search_term,
        'post_status' => 'publish',
        'orderby' => 'relevance',
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => '_movie_release_year',
                'value' => $search_term,
                'compare' => 'LIKE'
            ),
            array(
                'key' => '_movie_imdb_rating',
                'value' => $search_term,
                'compare' => 'LIKE'
            )
        )
    );
    
    $movies = get_posts($args);
    
    // If no results, try broader search
    if (empty($movies)) {
        $args['meta_query'] = array();
        $args['posts_per_page'] = 5;
        $movies = get_posts($args);
    }
    
    $results = array();
    
    foreach ($movies as $movie) {
        $poster = get_the_post_thumbnail_url($movie->ID, 'movie-thumb');
        if (!$poster) {
            $poster = esc_url(get_template_directory_uri() . '/images/no-poster.jpg');
        }
        
        $year = get_post_meta($movie->ID, '_movie_release_year', true);
        $rating = get_post_meta($movie->ID, '_movie_imdb_rating', true);
        $views = get_post_meta($movie->ID, '_movie_views', true);
        
        // Get genres
        $genres = get_the_terms($movie->ID, 'movie_genre');
        $genre_names = array();
        if ($genres && !is_wp_error($genres)) {
            foreach ($genres as $genre) {
                $genre_names[] = $genre->name;
            }
        }
        
        $results[] = array(
            'title' => $movie->post_title,
            'url' => get_permalink($movie->ID),
            'poster' => $poster,
            'year' => $year,
            'rating' => $rating,
            'views' => $views ? number_format($views) : '0',
            'genres' => implode(', ', $genre_names),
            'excerpt' => wp_trim_words(get_the_excerpt($movie->ID), 15)
        );
    }
    
    wp_send_json_success($results);
}
add_action('wp_ajax_movieflix_search_enhanced', 'movieflix_ajax_search_enhanced');
add_action('wp_ajax_nopriv_movieflix_search_enhanced', 'movieflix_ajax_search_enhanced');

/**
 * Add Theme Support for Block Editor
 */
function movieflix_block_editor_support() {
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    add_editor_style('style.css');
}
add_action('after_setup_theme', 'movieflix_block_editor_support');

/**
 * Add Structured Data for Movies
 */
function movieflix_add_movie_structured_data() {
    if (is_singular('movie')) {
        global $post;
        
        $title = get_the_title();
        $description = get_the_excerpt() ?: wp_trim_words(get_the_content(), 20);
        $image = get_the_post_thumbnail_url($post->ID, 'full');
        $year = get_post_meta($post->ID, '_movie_release_year', true);
        $rating = get_post_meta($post->ID, '_movie_imdb_rating', true);
        $duration = get_post_meta($post->ID, '_movie_duration', true);
        
        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'Movie',
            'name' => $title,
            'description' => $description,
            'url' => get_permalink(),
        );
        
        if ($image) {
            $structured_data['image'] = $image;
        }
        
        if ($year) {
            $structured_data['dateCreated'] = $year;
        }
        
        if ($rating) {
            $structured_data['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $rating,
                'bestRating' => '10',
                'worstRating' => '1',
            );
        }
        
        if ($duration) {
            $structured_data['duration'] = 'PT' . $duration . 'M';
        }
        
        echo '<script type="application/ld+json">' . wp_json_encode($structured_data) . '</script>';
    }
}
add_action('wp_head', 'movieflix_add_movie_structured_data');

/**
 * Add post class support
 */
function movieflix_post_class($classes, $class, $post_id) {
    if (is_singular('movie')) {
        $classes[] = 'movie-post';
    }
    return $classes;
}
add_filter('post_class', 'movieflix_post_class', 10, 3);

/**
 * Add wp_link_pages support
 */
function movieflix_link_pages() {
    $args = array(
        'before' => '<div class="page-links">' . __('Pages:', 'movieflix'),
        'after' => '</div>',
        'link_before' => '<span class="page-number">',
        'link_after' => '</span>',
    );
    wp_link_pages($args);
}