<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#366ad3">
    <meta name="msapplication-navbutton-color" content="#366ad3">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="#366ad3">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> itemscope="itemscope" itemtype="http://schema.org/WebPage">

<div class="mainholder">
    <!-- Header Navigation Bar -->
    <div class="th">
        <div class="centernav">
            <!-- Mobile Menu Toggle -->
            <button type="button" class="shme" aria-label="Toggle Menu" aria-expanded="false"><i class="fa fa-bars" aria-hidden="true"></i></button>
            
            <!-- Logo -->
            <header role="banner" itemscope itemtype="http://schema.org/WPHeader">
                <div class="site-branding logox">
                    <span class="logos">
                        <a title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" itemprop="url" href="<?php echo esc_url(home_url('/')); ?>">
                            <?php if (has_custom_logo()) : ?>
                                <?php 
                                $custom_logo_id = get_theme_mod('custom_logo');
                                $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                                ?>
                                <img src="<?php echo esc_url($logo[0]); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
                            <?php else : ?>
                                <span style="color: #fff; font-weight: 700; font-size: 24px;"><?php bloginfo('name'); ?></span>
                            <?php endif; ?>
                            <span class="hdl"><?php echo esc_attr(get_bloginfo('name', 'display')); ?></span>
                        </a>
                    </span>
                    <meta itemprop="name" content="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" />
                </div>
            </header>

            <!-- Main Navigation Menu -->
            <nav id="main-menu" class="mm">
                <span itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement" role="navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'main',
                        'container'      => '',
                        'fallback_cb'    => 'komik_starter_fallback_menu',
                        'link_before'    => '<span itemprop="name">',
                        'link_after'     => '</span>',
                    ));
                    ?>
                </span>
                <div class="clear"></div>
            </nav>

            <!-- Search Form -->
            <div class="searchx">
                <form action="<?php bloginfo('url'); ?>/" id="form" method="get" itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction">
                    <meta itemprop="target" content="<?php bloginfo('url'); ?>/?s={query}"/>
                    <input id="s" itemprop="query-input" class="search-live" type="text" placeholder="<?php _e('Search...', 'komik-starter'); ?>" name="s" value="<?php echo get_search_query(); ?>" autocomplete="off" />
                    <button type="submit" id="submit"><i class="fas fa-search" aria-hidden="true"></i></button>
                </form>
            </div>

            <!-- Mobile Search Toggle -->
            <button type="button" class="srcmob" aria-label="Toggle Search"><i class="fas fa-search" aria-hidden="true"></i></button>

        </div>
        <div class="clear"></div>
    </div>
    
    <?php komik_starter_display_ad('below_menu'); ?>

    <!-- Main Content Area -->
    <div id="content"<?php if(is_singular('post')){ echo ' class="readercontent"'; } ?>>
        <div class="wrapper">
<?php
/**
 * Fallback menu if no menu is set
 */
function komik_starter_fallback_menu() {
    echo '<ul id="menu-main" class="menu">';
    echo '<li class="menu-item"><a href="' . esc_url(home_url('/')) . '"><span itemprop="name">Home</span></a></li>';
    echo '<li class="menu-item"><a href="' . esc_url(get_post_type_archive_link('manhwa')) . '"><span itemprop="name">Daftar Komik</span></a></li>';
    echo '<li class="menu-item"><a href="' . esc_url(home_url('/bookmark')) . '"><span itemprop="name">Bookmark</span></a></li>';
    echo '<li class="menu-item"><a href="' . esc_url(home_url('/contact')) . '"><span itemprop="name">Contact</span></a></li>';
    echo '</ul>';
}
?>
