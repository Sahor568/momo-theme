<?php
/**
 * Search Results Template
 * 
 * @package MovieFlix
 */

get_header(); ?>

<main class="main-content search-results-page">
    <div class="search-results-header">
        <div class="container">
            <h1 class="search-results-title">
                <?php
                $search_query = get_search_query();
                if ($search_query) {
                    printf(__('Search Results for: "%s"', 'movieflix'), '<span class="search-term">' . esc_html($search_query) . '</span>');
                } else {
                    _e('Search Results', 'movieflix');
                }
                ?>
            </h1>
            
            <?php if (have_posts()) : ?>
                <div class="search-results-count">
                    <?php
                    global $wp_query;
                    $total_results = $wp_query->found_posts;
                    printf(
                        _n(
                            'Found %d movie',
                            'Found %d movies',
                            $total_results,
                            'movieflix'
                        ),
                        $total_results
                    );
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Enhanced Search Form -->
    <section class="search-form-section">
        <div class="container">
            <form class="enhanced-search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <div class="search-form-wrapper">
                    <div class="search-input-group">
                        <input type="text" 
                               class="search-input" 
                               name="s" 
                               placeholder="Search for movies..." 
                               value="<?php echo esc_attr($search_query); ?>"
                               autocomplete="off">
                        <input type="hidden" name="post_type" value="movie">
                        <button type="submit" class="search-submit-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            <span class="btn-text">Search</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Search Filters -->
    <section class="search-filters-section">
        <div class="container">
            <div class="search-filters">
                <select class="search-filter-select" id="search-genre-filter">
                    <option value="">All Genres</option>
                    <?php
                    $genres = get_terms(array(
                        'taxonomy' => 'movie_genre',
                        'hide_empty' => true,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ));
                    
                    if ($genres && !is_wp_error($genres)) :
                        foreach ($genres as $genre) :
                            $selected = (isset($_GET['genre']) && $_GET['genre'] === $genre->slug) ? 'selected' : '';
                            ?>
                            <option value="<?php echo esc_attr($genre->slug); ?>" <?php echo $selected; ?>>
                                <?php echo esc_html($genre->name); ?>
                            </option>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </select>
                
                <select class="search-filter-select" id="search-year-filter">
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
                            $selected = (isset($_GET['year']) && $_GET['year'] === $year->slug) ? 'selected' : '';
                            ?>
                            <option value="<?php echo esc_attr($year->slug); ?>" <?php echo $selected; ?>>
                                <?php echo esc_html($year->name); ?>
                            </option>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </select>
                
                <select class="search-filter-select" id="search-quality-filter">
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
                            $selected = (isset($_GET['quality']) && $_GET['quality'] === $quality->slug) ? 'selected' : '';
                            ?>
                            <option value="<?php echo esc_attr($quality->slug); ?>" <?php echo $selected; ?>>
                                <?php echo esc_html($quality->name); ?>
                            </option>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </select>
                
                <select class="search-filter-select" id="search-sort-filter">
                    <option value="date">Latest First</option>
                    <option value="title" <?php echo (isset($_GET['orderby']) && $_GET['orderby'] === 'title') ? 'selected' : ''; ?>>Title A-Z</option>
                    <option value="views" <?php echo (isset($_GET['orderby']) && $_GET['orderby'] === 'views') ? 'selected' : ''; ?>>Most Viewed</option>
                    <option value="rating" <?php echo (isset($_GET['orderby']) && $_GET['orderby'] === 'rating') ? 'selected' : ''; ?>>Highest Rated</option>
                </select>
                
                <button type="button" id="clear-search-filters" class="btn btn-secondary">
                    Clear Filters
                </button>
            </div>
        </div>
    </section>

    <!-- Search Results -->
    <section class="search-results-section">
        <div class="container">
            <?php if (have_posts()) : ?>
                <div class="movie-grid" id="search-results-container">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/movie-card'); ?>
                    <?php endwhile; ?>
                </div>
                
                <!-- Pagination -->
                <div class="pagination-wrapper">
                    <?php
                    $pagination_args = array(
                        'mid_size' => 2,
                        'prev_text' => __('‹ Previous', 'movieflix'),
                        'next_text' => __('Next ›', 'movieflix'),
                        'type' => 'array',
                        'add_args' => array(
                            's' => $search_query,
                            'post_type' => 'movie'
                        )
                    );
                    
                    $pagination_links = paginate_links($pagination_args);
                    
                    if ($pagination_links) {
                        echo '<div class="pagination-container">';
                        foreach ($pagination_links as $link) {
                            // Add pagination classes
                            $link = str_replace('page-numbers', 'pagination-btn', $link);
                            if (strpos($link, '<span') !== false && strpos($link, 'current') !== false) {
                                $link = str_replace('page-numbers current', 'pagination-current', $link);
                            }
                            if (strpos($link, 'dots') !== false) {
                                $link = str_replace('page-numbers dots', 'pagination-dots', $link);
                            }
                            echo $link;
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
                
            <?php else : ?>
                <div class="no-search-results">
                    <div class="no-results-content">
                        <div class="no-results-icon">🔍</div>
                        <h2><?php _e('No Movies Found', 'movieflix'); ?></h2>
                        <?php if ($search_query) : ?>
                            <p><?php printf(__('Sorry, no movies were found matching "%s". Try different keywords or browse our categories.', 'movieflix'), '<strong>' . esc_html($search_query) . '</strong>'); ?></p>
                        <?php else : ?>
                            <p><?php _e('Please enter a search term to find movies.', 'movieflix'); ?></p>
                        <?php endif; ?>
                        
                        <div class="search-suggestions">
                            <h3><?php _e('Search Suggestions:', 'movieflix'); ?></h3>
                            <ul>
                                <li><?php _e('Check your spelling', 'movieflix'); ?></li>
                                <li><?php _e('Try different keywords', 'movieflix'); ?></li>
                                <li><?php _e('Use more general terms', 'movieflix'); ?></li>
                                <li><?php _e('Browse by genre or year', 'movieflix'); ?></li>
                            </ul>
                        </div>
                        
                        <div class="browse-alternatives">
                            <h3><?php _e('Or browse by category:', 'movieflix'); ?></h3>
                            <div class="category-links">
                                <?php
                                $popular_genres = get_terms(array(
                                    'taxonomy' => 'movie_genre',
                                    'orderby' => 'count',
                                    'order' => 'DESC',
                                    'number' => 6,
                                    'hide_empty' => true
                                ));
                                
                                if ($popular_genres && !is_wp_error($popular_genres)) :
                                    foreach ($popular_genres as $genre) :
                                        ?>
                                        <a href="<?php echo esc_url(get_term_link($genre)); ?>" class="category-link">
                                            <?php echo esc_html($genre->name); ?>
                                        </a>
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </div>
                        
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                            <?php _e('Back to Home', 'movieflix'); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<style>
/* Search Results Page Styles */
.search-results-page {
    min-height: 80vh;
    padding: var(--spacing-xl) 0;
}

.search-results-header {
    background: var(--card-bg);
    padding: var(--spacing-xl) 0;
    margin-bottom: var(--spacing-xl);
    text-align: center;
    box-shadow: var(--box-shadow);
}

.search-results-title {
    font-size: 2rem;
    color: var(--text-light);
    margin-bottom: var(--spacing-md);
    font-weight: 700;
}

.search-term {
    color: var(--primary-color);
    font-style: italic;
}

.search-results-count {
    color: var(--text-gray);
    font-size: 1.1rem;
    font-weight: 500;
}

/* Enhanced Search Form */
.search-form-section {
    margin-bottom: var(--spacing-xl);
}

.enhanced-search-form {
    max-width: 600px;
    margin: 0 auto;
}

.search-form-wrapper {
    background: var(--card-bg);
    padding: var(--spacing-xl);
    border-radius: 15px;
    box-shadow: var(--box-shadow);
}

.search-input-group {
    display: flex;
    gap: var(--spacing-md);
    align-items: center;
}

.search-input {
    flex: 1;
    padding: 1rem var(--spacing-lg);
    background: var(--dark-bg);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    color: var(--text-light);
    font-size: 1.1rem;
    transition: border-color 0.3s ease;
    min-height: 50px;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(229, 9, 20, 0.1);
}

.search-input::placeholder {
    color: var(--text-gray);
}

.search-submit-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 1rem var(--spacing-xl);
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 1rem;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    min-height: 50px;
    white-space: nowrap;
}

.search-submit-btn:hover,
.search-submit-btn:focus {
    background: var(--hover-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(229, 9, 20, 0.3);
    outline: none;
}

.search-submit-btn svg {
    flex-shrink: 0;
}

/* Search Filters */
.search-filters-section {
    margin-bottom: var(--spacing-xl);
}

.search-filters {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: var(--spacing-md);
    max-width: var(--content-max-width);
    margin: 0 auto;
    align-items: center;
}

.search-filter-select {
    padding: 0.75rem var(--spacing-md);
    background: var(--card-bg);
    color: var(--text-light);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    cursor: pointer;
    transition: var(--transition);
    font-size: 0.95rem;
    min-height: 44px;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23b3b3b3' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right var(--spacing-md) center;
    background-size: 1rem;
    padding-right: 3rem;
}

.search-filter-select:hover,
.search-filter-select:focus {
    background-color: var(--secondary-color);
    border-color: var(--primary-color);
    outline: none;
}

.search-filter-select option {
    background: var(--dark-bg);
    color: var(--text-light);
    padding: var(--spacing-sm);
}

/* No Search Results */
.no-search-results {
    text-align: center;
    padding: var(--spacing-xxl) var(--spacing-xl);
    max-width: 800px;
    margin: 0 auto;
}

.no-results-content {
    background: var(--card-bg);
    padding: var(--spacing-xxl);
    border-radius: 15px;
    box-shadow: var(--box-shadow);
}

.no-results-icon {
    font-size: 4rem;
    margin-bottom: var(--spacing-lg);
    opacity: 0.7;
}

.no-search-results h2 {
    color: var(--text-light);
    font-size: 2rem;
    margin-bottom: var(--spacing-md);
    font-weight: 700;
}

.no-search-results p {
    color: var(--text-gray);
    font-size: 1.1rem;
    margin-bottom: var(--spacing-xl);
    line-height: 1.6;
}

.search-suggestions,
.browse-alternatives {
    margin: var(--spacing-xl) 0;
    text-align: left;
}

.search-suggestions h3,
.browse-alternatives h3 {
    color: var(--text-light);
    font-size: 1.3rem;
    margin-bottom: var(--spacing-md);
    text-align: center;
}

.search-suggestions ul {
    list-style: none;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-sm);
}

.search-suggestions li {
    background: var(--secondary-color);
    padding: var(--spacing-md);
    border-radius: 8px;
    color: var(--text-gray);
    position: relative;
    padding-left: 2rem;
}

.search-suggestions li:before {
    content: "💡";
    position: absolute;
    left: var(--spacing-sm);
    top: var(--spacing-md);
}

.category-links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-xl);
}

.category-link {
    background: var(--secondary-color);
    color: var(--text-light);
    padding: var(--spacing-md) var(--spacing-lg);
    border-radius: 8px;
    text-decoration: none;
    transition: var(--transition);
    text-align: center;
    font-weight: 500;
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-link:hover,
.category-link:focus {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(229, 9, 20, 0.3);
    outline: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .search-results-title {
        font-size: 1.5rem;
    }
    
    .search-input-group {
        flex-direction: column;
        gap: var(--spacing-md);
    }
    
    .search-submit-btn {
        width: 100%;
        justify-content: center;
    }
    
    .search-filters {
        grid-template-columns: 1fr;
    }
    
    .search-form-wrapper {
        padding: var(--spacing-lg);
    }
    
    .no-results-content {
        padding: var(--spacing-lg);
    }
    
    .no-results-icon {
        font-size: 3rem;
    }
    
    .no-search-results h2 {
        font-size: 1.5rem;
    }
    
    .search-suggestions ul {
        grid-template-columns: 1fr;
    }
    
    .category-links {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .search-results-header {
        padding: var(--spacing-lg) 0;
    }
    
    .search-results-title {
        font-size: 1.3rem;
    }
    
    .search-input {
        font-size: 1rem;
        padding: 0.8rem var(--spacing-md);
    }
    
    .search-submit-btn {
        font-size: 0.9rem;
        padding: 0.8rem var(--spacing-md);
    }
    
    .category-links {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Handle search filters
    $('.search-filter-select').on('change', function() {
        var searchQuery = '<?php echo esc_js($search_query); ?>';
        var genre = $('#search-genre-filter').val();
        var year = $('#search-year-filter').val();
        var quality = $('#search-quality-filter').val();
        var orderby = $('#search-sort-filter').val();
        
        var url = '<?php echo esc_url(home_url('/')); ?>';
        var params = [];
        
        if (searchQuery) {
            params.push('s=' + encodeURIComponent(searchQuery));
        }
        
        params.push('post_type=movie');
        
        if (genre) {
            params.push('genre=' + encodeURIComponent(genre));
        }
        
        if (year) {
            params.push('year=' + encodeURIComponent(year));
        }
        
        if (quality) {
            params.push('quality=' + encodeURIComponent(quality));
        }
        
        if (orderby && orderby !== 'date') {
            params.push('orderby=' + encodeURIComponent(orderby));
        }
        
        if (params.length > 0) {
            url += '?' + params.join('&');
        }
        
        window.location.href = url;
    });
    
    // Clear filters
    $('#clear-search-filters').on('click', function() {
        var searchQuery = '<?php echo esc_js($search_query); ?>';
        var url = '<?php echo esc_url(home_url('/')); ?>';
        
        if (searchQuery) {
            url += '?s=' + encodeURIComponent(searchQuery) + '&post_type=movie';
        }
        
        window.location.href = url;
    });
    
    // Enhanced search form submission
    $('.enhanced-search-form').on('submit', function(e) {
        var searchInput = $(this).find('.search-input');
        var searchTerm = searchInput.val().trim();
        
        if (!searchTerm) {
            e.preventDefault();
            searchInput.focus();
            return false;
        }
    });
});
</script>

<?php get_footer(); ?>