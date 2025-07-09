<?php get_header(); ?>

<main class="main-content">
    <!-- Category Tabs -->
    <section class="movie-section">
        <div class="category-tabs">
            <button class="tab-button active" data-category="all">All Movies</button>
            <?php
            $genres = get_terms(array(
                'taxonomy' => 'movie_genre',
                'hide_empty' => true,
                'orderby' => 'name',
                'order' => 'ASC'
            ));
            
            if ($genres && !is_wp_error($genres)) :
                foreach ($genres as $genre) :
                    ?>
                    <button class="tab-button" data-category="<?php echo esc_attr($genre->slug); ?>">
                        <?php echo esc_html($genre->name); ?>
                    </button>
                    <?php
                endforeach;
            endif;
            ?>
        </div>
    </section>

    <!-- Filters -->
    <section class="movie-section">
        <div class="filters-container">
            <select class="filter-select" id="year-filter">
                <option value="">All Years</option>
                <?php
                $years = get_terms(array(
                    'taxonomy' => 'movie_year',
                    'hide_empty' => true,
                    'orderby' => 'name',
                    'order' => 'DESC'
                ));
                
                if ($years && !is_wp_error($years)) :
                    foreach ($years as $year) :
                        ?>
                        <option value="<?php echo esc_attr($year->slug); ?>"><?php echo esc_html($year->name); ?></option>
                        <?php
                    endforeach;
                endif;
                ?>
            </select>
            
            <select class="filter-select" id="quality-filter">
                <option value="">All Quality</option>
                <?php
                $qualities = get_terms(array(
                    'taxonomy' => 'movie_quality',
                    'hide_empty' => true,
                    'orderby' => 'name',
                    'order' => 'ASC'
                ));
                
                if ($qualities && !is_wp_error($qualities)) :
                    foreach ($qualities as $quality) :
                        ?>
                        <option value="<?php echo esc_attr($quality->slug); ?>"><?php echo esc_html($quality->name); ?></option>
                        <?php
                    endforeach;
                endif;
                ?>
            </select>
            
            <select class="filter-select" id="language-filter">
                <option value="">All Languages</option>
                <?php
                $languages = get_terms(array(
                    'taxonomy' => 'movie_language',
                    'hide_empty' => true,
                    'orderby' => 'name',
                    'order' => 'ASC'
                ));
                
                if ($languages && !is_wp_error($languages)) :
                    foreach ($languages as $language) :
                        ?>
                        <option value="<?php echo esc_attr($language->slug); ?>"><?php echo esc_html($language->name); ?></option>
                        <?php
                    endforeach;
                endif;
                ?>
            </select>
            
            <button type="button" id="clear-filters" class="btn btn-secondary">
                Clear Filters
            </button>
        </div>
    </section>

    <!-- Latest Movies -->
    <section class="movie-section">
        <div class="section-header">
            <h2 class="section-title">All Movies and Series</h2>
        </div>
        
        <div class="movie-grid" id="movies-container">
            <?php
            $movies_per_page = movieflix_get_movies_per_page();
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            
            $movies_query = new WP_Query(array(
                'post_type' => 'movie',
                'posts_per_page' => $movies_per_page,
                'paged' => $paged,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC'
            ));
            
            if ($movies_query->have_posts()) :
                while ($movies_query->have_posts()) : $movies_query->the_post();
                    get_template_part('template-parts/movie-card');
                endwhile;
                wp_reset_postdata();
            else :
                echo '<div class="no-results">';
                echo '<div class="no-results-icon">🎬</div>';
                echo '<h3>No Movies Found</h3>';
                echo '<p>No movies have been added yet. Please check back later.</p>';
                echo '</div>';
            endif;
            ?>
        </div>
        
        <div class="pagination-wrapper" id="movies-pagination">
            <?php
            if ($movies_query->max_num_pages > 1) {
                $pagination_args = array(
                    'total' => $movies_query->max_num_pages,
                    'current' => max(1, $paged),
                    'mid_size' => 2,
                    'prev_text' => __('‹ Previous', 'movieflix'),
                    'next_text' => __('Next ›', 'movieflix'),
                    'type' => 'array',
                    'add_args' => false,
                );
                
                $pagination_links = paginate_links($pagination_args);
                
                if ($pagination_links) {
                    echo '<div class="pagination-container">';
                    foreach ($pagination_links as $link) {
                        // Extract page number and add data attribute
                        if (preg_match('/href=["\']([^"\']*)["\']/', $link, $matches)) {
                            $url = $matches[1];
                            $page_num = 1; // Default to page 1
                            
                            if (preg_match('/\/page\/(\d+)/', $url, $page_matches)) {
                                $page_num = $page_matches[1];
                            } elseif (preg_match('/paged=(\d+)/', $url, $page_matches)) {
                                $page_num = $page_matches[1];
                            }
                            
                            // Add data-page attribute and pagination classes
                            $link = str_replace('<a ', '<a data-page="' . $page_num . '" ', $link);
                            $link = str_replace('page-numbers', 'pagination-btn', $link);
                        }
                        
                        // Handle current page span
                        if (strpos($link, '<span') !== false && strpos($link, 'current') !== false) {
                            $link = str_replace('page-numbers current', 'pagination-current', $link);
                        }
                        
                        // Handle dots
                        if (strpos($link, 'dots') !== false) {
                            $link = str_replace('page-numbers dots', 'pagination-dots', $link);
                        }
                        
                        echo $link;
                    }
                    echo '</div>';
                }
            }
            ?>
        </div>
    </section>
</main>

<script>
jQuery(document).ready(function($) {
    // Clear filters functionality
    $('#clear-filters').on('click', function() {
        $('.filter-select').val('');
        $('.tab-button').removeClass('active');
        $('.tab-button[data-category="all"]').addClass('active');
        
        // Trigger filter update
        if (typeof loadMoviesWithFilters === 'function') {
            loadMoviesWithFilters({}, 'all', 1);
        }
    });
    
    // Handle pagination clicks
    $(document).on('click', '.pagination-btn', function(e) {
        e.preventDefault();
        
        var page = $(this).data('page');
        if (!page) return;
        
        // Check if AJAX filtering is available
        if (typeof loadMoviesWithFilters === 'function') {
            var activeCategory = $('.tab-button.active').data('category') || 'all';
            var filters = {};
            
            // Get current filter values
            $('.filter-select').each(function() {
                var value = $(this).val();
                if (value) {
                    var filterId = $(this).attr('id');
                    if (filterId === 'year-filter') filters.year = value;
                    if (filterId === 'quality-filter') filters.quality = value;
                    if (filterId === 'language-filter') filters.language = value;
                }
            });
            
            loadMoviesWithFilters(filters, activeCategory, page);
            
            // Scroll to movies container
            $('html, body').animate({
                scrollTop: $('#movies-container').offset().top - 100
            }, 500);
        } else {
            // Fallback: navigate to the page
            window.location.href = '?paged=' + page;
        }
    });
});
</script>

<?php get_footer(); ?>