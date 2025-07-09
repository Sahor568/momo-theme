<div class="movie-card">
    <div class="movie-poster">
        <a href="<?php the_permalink(); ?>">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('movie-poster', array('class' => 'lazy-load')); ?>
            <?php else : ?>
                <img src="<?php echo esc_url(get_template_directory_uri() . '/images/no-poster.jpg'); ?>" alt="No Poster" class="lazy-load">
            <?php endif; ?>
        </a>
        
        <?php
        $qualities = get_the_terms(get_the_ID(), 'movie_quality');
        if ($qualities && !is_wp_error($qualities)) :
            ?>
            <div class="movie-quality"><?php echo esc_html($qualities[0]->name); ?></div>
            <?php
        endif;
        ?>
    </div>
    
    <div class="movie-info">
        <h3 class="movie-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        
        <div class="movie-meta">
            <span class="movie-year">
                <?php
                $release_year = get_post_meta(get_the_ID(), '_movie_release_year', true);
                echo $release_year ? esc_html($release_year) : 'N/A';
                ?>
            </span>
            
            <span class="movie-rating">
                <?php
                $imdb_rating = get_post_meta(get_the_ID(), '_movie_imdb_rating', true);
                if ($imdb_rating) :
                    echo '⭐ ' . esc_html($imdb_rating);
                endif;
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