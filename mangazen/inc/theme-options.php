<?php
/**
 * Theme Options - Ads Management & Popular Manhwa Editor
 * 
 * @package Komik_Starter
 * @version 1.0.0
 */

defined("ABSPATH") || die("!");

/**
 * =================================================================
 * ADMIN MENU REGISTRATION
 * =================================================================
 */
add_action('admin_menu', 'komik_starter_theme_menu');
function komik_starter_theme_menu() {
    // Main Theme Options Menu
    add_menu_page(
        __('Theme Options', 'komik-starter'),
        __('Theme Options', 'komik-starter'),
        'manage_options',
        'komik-theme-options',
        'komik_starter_popular_manhwa_page',
        'dashicons-admin-customizer',
        60
    );
    
    // Popular Manhwa Submenu
    add_submenu_page(
        'komik-theme-options',
        __('Popular Manhwa', 'komik-starter'),
        __('Popular Manhwa', 'komik-starter'),
        'manage_options',
        'komik-theme-options',
        'komik_starter_popular_manhwa_page'
    );
    
    // Ads Management Submenu
    add_submenu_page(
        'komik-theme-options',
        __('Ads Management', 'komik-starter'),
        __('Ads Management', 'komik-starter'),
        'manage_options',
        'komik-ads',
        'komik_starter_ads_page'
    );
    
    // Color Settings Submenu
    add_submenu_page(
        'komik-theme-options',
        __('Color Settings', 'komik-starter'),
        __('Color Settings', 'komik-starter'),
        'manage_options',
        'komik-colors',
        'komik_starter_colors_page'
    );
    
    // Contact Settings Submenu
    add_submenu_page(
        'komik-theme-options',
        __('Contact Settings', 'komik-starter'),
        __('Contact Settings', 'komik-starter'),
        'manage_options',
        'komik-contact',
        'komik_starter_contact_page'
    );
    
    // Announcement Bar Submenu
    add_submenu_page(
        'komik-theme-options',
        __('Announcement Bar', 'komik-starter'),
        __('Announcement Bar', 'komik-starter'),
        'manage_options',
        'komik-announcement',
        'komik_starter_announcement_page'
    );
    
    // Footer Settings Submenu
    add_submenu_page(
        'komik-theme-options',
        __('Footer Settings', 'komik-starter'),
        __('Footer Settings', 'komik-starter'),
        'manage_options',
        'komik-footer',
        'komik_starter_footer_page'
    );
    
    // Content Protection Submenu
    add_submenu_page(
        'komik-theme-options',
        __('Content Protection', 'komik-starter'),
        __('Content Protection', 'komik-starter'),
        'manage_options',
        'komik-protection',
        'komik_starter_protection_page'
    );
    
    // Hero Slider Submenu
    add_submenu_page(
        'komik-theme-options',
        __('Hero Slider', 'komik-starter'),
        __('Hero Slider', 'komik-starter'),
        'manage_options',
        'komik-hero-slider',
        'komik_starter_hero_slider_page'
    );
    
    // Analytics/Tracking Submenu
    add_submenu_page(
        'komik-theme-options',
        __('Analytics & Tracking', 'komik-starter'),
        __('Analytics & Tracking', 'komik-starter'),
        'manage_options',
        'komik-analytics',
        'komik_starter_analytics_page'
    );
    
    // Register settings
    add_action('admin_init', 'komik_starter_register_settings');
}

/**
 * Register all settings
 */
function komik_starter_register_settings() {
    // Popular Manhwa settings
    register_setting('komik-popular-settings', 'komik_popular_manhwa');
    register_setting('komik-popular-settings', 'komik_popular_count');
    register_setting('komik-popular-settings', 'komik_popular_mode');
    register_setting('komik-popular-settings', 'komik_popular_limit');
    
    // Ads settings
    register_setting('komik-ads-settings', 'komik_ads_header');
    register_setting('komik-ads-settings', 'komik_ads_below_menu');
    register_setting('komik-ads-settings', 'komik_ads_below_menu_2');
    register_setting('komik-ads-settings', 'komik_ads_below_menu_3');
    register_setting('komik-ads-settings', 'komik_ads_below_menu_4');
    register_setting('komik-ads-settings', 'komik_ads_homepage_top');
    register_setting('komik-ads-settings', 'komik_ads_homepage_top_2');
    register_setting('komik-ads-settings', 'komik_ads_homepage_top_3');
    register_setting('komik-ads-settings', 'komik_ads_homepage_top_4');
    register_setting('komik-ads-settings', 'komik_ads_homepage_middle');
    register_setting('komik-ads-settings', 'komik_ads_homepage_middle_2');
    register_setting('komik-ads-settings', 'komik_ads_homepage_middle_3');
    register_setting('komik-ads-settings', 'komik_ads_homepage_middle_4');
    register_setting('komik-ads-settings', 'komik_ads_sidebar');
    register_setting('komik-ads-settings', 'komik_ads_series_before');
    register_setting('komik-ads-settings', 'komik_ads_series_before_2');
    register_setting('komik-ads-settings', 'komik_ads_series_before_3');
    register_setting('komik-ads-settings', 'komik_ads_series_before_4');
    register_setting('komik-ads-settings', 'komik_ads_series_after');
    register_setting('komik-ads-settings', 'komik_ads_series_after_2');
    register_setting('komik-ads-settings', 'komik_ads_series_after_3');
    register_setting('komik-ads-settings', 'komik_ads_series_after_4');
    register_setting('komik-ads-settings', 'komik_ads_reader_top');
    register_setting('komik-ads-settings', 'komik_ads_reader_middle');
    register_setting('komik-ads-settings', 'komik_ads_reader_bottom');
    register_setting('komik-ads-settings', 'komik_ads_float_left');
    register_setting('komik-ads-settings', 'komik_ads_float_right');
    register_setting('komik-ads-settings', 'komik_ads_float_bottom');
    register_setting('komik-ads-settings', 'komik_ads_popup');
    
    // Adsterra settings
    register_setting('komik-ads-settings', 'komik_ads_adsterra_popunder');
    register_setting('komik-ads-settings', 'komik_ads_adsterra_smartlink');
    register_setting('komik-ads-settings', 'komik_ads_adsterra_socialbar');
    
    // Direct Link settings
    register_setting('komik-ads-settings', 'komik_ads_direct_link_enable');
    register_setting('komik-ads-settings', 'komik_ads_direct_link_url');
    register_setting('komik-ads-settings', 'komik_ads_direct_link_delay');
    register_setting('komik-ads-settings', 'komik_ads_direct_link_cooldown');
    
    // Color settings
    register_setting('komik-color-settings', 'komik_color_preset');
    register_setting('komik-color-settings', 'komik_color_primary');
    register_setting('komik-color-settings', 'komik_color_secondary');
    register_setting('komik-color-settings', 'komik_color_accent');
    register_setting('komik-color-settings', 'komik_color_bg');
    register_setting('komik-color-settings', 'komik_color_card');
    register_setting('komik-color-settings', 'komik_color_text');
    
    // Contact settings
    register_setting('komik-contact-settings', 'komik_contact_email');
    register_setting('komik-contact-settings', 'komik_contact_facebook');
    register_setting('komik-contact-settings', 'komik_contact_twitter');
    register_setting('komik-contact-settings', 'komik_contact_instagram');
    register_setting('komik-contact-settings', 'komik_contact_discord');
    register_setting('komik-contact-settings', 'komik_contact_telegram');
    
    // Announcement bar settings
    register_setting('komik-announcement-settings', 'komik_announcement_enable');
    register_setting('komik-announcement-settings', 'komik_announcement_title');
    register_setting('komik-announcement-settings', 'komik_announcement_content');
    register_setting('komik-announcement-settings', 'komik_announcement_text');
    register_setting('komik-announcement-settings', 'komik_announcement_link');
    register_setting('komik-announcement-settings', 'komik_announcement_type');
    register_setting('komik-announcement-settings', 'komik_announcement_dismissable');
    
    // Footer settings
    register_setting('komik-footer-settings', 'komik_footer_disclaimer');
    register_setting('komik-footer-settings', 'komik_footer_copyright');
    register_setting('komik-footer-settings', 'komik_footer_show_az');
    
    // Hero Slider settings
    register_setting('komik-hero-settings', 'komik_hero_enable');
    register_setting('komik-hero-settings', 'komik_hero_count');
    register_setting('komik-hero-settings', 'komik_hero_mode');
    register_setting('komik-hero-settings', 'komik_hero_autoplay');
    register_setting('komik-hero-settings', 'komik_hero_interval');
    register_setting('komik-hero-settings', 'komik_hero_manhwa');
    
    // Analytics settings
    register_setting('komik-analytics-settings', 'komik_histats_code');
    register_setting('komik-analytics-settings', 'komik_google_analytics');
    register_setting('komik-analytics-settings', 'komik_custom_head_code');
    register_setting('komik-analytics-settings', 'komik_custom_footer_code');
}

/**
 * =================================================================
 * POPULAR MANHWA EDITOR PAGE
 * =================================================================
 */
function komik_starter_popular_manhwa_page() {
    // Handle form submission
    if (isset($_POST['komik_save_popular']) && wp_verify_nonce($_POST['komik_popular_nonce'], 'komik_save_popular')) {
        $weekly_ids = isset($_POST['popular_weekly']) ? array_map('intval', $_POST['popular_weekly']) : array();
        $monthly_ids = isset($_POST['popular_monthly']) ? array_map('intval', $_POST['popular_monthly']) : array();
        $all_ids = isset($_POST['popular_all']) ? array_map('intval', $_POST['popular_all']) : array();
        $limit = isset($_POST['popular_limit']) ? intval($_POST['popular_limit']) : 5;
        
        // Popular Today (Homepage slider)
        $today_ids = isset($_POST['popular_today']) ? array_map('intval', $_POST['popular_today']) : array();
        $today_mode = isset($_POST['today_mode']) ? sanitize_text_field($_POST['today_mode']) : 'views';
        $today_limit = isset($_POST['today_limit']) ? intval($_POST['today_limit']) : 10;
        
        // Per-tab mode
        $weekly_mode = isset($_POST['weekly_mode']) ? sanitize_text_field($_POST['weekly_mode']) : 'manual';
        $monthly_mode = isset($_POST['monthly_mode']) ? sanitize_text_field($_POST['monthly_mode']) : 'manual';
        $all_mode = isset($_POST['all_mode']) ? sanitize_text_field($_POST['all_mode']) : 'manual';
        
        update_option('komik_popular_weekly', $weekly_ids);
        update_option('komik_popular_monthly', $monthly_ids);
        update_option('komik_popular_all', $all_ids);
        update_option('komik_popular_limit', $limit);
        update_option('komik_popular_weekly_mode', $weekly_mode);
        update_option('komik_popular_monthly_mode', $monthly_mode);
        update_option('komik_popular_all_mode', $all_mode);
        
        // Save Popular Today settings
        update_option('komik_popular_today', $today_ids);
        update_option('komik_popular_today_mode', $today_mode);
        update_option('komik_popular_today_limit', $today_limit);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Popular Manhwa settings saved!', 'komik-starter') . '</p></div>';
    }
    
    // Get current settings
    $weekly_ids = get_option('komik_popular_weekly', array());
    $monthly_ids = get_option('komik_popular_monthly', array());
    $all_ids = get_option('komik_popular_all', array());
    $limit = get_option('komik_popular_limit', 5);
    $weekly_mode = get_option('komik_popular_weekly_mode', 'manual');
    $monthly_mode = get_option('komik_popular_monthly_mode', 'manual');
    $all_mode = get_option('komik_popular_all_mode', 'manual');
    
    // Popular Today settings
    $today_ids = get_option('komik_popular_today', array());
    $today_mode = get_option('komik_popular_today_mode', 'views');
    $today_limit = get_option('komik_popular_today_limit', 10);
    
    // Get all manhwa
    $all_manhwa = get_posts(array(
        'post_type' => 'manhwa',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'publish',
    ));
    ?>
    
    <div class="wrap">
        <h1><span class="dashicons dashicons-star-filled" style="font-size: 30px; margin-right: 10px;"></span> <?php _e('Popular Manhwa Editor', 'komik-starter'); ?></h1>
        
        <style>
            .komik-options-wrap { background: #fff; padding: 25px; border-radius: 8px; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
            .komik-options-wrap h2 { margin-top: 0; color: #1e1e1e; border-bottom: 2px solid #2271b1; padding-bottom: 10px; }
            .komik-tabs { display: flex; gap: 0; margin-bottom: 0; border-bottom: 2px solid #dcdcde; }
            .komik-tab { padding: 12px 24px; cursor: pointer; border: 2px solid transparent; border-bottom: none; border-radius: 8px 8px 0 0; background: #f0f0f0; color: #646970; font-weight: 600; transition: all 0.2s; margin-bottom: -2px; }
            .komik-tab:hover { background: #e0e0e0; }
            .komik-tab.active { background: #fff; border-color: #2271b1; border-bottom-color: #fff; color: #2271b1; }
            .komik-tab-content { display: none; padding: 20px; background: #fff; border: 2px solid #2271b1; border-top: none; border-radius: 0 0 8px 8px; }
            .komik-tab-content.active { display: block; }
            .komik-manhwa-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; max-height: 350px; overflow-y: auto; padding: 10px; background: #f9f9f9; border-radius: 8px; }
            .komik-manhwa-item { display: flex; align-items: center; gap: 10px; padding: 8px 12px; background: #fff; border-radius: 6px; border: 2px solid transparent; cursor: pointer; transition: all 0.2s; }
            .komik-manhwa-item:hover { border-color: #dcdcde; }
            .komik-manhwa-item.selected { border-color: #2271b1; background: #f0f6fc; }
            .komik-manhwa-item img { width: 35px; height: 50px; object-fit: cover; border-radius: 4px; }
            .komik-manhwa-item .title { flex: 1; font-size: 12px; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .komik-manhwa-item .order { width: 22px; height: 22px; background: #2271b1; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; }
            .komik-search-box { margin-bottom: 15px; }
            .komik-search-box input { width: 100%; padding: 10px 15px; border: 1px solid #dcdcde; border-radius: 6px; font-size: 14px; }
            .komik-mode-select { display: flex; gap: 10px; margin-bottom: 15px; padding: 15px; background: #f0f6fc; border-radius: 8px; border: 1px solid #2271b1; }
            .komik-mode-select label { display: flex; align-items: center; gap: 6px; cursor: pointer; padding: 8px 15px; border-radius: 6px; background: #fff; border: 2px solid #dcdcde; font-size: 13px; }
            .komik-mode-select label:hover { border-color: #2271b1; }
            .komik-mode-select label.active { border-color: #2271b1; background: #2271b1; color: #fff; }
            .manual-grid { display: none; }
            .manual-grid.active { display: block; }
        </style>
        
        <form method="post" id="popular-manhwa-form">
            <?php wp_nonce_field('komik_save_popular', 'komik_popular_nonce'); ?>
            
            <div class="komik-options-wrap">
                <h2><?php _e('Settings', 'komik-starter'); ?></h2>
                <p>
                    <strong><?php _e('Show in Sidebar:', 'komik-starter'); ?></strong>
                    <input type="number" name="popular_limit" value="<?php echo esc_attr($limit); ?>" min="1" max="20" style="width: 60px; margin-left: 5px;">
                    <?php _e('items per tab', 'komik-starter'); ?>
                </p>
            </div>
            
            <!-- Popular Today Section (Homepage Slider) -->
            <div class="komik-options-wrap" style="border-left: 4px solid #f59e0b;">
                <h2><span class="dashicons dashicons-megaphone" style="color: #f59e0b;"></span> <?php _e('Popular Today (Homepage Slider)', 'komik-starter'); ?></h2>
                <p style="color: #646970; margin-bottom: 15px;">
                    <?php _e('Configure which manhwa appear in the "Popular Today" slider on the homepage.', 'komik-starter'); ?>
                </p>
                
                <p style="margin-bottom: 15px;">
                    <strong><?php _e('Show:', 'komik-starter'); ?></strong>
                    <input type="number" name="today_limit" value="<?php echo esc_attr($today_limit); ?>" min="1" max="20" style="width: 60px; margin-left: 5px;">
                    <?php _e('items in slider', 'komik-starter'); ?>
                </p>
                
                <h3 style="margin-top:0;"><?php _e('Data Source', 'komik-starter'); ?></h3>
                <div class="komik-mode-select" data-category="today">
                    <label class="<?php echo $today_mode == 'manual' ? 'active' : ''; ?>">
                        <input type="radio" name="today_mode" value="manual" <?php checked($today_mode, 'manual'); ?> style="display:none;">
                        <span class="dashicons dashicons-edit"></span> <?php _e('Manual', 'komik-starter'); ?>
                    </label>
                    <label class="<?php echo $today_mode == 'views' ? 'active' : ''; ?>">
                        <input type="radio" name="today_mode" value="views" <?php checked($today_mode, 'views'); ?> style="display:none;">
                        <span class="dashicons dashicons-visibility"></span> <?php _e('By Views', 'komik-starter'); ?>
                    </label>
                    <label class="<?php echo $today_mode == 'rating' ? 'active' : ''; ?>">
                        <input type="radio" name="today_mode" value="rating" <?php checked($today_mode, 'rating'); ?> style="display:none;">
                        <span class="dashicons dashicons-star-filled"></span> <?php _e('By Rating', 'komik-starter'); ?>
                    </label>
                </div>
                
                <div class="manual-grid <?php echo $today_mode == 'manual' ? 'active' : ''; ?>" data-category="today">
                    <div class="komik-search-box">
                        <input type="text" class="manhwa-search" data-tab="today" placeholder="<?php _e('Search manhwa...', 'komik-starter'); ?>">
                    </div>
                    <p style="color:#646970;margin-bottom:10px;"><span class="dashicons dashicons-info"></span> <?php _e('Click manhwa to select. Selected:', 'komik-starter'); ?> <strong id="today-selected"><?php echo count($today_ids); ?></strong></p>
                    <div class="komik-manhwa-grid" id="today-grid">
                        <?php foreach ($all_manhwa as $manhwa) : 
                            $is_selected = in_array($manhwa->ID, $today_ids);
                            $order = $is_selected ? array_search($manhwa->ID, $today_ids) + 1 : '';
                            $thumb = get_the_post_thumbnail_url($manhwa->ID, 'thumbnail') ?: get_template_directory_uri() . '/assets/images/placeholder.jpg';
                        ?>
                        <div class="komik-manhwa-item <?php echo $is_selected ? 'selected' : ''; ?>" 
                             data-id="<?php echo $manhwa->ID; ?>" 
                             data-title="<?php echo esc_attr($manhwa->post_title); ?>"
                             data-category="today">
                            <img src="<?php echo esc_url($thumb); ?>" alt="">
                            <span class="title"><?php echo esc_html($manhwa->post_title); ?></span>
                            <?php if ($is_selected) : ?>
                            <span class="order"><?php echo $order; ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="komik-options-wrap">
                <h2><?php _e('Configure Popular Manhwa per Tab', 'komik-starter'); ?></h2>
                
                <div class="komik-tabs">
                    <div class="komik-tab active" data-tab="weekly">
                        <span class="dashicons dashicons-calendar"></span> <?php _e('Mingguan', 'komik-starter'); ?>
                    </div>
                    <div class="komik-tab" data-tab="monthly">
                        <span class="dashicons dashicons-calendar-alt"></span> <?php _e('Bulanan', 'komik-starter'); ?>
                    </div>
                    <div class="komik-tab" data-tab="all">
                        <span class="dashicons dashicons-awards"></span> <?php _e('Semua', 'komik-starter'); ?>
                    </div>
                </div>
                
                <!-- Weekly Tab -->
                <div class="komik-tab-content active" data-tab="weekly">
                    <h3 style="margin-top:0;"><?php _e('Mingguan - Data Source', 'komik-starter'); ?></h3>
                    <div class="komik-mode-select" data-category="weekly">
                        <label class="<?php echo $weekly_mode == 'manual' ? 'active' : ''; ?>">
                            <input type="radio" name="weekly_mode" value="manual" <?php checked($weekly_mode, 'manual'); ?> style="display:none;">
                            <span class="dashicons dashicons-edit"></span> <?php _e('Manual', 'komik-starter'); ?>
                        </label>
                        <label class="<?php echo $weekly_mode == 'views' ? 'active' : ''; ?>">
                            <input type="radio" name="weekly_mode" value="views" <?php checked($weekly_mode, 'views'); ?> style="display:none;">
                            <span class="dashicons dashicons-visibility"></span> <?php _e('By Views', 'komik-starter'); ?>
                        </label>
                        <label class="<?php echo $weekly_mode == 'rating' ? 'active' : ''; ?>">
                            <input type="radio" name="weekly_mode" value="rating" <?php checked($weekly_mode, 'rating'); ?> style="display:none;">
                            <span class="dashicons dashicons-star-filled"></span> <?php _e('By Rating', 'komik-starter'); ?>
                        </label>
                    </div>
                    
                    <div class="manual-grid <?php echo $weekly_mode == 'manual' ? 'active' : ''; ?>" data-category="weekly">
                        <div class="komik-search-box">
                            <input type="text" class="manhwa-search" data-tab="weekly" placeholder="<?php _e('Search manhwa...', 'komik-starter'); ?>">
                        </div>
                        <p style="color:#646970;margin-bottom:10px;"><span class="dashicons dashicons-info"></span> <?php _e('Click manhwa to select. Selected:', 'komik-starter'); ?> <strong id="weekly-selected"><?php echo count($weekly_ids); ?></strong></p>
                        <div class="komik-manhwa-grid" id="weekly-grid">
                            <?php foreach ($all_manhwa as $manhwa) : 
                                $is_selected = in_array($manhwa->ID, $weekly_ids);
                                $order = $is_selected ? array_search($manhwa->ID, $weekly_ids) + 1 : '';
                                $thumb = get_the_post_thumbnail_url($manhwa->ID, 'thumbnail') ?: get_template_directory_uri() . '/assets/images/placeholder.jpg';
                            ?>
                            <div class="komik-manhwa-item <?php echo $is_selected ? 'selected' : ''; ?>" 
                                 data-id="<?php echo $manhwa->ID; ?>" 
                                 data-title="<?php echo esc_attr($manhwa->post_title); ?>"
                                 data-category="weekly">
                                <img src="<?php echo esc_url($thumb); ?>" alt="">
                                <span class="title"><?php echo esc_html($manhwa->post_title); ?></span>
                                <?php if ($is_selected) : ?>
                                <span class="order"><?php echo $order; ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Monthly Tab -->
                <div class="komik-tab-content" data-tab="monthly">
                    <h3 style="margin-top:0;"><?php _e('Bulanan - Data Source', 'komik-starter'); ?></h3>
                    <div class="komik-mode-select" data-category="monthly">
                        <label class="<?php echo $monthly_mode == 'manual' ? 'active' : ''; ?>">
                            <input type="radio" name="monthly_mode" value="manual" <?php checked($monthly_mode, 'manual'); ?> style="display:none;">
                            <span class="dashicons dashicons-edit"></span> <?php _e('Manual', 'komik-starter'); ?>
                        </label>
                        <label class="<?php echo $monthly_mode == 'views' ? 'active' : ''; ?>">
                            <input type="radio" name="monthly_mode" value="views" <?php checked($monthly_mode, 'views'); ?> style="display:none;">
                            <span class="dashicons dashicons-visibility"></span> <?php _e('By Views', 'komik-starter'); ?>
                        </label>
                        <label class="<?php echo $monthly_mode == 'rating' ? 'active' : ''; ?>">
                            <input type="radio" name="monthly_mode" value="rating" <?php checked($monthly_mode, 'rating'); ?> style="display:none;">
                            <span class="dashicons dashicons-star-filled"></span> <?php _e('By Rating', 'komik-starter'); ?>
                        </label>
                    </div>
                    
                    <div class="manual-grid <?php echo $monthly_mode == 'manual' ? 'active' : ''; ?>" data-category="monthly">
                        <div class="komik-search-box">
                            <input type="text" class="manhwa-search" data-tab="monthly" placeholder="<?php _e('Search manhwa...', 'komik-starter'); ?>">
                        </div>
                        <p style="color:#646970;margin-bottom:10px;"><span class="dashicons dashicons-info"></span> <?php _e('Click manhwa to select. Selected:', 'komik-starter'); ?> <strong id="monthly-selected"><?php echo count($monthly_ids); ?></strong></p>
                        <div class="komik-manhwa-grid" id="monthly-grid">
                            <?php foreach ($all_manhwa as $manhwa) : 
                                $is_selected = in_array($manhwa->ID, $monthly_ids);
                                $order = $is_selected ? array_search($manhwa->ID, $monthly_ids) + 1 : '';
                                $thumb = get_the_post_thumbnail_url($manhwa->ID, 'thumbnail') ?: get_template_directory_uri() . '/assets/images/placeholder.jpg';
                            ?>
                            <div class="komik-manhwa-item <?php echo $is_selected ? 'selected' : ''; ?>" 
                                 data-id="<?php echo $manhwa->ID; ?>" 
                                 data-title="<?php echo esc_attr($manhwa->post_title); ?>"
                                 data-category="monthly">
                                <img src="<?php echo esc_url($thumb); ?>" alt="">
                                <span class="title"><?php echo esc_html($manhwa->post_title); ?></span>
                                <?php if ($is_selected) : ?>
                                <span class="order"><?php echo $order; ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- All Time Tab -->
                <div class="komik-tab-content" data-tab="all">
                    <h3 style="margin-top:0;"><?php _e('Semua - Data Source', 'komik-starter'); ?></h3>
                    <div class="komik-mode-select" data-category="all">
                        <label class="<?php echo $all_mode == 'manual' ? 'active' : ''; ?>">
                            <input type="radio" name="all_mode" value="manual" <?php checked($all_mode, 'manual'); ?> style="display:none;">
                            <span class="dashicons dashicons-edit"></span> <?php _e('Manual', 'komik-starter'); ?>
                        </label>
                        <label class="<?php echo $all_mode == 'views' ? 'active' : ''; ?>">
                            <input type="radio" name="all_mode" value="views" <?php checked($all_mode, 'views'); ?> style="display:none;">
                            <span class="dashicons dashicons-visibility"></span> <?php _e('By Views', 'komik-starter'); ?>
                        </label>
                        <label class="<?php echo $all_mode == 'rating' ? 'active' : ''; ?>">
                            <input type="radio" name="all_mode" value="rating" <?php checked($all_mode, 'rating'); ?> style="display:none;">
                            <span class="dashicons dashicons-star-filled"></span> <?php _e('By Rating', 'komik-starter'); ?>
                        </label>
                    </div>
                    
                    <div class="manual-grid <?php echo $all_mode == 'manual' ? 'active' : ''; ?>" data-category="all">
                        <div class="komik-search-box">
                            <input type="text" class="manhwa-search" data-tab="all" placeholder="<?php _e('Search manhwa...', 'komik-starter'); ?>">
                        </div>
                        <p style="color:#646970;margin-bottom:10px;"><span class="dashicons dashicons-info"></span> <?php _e('Click manhwa to select. Selected:', 'komik-starter'); ?> <strong id="all-selected"><?php echo count($all_ids); ?></strong></p>
                        <div class="komik-manhwa-grid" id="all-grid">
                            <?php foreach ($all_manhwa as $manhwa) : 
                                $is_selected = in_array($manhwa->ID, $all_ids);
                                $order = $is_selected ? array_search($manhwa->ID, $all_ids) + 1 : '';
                                $thumb = get_the_post_thumbnail_url($manhwa->ID, 'thumbnail') ?: get_template_directory_uri() . '/assets/images/placeholder.jpg';
                            ?>
                            <div class="komik-manhwa-item <?php echo $is_selected ? 'selected' : ''; ?>" 
                                 data-id="<?php echo $manhwa->ID; ?>" 
                                 data-title="<?php echo esc_attr($manhwa->post_title); ?>"
                                 data-category="all">
                                <img src="<?php echo esc_url($thumb); ?>" alt="">
                                <span class="title"><?php echo esc_html($manhwa->post_title); ?></span>
                                <?php if ($is_selected) : ?>
                                <span class="order"><?php echo $order; ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <p style="margin-top: 20px;">
                <?php submit_button(__('Save Changes', 'komik-starter'), 'primary', 'komik_save_popular', false); ?>
            </p>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        var selections = {
            today: <?php echo json_encode(array_map('intval', $today_ids)); ?>,
            weekly: <?php echo json_encode(array_map('intval', $weekly_ids)); ?>,
            monthly: <?php echo json_encode(array_map('intval', $monthly_ids)); ?>,
            all: <?php echo json_encode(array_map('intval', $all_ids)); ?>
        };
        
        // Tab switching
        $('.komik-tab').on('click', function() {
            var tab = $(this).data('tab');
            $('.komik-tab').removeClass('active');
            $(this).addClass('active');
            $('.komik-tab-content').removeClass('active');
            $('.komik-tab-content[data-tab="' + tab + '"]').addClass('active');
        });
        
        // Per-tab mode toggle
        $('.komik-mode-select label').on('click', function() {
            var $parent = $(this).parent();
            var category = $parent.data('category');
            $parent.find('label').removeClass('active');
            $(this).addClass('active');
            $(this).find('input').prop('checked', true);
            
            // Show/hide manual grid
            var mode = $(this).find('input').val();
            var $manualGrid = $('.manual-grid[data-category="' + category + '"]');
            if (mode === 'manual') {
                $manualGrid.addClass('active');
            } else {
                $manualGrid.removeClass('active');
            }
        });
        
        // Search
        $('.manhwa-search').on('keyup', function() {
            var query = $(this).val().toLowerCase();
            var tab = $(this).data('tab');
            $('#' + tab + '-grid .komik-manhwa-item').each(function() {
                var title = $(this).data('title').toLowerCase();
                $(this).toggle(title.indexOf(query) > -1);
            });
        });
        
        // Toggle selection
        $('.komik-manhwa-item').on('click', function() {
            var id = parseInt($(this).data('id'));
            var category = $(this).data('category');
            var $el = $(this);
            
            if ($el.hasClass('selected')) {
                // Remove
                selections[category] = selections[category].filter(function(i) { return i !== id; });
                $el.removeClass('selected').find('.order').remove();
            } else {
                // Add
                if (selections[category].length >= 20) {
                    alert('Maximum 20 items per category');
                    return;
                }
                selections[category].push(id);
                $el.addClass('selected').append('<span class="order">' + selections[category].length + '</span>');
            }
            
            updateCounts(category);
            updateHiddenInputs();
        });
        
        function updateCounts(category) {
            $('#' + category + '-selected').text(selections[category].length);
            $('#' + category + '-count').text(selections[category].length);
            
            // Update order numbers
            $('#' + category + '-grid .komik-manhwa-item').each(function() {
                var id = parseInt($(this).data('id'));
                var order = selections[category].indexOf(id);
                $(this).find('.order').remove();
                if (order > -1) {
                    $(this).append('<span class="order">' + (order + 1) + '</span>');
                }
            });
        }
        
        function updateHiddenInputs() {
            // Remove existing hidden inputs
            $('input[name^="popular_today"], input[name^="popular_weekly"], input[name^="popular_monthly"], input[name^="popular_all"]').remove();
            
            // Add new hidden inputs
            selections.today.forEach(function(id) {
                $('#popular-manhwa-form').append('<input type="hidden" name="popular_today[]" value="' + id + '">');
            });
            selections.weekly.forEach(function(id) {
                $('#popular-manhwa-form').append('<input type="hidden" name="popular_weekly[]" value="' + id + '">');
            });
            selections.monthly.forEach(function(id) {
                $('#popular-manhwa-form').append('<input type="hidden" name="popular_monthly[]" value="' + id + '">');
            });
            selections.all.forEach(function(id) {
                $('#popular-manhwa-form').append('<input type="hidden" name="popular_all[]" value="' + id + '">');
            });
        }
        
        // Initialize hidden inputs
        updateHiddenInputs();
    });
    </script>
    
    <?php
}

/**
 * =================================================================
 * ADS MANAGEMENT PAGE
 * =================================================================
 */
function komik_starter_ads_page() {
    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-megaphone" style="font-size: 30px; margin-right: 10px;"></span> <?php _e('Ads Management', 'komik-starter'); ?></h1>
        
        <style>
            .komik-ads-wrap { background: #fff; padding: 25px; border-radius: 8px; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
            .komik-ads-wrap h2 { margin-top: 0; padding-bottom: 10px; border-bottom: 2px solid #2271b1; color: #1e1e1e; }
            .komik-ads-section { margin-bottom: 30px; }
            .komik-ads-section h3 { margin: 0 0 15px; padding: 10px 15px; background: #f0f6fc; border-left: 4px solid #2271b1; font-size: 14px; }
            .komik-ads-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
            .komik-ad-field { }
            .komik-ad-field label { display: block; font-weight: 600; margin-bottom: 8px; color: #1e1e1e; }
            .komik-ad-field label .dashicons { color: #2271b1; margin-right: 5px; }
            .komik-ad-field textarea { width: 100%; height: 120px; font-family: monospace; font-size: 12px; }
            .komik-ad-field .description { font-size: 12px; color: #646970; margin-top: 5px; }
            .komik-tabs { display: flex; gap: 0; border-bottom: 1px solid #dcdcde; margin-bottom: 0; }
            .komik-tab { padding: 12px 20px; cursor: pointer; background: #f0f0f1; border: 1px solid #dcdcde; border-bottom: none; margin-bottom: -1px; }
            .komik-tab.active { background: #fff; border-bottom-color: #fff; font-weight: 600; }
            .komik-tab-content { display: none; padding: 20px 0; }
            .komik-tab-content.active { display: block; }
        </style>
        
        <form method="post" action="options.php">
            <?php settings_fields('komik-ads-settings'); ?>
            <?php do_settings_sections('komik-ads-settings'); ?>
            
            <div class="komik-ads-wrap">
                <h2><?php _e('Ad Placements', 'komik-starter'); ?></h2>
                
                <div class="komik-tabs">
                    <div class="komik-tab active" data-tab="general"><?php _e('General', 'komik-starter'); ?></div>
                    <div class="komik-tab" data-tab="homepage"><?php _e('Homepage', 'komik-starter'); ?></div>
                    <div class="komik-tab" data-tab="series"><?php _e('Series Page', 'komik-starter'); ?></div>
                    <div class="komik-tab" data-tab="reader"><?php _e('Chapter Reader', 'komik-starter'); ?></div>
                    <div class="komik-tab" data-tab="floating"><?php _e('Floating Ads', 'komik-starter'); ?></div>
                    <div class="komik-tab" data-tab="adsterra" style="background: linear-gradient(135deg, #00d4aa 0%, #00a085 100%); color: #fff;"><?php _e('Adsterra', 'komik-starter'); ?></div>
                </div>
                
                <!-- General Tab -->
                <div class="komik-tab-content active" id="tab-general">
                    <div class="komik-ads-grid">
                        <div class="komik-ad-field">
                            <label><span class="dashicons dashicons-editor-code"></span> <?php _e('Header Scripts', 'komik-starter'); ?></label>
                            <textarea name="komik_ads_header" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_header')); ?></textarea>
                            <p class="description"><?php _e('Scripts to add before &lt;/head&gt; tag (e.g., Google Analytics, AdSense verification)', 'komik-starter'); ?></p>
                        </div>
                        
                        <h4 style="margin: 20px 0 10px; color: #2271b1; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                            <span class="dashicons dashicons-menu"></span> <?php _e('Below Main Menu (2x2 Grid on Desktop)', 'komik-starter'); ?>
                        </h4>
                        
                        <p style="color: #646970; margin-bottom: 15px;"><span class="dashicons dashicons-info"></span> <?php _e('Row 1 - Top', 'komik-starter'); ?></p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                            <div class="komik-ad-field">
                                <label><span class="dashicons dashicons-align-left"></span> <?php _e('Row 1 - Left', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_below_menu" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_below_menu')); ?></textarea>
                            </div>
                            <div class="komik-ad-field">
                                <label><span class="dashicons dashicons-align-right"></span> <?php _e('Row 1 - Right', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_below_menu_2" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_below_menu_2')); ?></textarea>
                            </div>
                        </div>
                        
                        <p style="color: #646970; margin-bottom: 15px;"><span class="dashicons dashicons-info"></span> <?php _e('Row 2 - Bottom', 'komik-starter'); ?></p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="komik-ad-field">
                                <label><span class="dashicons dashicons-align-left"></span> <?php _e('Row 2 - Left', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_below_menu_3" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_below_menu_3')); ?></textarea>
                            </div>
                            <div class="komik-ad-field">
                                <label><span class="dashicons dashicons-align-right"></span> <?php _e('Row 2 - Right', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_below_menu_4" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_below_menu_4')); ?></textarea>
                            </div>
                        </div>
                        <div class="komik-ad-field">
                            <label><span class="dashicons dashicons-align-left"></span> <?php _e('Sidebar Top', 'komik-starter'); ?></label>
                            <textarea name="komik_ads_sidebar" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_sidebar')); ?></textarea>
                            <p class="description"><?php _e('Displayed at top of sidebar', 'komik-starter'); ?></p>
                        </div>
                    </div>
                </div>
                <!-- Homepage Tab -->
                <div class="komik-tab-content" id="tab-homepage">
                    <div class="komik-ads-grid">
                        <h4 style="margin: 0 0 10px; color: #2271b1; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                            <span class="dashicons dashicons-arrow-up-alt"></span> <?php _e('Homepage Top (2x2 Grid)', 'komik-starter'); ?>
                        </h4>
                        
                        <p style="color: #646970; margin-bottom: 15px;"><span class="dashicons dashicons-info"></span> <?php _e('Row 1 - Top', 'komik-starter'); ?></p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                            <div class="komik-ad-field">
                                <label><?php _e('Row 1 - Left', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_homepage_top" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_homepage_top')); ?></textarea>
                            </div>
                            <div class="komik-ad-field">
                                <label><?php _e('Row 1 - Right', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_homepage_top_2" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_homepage_top_2')); ?></textarea>
                            </div>
                        </div>
                        
                        <p style="color: #646970; margin-bottom: 15px;"><span class="dashicons dashicons-info"></span> <?php _e('Row 2 - Bottom', 'komik-starter'); ?></p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                            <div class="komik-ad-field">
                                <label><?php _e('Row 2 - Left', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_homepage_top_3" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_homepage_top_3')); ?></textarea>
                            </div>
                            <div class="komik-ad-field">
                                <label><?php _e('Row 2 - Right', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_homepage_top_4" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_homepage_top_4')); ?></textarea>
                            </div>
                        </div>
                        
                        <h4 style="margin: 20px 0 10px; color: #2271b1; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                            <?php _e('Row 1 - Top Ads (2 Columns)', 'komik-starter'); ?>
                        </h4>
                        <div class="komik-ad-field">
                            <label><span class="dashicons dashicons-columns"></span> <?php _e('Row 1 - Left', 'komik-starter'); ?></label>
                            <textarea name="komik_ads_homepage_middle" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_homepage_middle')); ?></textarea>
                            <p class="description"><?php _e('Top row, left column', 'komik-starter'); ?></p>
                        </div>
                        <div class="komik-ad-field">
                            <label><span class="dashicons dashicons-columns"></span> <?php _e('Row 1 - Right', 'komik-starter'); ?></label>
                            <textarea name="komik_ads_homepage_middle_2" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_homepage_middle_2')); ?></textarea>
                            <p class="description"><?php _e('Top row, right column', 'komik-starter'); ?></p>
                        </div>
                        
                        <h4 style="margin: 20px 0 10px; color: #2271b1; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                            <?php _e('Row 2 - Bottom Ads (2 Columns)', 'komik-starter'); ?>
                        </h4>
                        <div class="komik-ad-field">
                            <label><span class="dashicons dashicons-columns"></span> <?php _e('Row 2 - Left', 'komik-starter'); ?></label>
                            <textarea name="komik_ads_homepage_middle_3" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_homepage_middle_3')); ?></textarea>
                            <p class="description"><?php _e('Bottom row, left column', 'komik-starter'); ?></p>
                        </div>
                        <div class="komik-ad-field">
                            <label><span class="dashicons dashicons-columns"></span> <?php _e('Row 2 - Right', 'komik-starter'); ?></label>
                            <textarea name="komik_ads_homepage_middle_4" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_homepage_middle_4')); ?></textarea>
                            <p class="description"><?php _e('Bottom row, right column', 'komik-starter'); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Series Tab -->
                <div class="komik-tab-content" id="tab-series">
                    <div class="komik-ads-grid">
                        <!-- Before Series Info -->
                        <h4 style="margin: 0 0 10px; color: #2271b1; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                            <span class="dashicons dashicons-book"></span> <?php _e('Before Series Info (2x2 Grid)', 'komik-starter'); ?>
                        </h4>
                        
                        <p style="color: #646970; margin-bottom: 15px;"><span class="dashicons dashicons-info"></span> <?php _e('Row 1 - Top', 'komik-starter'); ?></p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                            <div class="komik-ad-field">
                                <label><?php _e('Row 1 - Left', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_series_before" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_series_before')); ?></textarea>
                            </div>
                            <div class="komik-ad-field">
                                <label><?php _e('Row 1 - Right', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_series_before_2" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_series_before_2')); ?></textarea>
                            </div>
                        </div>
                        
                        <p style="color: #646970; margin-bottom: 15px;"><span class="dashicons dashicons-info"></span> <?php _e('Row 2 - Bottom', 'komik-starter'); ?></p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px;">
                            <div class="komik-ad-field">
                                <label><?php _e('Row 2 - Left', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_series_before_3" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_series_before_3')); ?></textarea>
                            </div>
                            <div class="komik-ad-field">
                                <label><?php _e('Row 2 - Right', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_series_before_4" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_series_before_4')); ?></textarea>
                            </div>
                        </div>
                        
                        <!-- After Chapter List -->
                        <h4 style="margin: 0 0 10px; color: #2271b1; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                            <span class="dashicons dashicons-book-alt"></span> <?php _e('After Chapter List (2x2 Grid)', 'komik-starter'); ?>
                        </h4>
                        
                        <p style="color: #646970; margin-bottom: 15px;"><span class="dashicons dashicons-info"></span> <?php _e('Row 1 - Top', 'komik-starter'); ?></p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                            <div class="komik-ad-field">
                                <label><?php _e('Row 1 - Left', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_series_after" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_series_after')); ?></textarea>
                            </div>
                            <div class="komik-ad-field">
                                <label><?php _e('Row 1 - Right', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_series_after_2" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_series_after_2')); ?></textarea>
                            </div>
                        </div>
                        
                        <p style="color: #646970; margin-bottom: 15px;"><span class="dashicons dashicons-info"></span> <?php _e('Row 2 - Bottom', 'komik-starter'); ?></p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="komik-ad-field">
                                <label><?php _e('Row 2 - Left', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_series_after_3" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_series_after_3')); ?></textarea>
                            </div>
                            <div class="komik-ad-field">
                                <label><?php _e('Row 2 - Right', 'komik-starter'); ?></label>
                                <textarea name="komik_ads_series_after_4" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_series_after_4')); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Reader Tab -->
                <div class="komik-tab-content" id="tab-reader">
                    <div class="komik-ads-grid">
                        <div class="komik-ad-field">
                            <label><span class="dashicons dashicons-arrow-up-alt"></span> <?php _e('Above Images', 'komik-starter'); ?></label>
                            <textarea name="komik_ads_reader_top" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_reader_top')); ?></textarea>
                            <p class="description"><?php _e('Displayed above chapter images', 'komik-starter'); ?></p>
                        </div>
                        <div class="komik-ad-field">
                            <label><span class="dashicons dashicons-minus"></span> <?php _e('Between Images', 'komik-starter'); ?></label>
                            <textarea name="komik_ads_reader_middle" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_reader_middle')); ?></textarea>
                            <p class="description"><?php _e('Displayed in middle of chapter images', 'komik-starter'); ?></p>
                        </div>
                        <div class="komik-ad-field">
                            <label><span class="dashicons dashicons-arrow-down-alt"></span> <?php _e('Below Images', 'komik-starter'); ?></label>
                            <textarea name="komik_ads_reader_bottom" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_reader_bottom')); ?></textarea>
                            <p class="description"><?php _e('Displayed below chapter images', 'komik-starter'); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Floating Tab -->
                <div class="komik-tab-content" id="tab-floating">
                    <div class="komik-ads-grid">
                        <div class="komik-ad-field">
                            <label><span class="dashicons dashicons-align-left"></span> <?php _e('Float Left', 'komik-starter'); ?></label>
                            <textarea name="komik_ads_float_left" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_float_left')); ?></textarea>
                            <p class="description"><?php _e('Fixed position on left side of screen', 'komik-starter'); ?></p>
                        </div>
                        <div class="komik-ad-field">
                            <label><span class="dashicons dashicons-align-right"></span> <?php _e('Float Right', 'komik-starter'); ?></label>
                            <textarea name="komik_ads_float_right" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_float_right')); ?></textarea>
                            <p class="description"><?php _e('Fixed position on right side of screen', 'komik-starter'); ?></p>
                        </div>
                        <div class="komik-ad-field">
                            <label><span class="dashicons dashicons-arrow-down-alt"></span> <?php _e('Float Bottom', 'komik-starter'); ?></label>
                            <textarea name="komik_ads_float_bottom" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_float_bottom')); ?></textarea>
                            <p class="description"><?php _e('Sticky banner at bottom of screen', 'komik-starter'); ?></p>
                        </div>
                        <div class="komik-ad-field">
                            <label><span class="dashicons dashicons-external"></span> <?php _e('Popup Ad', 'komik-starter'); ?></label>
                            <textarea name="komik_ads_popup" class="large-text code"><?php echo esc_textarea(get_option('komik_ads_popup')); ?></textarea>
                            <p class="description"><?php _e('Popup ad shown on page load (use sparingly)', 'komik-starter'); ?></p>
                        </div>
                        
                        <!-- Direct Link Section -->
                        <div class="komik-ad-field" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 8px; margin-top: 20px;">
                            <h4 style="margin: 0 0 15px; color: #fff; display: flex; align-items: center; gap: 10px;">
                                <span class="dashicons dashicons-admin-links"></span> 
                                <?php _e('Direct Link / Click Ads', 'komik-starter'); ?>
                                <span style="background: rgba(255,255,255,0.2); font-size: 10px; padding: 2px 8px; border-radius: 3px;"><?php _e('Opens on first click', 'komik-starter'); ?></span>
                            </h4>
                            
                            <div style="background: rgba(255,255,255,0.95); padding: 15px; border-radius: 6px;">
                                <div style="margin-bottom: 15px;">
                                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                        <input type="checkbox" name="komik_ads_direct_link_enable" value="1" <?php checked(get_option('komik_ads_direct_link_enable'), '1'); ?>>
                                        <strong><?php _e('Enable Direct Link', 'komik-starter'); ?></strong>
                                    </label>
                                    <p style="color: #666; font-size: 12px; margin: 5px 0 0 28px;"><?php _e('When enabled, the first click anywhere on the page will open the Direct Link URL', 'komik-starter'); ?></p>
                                </div>
                                
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e('Direct Link URLs (one per line)', 'komik-starter'); ?></label>
                                    <textarea name="komik_ads_direct_link_url" rows="5" placeholder="https://manhwaindo.web.id/&#10;https://example.com/offer1&#10;https://example.com/offer2" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace;"><?php echo esc_textarea(get_option('komik_ads_direct_link_url')); ?></textarea>
                                    <p style="color: #666; font-size: 12px; margin: 5px 0 0;"><?php _e('Enter multiple URLs, one per line. URLs will be rotated randomly.', 'komik-starter'); ?></p>
                                </div>
                                
                                <div>
                                    <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e('Delay Before Active (seconds)', 'komik-starter'); ?></label>
                                    <input type="number" name="komik_ads_direct_link_delay" value="<?php echo esc_attr(get_option('komik_ads_direct_link_delay', 0)); ?>" min="0" max="60" style="width: 120px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                    <p style="color: #666; font-size: 12px; margin: 5px 0 0;"><?php _e('Wait X seconds before direct link becomes active', 'komik-starter'); ?></p>
                                </div>
                                
                                <div style="margin-top: 15px; padding: 10px; background: #e8f5e9; border-radius: 4px; border-left: 3px solid #4caf50;">
                                    <p style="margin: 0; font-size: 12px; color: #2e7d32;">
                                        <strong>📌 <?php _e('How it works:', 'komik-starter'); ?></strong><br>
                                        <?php _e('Every page load = 1 click redirect. User clicks anywhere → opens random URL from list.', 'komik-starter'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Adsterra Tab -->
                <div class="komik-tab-content" id="tab-adsterra">
                    <div style="background: linear-gradient(135deg, #00d4aa 0%, #00a085 100%); color: #fff; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h3 style="margin: 0 0 5px; color: #fff;">
                            <img src="https://www.adsterra.com/favicon.ico" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 8px;">
                            Adsterra Ad Codes
                        </h3>
                        <p style="margin: 0; opacity: 0.9; font-size: 13px;"><?php _e('Paste your Adsterra ad codes here. Get codes from', 'komik-starter'); ?> <a href="https://partners.adsterra.com" target="_blank" style="color: #fff; text-decoration: underline;">partners.adsterra.com</a></p>
                    </div>
                    
                    <div class="komik-ads-grid">
                        <div class="komik-ad-field" style="background: #f9f9f9; padding: 20px; border-radius: 8px; border-left: 4px solid #e74c3c;">
                            <label style="font-size: 15px;">
                                <span class="dashicons dashicons-admin-page" style="color: #e74c3c;"></span> 
                                <?php _e('Popunder Code', 'komik-starter'); ?>
                                <span style="background: #e74c3c; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 3px; margin-left: 5px;">HIGH CPM</span>
                            </label>
                            <textarea name="komik_ads_adsterra_popunder" class="large-text code" style="height: 150px;"><?php echo esc_textarea(get_option('komik_ads_adsterra_popunder')); ?></textarea>
                            <p class="description">
                                <?php _e('Popunder opens a new tab/window when user clicks anywhere on the page.', 'komik-starter'); ?><br>
                                <strong><?php _e('Location:', 'komik-starter'); ?></strong> <?php _e('Loads on all pages', 'komik-starter'); ?>
                            </p>
                            <div style="background: #2d2d2d; color: #f8f8f2; padding: 10px 15px; border-radius: 4px; font-family: monospace; font-size: 11px; margin-top: 10px; overflow-x: auto;">
                                &lt;script type="text/javascript" src="//pl12345678.profitableratecpm.com/ab/cd/ef/abcdef.js"&gt;&lt;/script&gt;
                            </div>
                        </div>
                        
                        <div class="komik-ad-field" style="background: #f9f9f9; padding: 20px; border-radius: 8px; border-left: 4px solid #3498db;">
                            <label style="font-size: 15px;">
                                <span class="dashicons dashicons-admin-links" style="color: #3498db;"></span> 
                                <?php _e('Direct Link / Smartlink', 'komik-starter'); ?>
                            </label>
                            <textarea name="komik_ads_adsterra_smartlink" class="large-text code" style="height: 100px;" placeholder="https://www.profitableratecpm.com/abcdef"><?php echo esc_textarea(get_option('komik_ads_adsterra_smartlink')); ?></textarea>
                            <p class="description">
                                <?php _e('Paste your Adsterra Direct Link URL here. Can be used for custom buttons or links.', 'komik-starter'); ?><br>
                                <strong><?php _e('Usage:', 'komik-starter'); ?></strong> <?php _e('Create buttons/links that redirect to this URL', 'komik-starter'); ?>
                            </p>
                        </div>
                        
                        <div class="komik-ad-field" style="background: #f9f9f9; padding: 20px; border-radius: 8px; border-left: 4px solid #9b59b6;">
                            <label style="font-size: 15px;">
                                <span class="dashicons dashicons-share" style="color: #9b59b6;"></span> 
                                <?php _e('Social Bar Code', 'komik-starter'); ?>
                                <span style="background: #9b59b6; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 3px; margin-left: 5px;">PUSH</span>
                            </label>
                            <textarea name="komik_ads_adsterra_socialbar" class="large-text code" style="height: 150px;"><?php echo esc_textarea(get_option('komik_ads_adsterra_socialbar')); ?></textarea>
                            <p class="description">
                                <?php _e('Social Bar displays push notification style ads. Very effective!', 'komik-starter'); ?><br>
                                <strong><?php _e('Location:', 'komik-starter'); ?></strong> <?php _e('Fixed position, usually bottom-right corner', 'komik-starter'); ?>
                            </p>
                            <div style="background: #2d2d2d; color: #f8f8f2; padding: 10px 15px; border-radius: 4px; font-family: monospace; font-size: 11px; margin-top: 10px; overflow-x: auto;">
                                &lt;script type="text/javascript" src="//pl12345678.profitablegatetocontent.com/ab/cd.js"&gt;&lt;/script&gt;
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php submit_button(); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.komik-tab').on('click', function() {
            var tab = $(this).data('tab');
            $('.komik-tab').removeClass('active');
            $(this).addClass('active');
            $('.komik-tab-content').removeClass('active');
            $('#tab-' + tab).addClass('active');
        });
    });
    </script>
    
    <?php
}

/**
 * =================================================================
 * HELPER FUNCTIONS FOR DISPLAYING ADS
 * =================================================================
 */

/**
 * Display ad by position
 */
function komik_starter_display_ad($position) {
    // Special handling for below_menu - 2x2 grid on desktop
    if ($position === 'below_menu') {
        $ad1 = get_option('komik_ads_below_menu');
        $ad2 = get_option('komik_ads_below_menu_2');
        $ad3 = get_option('komik_ads_below_menu_3');
        $ad4 = get_option('komik_ads_below_menu_4');
        
        $has_row1 = !empty($ad1) || !empty($ad2);
        $has_row2 = !empty($ad3) || !empty($ad4);
        
        if ($has_row1 || $has_row2) {
            echo '<div class="komik-ad komik-ad-below_menu">';
            echo '<style>
                .komik-ad-below_menu { padding: 15px 28px; }
                .komik-ad-below_menu-row { 
                    display: grid; 
                    grid-template-columns: 1fr 1fr; 
                    gap: 15px;
                    max-width: 1200px;
                    margin: 0 auto 15px;
                }
                .komik-ad-below_menu-row:last-child { margin-bottom: 0; }
                .komik-ad-below_menu-row .ad-col {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                .komik-ad-below_menu-row .ad-col iframe,
                .komik-ad-below_menu-row .ad-col img {
                    max-width: 100%;
                }
                @media (max-width: 768px) {
                    .komik-ad-below_menu-row {
                        grid-template-columns: 1fr;
                    }
                }
            </style>';
            
            // Row 1
            if ($has_row1) {
                echo '<div class="komik-ad-below_menu-row">';
                if (!empty($ad1)) {
                    echo '<div class="ad-col">' . $ad1 . '</div>';
                }
                if (!empty($ad2)) {
                    echo '<div class="ad-col">' . $ad2 . '</div>';
                }
                echo '</div>';
            }
            
            // Row 2
            if ($has_row2) {
                echo '<div class="komik-ad-below_menu-row">';
                if (!empty($ad3)) {
                    echo '<div class="ad-col">' . $ad3 . '</div>';
                }
                if (!empty($ad4)) {
                    echo '<div class="ad-col">' . $ad4 . '</div>';
                }
                echo '</div>';
            }
            
            echo '</div>';
        }
        return;
    }
    
    // Grid positions: homepage_top, series_before, series_after
    $grid_positions = array('homepage_top', 'series_before', 'series_after');
    
    if (in_array($position, $grid_positions)) {
        $ad1 = get_option('komik_ads_' . $position);
        $ad2 = get_option('komik_ads_' . $position . '_2');
        $ad3 = get_option('komik_ads_' . $position . '_3');
        $ad4 = get_option('komik_ads_' . $position . '_4');
        
        $has_row1 = !empty($ad1) || !empty($ad2);
        $has_row2 = !empty($ad3) || !empty($ad4);
        
        if ($has_row1 || $has_row2) {
            echo '<div class="komik-ad komik-ad-' . esc_attr($position) . '">';
            echo '<style>
                .komik-ad-' . esc_attr($position) . ' { padding: 15px 0; }
                .komik-ad-' . esc_attr($position) . '-row { 
                    display: grid; 
                    grid-template-columns: 1fr 1fr; 
                    gap: 15px;
                    max-width: 1200px;
                    margin: 0 auto 15px;
                }
                .komik-ad-' . esc_attr($position) . '-row:last-child { margin-bottom: 0; }
                .komik-ad-' . esc_attr($position) . '-row .ad-col {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                .komik-ad-' . esc_attr($position) . '-row .ad-col iframe,
                .komik-ad-' . esc_attr($position) . '-row .ad-col img {
                    max-width: 100%;
                }
                @media (max-width: 768px) {
                    .komik-ad-' . esc_attr($position) . '-row {
                        grid-template-columns: 1fr;
                    }
                }
            </style>';
            
            // Row 1
            if ($has_row1) {
                echo '<div class="komik-ad-' . esc_attr($position) . '-row">';
                if (!empty($ad1)) {
                    echo '<div class="ad-col">' . $ad1 . '</div>';
                }
                if (!empty($ad2)) {
                    echo '<div class="ad-col">' . $ad2 . '</div>';
                }
                echo '</div>';
            }
            
            // Row 2
            if ($has_row2) {
                echo '<div class="komik-ad-' . esc_attr($position) . '-row">';
                if (!empty($ad3)) {
                    echo '<div class="ad-col">' . $ad3 . '</div>';
                }
                if (!empty($ad4)) {
                    echo '<div class="ad-col">' . $ad4 . '</div>';
                }
                echo '</div>';
            }
            
            echo '</div>';
        }
        return;
    }
    
    // Default handling for other positions
    $ad = get_option('komik_ads_' . $position);
    if (!empty($ad)) {
        echo '<div class="komik-ad komik-ad-' . esc_attr($position) . '">' . $ad . '</div>';
    }
}

/**
 * Get popular manhwa based on settings
 */
function komik_starter_get_popular() {
    $mode = get_option('komik_popular_mode', 'manual');
    $count = get_option('komik_popular_count', 10);
    
    if ($mode == 'manual') {
        $ids = get_option('komik_popular_manhwa', array());
        if (empty($ids)) {
            return array();
        }
        
        return get_posts(array(
            'post_type' => 'manhwa',
            'posts_per_page' => $count,
            'post__in' => $ids,
            'orderby' => 'post__in',
            'post_status' => 'publish',
        ));
    } elseif ($mode == 'views') {
        return get_posts(array(
            'post_type' => 'manhwa',
            'posts_per_page' => $count,
            'meta_key' => '_manhwa_views',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'post_status' => 'publish',
        ));
    } elseif ($mode == 'rating') {
        return get_posts(array(
            'post_type' => 'manhwa',
            'posts_per_page' => $count,
            'meta_key' => '_manhwa_rating',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'post_status' => 'publish',
        ));
    }
    
    return array();
}

/**
 * Display floating ads
 */
add_action('wp_footer', 'komik_starter_floating_ads');
function komik_starter_floating_ads() {
    // Float Left
    $float_left = get_option('komik_ads_float_left');
    if ($float_left) {
        echo '<div id="komik-float-left" style="position:fixed;top:100px;left:0;z-index:9999;">
            <div style="text-align:center;"><span onclick="document.getElementById(\'komik-float-left\').style.display=\'none\'" style="cursor:pointer;font-size:12px;">×</span></div>
            ' . $float_left . '
        </div>';
    }
    
    // Float Right
    $float_right = get_option('komik_ads_float_right');
    if ($float_right) {
        echo '<div id="komik-float-right" style="position:fixed;top:100px;right:0;z-index:9999;">
            <div style="text-align:center;"><span onclick="document.getElementById(\'komik-float-right\').style.display=\'none\'" style="cursor:pointer;font-size:12px;">×</span></div>
            ' . $float_right . '
        </div>';
    }
    
    // Float Bottom
    $float_bottom = get_option('komik_ads_float_bottom');
    if ($float_bottom) {
        echo '<div id="komik-float-bottom" style="position:fixed;bottom:0;left:0;right:0;z-index:9999;text-align:center;background;padding:15px 10px 10px;">
            <span onclick="document.getElementById(\'komik-float-bottom\').style.display=\'none\'" style="position:absolute;top:-12px;left:50%;transform:translateX(-50%);cursor:pointer;color:#fff;font-size:14px;background:#333;border:2px solid #555;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.3);transition:all 0.2s;" onmouseover="this.style.background=\'#e74c3c\';this.style.borderColor=\'#e74c3c\';" onmouseout="this.style.background=\'#333\';this.style.borderColor=\'#555\';">✕</span>
            ' . $float_bottom . '
        </div>';
    }
}

/**
 * Add header scripts
 */
add_action('wp_head', 'komik_starter_header_scripts', 99);
function komik_starter_header_scripts() {
    $header = get_option('komik_ads_header');
    if ($header) {
        echo $header;
    }
}

/**
 * =================================================================
 * COLOR SETTINGS PAGE
 * =================================================================
 */
function komik_starter_colors_page() {
    // Color presets
    $presets = array(
        'default' => array(
            'name' => 'Default Blue',
            'primary' => '#366ad3',
            'secondary' => '#1a1a2e',
            'accent' => '#667eea',
            'bg' => '#0f0f23',
            'card' => '#1a1a2e',
            'text' => '#ffffff',
        ),
        'purple' => array(
            'name' => 'Purple Dream',
            'primary' => '#9333ea',
            'secondary' => '#1e1b4b',
            'accent' => '#a855f7',
            'bg' => '#0c0a1d',
            'card' => '#1e1b4b',
            'text' => '#ffffff',
        ),
        'green' => array(
            'name' => 'Emerald',
            'primary' => '#10b981',
            'secondary' => '#064e3b',
            'accent' => '#34d399',
            'bg' => '#022c22',
            'card' => '#064e3b',
            'text' => '#ffffff',
        ),
        'red' => array(
            'name' => 'Crimson',
            'primary' => '#ef4444',
            'secondary' => '#450a0a',
            'accent' => '#f87171',
            'bg' => '#1c0a0a',
            'card' => '#450a0a',
            'text' => '#ffffff',
        ),
        'orange' => array(
            'name' => 'Sunset Orange',
            'primary' => '#f97316',
            'secondary' => '#431407',
            'accent' => '#fb923c',
            'bg' => '#1c0f05',
            'card' => '#431407',
            'text' => '#ffffff',
        ),
        'pink' => array(
            'name' => 'Rose Pink',
            'primary' => '#ec4899',
            'secondary' => '#500724',
            'accent' => '#f472b6',
            'bg' => '#1a0612',
            'card' => '#500724',
            'text' => '#ffffff',
        ),
        'custom' => array(
            'name' => 'Custom Colors',
            'primary' => '#366ad3',
            'secondary' => '#1a1a2e',
            'accent' => '#667eea',
            'bg' => '#0f0f23',
            'card' => '#1a1a2e',
            'text' => '#ffffff',
        ),
    );
    
    $current_preset = get_option('komik_color_preset', 'default');
    $custom_colors = array(
        'primary' => get_option('komik_color_primary', '#366ad3'),
        'secondary' => get_option('komik_color_secondary', '#1a1a2e'),
        'accent' => get_option('komik_color_accent', '#667eea'),
        'bg' => get_option('komik_color_bg', '#0f0f23'),
        'card' => get_option('komik_color_card', '#1a1a2e'),
        'text' => get_option('komik_color_text', '#ffffff'),
    );
    ?>
    
    <div class="wrap">
        <h1><span class="dashicons dashicons-art" style="font-size: 30px; margin-right: 10px;"></span> <?php _e('Color Settings', 'komik-starter'); ?></h1>
        
        <style>
            .komik-color-wrap { background: #fff; padding: 25px; border-radius: 8px; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
            .komik-color-wrap h2 { margin-top: 0; color: #1e1e1e; border-bottom: 2px solid #2271b1; padding-bottom: 10px; }
            .komik-presets { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; margin-bottom: 30px; }
            .komik-preset-card { padding: 15px; border: 3px solid #dcdcde; border-radius: 12px; cursor: pointer; transition: all 0.3s; text-align: center; }
            .komik-preset-card:hover { border-color: #2271b1; transform: translateY(-2px); }
            .komik-preset-card.active { border-color: #2271b1; background: #f0f6fc; box-shadow: 0 4px 12px rgba(34, 113, 177, 0.2); }
            .komik-preset-preview { display: flex; gap: 4px; margin-bottom: 10px; justify-content: center; }
            .komik-preset-preview span { width: 24px; height: 24px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.3); }
            .komik-preset-name { font-weight: 600; font-size: 14px; color: #1e1e1e; }
            .komik-custom-colors { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; padding: 20px; background: #f9f9f9; border-radius: 8px; margin-top: 20px; }
            .komik-color-field { }
            .komik-color-field label { display: block; font-weight: 600; margin-bottom: 8px; font-size: 13px; }
            .komik-color-field input[type="color"] { width: 60px; height: 40px; border: none; border-radius: 8px; cursor: pointer; padding: 0; }
            .komik-color-field input[type="text"] { width: 90px; margin-left: 10px; padding: 8px; border: 1px solid #dcdcde; border-radius: 6px; font-family: monospace; }
            .komik-color-row { display: flex; align-items: center; }
            .custom-colors-section { display: none; }
            .custom-colors-section.active { display: block; }
            .komik-preview-box { margin-top: 30px; padding: 20px; border-radius: 12px; transition: all 0.3s; }
            .komik-preview-box h3 { margin-top: 0; }
            .komik-preview-btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; margin-right: 10px; }
            .komik-preview-card { padding: 15px; border-radius: 8px; margin-top: 15px; }
        </style>
        
        <form method="post" action="options.php">
            <?php settings_fields('komik-color-settings'); ?>
            
            <div class="komik-color-wrap">
                <h2><?php _e('Color Theme Presets', 'komik-starter'); ?></h2>
                <p style="color: #646970; margin-bottom: 20px;">
                    <?php _e('Choose a preset color theme or create your own custom colors.', 'komik-starter'); ?>
                </p>
                
                <div class="komik-presets">
                    <?php foreach ($presets as $key => $preset) : ?>
                    <label class="komik-preset-card <?php echo $current_preset == $key ? 'active' : ''; ?>" data-preset="<?php echo $key; ?>">
                        <input type="radio" name="komik_color_preset" value="<?php echo $key; ?>" <?php checked($current_preset, $key); ?> style="display:none;">
                        <div class="komik-preset-preview">
                            <span style="background: <?php echo $preset['primary']; ?>;"></span>
                            <span style="background: <?php echo $preset['accent']; ?>;"></span>
                            <span style="background: <?php echo $preset['secondary']; ?>;"></span>
                            <span style="background: <?php echo $preset['bg']; ?>;"></span>
                        </div>
                        <div class="komik-preset-name"><?php echo esc_html($preset['name']); ?></div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="komik-color-wrap custom-colors-section <?php echo $current_preset == 'custom' ? 'active' : ''; ?>">
                <h2><?php _e('Custom Colors', 'komik-starter'); ?></h2>
                <p style="color: #646970;">
                    <?php _e('Fine-tune your theme colors. Changes will be applied after saving.', 'komik-starter'); ?>
                </p>
                
                <div class="komik-custom-colors">
                    <div class="komik-color-field">
                        <label><?php _e('Primary Color', 'komik-starter'); ?></label>
                        <div class="komik-color-row">
                            <input type="color" id="color_primary" name="komik_color_primary" value="<?php echo esc_attr($custom_colors['primary']); ?>">
                            <input type="text" id="color_primary_hex" value="<?php echo esc_attr($custom_colors['primary']); ?>">
                        </div>
                        <p class="description"><?php _e('Header, buttons, links', 'komik-starter'); ?></p>
                    </div>
                    
                    <div class="komik-color-field">
                        <label><?php _e('Accent Color', 'komik-starter'); ?></label>
                        <div class="komik-color-row">
                            <input type="color" id="color_accent" name="komik_color_accent" value="<?php echo esc_attr($custom_colors['accent']); ?>">
                            <input type="text" id="color_accent_hex" value="<?php echo esc_attr($custom_colors['accent']); ?>">
                        </div>
                        <p class="description"><?php _e('Highlights, hover states', 'komik-starter'); ?></p>
                    </div>
                    
                    <div class="komik-color-field">
                        <label><?php _e('Secondary Color', 'komik-starter'); ?></label>
                        <div class="komik-color-row">
                            <input type="color" id="color_secondary" name="komik_color_secondary" value="<?php echo esc_attr($custom_colors['secondary']); ?>">
                            <input type="text" id="color_secondary_hex" value="<?php echo esc_attr($custom_colors['secondary']); ?>">
                        </div>
                        <p class="description"><?php _e('Secondary elements', 'komik-starter'); ?></p>
                    </div>
                    
                    <div class="komik-color-field">
                        <label><?php _e('Background Color', 'komik-starter'); ?></label>
                        <div class="komik-color-row">
                            <input type="color" id="color_bg" name="komik_color_bg" value="<?php echo esc_attr($custom_colors['bg']); ?>">
                            <input type="text" id="color_bg_hex" value="<?php echo esc_attr($custom_colors['bg']); ?>">
                        </div>
                        <p class="description"><?php _e('Main background', 'komik-starter'); ?></p>
                    </div>
                    
                    <div class="komik-color-field">
                        <label><?php _e('Card Background', 'komik-starter'); ?></label>
                        <div class="komik-color-row">
                            <input type="color" id="color_card" name="komik_color_card" value="<?php echo esc_attr($custom_colors['card']); ?>">
                            <input type="text" id="color_card_hex" value="<?php echo esc_attr($custom_colors['card']); ?>">
                        </div>
                        <p class="description"><?php _e('Cards, sections', 'komik-starter'); ?></p>
                    </div>
                    
                    <div class="komik-color-field">
                        <label><?php _e('Text Color', 'komik-starter'); ?></label>
                        <div class="komik-color-row">
                            <input type="color" id="color_text" name="komik_color_text" value="<?php echo esc_attr($custom_colors['text']); ?>">
                            <input type="text" id="color_text_hex" value="<?php echo esc_attr($custom_colors['text']); ?>">
                        </div>
                        <p class="description"><?php _e('Main text color', 'komik-starter'); ?></p>
                    </div>
                </div>
                
                <!-- Live Preview -->
                <div class="komik-preview-box" id="live-preview" style="background: <?php echo $custom_colors['bg']; ?>; color: <?php echo $custom_colors['text']; ?>;">
                    <h3 style="color: <?php echo $custom_colors['text']; ?>;"><?php _e('Live Preview', 'komik-starter'); ?></h3>
                    <button type="button" class="komik-preview-btn" style="background: <?php echo $custom_colors['primary']; ?>; color: #fff;">Primary Button</button>
                    <button type="button" class="komik-preview-btn" style="background: <?php echo $custom_colors['accent']; ?>; color: #fff;">Accent Button</button>
                    <div class="komik-preview-card" style="background: <?php echo $custom_colors['card']; ?>;">
                        <p style="margin: 0;">This is how card elements will look like.</p>
                    </div>
                </div>
            </div>
            
            <?php submit_button(__('Save Color Settings', 'komik-starter')); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        var presets = <?php echo json_encode($presets); ?>;
        
        // Preset selection
        $('.komik-preset-card').on('click', function() {
            $('.komik-preset-card').removeClass('active');
            $(this).addClass('active');
            
            var preset = $(this).data('preset');
            
            if (preset === 'custom') {
                $('.custom-colors-section').addClass('active');
            } else {
                $('.custom-colors-section').removeClass('active');
                
                // Update custom color inputs with preset values
                if (presets[preset]) {
                    $('#color_primary').val(presets[preset].primary);
                    $('#color_primary_hex').val(presets[preset].primary);
                    $('#color_accent').val(presets[preset].accent);
                    $('#color_accent_hex').val(presets[preset].accent);
                    $('#color_secondary').val(presets[preset].secondary);
                    $('#color_secondary_hex').val(presets[preset].secondary);
                    $('#color_bg').val(presets[preset].bg);
                    $('#color_bg_hex').val(presets[preset].bg);
                    $('#color_card').val(presets[preset].card);
                    $('#color_card_hex').val(presets[preset].card);
                    $('#color_text').val(presets[preset].text);
                    $('#color_text_hex').val(presets[preset].text);
                    updatePreview();
                }
            }
        });
        
        // Sync color inputs
        $('input[type="color"]').on('input', function() {
            var id = $(this).attr('id');
            $('#' + id + '_hex').val($(this).val());
            $('input[name="' + $(this).attr('name') + '"]').val($(this).val());
            updatePreview();
        });
        
        $('input[id$="_hex"]').on('input', function() {
            var id = $(this).attr('id').replace('_hex', '');
            var val = $(this).val();
            if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                $('#' + id).val(val);
                $('input[name="komik_color_' + id.replace('color_', '') + '"]').val(val);
                updatePreview();
            }
        });
        
        function updatePreview() {
            $('#live-preview').css({
                'background': $('#color_bg').val(),
                'color': $('#color_text').val()
            });
            $('#live-preview h3').css('color', $('#color_text').val());
            $('#live-preview .komik-preview-btn:first').css('background', $('#color_primary').val());
            $('#live-preview .komik-preview-btn:last').css('background', $('#color_accent').val());
            $('#live-preview .komik-preview-card').css('background', $('#color_card').val());
        }
    });
    </script>
    
    <?php
}

/**
 * Output custom colors CSS
 */
add_action('wp_head', 'komik_starter_custom_colors_css', 100);
function komik_starter_custom_colors_css() {
    $preset = get_option('komik_color_preset', 'default');
    
    // Preset colors
    $presets = array(
        'default' => array('#366ad3', '#1a1a2e', '#667eea', '#0f0f23', '#1a1a2e', '#ffffff'),
        'purple' => array('#9333ea', '#1e1b4b', '#a855f7', '#0c0a1d', '#1e1b4b', '#ffffff'),
        'green' => array('#10b981', '#064e3b', '#34d399', '#022c22', '#064e3b', '#ffffff'),
        'red' => array('#ef4444', '#450a0a', '#f87171', '#1c0a0a', '#450a0a', '#ffffff'),
        'orange' => array('#f97316', '#431407', '#fb923c', '#1c0f05', '#431407', '#ffffff'),
        'pink' => array('#ec4899', '#500724', '#f472b6', '#1a0612', '#500724', '#ffffff'),
    );
    
    if ($preset == 'custom') {
        $primary = get_option('komik_color_primary', '#366ad3');
        $secondary = get_option('komik_color_secondary', '#1a1a2e');
        $accent = get_option('komik_color_accent', '#667eea');
        $bg = get_option('komik_color_bg', '#0f0f23');
        $card = get_option('komik_color_card', '#1a1a2e');
        $text = get_option('komik_color_text', '#ffffff');
    } elseif (isset($presets[$preset])) {
        list($primary, $secondary, $accent, $bg, $card, $text) = $presets[$preset];
    } else {
        return; // Use default CSS
    }
    
    // Output CSS variables
    ?>
    <style id="komik-custom-colors">
    :root {
        --komik-primary: <?php echo esc_attr($primary); ?>;
        --komik-accent: <?php echo esc_attr($accent); ?>;
        --komik-bg: <?php echo esc_attr($bg); ?>;
        --komik-card: <?php echo esc_attr($card); ?>;
        --komik-text: <?php echo esc_attr($text); ?>;
        
        /* Override theme CSS variables */
        --color-accent: <?php echo esc_attr($primary); ?>;
        --color-bg-primary: <?php echo esc_attr($bg); ?>;
        --color-bg-secondary: <?php echo esc_attr($secondary); ?>;
        --color-bg-card: <?php echo esc_attr($card); ?>;
    }
    
    /* ==========================================
       BASE & BODY
       ========================================== */
    body { 
        background-color: var(--komik-bg); 
        color: var(--komik-text); 
    }
    
    /* ==========================================
       HEADER & NAVIGATION
       ========================================== */
    .th, .topmobile { 
        background: var(--komik-primary) !important;
        background-image: none !important;
    }
    
    .th .centernav {
        background: transparent;
    }
    
    .logox, .logos, header[role="banner"] {
        background: transparent;
    }
    
    #main-menu {
        background: var(--komik-secondary) !important;
    }
    
    #main-menu ul li a {
        color: var(--komik-text);
    }
    
    #main-menu ul li a:hover,
    #main-menu ul li.current-menu-item > a {
        color: var(--komik-accent);
    }
    
    #main-menu ul li ul {
        background: var(--komik-secondary);
    }
    
    #main-menu ul li ul li a {
        color: var(--komik-text);
    }
    
    #main-menu ul li ul li a:hover {
        background: var(--komik-primary);
    }
    
    /* Search Box in Header */
    .searchx {
        background: transparent;
    }
    
    .searchx #form {
        background: var(--komik-card);
        border-color: rgba(255,255,255,0.2);
    }
    
    .searchx #form #s {
        background: transparent;
        color: var(--komik-text);
    }
    
    .searchx #form #s::placeholder {
        color: rgba(255,255,255,0.6);
    }
    
    .searchx #form #submit {
        color: var(--komik-accent);
    }
    
    .searchx #form:focus-within {
        border-color: var(--komik-accent);
    }
    
    /* Mobile Menu */
    .shme {
        color: var(--komik-text);
    }
    
    .srcmob {
        color: var(--komik-text);
    }
    
    /* ==========================================
       SEARCH
       ========================================== */
    .searchx #form #s {
        background: var(--komik-card);
        border-color: rgba(255,255,255,0.1);
    }
    
    .searchx #form #s:focus {
        border-color: var(--komik-accent);
    }
    
    .searchx #form #submit {
        color: var(--komik-accent);
    }
    
    /* ==========================================
       CONTENT & CARDS
       ========================================== */
    
    /* Ad sections should have transparent background */
    .bixbox.ad-section,
    .ad-section {
        background: transparent !important;
        box-shadow: none !important;
    }
    
    .releases h2::before,
    .releases h3::before {
        background: var(--komik-accent);
    }
    
    .releases .vl,
    .vl-btn {
        background: var(--komik-primary);
    }
    
    /* ==========================================
       LINKS & BUTTONS
       ========================================== */
    a { 
        color: var(--komik-text); 
    }
    
    a:hover { 
        color: var(--komik-accent); 
    }
    
    .btn, button, input[type="submit"] {
        background: var(--komik-primary);
        color: #fff;
    }
    
    .btn:hover, button:hover, input[type="submit"]:hover {
        background: var(--komik-accent);
    }
    
    /* ==========================================
       MANHWA CARDS & GRID
       ========================================== */
    .bs .bsx, .utao .uta {
        background: var(--komik-card);
    }
    
    .bs .bsx:hover, .utao .uta:hover {
        background: var(--komik-secondary);
    }
    
    .hot-badge, .typename {
        background: var(--komik-accent);
    }
    
    .color-badge {
        background: var(--komik-primary);
    }
    
    .tt, .bs .tt, .luf h4 a {
        color: var(--komik-text);
    }
    
    .bs .tt:hover, .luf h4 a:hover {
        color: var(--komik-accent);
    }
    
    /* Rating stars 
    .rating-info .numscore i.fas.fa-star,
    .rating-info .numscore i.fas.fa-star-half-alt {
        color: var(--komik-accent);
    }*/
    
    /* ==========================================
       SERIES DETAIL PAGE
       ========================================== */
    .thumbook .rt .bookmark {
        background: var(--komik-primary);
    }
    
    .thumbook .rt .bookmark:hover {
        background: var(--komik-accent);
    }
    
    .infox .spe span a, .genre-list a {
        color: var(--komik-accent);
    }
    
    .mgen a, .seriestugenre a {
        background: var(--komik-secondary);
        color: var(--komik-text);
    }
    
    .mgen a:hover, .seriestugenre a:hover {
        background: var(--komik-primary);
    }
    
    /* Chapter buttons */
    .chbtn {
        background: var(--komik-primary);
    }
    
    .chbtn:hover {
        background: var(--komik-accent);
    }
    
    .chbtn.first {
        background: var(--komik-secondary);
    }
    
    /* Chapter list */
    .chapter-grid {
        border-color: rgba(255,255,255,0.1);
    }
    
    .chapter-item {
        border-color: rgba(255,255,255,0.08);
    }
    
    .chapter-item:hover {
        background: var(--komik-secondary);
    }
    
    .chapter-item:hover .ch-num {
        color: var(--komik-accent);
    }
    
    .chapter-section .headchapter h2 {
        border-bottom-color: var(--komik-accent);
    }
    
    .search-chapter input:focus {
        border-color: var(--komik-accent);
    }
    
    /* Social share */
    .social-share .share-btn {
        background: var(--komik-secondary);
    }
    
    .social-share .share-btn:hover {
        background: var(--komik-primary);
    }
    
    /* ==========================================
       CHAPTER READER
       ========================================== */
    .chapterbody {
        background: var(--komik-bg);
    }
    
    .headpost h1, .headpost .allc a {
        color: var(--komik-text);
    }
    
    .headpost .allc a:hover {
        color: var(--komik-accent);
    }
    
    .nextprev a, .ch-prev-btn, .ch-next-btn, .ch-home-btn {
        background: var(--komik-secondary);
        color: var(--komik-text);
    }
    
    .nextprev a:hover, .ch-prev-btn:hover, .ch-next-btn:hover, .ch-home-btn:hover {
        background: var(--komik-primary);
    }
    
    .readingnav, .readingnavbot {
        background: var(--komik-card);
    }
    
    .readingprogress {
        background: var(--komik-primary);
    }
    
    /* ==========================================
       SIDEBAR
       ========================================== */
    #sidebar .section {
        background: var(--komik-card);
    }
    
    #sidebar .section h4 {
        color: var(--komik-text);
    }
    
    #sidebar .serieslist ul li:hover {
        background: var(--komik-secondary);
    }
    
    #sidebar .serieslist ul li .leftseries h2 a:hover {
        color: var(--komik-accent);
    }
    
    /* ==========================================
       FOOTER
       ========================================== */
    #footer {
        background: var(--komik-card) !important;
    }
    
    .footer-widgets {
        border-bottom-color: rgba(255,255,255,0.1);
    }
    
    .footer-title {
        color: var(--komik-text);
        border-bottom-color: var(--komik-accent);
    }
    
    .footer-site-name {
        color: var(--komik-accent);
    }
    
    .footer-menu li a:hover,
    .footer-series-list li a:hover .footer-series-title {
        color: var(--komik-accent);
    }
    
    .footer-menu li a i {
        color: var(--komik-accent);
    }
    
    .footer-social .social-icon {
        background: rgba(255,255,255,0.1);
    }
    
    .footer-copyright strong {
        color: var(--komik-accent);
    }
    
    .footer-bottom-menu ul li a:hover {
        color: var(--komik-accent);
    }
    
    .footer-back-top .back-to-top,
    .scrollToTop {
        background: var(--komik-accent);
    }
    
    .scrollToTop:hover {
        background: #fff;
        color: var(--komik-accent);
    }
    
    /* ==========================================
       PAGINATION
       ========================================== */
    .pagination a, .pagination span {
        background: var(--komik-secondary);
        color: var(--komik-text);
    }
    
    .pagination .page-numbers.current,
    .pagination a:hover {
        background: var(--komik-primary);
        color: #fff;
    }
    
    .hpage a {
        background: var(--komik-primary);
    }
    
    .hpage a:hover {
        background: var(--komik-accent);
    }
    
    /* ==========================================
       BREADCRUMB
       ========================================== */
    .ts-breadcrumb a:hover {
        color: var(--komik-accent);
    }
    
    /* ==========================================
       COMMENTS
       ========================================== */
    .commentx, .comment-body {
        background: var(--komik-card);
    }
    
    .commentx #submit {
        background: var(--komik-primary);
    }
    
    .commentx #submit:hover {
        background: var(--komik-accent);
    }
    
    .comment-list .comment-body .reply a:hover {
        color: var(--komik-accent);
    }
    
    /* ==========================================
       ARCHIVE & SEARCH PAGES
       ========================================== */
    .archive-title, .search-title {
        color: var(--komik-text);
    }
    
    .filter-options select,
    .filter-options input {
        background: var(--komik-card);
        border-color: rgba(255,255,255,0.1);
        color: var(--komik-text);
    }
    
    .filter-options select:focus,
    .filter-options input:focus {
        border-color: var(--komik-accent);
    }
    
    /* ==========================================
       POPULAR TODAY SECTION
       ========================================== */
    .popular-today {
        background: var(--komik-card);
    }
    
    .popular-item .bigor .tt a:hover {
        color: var(--komik-accent);
    }
    
    /* ==========================================
       404 & ERROR PAGES
       ========================================== */
    .error-404 h1 {
        color: var(--komik-accent);
    }
    
    .error-404 .search-submit {
        background: var(--komik-primary);
    }
    
    /* ==========================================
       SCROLLBAR
       ========================================== */
    ::-webkit-scrollbar-thumb {
        background: var(--komik-primary);
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: var(--komik-accent);
    }
    
    /* ==========================================
       SELECTION
       ========================================== */
    ::selection {
        background: var(--komik-accent);
        color: #fff;
    }
    
    ::-moz-selection {
        background: var(--komik-accent);
        color: #fff;
    }
    
    /* ==========================================
       LOGIN & REGISTER PAGES
       ========================================== */
    .account-page {
        background: var(--komik-bg);
    }
    
    .account-card {
        background: var(--komik-card);
    }
    
    .account-header h1, .account-header h2 {
        color: var(--komik-text);
    }
    
    .account-header p {
        color: rgba(255,255,255,0.7);
    }
    
    .account-form .form-group input[type="text"],
    .account-form .form-group input[type="email"],
    .account-form .form-group input[type="password"] {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.1);
        color: var(--komik-text);
    }
    
    .account-form .form-group input:focus {
        border-color: var(--komik-accent);
    }
    
    .btn-submit {
        background: linear-gradient(135deg, var(--komik-primary) 0%, var(--komik-accent) 100%) !important;
    }
    
    .btn-submit:hover {
        background: linear-gradient(135deg, var(--komik-accent) 0%, var(--komik-primary) 100%) !important;
    }
    
    .account-form a {
        color: var(--komik-accent);
    }
    
    .account-form a:hover {
        color: var(--komik-primary);
    }
    
    /* ==========================================
       PROFILE PAGE
       ========================================== */
    .profile-page {
        background: var(--komik-bg);
    }
    
    .profile-card, .profile-section {
        background: var(--komik-card);
    }
    
    .profile-name {
        color: var(--komik-text);
    }
    
    .profile-tabs .tab-btn {
        background: var(--komik-secondary);
        color: var(--komik-text);
    }
    
    .profile-tabs .tab-btn.active,
    .profile-tabs .tab-btn:hover {
        background: var(--komik-primary);
        color: #fff;
    }
    
    /* ==========================================
       BOOKMARKS & HISTORY PAGES
       ========================================== */
    .bookmark-page, .history-page {
        background: var(--komik-bg);
    }
    
    .bookmark-item, .history-item {
        background: var(--komik-card);
    }
    
    .bookmark-item:hover, .history-item:hover {
        background: var(--komik-secondary);
    }
    
    .bookmark-title a, .history-title a {
        color: var(--komik-text);
    }
    
    .bookmark-title a:hover, .history-title a:hover {
        color: var(--komik-accent);
    }
    
    .bookmark-remove, .history-remove {
        color: rgba(255,255,255,0.5);
    }
    
    .bookmark-remove:hover, .history-remove:hover {
        color: #ef4444;
    }
    
    /* ==========================================
       BIXBOX & SECTION BOXES
       ========================================== */
    .bixbox {
        background: var(--komik-card);
    }
    
    .bixbox .releases h2, .bixbox h2 {
        color: var(--komik-text);
    }
    
    .bixbox .releases h2::before {
        background: var(--komik-accent);
    }
    
    /* ==========================================
       AZ FILTER / ALPHABET LIST
       ========================================== */
    .az-list {
        background: var(--komik-card);
    }
    
    .az-list li a {
        background: var(--komik-secondary);
        color: var(--komik-text);
    }
    
    .az-list li a:hover,
    .az-list li a.active {
        background: var(--komik-primary);
        color: #fff;
    }
    
    /* ==========================================
       GENRE PAGE
       ========================================== */
    .genre-page .genre-header {
        background: var(--komik-card);
    }
    
    .genre-page .genre-title {
        color: var(--komik-text);
    }
    
    .genre-filters select {
        background: var(--komik-secondary);
        border-color: rgba(255,255,255,0.1);
        color: var(--komik-text);
    }
    
    /* ==========================================
       PROJECT PAGE
       ========================================== */
    .project-info {
        background: var(--komik-card);
    }
    
    .project-title {
        color: var(--komik-text);
    }
    
    /* ==========================================
       FORMS & INPUTS (GLOBAL)
       ========================================== */
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="search"],
    input[type="url"],
    input[type="number"],
    textarea,
    select {
        background: var(--komik-card);
        border-color: rgba(255,255,255,0.1);
        color: var(--komik-text);
    }
    
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    input[type="search"]:focus,
    input[type="url"]:focus,
    input[type="number"]:focus,
    textarea:focus,
    select:focus {
        border-color: var(--komik-accent);
        outline-color: var(--komik-accent);
    }
    
    /* ==========================================
       MODALS & POPUPS
       ========================================== */
    .modal-content, .popup-content {
        background: var(--komik-card);
    }
    
    .modal-header, .popup-header {
        border-bottom-color: rgba(255,255,255,0.1);
    }
    
    .modal-close, .popup-close {
        color: var(--komik-text);
    }
    
    .modal-close:hover, .popup-close:hover {
        color: var(--komik-accent);
    }
    
    /* ==========================================
       TABS (GLOBAL)
       ========================================== */
    .tabs .tab-item {
        background: var(--komik-secondary);
        color: var(--komik-text);
    }
    
    .tabs .tab-item.active,
    .tabs .tab-item:hover {
        background: var(--komik-primary);
        color: #fff;
    }
    
    /* ==========================================
       BADGES & LABELS
       ========================================== */
    .badge, .label, .tag {
        background: var(--komik-primary);
        color: #fff;
    }
    
    .badge.new, .sts.Ongoing {
        background: var(--komik-accent);
    }
    
    /* ==========================================
       LOADING & SPINNERS
       ========================================== */
    .loading-spinner {
        border-color: rgba(255,255,255,0.1);
        border-top-color: var(--komik-accent);
    }
    
    /* ==========================================
       ANNOUNCEMENT BAR
       ========================================== */
    .announcement-bar {
        background: linear-gradient(135deg, var(--komik-primary) 0%, var(--komik-accent) 100%);
    }
    
    /* ==========================================
       CONTACT PAGE
       ========================================== */
    .contact-page {
        background: var(--komik-bg);
    }
    
    .contact-card {
        background: var(--komik-card);
    }
    
    .contact-card h2 {
        color: var(--komik-text);
    }
    
    .social-links a {
        background: var(--komik-secondary);
        color: var(--komik-text);
    }
    
    .social-links a:hover {
        background: var(--komik-primary);
        color: #fff;
    }
    
    /* ==========================================
       MEDIA PICKER (Comments)
       ========================================== */
    .media-picker-inline {
        background: var(--komik-card);
        border-color: rgba(255,255,255,0.1);
    }
    
    .media-picker-tabs {
        background: var(--komik-secondary);
    }
    
    .sticker-cat {
        background: var(--komik-secondary);
    }
    
    .sticker-cat.active {
        border-color: var(--komik-accent);
    }
    
    .sticker-item {
        background: var(--komik-secondary);
        color: var(--komik-text);
    }
    
    .sticker-item:hover {
        background: var(--komik-primary);
    }
    
    /* ==========================================
       STATUS COLORS
       ========================================== */
    .status-ongoing { color: var(--komik-accent); }
    .status-completed { color: #10b981; }
    .status-hiatus { color: #f59e0b; }
    .status-dropped { color: #ef4444; }
    
    </style>
    <?php
}

/**
 * =================================================================
 * CONTACT SETTINGS PAGE
 * =================================================================
 */
function komik_starter_contact_page() {
    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-email-alt" style="font-size: 30px; margin-right: 10px;"></span> <?php _e('Contact Settings', 'komik-starter'); ?></h1>
        
        <style>
            .komik-contact-wrap { background: #fff; padding: 25px; border-radius: 8px; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 800px; }
            .komik-contact-wrap h2 { margin-top: 0; color: #1e1e1e; border-bottom: 2px solid #2271b1; padding-bottom: 10px; }
            .komik-contact-field { margin-bottom: 20px; }
            .komik-contact-field label { display: block; font-weight: 600; margin-bottom: 8px; color: #1e1e1e; }
            .komik-contact-field label .dashicons { color: #2271b1; margin-right: 5px; }
            .komik-contact-field input { width: 100%; max-width: 400px; padding: 10px 12px; border: 1px solid #dcdcde; border-radius: 6px; font-size: 14px; }
            .komik-contact-field input:focus { border-color: #2271b1; outline: none; box-shadow: 0 0 0 2px rgba(34,113,177,0.2); }
            .komik-contact-field .description { color: #646970; font-size: 13px; margin-top: 5px; }
            .komik-section-title { background: #f0f6fc; padding: 10px 15px; border-left: 4px solid #2271b1; margin: 25px 0 15px; font-weight: 600; }
        </style>
        
        <form method="post" action="options.php">
            <?php settings_fields('komik-contact-settings'); ?>
            
            <div class="komik-contact-wrap">
                <h2><?php _e('Contact Information', 'komik-starter'); ?></h2>
                
                <div class="komik-contact-field">
                    <label><span class="dashicons dashicons-email"></span> <?php _e('Contact Email', 'komik-starter'); ?></label>
                    <input type="email" name="komik_contact_email" value="<?php echo esc_attr(get_option('komik_contact_email', get_option('admin_email'))); ?>" placeholder="email@example.com">
                    <p class="description"><?php _e('Email yang ditampilkan di halaman Contact', 'komik-starter'); ?></p>
                </div>
                
                <div class="komik-section-title"><?php _e('Social Media Links', 'komik-starter'); ?></div>
                
                <div class="komik-contact-field">
                    <label><span class="dashicons dashicons-facebook"></span> <?php _e('Facebook URL', 'komik-starter'); ?></label>
                    <input type="url" name="komik_contact_facebook" value="<?php echo esc_attr(get_option('komik_contact_facebook')); ?>" placeholder="https://facebook.com/yourpage">
                </div>
                
                <div class="komik-contact-field">
                    <label><span class="dashicons dashicons-twitter"></span> <?php _e('Twitter URL', 'komik-starter'); ?></label>
                    <input type="url" name="komik_contact_twitter" value="<?php echo esc_attr(get_option('komik_contact_twitter')); ?>" placeholder="https://twitter.com/yourhandle">
                </div>
                
                <div class="komik-contact-field">
                    <label><span class="dashicons dashicons-instagram"></span> <?php _e('Instagram URL', 'komik-starter'); ?></label>
                    <input type="url" name="komik_contact_instagram" value="<?php echo esc_attr(get_option('komik_contact_instagram')); ?>" placeholder="https://instagram.com/yourprofile">
                </div>
                
                <div class="komik-contact-field">
                    <label><span class="dashicons dashicons-format-chat"></span> <?php _e('Discord URL', 'komik-starter'); ?></label>
                    <input type="url" name="komik_contact_discord" value="<?php echo esc_attr(get_option('komik_contact_discord')); ?>" placeholder="https://discord.gg/yourserver">
                </div>
                
                <div class="komik-contact-field">
                    <label><span class="dashicons dashicons-share"></span> <?php _e('Telegram URL', 'komik-starter'); ?></label>
                    <input type="url" name="komik_contact_telegram" value="<?php echo esc_attr(get_option('komik_contact_telegram')); ?>" placeholder="https://t.me/yourchannel">
                </div>
            </div>
            
            <?php submit_button(__('Save Contact Settings', 'komik-starter')); ?>
        </form>
    </div>
    <?php
}

/**
 * =================================================================
 * ANNOUNCEMENT BAR SETTINGS PAGE
 * =================================================================
 */
function komik_starter_announcement_page() {
    $is_enabled = get_option('komik_announcement_enable', '0');
    $title = get_option('komik_announcement_title', '');
    $content = get_option('komik_announcement_content', '');
    $type = get_option('komik_announcement_type', 'fire');
    $dismissable = get_option('komik_announcement_dismissable', '1');
    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-megaphone" style="font-size: 30px; margin-right: 10px;"></span> <?php _e('Announcement Box', 'komik-starter'); ?></h1>
        
        <style>
            .komik-announce-wrap { background: #fff; padding: 25px; border-radius: 8px; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 900px; }
            .komik-announce-wrap h2 { margin-top: 0; color: #1e1e1e; border-bottom: 2px solid #2271b1; padding-bottom: 10px; }
            .komik-announce-field { margin-bottom: 20px; }
            .komik-announce-field label { display: block; font-weight: 600; margin-bottom: 8px; color: #1e1e1e; }
            .komik-announce-field label .dashicons { color: #2271b1; margin-right: 5px; }
            .komik-announce-field input[type="text"] { width: 100%; padding: 12px 14px; border: 1px solid #dcdcde; border-radius: 6px; font-size: 16px; }
            .komik-announce-field textarea { width: 100%; padding: 12px 14px; border: 1px solid #dcdcde; border-radius: 6px; font-size: 14px; font-family: monospace; }
            .komik-announce-field input:focus, .komik-announce-field textarea:focus { border-color: #2271b1; outline: none; box-shadow: 0 0 0 2px rgba(34,113,177,0.2); }
            .komik-announce-field .description { color: #646970; font-size: 13px; margin-top: 5px; }
            .komik-toggle { display: flex; align-items: center; gap: 10px; }
            .komik-toggle input[type="checkbox"] { width: 20px; height: 20px; }
            .type-options { display: flex; gap: 10px; flex-wrap: wrap; }
            .type-option { display: flex; align-items: center; gap: 8px; padding: 10px 15px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; }
            .type-option:hover { border-color: #2271b1; }
            .type-option.active { border-color: #2271b1; background: #f0f6fc; }
            .type-option input { display: none; }
            .type-dot { width: 20px; height: 20px; border-radius: 4px; }
            .type-dot.fire { background: #dc3545; }
            .type-dot.blue { background: #2196F3; }
            .type-dot.green { background: #4CAF50; }
            .type-dot.purple { background: #9C27B0; }
            .komik-preview { margin-top: 30px; }
            .komik-preview h3 { margin: 0 0 15px; font-size: 14px; color: #666; }
            .preview-announce-box { background: #1a1a2e; border-radius: 10px; padding: 25px 30px; border-top: 3px solid #dc3545; }
            .preview-announce-box.blue { border-top-color: #2196F3; }
            .preview-announce-box.green { border-top-color: #4CAF50; }
            .preview-announce-box.purple { border-top-color: #9C27B0; }
            .preview-announce-title { text-align: center; font-size: 1.3rem; color: #fff; margin-bottom: 20px; }
            .preview-announce-content { color: #ccc; font-size: 14px; line-height: 1.8; }
        </style>
        
        <form method="post" action="options.php">
            <?php settings_fields('komik-announcement-settings'); ?>
            
            <div class="komik-announce-wrap">
                <h2><?php _e('Announcement Box Settings', 'komik-starter'); ?></h2>
                
                <div class="komik-announce-field">
                    <label class="komik-toggle">
                        <input type="checkbox" name="komik_announcement_enable" value="1" <?php checked($is_enabled, '1'); ?>>
                        <span><span class="dashicons dashicons-visibility"></span> <?php _e('Enable Announcement Box', 'komik-starter'); ?></span>
                    </label>
                </div>
                
                <div class="komik-announce-field">
                    <label><span class="dashicons dashicons-heading"></span> <?php _e('Title (Judul)', 'komik-starter'); ?></label>
                    <input type="text" name="komik_announcement_title" value="<?php echo esc_attr($title); ?>" placeholder="🔥 Judul Announcement 🔥">
                    <p class="description"><?php _e('Judul di bagian atas. Bisa pakai emoji.', 'komik-starter'); ?></p>
                </div>
                
                <div class="komik-announce-field">
                    <label><span class="dashicons dashicons-editor-paragraph"></span> <?php _e('Content (Isi)', 'komik-starter'); ?></label>
                    <textarea name="komik_announcement_content" rows="12" placeholder="Isi announcement..."><?php echo esc_textarea($content); ?></textarea>
                    <p class="description"><?php _e('Support HTML: &lt;strong&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;a href=""&gt;, &lt;br&gt;', 'komik-starter'); ?></p>
                </div>
                
                <div class="komik-announce-field">
                    <label><span class="dashicons dashicons-art"></span> <?php _e('Border Color', 'komik-starter'); ?></label>
                    <div class="type-options">
                        <label class="type-option <?php echo $type === 'fire' ? 'active' : ''; ?>">
                            <input type="radio" name="komik_announcement_type" value="fire" <?php checked($type, 'fire'); ?>>
                            <span class="type-dot fire"></span>
                            <span>🔥 Red</span>
                        </label>
                        <label class="type-option <?php echo $type === 'blue' ? 'active' : ''; ?>">
                            <input type="radio" name="komik_announcement_type" value="blue" <?php checked($type, 'blue'); ?>>
                            <span class="type-dot blue"></span>
                            <span>💙 Blue</span>
                        </label>
                        <label class="type-option <?php echo $type === 'green' ? 'active' : ''; ?>">
                            <input type="radio" name="komik_announcement_type" value="green" <?php checked($type, 'green'); ?>>
                            <span class="type-dot green"></span>
                            <span>💚 Green</span>
                        </label>
                        <label class="type-option <?php echo $type === 'purple' ? 'active' : ''; ?>">
                            <input type="radio" name="komik_announcement_type" value="purple" <?php checked($type, 'purple'); ?>>
                            <span class="type-dot purple"></span>
                            <span>💜 Purple</span>
                        </label>
                    </div>
                </div>
                
                <div class="komik-announce-field">
                    <label class="komik-toggle">
                        <input type="checkbox" name="komik_announcement_dismissable" value="1" <?php checked($dismissable, '1'); ?>>
                        <span><span class="dashicons dashicons-dismiss"></span> <?php _e('Allow Dismiss', 'komik-starter'); ?></span>
                    </label>
                </div>
                
                <div class="komik-preview">
                    <h3><?php _e('Preview:', 'komik-starter'); ?></h3>
                    <div class="preview-announce-box <?php echo esc_attr($type); ?>" id="preview-box">
                        <div class="preview-announce-title" id="preview-title"><?php echo !empty($title) ? esc_html($title) : '🔥 Judul Announcement 🔥'; ?></div>
                        <div class="preview-announce-content" id="preview-content"><?php echo !empty($content) ? nl2br(esc_html($content)) : 'Isi announcement...'; ?></div>
                    </div>
                </div>
            </div>
            
            <?php submit_button(__('Save Announcement Settings', 'komik-starter')); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.type-option').on('click', function() {
            $('.type-option').removeClass('active');
            $(this).addClass('active');
            var type = $(this).find('input').val();
            $('#preview-box').removeClass('fire blue green purple').addClass(type);
        });
        $('input[name="komik_announcement_title"]').on('input', function() {
            $('#preview-title').text($(this).val() || '🔥 Judul Announcement 🔥');
        });
        $('textarea[name="komik_announcement_content"]').on('input', function() {
            $('#preview-content').html(($(this).val() || 'Isi announcement...').replace(/\n/g, '<br>'));
        });
    });
    </script>
    <?php
}

/**
 * =================================================================
 * FOOTER SETTINGS PAGE
 * =================================================================
 */
function komik_starter_footer_page() {
    // Handle form submission
    if (isset($_POST['komik_save_footer']) && wp_verify_nonce($_POST['komik_footer_nonce'], 'komik_save_footer')) {
        update_option('komik_footer_disclaimer', wp_kses_post($_POST['footer_disclaimer']));
        update_option('komik_footer_copyright', sanitize_text_field($_POST['footer_copyright']));
        update_option('komik_footer_show_az', isset($_POST['footer_show_az']) ? 1 : 0);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Footer settings saved!', 'komik-starter') . '</p></div>';
    }
    
    // Get current settings
    $disclaimer = get_option('komik_footer_disclaimer', __('All the comics on this website are only previews of the original comics, there may be many language errors, character names, and story lines. For the original version, please buy the comic if it\'s available in your city.', 'komik-starter'));
    $copyright = get_option('komik_footer_copyright', '');
    $show_az = get_option('komik_footer_show_az', 1);
    ?>
    
    <div class="wrap">
        <h1><span class="dashicons dashicons-admin-generic" style="font-size: 30px; margin-right: 10px;"></span> <?php _e('Footer Settings', 'komik-starter'); ?></h1>
        
        <style>
            .footer-options-wrap { background: #fff; padding: 25px; border-radius: 8px; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 800px; }
            .footer-options-wrap h2 { margin-top: 0; color: #1e1e1e; border-bottom: 2px solid #2271b1; padding-bottom: 10px; }
            .footer-field { margin-bottom: 20px; }
            .footer-field label { display: block; font-weight: 600; margin-bottom: 8px; color: #1e1e1e; }
            .footer-field input[type="text"], .footer-field textarea { width: 100%; padding: 10px 12px; border: 1px solid #dcdcde; border-radius: 6px; font-size: 14px; }
            .footer-field textarea { min-height: 100px; resize: vertical; }
            .footer-field .description { color: #646970; font-size: 12px; margin-top: 5px; }
            .footer-checkbox { display: flex; align-items: center; gap: 10px; }
            .footer-checkbox input { width: 18px; height: 18px; }
        </style>
        
        <form method="post" action="">
            <?php wp_nonce_field('komik_save_footer', 'komik_footer_nonce'); ?>
            
            <div class="footer-options-wrap">
                <h2><?php _e('Footer Content', 'komik-starter'); ?></h2>
                
                <div class="footer-field">
                    <label class="footer-checkbox">
                        <input type="checkbox" name="footer_show_az" value="1" <?php checked($show_az, 1); ?>>
                        <?php _e('Show A-Z List Navigation', 'komik-starter'); ?>
                    </label>
                    <p class="description"><?php _e('Enable/disable the A-Z alphabetical navigation in footer.', 'komik-starter'); ?></p>
                </div>
                
                <div class="footer-field">
                    <label for="footer_disclaimer"><?php _e('Disclaimer Text', 'komik-starter'); ?></label>
                    <textarea name="footer_disclaimer" id="footer_disclaimer" placeholder="<?php _e('Enter disclaimer text...', 'komik-starter'); ?>"><?php echo esc_textarea($disclaimer); ?></textarea>
                    <p class="description"><?php _e('This text appears at the bottom of your website.', 'komik-starter'); ?></p>
                </div>
                
                <div class="footer-field">
                    <label for="footer_copyright"><?php _e('Custom Copyright Text (Optional)', 'komik-starter'); ?></label>
                    <input type="text" name="footer_copyright" id="footer_copyright" value="<?php echo esc_attr($copyright); ?>" placeholder="<?php _e('Leave empty for default', 'komik-starter'); ?>">
                    <p class="description"><?php _e('Default: © 2024 Site Name. All rights reserved.', 'komik-starter'); ?></p>
                </div>
            </div>
            
            <p style="margin-top: 20px;">
                <button type="submit" name="komik_save_footer" class="button button-primary button-hero">
                    <span class="dashicons dashicons-saved" style="margin-top: 5px;"></span> <?php _e('Save Footer Settings', 'komik-starter'); ?>
                </button>
            </p>
        </form>
    </div>
    <?php
}

/**
 * =================================================================
 * CONTENT PROTECTION PAGE
 * =================================================================
 */
function komik_starter_protection_page() {
    // Handle form submission
    if (isset($_POST['komik_save_protection']) && wp_verify_nonce($_POST['komik_protection_nonce'], 'komik_save_protection')) {
        update_option('komik_protection_enable', isset($_POST['protection_enable']) ? 1 : 0);
        update_option('komik_protection_right_click', isset($_POST['protection_right_click']) ? 1 : 0);
        update_option('komik_protection_text_select', isset($_POST['protection_text_select']) ? 1 : 0);
        update_option('komik_protection_keyboard', isset($_POST['protection_keyboard']) ? 1 : 0);
        update_option('komik_protection_drag', isset($_POST['protection_drag']) ? 1 : 0);
        update_option('komik_protection_message', sanitize_text_field($_POST['protection_message']));
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Content Protection settings saved!', 'komik-starter') . '</p></div>';
    }
    
    // Get current settings
    $enable = get_option('komik_protection_enable', 0);
    $right_click = get_option('komik_protection_right_click', 1);
    $text_select = get_option('komik_protection_text_select', 1);
    $keyboard = get_option('komik_protection_keyboard', 1);
    $drag = get_option('komik_protection_drag', 1);
    $message = get_option('komik_protection_message', __('Content is protected!', 'komik-starter'));
    ?>
    
    <div class="wrap">
        <h1><span class="dashicons dashicons-shield" style="font-size: 30px; margin-right: 10px;"></span> <?php _e('Content Protection', 'komik-starter'); ?></h1>
        
        <style>
            .protection-wrap { background: #fff; padding: 25px; border-radius: 8px; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 800px; }
            .protection-wrap h2 { margin-top: 0; color: #1e1e1e; border-bottom: 2px solid #2271b1; padding-bottom: 10px; }
            .protection-toggle { display: flex; align-items: center; gap: 15px; padding: 15px 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 20px; }
            .protection-toggle.main { background: linear-gradient(135deg, #1e3a5f 0%, #2271b1 100%); color: #fff; }
            .protection-toggle h3 { margin: 0; flex: 1; }
            .toggle-switch { position: relative; width: 60px; height: 32px; }
            .toggle-switch input { opacity: 0; width: 0; height: 0; }
            .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #ccc; border-radius: 32px; transition: 0.3s; }
            .toggle-slider:before { position: absolute; content: ""; height: 24px; width: 24px; left: 4px; bottom: 4px; background: #fff; border-radius: 50%; transition: 0.3s; }
            .toggle-switch input:checked + .toggle-slider { background: #10b981; }
            .toggle-switch input:checked + .toggle-slider:before { transform: translateX(28px); }
            .protection-options { padding: 20px; background: #f0f6fc; border-radius: 8px; border: 1px solid #2271b1; }
            .protection-option { display: flex; align-items: center; gap: 15px; padding: 12px 0; border-bottom: 1px solid #dcdcde; }
            .protection-option:last-child { border-bottom: none; }
            .protection-option label { flex: 1; cursor: pointer; }
            .protection-option .icon { width: 40px; height: 40px; background: #2271b1; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
            .protection-option p { margin: 3px 0 0; font-size: 12px; color: #646970; }
            .message-field { margin-top: 20px; }
            .message-field label { display: block; margin-bottom: 8px; font-weight: 600; }
            .message-field input { width: 100%; padding: 12px; border: 1px solid #dcdcde; border-radius: 6px; font-size: 14px; }
            .protection-status { padding: 15px 20px; border-radius: 8px; margin-top: 20px; font-weight: 600; }
            .protection-status.enabled { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
            .protection-status.disabled { background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }
        </style>
        
        <form method="post">
            <?php wp_nonce_field('komik_save_protection', 'komik_protection_nonce'); ?>
            
            <div class="protection-wrap">
                <h2><?php _e('Protection Settings', 'komik-starter'); ?></h2>
                
                <!-- Main Toggle -->
                <div class="protection-toggle main">
                    <span class="dashicons dashicons-shield" style="font-size: 28px;"></span>
                    <h3><?php _e('Enable Content Protection', 'komik-starter'); ?></h3>
                    <label class="toggle-switch">
                        <input type="checkbox" name="protection_enable" value="1" <?php checked($enable, 1); ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="protection-status <?php echo $enable ? 'enabled' : 'disabled'; ?>">
                    <?php if ($enable) : ?>
                        <span class="dashicons dashicons-yes-alt"></span> <?php _e('Content Protection is ACTIVE. Visitors cannot copy content.', 'komik-starter'); ?>
                    <?php else : ?>
                        <span class="dashicons dashicons-no-alt"></span> <?php _e('Content Protection is INACTIVE. Content can be copied freely.', 'komik-starter'); ?>
                    <?php endif; ?>
                </div>
                
                <h3 style="margin-top: 30px;"><?php _e('Protection Options', 'komik-starter'); ?></h3>
                <div class="protection-options">
                    <div class="protection-option">
                        <div class="icon"><span class="dashicons dashicons-no"></span></div>
                        <label>
                            <strong><?php _e('Disable Right Click', 'komik-starter'); ?></strong>
                            <p><?php _e('Prevents context menu from appearing on right click.', 'komik-starter'); ?></p>
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" name="protection_right_click" value="1" <?php checked($right_click, 1); ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    
                    <div class="protection-option">
                        <div class="icon"><span class="dashicons dashicons-editor-strikethrough"></span></div>
                        <label>
                            <strong><?php _e('Disable Text Selection', 'komik-starter'); ?></strong>
                            <p><?php _e('Prevents users from selecting and highlighting text.', 'komik-starter'); ?></p>
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" name="protection_text_select" value="1" <?php checked($text_select, 1); ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    
                    <div class="protection-option">
                        <div class="icon"><span class="dashicons dashicons-laptop"></span></div>
                        <label>
                            <strong><?php _e('Disable Keyboard Shortcuts', 'komik-starter'); ?></strong>
                            <p><?php _e('Blocks Ctrl+C, Ctrl+U, Ctrl+S, Ctrl+A, F12 and other copy shortcuts.', 'komik-starter'); ?></p>
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" name="protection_keyboard" value="1" <?php checked($keyboard, 1); ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    
                    <div class="protection-option">
                        <div class="icon"><span class="dashicons dashicons-move"></span></div>
                        <label>
                            <strong><?php _e('Disable Image Drag', 'komik-starter'); ?></strong>
                            <p><?php _e('Prevents users from dragging and saving images.', 'komik-starter'); ?></p>
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" name="protection_drag" value="1" <?php checked($drag, 1); ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="message-field">
                    <label for="protection_message"><?php _e('Warning Message (shown when blocked)', 'komik-starter'); ?></label>
                    <input type="text" name="protection_message" id="protection_message" value="<?php echo esc_attr($message); ?>" placeholder="<?php _e('Content is protected!', 'komik-starter'); ?>">
                </div>
            </div>
            
            <p style="margin-top: 20px;">
                <button type="submit" name="komik_save_protection" class="button button-primary button-hero">
                    <span class="dashicons dashicons-saved" style="margin-top: 5px;"></span> <?php _e('Save Protection Settings', 'komik-starter'); ?>
                </button>
            </p>
        </form>
    </div>
    <?php
}

/**
 * =================================================================
 * HERO SLIDER SETTINGS PAGE
 * =================================================================
 */
function komik_starter_hero_slider_page() {
    // Handle form submission
    if (isset($_POST['komik_save_hero']) && wp_verify_nonce($_POST['komik_hero_nonce'], 'komik_save_hero')) {
        $enable = isset($_POST['hero_enable']) ? '1' : '0';
        $count = isset($_POST['hero_count']) ? intval($_POST['hero_count']) : 5;
        $mode = isset($_POST['hero_mode']) ? sanitize_text_field($_POST['hero_mode']) : 'latest';
        $autoplay = isset($_POST['hero_autoplay']) ? '1' : '0';
        $interval = isset($_POST['hero_interval']) ? intval($_POST['hero_interval']) : 5000;
        $manhwa_ids = isset($_POST['hero_manhwa']) ? array_map('intval', $_POST['hero_manhwa']) : array();
        
        update_option('komik_hero_enable', $enable);
        update_option('komik_hero_count', $count);
        update_option('komik_hero_mode', $mode);
        update_option('komik_hero_autoplay', $autoplay);
        update_option('komik_hero_interval', $interval);
        update_option('komik_hero_manhwa', $manhwa_ids);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Hero Slider settings saved!', 'komik-starter') . '</p></div>';
    }
    
    // Get current settings
    $enable = get_option('komik_hero_enable', '1');
    $count = get_option('komik_hero_count', 5);
    $mode = get_option('komik_hero_mode', 'latest');
    $autoplay = get_option('komik_hero_autoplay', '1');
    $interval = get_option('komik_hero_interval', 5000);
    $manhwa_ids = get_option('komik_hero_manhwa', array());
    
    // Get all manhwa
    $all_manhwa = get_posts(array(
        'post_type' => 'manhwa',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'publish',
    ));
    ?>
    
    <div class="wrap">
        <h1><span class="dashicons dashicons-slides" style="font-size: 30px; margin-right: 10px;"></span> <?php _e('Hero Slider Settings', 'komik-starter'); ?></h1>
        
        <style>
            .komik-hero-wrap { background: #fff; padding: 25px; border-radius: 8px; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
            .komik-hero-wrap h2 { margin-top: 0; color: #1e1e1e; border-bottom: 2px solid #2271b1; padding-bottom: 10px; }
            .komik-hero-row { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px; }
            .komik-hero-row label { font-weight: 600; min-width: 150px; }
            .komik-hero-row input[type="number"] { width: 80px; padding: 8px; }
            .komik-hero-row select { padding: 8px 15px; min-width: 200px; }
            .komik-toggle { position: relative; display: inline-block; width: 50px; height: 26px; }
            .komik-toggle input { opacity: 0; width: 0; height: 0; }
            .komik-toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .3s; border-radius: 26px; }
            .komik-toggle-slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
            .komik-toggle input:checked + .komik-toggle-slider { background-color: #2271b1; }
            .komik-toggle input:checked + .komik-toggle-slider:before { transform: translateX(24px); }
            .komik-mode-cards { display: flex; gap: 15px; flex-wrap: wrap; }
            .komik-mode-card { padding: 20px; border: 2px solid #dcdcde; border-radius: 8px; cursor: pointer; text-align: center; min-width: 150px; transition: all 0.2s; }
            .komik-mode-card:hover { border-color: #2271b1; }
            .komik-mode-card.active { border-color: #2271b1; background: #f0f6fc; }
            .komik-mode-card .dashicons { font-size: 30px; width: 30px; height: 30px; margin-bottom: 10px; color: #2271b1; }
            .komik-mode-card h4 { margin: 0; font-size: 14px; }
            .komik-mode-card p { margin: 5px 0 0; font-size: 12px; color: #646970; }
            .komik-manhwa-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; max-height: 400px; overflow-y: auto; padding: 10px; background: #f9f9f9; border-radius: 8px; margin-top: 15px; }
            .komik-manhwa-item { display: flex; align-items: center; gap: 10px; padding: 8px 12px; background: #fff; border-radius: 6px; border: 2px solid transparent; cursor: pointer; transition: all 0.2s; }
            .komik-manhwa-item:hover { border-color: #dcdcde; }
            .komik-manhwa-item.selected { border-color: #2271b1; background: #f0f6fc; }
            .komik-manhwa-item img { width: 40px; height: 55px; object-fit: cover; border-radius: 4px; }
            .komik-manhwa-item .title { flex: 1; font-size: 12px; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .komik-manhwa-item .order { width: 22px; height: 22px; background: #2271b1; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; }
            .manual-section { display: none; margin-top: 20px; }
            .manual-section.active { display: block; }
            .komik-search-box { margin-bottom: 15px; }
            .komik-search-box input { width: 100%; padding: 10px 15px; border: 1px solid #dcdcde; border-radius: 6px; }
        </style>
        
        <form method="post" id="hero-slider-form">
            <?php wp_nonce_field('komik_save_hero', 'komik_hero_nonce'); ?>
            
            <!-- Enable/Disable Section -->
            <div class="komik-hero-wrap">
                <h2><span class="dashicons dashicons-admin-generic"></span> <?php _e('General Settings', 'komik-starter'); ?></h2>
                
                <div class="komik-hero-row">
                    <label><?php _e('Enable Hero Slider', 'komik-starter'); ?></label>
                    <label class="komik-toggle">
                        <input type="checkbox" name="hero_enable" value="1" <?php checked($enable, '1'); ?>>
                        <span class="komik-toggle-slider"></span>
                    </label>
                    <span style="color: #646970;"><?php _e('Show hero slider on homepage', 'komik-starter'); ?></span>
                </div>
                
                <div class="komik-hero-row">
                    <label><?php _e('Number of Slides', 'komik-starter'); ?></label>
                    <input type="number" name="hero_count" value="<?php echo esc_attr($count); ?>" min="1" max="10">
                    <span style="color: #646970;"><?php _e('Maximum slides to display', 'komik-starter'); ?></span>
                </div>
            </div>
            
            <!-- Autoplay Section -->
            <div class="komik-hero-wrap">
                <h2><span class="dashicons dashicons-controls-play"></span> <?php _e('Autoplay Settings', 'komik-starter'); ?></h2>
                
                <div class="komik-hero-row">
                    <label><?php _e('Enable Autoplay', 'komik-starter'); ?></label>
                    <label class="komik-toggle">
                        <input type="checkbox" name="hero_autoplay" value="1" <?php checked($autoplay, '1'); ?>>
                        <span class="komik-toggle-slider"></span>
                    </label>
                    <span style="color: #646970;"><?php _e('Automatically rotate slides', 'komik-starter'); ?></span>
                </div>
                
                <div class="komik-hero-row">
                    <label><?php _e('Interval (ms)', 'komik-starter'); ?></label>
                    <input type="number" name="hero_interval" value="<?php echo esc_attr($interval); ?>" min="1000" max="15000" step="500">
                    <span style="color: #646970;"><?php _e('Time between slides (1000ms = 1 second)', 'komik-starter'); ?></span>
                </div>
            </div>
            
            <!-- Mode Section -->
            <div class="komik-hero-wrap">
                <h2><span class="dashicons dashicons-admin-settings"></span> <?php _e('Content Mode', 'komik-starter'); ?></h2>
                <p style="color: #646970; margin-bottom: 20px;"><?php _e('Choose how to select manhwa for the slider', 'komik-starter'); ?></p>
                
                <div class="komik-mode-cards">
                    <div class="komik-mode-card <?php echo $mode === 'manual' ? 'active' : ''; ?>" data-mode="manual">
                        <input type="radio" name="hero_mode" value="manual" <?php checked($mode, 'manual'); ?> style="display:none;">
                        <span class="dashicons dashicons-edit"></span>
                        <h4><?php _e('Manual', 'komik-starter'); ?></h4>
                        <p><?php _e('Select manhwa manually', 'komik-starter'); ?></p>
                    </div>
                    
                    <div class="komik-mode-card <?php echo $mode === 'latest' ? 'active' : ''; ?>" data-mode="latest">
                        <input type="radio" name="hero_mode" value="latest" <?php checked($mode, 'latest'); ?> style="display:none;">
                        <span class="dashicons dashicons-clock"></span>
                        <h4><?php _e('Latest Update', 'komik-starter'); ?></h4>
                        <p><?php _e('Recently updated manhwa', 'komik-starter'); ?></p>
                    </div>
                    
                    <div class="komik-mode-card <?php echo $mode === 'popular' ? 'active' : ''; ?>" data-mode="popular">
                        <input type="radio" name="hero_mode" value="popular" <?php checked($mode, 'popular'); ?> style="display:none;">
                        <span class="dashicons dashicons-chart-bar"></span>
                        <h4><?php _e('Most Popular', 'komik-starter'); ?></h4>
                        <p><?php _e('By views count', 'komik-starter'); ?></p>
                    </div>
                    
                    <div class="komik-mode-card <?php echo $mode === 'rating' ? 'active' : ''; ?>" data-mode="rating">
                        <input type="radio" name="hero_mode" value="rating" <?php checked($mode, 'rating'); ?> style="display:none;">
                        <span class="dashicons dashicons-star-filled"></span>
                        <h4><?php _e('Top Rated', 'komik-starter'); ?></h4>
                        <p><?php _e('Highest rated manhwa', 'komik-starter'); ?></p>
                    </div>
                </div>
                
                <!-- Manual Selection Grid -->
                <div class="manual-section <?php echo $mode === 'manual' ? 'active' : ''; ?>">
                    <h3><?php _e('Select Manhwa for Slider', 'komik-starter'); ?></h3>
                    <div class="komik-search-box">
                        <input type="text" id="hero-search" placeholder="<?php _e('Search manhwa...', 'komik-starter'); ?>">
                    </div>
                    <p style="color:#646970;"><span class="dashicons dashicons-info"></span> <?php _e('Click to select. Selected:', 'komik-starter'); ?> <strong id="hero-selected"><?php echo count($manhwa_ids); ?></strong></p>
                    
                    <div class="komik-manhwa-grid" id="hero-grid">
                        <?php foreach ($all_manhwa as $manhwa) : 
                            $is_selected = in_array($manhwa->ID, $manhwa_ids);
                            $order = $is_selected ? array_search($manhwa->ID, $manhwa_ids) + 1 : '';
                            $thumb = get_the_post_thumbnail_url($manhwa->ID, 'thumbnail') ?: get_template_directory_uri() . '/assets/images/placeholder.jpg';
                        ?>
                        <div class="komik-manhwa-item <?php echo $is_selected ? 'selected' : ''; ?>" data-id="<?php echo $manhwa->ID; ?>" data-title="<?php echo esc_attr($manhwa->post_title); ?>">
                            <img src="<?php echo esc_url($thumb); ?>" alt="">
                            <span class="title"><?php echo esc_html($manhwa->post_title); ?></span>
                            <?php if ($is_selected) : ?>
                            <span class="order"><?php echo $order; ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <p style="margin-top: 20px;">
                <button type="submit" name="komik_save_hero" class="button button-primary button-hero">
                    <span class="dashicons dashicons-saved" style="margin-top: 5px;"></span> <?php _e('Save Hero Slider Settings', 'komik-starter'); ?>
                </button>
            </p>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        var selections = <?php echo json_encode(array_map('intval', $manhwa_ids)); ?>;
        
        // Mode card selection
        $('.komik-mode-card').on('click', function() {
            $('.komik-mode-card').removeClass('active');
            $(this).addClass('active');
            $(this).find('input').prop('checked', true);
            
            var mode = $(this).data('mode');
            if (mode === 'manual') {
                $('.manual-section').addClass('active');
            } else {
                $('.manual-section').removeClass('active');
            }
        });
        
        // Search
        $('#hero-search').on('keyup', function() {
            var query = $(this).val().toLowerCase();
            $('#hero-grid .komik-manhwa-item').each(function() {
                var title = $(this).data('title').toLowerCase();
                $(this).toggle(title.indexOf(query) > -1);
            });
        });
        
        // Toggle selection
        $('.komik-manhwa-item').on('click', function() {
            var id = parseInt($(this).data('id'));
            var $el = $(this);
            
            if ($el.hasClass('selected')) {
                selections = selections.filter(function(i) { return i !== id; });
                $el.removeClass('selected').find('.order').remove();
            } else {
                if (selections.length >= 10) {
                    alert('Maximum 10 items for hero slider');
                    return;
                }
                selections.push(id);
                $el.addClass('selected').append('<span class="order">' + selections.length + '</span>');
            }
            
            updateCounts();
            updateHiddenInputs();
        });
        
        function updateCounts() {
            $('#hero-selected').text(selections.length);
            
            // Update order numbers
            $('#hero-grid .komik-manhwa-item').each(function() {
                var id = parseInt($(this).data('id'));
                var order = selections.indexOf(id);
                $(this).find('.order').remove();
                if (order > -1) {
                    $(this).append('<span class="order">' + (order + 1) + '</span>');
                }
            });
        }
        
        function updateHiddenInputs() {
            $('input[name^="hero_manhwa"]').remove();
            selections.forEach(function(id) {
                $('#hero-slider-form').append('<input type="hidden" name="hero_manhwa[]" value="' + id + '">');
            });
        }
        
        updateHiddenInputs();
    });
    </script>
    
    <?php
}

/**
 * =================================================================
 * ANALYTICS & TRACKING PAGE
 * =================================================================
 */
function komik_starter_analytics_page() {
    // Handle form submission
    if (isset($_POST['komik_save_analytics']) && wp_verify_nonce($_POST['komik_analytics_nonce'], 'komik_save_analytics')) {
        $histats = isset($_POST['histats_code']) ? wp_unslash($_POST['histats_code']) : '';
        $ga = isset($_POST['google_analytics']) ? sanitize_text_field($_POST['google_analytics']) : '';
        $head_code = isset($_POST['custom_head_code']) ? wp_unslash($_POST['custom_head_code']) : '';
        $footer_code = isset($_POST['custom_footer_code']) ? wp_unslash($_POST['custom_footer_code']) : '';
        
        update_option('komik_histats_code', $histats);
        update_option('komik_google_analytics', $ga);
        update_option('komik_custom_head_code', $head_code);
        update_option('komik_custom_footer_code', $footer_code);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Analytics settings saved!', 'komik-starter') . '</p></div>';
    }
    
    // Get current settings
    $histats = get_option('komik_histats_code', '');
    $ga = get_option('komik_google_analytics', '');
    $head_code = get_option('komik_custom_head_code', '');
    $footer_code = get_option('komik_custom_footer_code', '');
    ?>
    
    <div class="wrap">
        <h1><span class="dashicons dashicons-chart-area" style="font-size: 30px; margin-right: 10px;"></span> <?php _e('Analytics & Tracking', 'komik-starter'); ?></h1>
        
        <style>
            .komik-analytics-wrap { background: #fff; padding: 25px; border-radius: 8px; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
            .komik-analytics-wrap h2 { margin-top: 0; color: #1e1e1e; border-bottom: 2px solid #2271b1; padding-bottom: 10px; display: flex; align-items: center; gap: 10px; }
            .komik-analytics-wrap h2 img { width: 24px; height: 24px; }
            .komik-field { margin-bottom: 20px; }
            .komik-field label { display: block; font-weight: 600; margin-bottom: 8px; color: #1e1e1e; }
            .komik-field .description { color: #646970; font-size: 13px; margin-top: 5px; }
            .komik-field input[type="text"] { width: 100%; max-width: 500px; padding: 10px 12px; border: 1px solid #dcdcde; border-radius: 6px; }
            .komik-field textarea { width: 100%; height: 150px; font-family: monospace; font-size: 13px; padding: 12px; border: 1px solid #dcdcde; border-radius: 6px; background: #f9f9f9; }
            .komik-field textarea:focus { background: #fff; border-color: #2271b1; outline: none; }
            .code-example { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 6px; font-family: monospace; font-size: 12px; overflow-x: auto; margin-top: 10px; }
            .histats-logo { background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: #fff; padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; }
            .ga-logo { background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); color: #fff; padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; }
        </style>
        
        <form method="post">
            <?php wp_nonce_field('komik_save_analytics', 'komik_analytics_nonce'); ?>
            
            <!-- Histats Section -->
            <div class="komik-analytics-wrap" style="border-left: 4px solid #e74c3c;">
                <h2><span class="histats-logo">Histats</span> <?php _e('Histats Counter Code', 'komik-starter'); ?></h2>
                
                <div class="komik-field">
                    <label for="histats_code"><?php _e('Histats Counter/Widget Code', 'komik-starter'); ?></label>
                    <textarea name="histats_code" id="histats_code" placeholder="<!-- Histats.com  START  (aass)-->"><?php echo esc_textarea($histats); ?></textarea>
                    <p class="description">
                        <?php _e('Paste your complete Histats counter code here. Get your code from', 'komik-starter'); ?> 
                        <a href="https://www.histats.com" target="_blank">histats.com</a>
                    </p>
                    <div class="code-example">
                        &lt;!-- Histats.com  START  (aass)--&gt;<br>
                        &lt;script type="text/javascript"&gt;<br>
                        &nbsp;&nbsp;var _Hasync= _Hasync|| [];<br>
                        &nbsp;&nbsp;_Hasync.push(['Histats.start', '1,XXXXXXX,4,0,0,0,00010000']);<br>
                        &nbsp;&nbsp;...<br>
                        &lt;/script&gt;<br>
                        &lt;!-- Histats.com  END  --&gt;
                    </div>
                </div>
            </div>
            
            <!-- Google Analytics Section -->
            <div class="komik-analytics-wrap" style="border-left: 4px solid #f39c12;">
                <h2><span class="ga-logo">GA</span> <?php _e('Google Analytics', 'komik-starter'); ?></h2>
                
                <div class="komik-field">
                    <label for="google_analytics"><?php _e('Google Analytics Measurement ID', 'komik-starter'); ?></label>
                    <input type="text" name="google_analytics" id="google_analytics" value="<?php echo esc_attr($ga); ?>" placeholder="G-XXXXXXXXXX">
                    <p class="description">
                        <?php _e('Enter your Google Analytics 4 Measurement ID (starts with G-). Get it from', 'komik-starter'); ?> 
                        <a href="https://analytics.google.com" target="_blank">analytics.google.com</a>
                    </p>
                </div>
            </div>
            
            <!-- Custom Head Code -->
            <div class="komik-analytics-wrap" style="border-left: 4px solid #9b59b6;">
                <h2><span class="dashicons dashicons-editor-code"></span> <?php _e('Custom Head Code', 'komik-starter'); ?></h2>
                
                <div class="komik-field">
                    <label for="custom_head_code"><?php _e('Code to insert in <head>', 'komik-starter'); ?></label>
                    <textarea name="custom_head_code" id="custom_head_code" placeholder="<!-- Your custom head code -->"><?php echo esc_textarea($head_code); ?></textarea>
                    <p class="description">
                        <?php _e('This code will be inserted before the closing </head> tag. Use for meta tags, verification codes, etc.', 'komik-starter'); ?>
                    </p>
                </div>
            </div>
            
            <!-- Custom Footer Code -->
            <div class="komik-analytics-wrap" style="border-left: 4px solid #3498db;">
                <h2><span class="dashicons dashicons-editor-code"></span> <?php _e('Custom Footer Code', 'komik-starter'); ?></h2>
                
                <div class="komik-field">
                    <label for="custom_footer_code"><?php _e('Code to insert before </body>', 'komik-starter'); ?></label>
                    <textarea name="custom_footer_code" id="custom_footer_code" placeholder="<!-- Your custom footer code -->"><?php echo esc_textarea($footer_code); ?></textarea>
                    <p class="description">
                        <?php _e('This code will be inserted before the closing </body> tag. Use for chat widgets, other tracking scripts, etc.', 'komik-starter'); ?>
                    </p>
                </div>
            </div>
            
            <p style="margin-top: 20px;">
                <button type="submit" name="komik_save_analytics" class="button button-primary button-hero">
                    <span class="dashicons dashicons-saved" style="margin-top: 5px;"></span> <?php _e('Save Analytics Settings', 'komik-starter'); ?>
                </button>
            </p>
        </form>
    </div>
    
    <?php
}

/**
 * Output Analytics codes in head and footer
 */
add_action('wp_head', 'komik_starter_output_head_analytics', 999);
function komik_starter_output_head_analytics() {
    // Google Analytics
    $ga = get_option('komik_google_analytics', '');
    if (!empty($ga)) {
        ?>
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo esc_js($ga); ?>');
        </script>
        <?php
    }
    
    // Custom Head Code
    $head_code = get_option('komik_custom_head_code', '');
    if (!empty($head_code)) {
        echo "\n<!-- Custom Head Code -->\n";
        echo $head_code;
        echo "\n<!-- End Custom Head Code -->\n";
    }
}

add_action('wp_footer', 'komik_starter_output_footer_analytics', 999);
function komik_starter_output_footer_analytics() {
    // Histats Code
    $histats = get_option('komik_histats_code', '');
    if (!empty($histats)) {
        echo "\n<!-- Histats Counter -->\n";
        echo '<div class="histats-wrapper" style="display: flex; justify-content: center; align-items: center; width: 100%; padding: 10px 0;">';
        echo '<style>#histats_counter { text-align: center; } #histats_counter canvas, #histats_counter img { display: block; margin: 0 auto; }</style>';
        echo $histats;
        echo '</div>';
        echo "\n<!-- End Histats Counter -->\n";
    }
    
    // Custom Footer Code
    $footer_code = get_option('komik_custom_footer_code', '');
    if (!empty($footer_code)) {
        echo "\n<!-- Custom Footer Code -->\n";
        echo $footer_code;
        echo "\n<!-- End Custom Footer Code -->\n";
    }
    
    // Adsterra Popunder
    $adsterra_popunder = get_option('komik_ads_adsterra_popunder', '');
    if (!empty($adsterra_popunder)) {
        echo "\n<!-- Adsterra Popunder -->\n";
        echo $adsterra_popunder;
        echo "\n<!-- End Adsterra Popunder -->\n";
    }
    
    // Adsterra Social Bar
    $adsterra_socialbar = get_option('komik_ads_adsterra_socialbar', '');
    if (!empty($adsterra_socialbar)) {
        echo "\n<!-- Adsterra Social Bar -->\n";
        echo $adsterra_socialbar;
        echo "\n<!-- End Adsterra Social Bar -->\n";
    }
    
    // Direct Link Script
    $direct_link_enable = get_option('komik_ads_direct_link_enable', '');
    $direct_link_url = get_option('komik_ads_direct_link_url', '');
    $direct_link_delay = intval(get_option('komik_ads_direct_link_delay', 0));
    $direct_link_cooldown = intval(get_option('komik_ads_direct_link_cooldown', 24));
    
    // Parse multiple URLs
    $urls = array_filter(array_map('trim', explode("\n", $direct_link_url)));
    
    if ($direct_link_enable === '1' && !empty($urls)) {
        ?>
        <!-- Direct Link Ads -->
        <script>
        (function() {
            var directLinkUrls = <?php echo json_encode(array_values($urls)); ?>;
            var delaySeconds = <?php echo $direct_link_delay; ?>;
            var isActive = false;
            var hasClicked = false;
            
            // Activate after delay
            setTimeout(function() {
                isActive = true;
            }, delaySeconds * 1000);
            
            // Get random URL from array
            function getRandomUrl() {
                if (directLinkUrls.length === 1) {
                    return directLinkUrls[0];
                }
                return directLinkUrls[Math.floor(Math.random() * directLinkUrls.length)];
            }
            
            // Listen for first click on this page view
            document.addEventListener('click', function(e) {
                if (!isActive || hasClicked) return;
                
                // Ignore clicks on links, buttons, inputs
                var target = e.target;
                if (target.tagName === 'A' || target.tagName === 'BUTTON' || 
                    target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' ||
                    target.tagName === 'SELECT' || target.closest('a') || 
                    target.closest('button') || target.closest('.no-direct-link')) {
                    return;
                }
                
                hasClicked = true;
                
                // Open random URL in new tab
                window.open(getRandomUrl(), '_blank');
            }, true);
        })();
        </script>
        <!-- End Direct Link Ads -->
        <?php
    }
}
