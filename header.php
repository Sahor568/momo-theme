<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <?php 
    // Add site icon meta tags
    $site_icon_id = get_option('site_icon');
    if ($site_icon_id) {
        $site_icon_url = wp_get_attachment_image_url($site_icon_id, 'full');
        if ($site_icon_url) {
            echo '<link rel="icon" href="' . esc_url($site_icon_url) . '" sizes="32x32" />' . "\n";
            echo '<link rel="icon" href="' . esc_url($site_icon_url) . '" sizes="192x192" />' . "\n";
            echo '<link rel="apple-touch-icon" href="' . esc_url($site_icon_url) . '" />' . "\n";
            echo '<meta name="msapplication-TileImage" content="' . esc_url($site_icon_url) . '" />' . "\n";
        }
    }
    ?>
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" id="site-header">
    <div class="header-container">
        <div class="site-branding">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
                    <?php bloginfo('name'); ?>
                </a>
            <?php endif; ?>
        </div>
        
        <button class="menu-toggle" id="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
          ☰
        </button>
        
        <nav class="main-nav" id="site-navigation">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_id' => 'primary-menu',
                'fallback_cb' => false,
            ));
            ?>
        </nav>
        
        <div class="search-container">
            <form class="search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <div class="search-input-wrapper">
                    <input type="text" 
                           class="movie-search" 
                           name="s" 
                           placeholder="Search movies..." 
                           id="movie-search"
                           value="<?php echo get_search_query(); ?>">
                    <input type="hidden" name="post_type" value="movie">
                </div>
            </form>
            <div class="search-results" id="search-results"></div>
        </div>
    </div>
</header>