<?php get_header(); ?>

<main class="main-content">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <div class="movie-header">
                <div class="movie-poster-large">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('movie-poster'); ?>
                    <?php endif; ?>
                </div>

                <div class="movie-details">
                    <h1><?php the_title(); ?></h1>

                    <div class="movie-meta-details">
                        <?php
                        $release_year = get_post_meta(get_the_ID(), '_movie_release_year', true);
                        $duration = get_post_meta(get_the_ID(), '_movie_duration', true);
                        $imdb_rating = get_post_meta(get_the_ID(), '_movie_imdb_rating', true);
                        ?>

                        <?php if ($release_year) : ?>
                            <div class="meta-item">
                                <div class="meta-label">Release Year</div>
                                <div class="meta-value"><?php echo esc_html($release_year); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($duration) : ?>
                            <div class="meta-item">
                                <div class="meta-label">Duration</div>
                                <div class="meta-value"><?php echo esc_html($duration); ?> min</div>
                            </div>
                        <?php endif; ?>

                        <?php if ($imdb_rating) : ?>
                            <div class="meta-item">
                                <div class="meta-label">IMDB Rating</div>
                                <div class="meta-value">
                                    <span class="rating-stars">⭐</span>
                                    <?php echo esc_html($imdb_rating); ?>/10
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="meta-item">
                            <div class="meta-label">Views</div>
                            <div class="meta-value">
                                <?php
                                $views = get_post_meta(get_the_ID(), '_movie_views', true);
                                $views = $views ? intval($views) : 0;
                                echo number_format($views);
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="movie-description">
                        <?php the_content(); ?>
                        <?php movieflix_link_pages(); ?>
                    </div>
                </div>
            </div>

            <!-- Social Sharing Section -->
            <div class="social-sharing-section">
                <h3>Share This Movie</h3>
                <div class="social-sharing-buttons">
                    <?php
                    $movie_url = urlencode(get_permalink());
                    $movie_title = urlencode(get_the_title());
                    $movie_image = urlencode(get_the_post_thumbnail_url(get_the_ID(), 'movie-backdrop'));
                    ?>

                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $movie_url; ?>" target="_blank" class="social-btn facebook-btn" title="Share on Facebook">
                        Facebook
                    </a>

                    <a href="https://twitter.com/intent/tweet?url=<?php echo $movie_url; ?>&text=<?php echo $movie_title; ?>" target="_blank" class="social-btn twitter-btn" title="Share on Twitter">
                        Twitter
                    </a>

                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $movie_url; ?>" target="_blank" class="social-btn linkedin-btn" title="Share on LinkedIn">
                        LinkedIn
                    </a>

                    <a href="https://wa.me/?text=<?php echo $movie_title; ?>%20<?php echo $movie_url; ?>" target="_blank" class="social-btn whatsapp-btn" title="Share on WhatsApp">
                        WhatsApp
                    </a>

                    <button class="social-btn copy-btn" onclick="copyToClipboard('<?php echo get_permalink(); ?>')" title="Copy Link">
                        Copy Link
                    </button>
                </div>
            </div>

            <?php
            $trailer_url = get_post_meta(get_the_ID(), '_movie_trailer_url', true);
            if ($trailer_url) :
                $parsed_url = wp_parse_url($trailer_url);
                $video_id = '';

                if (isset($parsed_url['host'])) {
                    if ($parsed_url['host'] === 'youtu.be') {
                        $video_id = ltrim($parsed_url['path'], '/');
                    } elseif (strpos($parsed_url['host'], 'youtube.com') !== false) {
                        if (isset($parsed_url['query'])) {
                            parse_str($parsed_url['query'], $query_vars);
                            if (isset($query_vars['v'])) {
                                $video_id = $query_vars['v'];
                            }
                        }
                    }
                }

                if ($video_id) :
                    $embed_url = 'https://www.youtube.com/embed/' . $video_id;
                    ?>
                    <div class="video-player">
                        <h3 style="text-align: center;">Trailer</h3>
                        <iframe width="100%" height="400" src="<?php echo esc_url($embed_url); ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                <?php endif;
            endif;
            ?>

            <?php
            $watch_links = array(
                '480p' => get_post_meta(get_the_ID(), '_movie_watch_480p', true),
                '720p' => get_post_meta(get_the_ID(), '_movie_watch_720p', true),
                '1080p' => get_post_meta(get_the_ID(), '_movie_watch_1080p', true),
                '4K' => get_post_meta(get_the_ID(), '_movie_watch_4k', true),
            );

            $download_links = array(
                '480p' => get_post_meta(get_the_ID(), '_movie_download_480p', true),
                '720p' => get_post_meta(get_the_ID(), '_movie_download_720p', true),
                '1080p' => get_post_meta(get_the_ID(), '_movie_download_1080p', true),
                '4K' => get_post_meta(get_the_ID(), '_movie_download_4k', true),
            );

            $has_watch_links = array_filter($watch_links);
            $has_download_links = array_filter($download_links);

            if ($has_watch_links || $has_download_links) :
                ?>
                <div class="movie-actions-section">

                    <?php if ($has_watch_links) : ?>
                        <div class="watch-section">
                            <h3>Watch Now</h3>
                            <div class="watch-links">
                                <?php foreach ($watch_links as $quality => $url) : ?>
                                    <?php if ($url) : ?>
                                        <a href="<?php echo esc_url($url); ?>" class="watch-btn" data-quality="<?php echo esc_attr(strtolower($quality)); ?>" data-movie-id="<?php echo get_the_ID(); ?>" target="_blank" rel="noopener noreferrer">
                                            Watch <?php echo esc_html($quality); ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($has_download_links) : ?>
                        <div class="download-section">
                            <h3>Download Links</h3>
                            <div class="download-links">
                                <?php foreach ($download_links as $quality => $url) : ?>
                                    <?php if ($url) : ?>
                                        <a href="<?php echo esc_url($url); ?>" class="download-btn" data-quality="<?php echo esc_attr(strtolower($quality)); ?>" data-movie-id="<?php echo get_the_ID(); ?>">
                                            Download <?php echo esc_html($quality); ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

            <?php
            $screenshots = get_post_meta(get_the_ID(), '_movie_screenshots', true);
            if ($screenshots) :
                $screenshot_urls = explode(',', $screenshots);
                ?>
                <div class="screenshots-gallery">
                    <h3>Screenshots</h3>
                    <div class="screenshot-grid">
                        <?php foreach ($screenshot_urls as $screenshot_url) : ?>
                            <img src="<?php echo esc_url(trim($screenshot_url)); ?>" alt="Movie Screenshot" class="lazy-load">
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Comments Section -->
            <div class="comments-section" id="comments-section">
                <div class="section-header">
                    <h3>Comments</h3>
                </div>

                <div class="comment-form-section">
                    <h4>Leave a Comment</h4>
                    <div class="comment-form-messages"></div>
                    <form id="movie-comment-form" class="comment-form">
                        <input type="hidden" name="movie_id" value="<?php echo get_the_ID(); ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="comment_author_name">Name *</label>
                                <input type="text" id="comment_author_name" name="author_name" required>
                            </div>
                            <div class="form-group">
                                <label for="comment_author_email">Email *</label>
                                <input type="email" id="comment_author_email" name="author_email" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment_content">Your Comment *</label>
                            <textarea id="comment_content" name="comment_content" rows="5" required placeholder="Share your thoughts about this movie..."></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary comment-submit-btn">
                                <span class="btn-text">Post Comment</span>
                                <span class="btn-loading" style="display: none;">
                                    <span class="loading-spinner small"></span>
                                    Posting...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="comments-list">
                    <?php
                    $comments = movieflix_get_movie_comments(get_the_ID());
                    if (!empty($comments)) :
                        ?>
                        <h4>Comments (<?php echo count($comments); ?>)</h4>
                        <?php foreach ($comments as $comment) : ?>
                            <div class="comment-item" data-comment-id="<?php echo $comment->id; ?>">
                                <div class="comment-avatar">
                                    <div class="avatar-placeholder"><?php echo strtoupper(substr($comment->author_name, 0, 1)); ?></div>
                                </div>
                                <div class="comment-content">
                                    <div class="comment-header">
                                        <h5 class="comment-author"><?php echo esc_html($comment->author_name); ?></h5>
                                        <span class="comment-date"><?php echo esc_html(date('M j, Y g:i A', strtotime($comment->created_at))); ?></span>
                                    </div>
                                    <div class="comment-text"><?php echo nl2br(esc_html($comment->comment_content)); ?></div>
                                    <div class="comment-actions">
                                        <button class="reply-btn" data-comment-id="<?php echo $comment->id; ?>">Reply</button>
                                    </div>
                                </div>
                            </div>
                            <?php $replies = movieflix_get_movie_comments(get_the_ID(), $comment->id); ?>
                            <?php if (!empty($replies)) : ?>
                                <div class="comment-replies">
                                    <?php foreach ($replies as $reply) : ?>
                                        <div class="comment-item reply-comment" data-comment-id="<?php echo $reply->id; ?>">
                                            <div class="comment-avatar">
                                                <div class="avatar-placeholder"><?php echo strtoupper(substr($reply->author_name, 0, 1)); ?></div>
                                            </div>
                                            <div class="comment-content">
                                                <div class="comment-header">
                                                    <h5 class="comment-author"><?php echo esc_html($reply->author_name); ?></h5>
                                                    <span class="comment-date"><?php echo esc_html(date('M j, Y g:i A', strtotime($reply->created_at))); ?></span>
                                                </div>
                                                <div class="comment-text"><?php echo nl2br(esc_html($reply->comment_content)); ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="no-comments">
                            <p>No comments yet. Be the first to share your thoughts!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="related-movies-section">
                <h3>Related Movies</h3>
                <?php $related_movies = movieflix_get_related_movies(get_the_ID(), 5); ?>
                <?php if ($related_movies->have_posts()) : ?>
                    <div class="related-movies-grid">
                        <?php while ($related_movies->have_posts()) : $related_movies->the_post(); ?>
                            <div class="related-movie-card">
                                <div class="related-movie-poster">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('movie-thumb'); ?>
                                        <?php else : ?>
                                            <img src="<?php echo esc_url(get_template_directory_uri() . '/images/no-poster.jpg'); ?>" alt="No Poster">
                                        <?php endif; ?>
                                    </a>
                                </div>
                                <div class="related-movie-info">
                                    <h4 class="related-movie-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                    <div class="related-movie-meta">
                                        <span class="related-movie-year">
                                            <?php
                                            $year = get_post_meta(get_the_ID(), '_movie_release_year', true);
                                            echo $year ? esc_html($year) : 'N/A';
                                            ?>
                                        </span>
                                        <span class="movie-views">
                                            <?php
                                            $views = get_post_meta(get_the_ID(), '_movie_views', true);
                                            $views = $views ? intval($views) : 0;
                                            echo '👁 ' . number_format($views);
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>No related movies found.</p>
                <?php endif; ?>
            </div>

        </article>
    <?php endwhile; ?>
</main>

<script>
jQuery(document).ready(function($) {
    $('.watch-btn').on('click', function() {
        var movieId = $(this).data('movie-id');
        var quality = $(this).data('quality');

        if (movieId && quality) {
            $.ajax({
                url: movieflix_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'movieflix_track_watch',
                    movie_id: movieId,
                    quality: quality,
                    nonce: movieflix_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Watch tracked successfully');
                    }
                },
                error: function() {
                    console.log('Failed to track watch');
                }
            });
        }
    });

    $('.download-btn').on('click', function() {
        var movieId = $(this).data('movie-id');
        var quality = $(this).data('quality');

        if (movieId && quality) {
            $.ajax({
                url: movieflix_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'movieflix_track_download',
                    movie_id: movieId,
                    quality: quality,
                    nonce: movieflix_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Download tracked successfully');
                    }
                },
                error: function() {
                    console.log('Failed to track download');
                }
            });
        }
    });
});
</script>

<?php get_footer(); ?>