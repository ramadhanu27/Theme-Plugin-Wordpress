<?php
/**
 * MangaZen Theme Functions
 * Zen-like reading experience for manga/manhwa
 *
 * @package MangaZen
 * @version 1.0.0
 */

defined("ABSPATH") || die("!");

// Suppress deprecation notices (WordPress 6.4+ compatibility)
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
}

// Include PDF Generator for external download
require_once get_template_directory() . '/inc/pdf-generator.php';

// Include User Level System
require_once get_template_directory() . '/inc/user-level.php';

/**
 * Theme Setup
 */
function komik_starter_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Register navigation menus
    register_nav_menus(array(
        'main' => __('Main Menu', 'komik-starter'),
        'footer' => __('Footer Menu', 'komik-starter'),
    ));

    // Add support for HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Add support for custom logo (no cropping required)
    add_theme_support('custom-logo', array(
        'height'               => 60,
        'width'                => 250,
        'flex-width'           => true,
        'flex-height'          => true,
        'header-text'          => array('site-title', 'site-description'),
        'unlink-homepage-logo' => false,
    ));
}
add_action('after_setup_theme', 'komik_starter_setup');

/**
 * Enqueue Scripts and Styles
 */
function komik_starter_scripts() {
    // Main stylesheet
    wp_enqueue_style('komik-starter-style', get_stylesheet_uri(), array(), '1.0.0');

    // Google Fonts - Fira Sans & Roboto
    wp_enqueue_style('komik-starter-fonts', 'https://fonts.googleapis.com/css?family=Fira+Sans:400,400i,500,500i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,700,700i&display=swap', array(), null);

    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4');

    // Main script
    wp_enqueue_script('komik-starter-script', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);

    // Comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'komik_starter_scripts');

/**
 * Register Widget Areas
 */
function komik_starter_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar Right', 'komik-starter'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in the right sidebar.', 'komik-starter'),
        'before_widget' => '<div class="section">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="releases"><h4>',
        'after_title'   => '</h4></div>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widgets', 'komik-starter'),
        'id'            => 'footer-widgets',
        'description'   => __('Add widgets here to appear in footer area.', 'komik-starter'),
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'komik_starter_widgets_init');

/**
 * Custom Excerpt Length
 */
function komik_starter_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'komik_starter_excerpt_length', 999);

/**
 * Custom Excerpt More
 */
function komik_starter_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'komik_starter_excerpt_more');

/**
 * Add custom body classes
 */
function komik_starter_body_classes($classes) {
    // Add dark mode class
    $classes[] = 'dark-theme';

    if (is_singular()) {
        $classes[] = 'singular';
    }

    if (is_singular('post')) {
        $classes[] = 'single-post';
    }

    return $classes;
}
add_filter('body_class', 'komik_starter_body_classes');

/**
 * Post Views Counter
 */
function komik_starter_set_post_views($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if ($count == '') {
        $count = 0;
        delete_post_meta($postID, $count_key);
        return add_post_meta($postID, $count_key, '0');
    } else {
        $count++;
        return update_post_meta($postID, $count_key, $count);
    }
}
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

function komik_starter_get_post_views($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if ($count == '') {
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 Views";
    }
    return $count . ' Views';
}

/**
 * Breadcrumb Function
 */
function komik_starter_breadcrumb() {
    if (!is_home() && !is_front_page()) {
        echo '<div class="ts-breadcrumb bixbox">';
        echo '<ol itemscope itemtype="http://schema.org/BreadcrumbList">';
        echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
        echo '<a itemprop="item" href="' . esc_url(home_url('/')) . '"><span itemprop="name">' . esc_html(get_bloginfo('name')) . '</span></a>';
        echo '<meta itemprop="position" content="1">';
        echo '</li>';
        echo ' › ';

        if (is_single()) {
            $categories = get_the_category();
            if (!empty($categories)) {
                echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                echo '<a itemprop="item" href="' . esc_url(get_category_link($categories[0]->term_id)) . '"><span itemprop="name">' . esc_html($categories[0]->name) . '</span></a>';
                echo '<meta itemprop="position" content="2">';
                echo '</li>';
                echo ' › ';
            }
            echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<a itemprop="item" href="' . esc_url(get_permalink()) . '"><span itemprop="name">' . esc_html(get_the_title()) . '</span></a>';
            echo '<meta itemprop="position" content="3">';
            echo '</li>';
        } elseif (is_category()) {
            echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<span itemprop="name">' . single_cat_title('', false) . '</span>';
            echo '<meta itemprop="position" content="2">';
            echo '</li>';
        } elseif (is_page()) {
            echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<span itemprop="name">' . esc_html(get_the_title()) . '</span>';
            echo '<meta itemprop="position" content="2">';
            echo '</li>';
        } elseif (is_search()) {
            echo '<li>Search Results</li>';
        } elseif (is_archive()) {
            echo '<li>' . get_the_archive_title() . '</li>';
        }

        echo '</ol>';
        echo '</div>';
    }
}

/**
 * Next/Prev Navigation
 */
function komik_starter_navigation() {
    echo '<div class="nextprev">';
    previous_post_link('<span class="prev">%link</span>', '<i class="fas fa-angle-left"></i> Previous');
    next_post_link('<span class="next">%link</span>', 'Next <i class="fas fa-angle-right"></i>');
    echo '</div>';
}

/**
 * Pagination Function
 */
function komik_starter_pagination() {
    global $wp_query;

    if ($wp_query->max_num_pages <= 1) {
        return;
    }

    $big = 999999999;
    $pages = paginate_links(array(
        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format'    => '?paged=%#%',
        'current'   => max(1, get_query_var('paged')),
        'total'     => $wp_query->max_num_pages,
        'type'      => 'array',
        'prev_text' => '<i class="fas fa-chevron-left"></i>',
        'next_text' => '<i class="fas fa-chevron-right"></i>',
    ));

    if (is_array($pages)) {
        echo '<div class="pagination">';
        foreach ($pages as $page) {
            echo $page;
        }
        echo '</div>';
    }
}

/**
 * Custom Logo
 */
function komik_starter_custom_logo() {
    if (has_custom_logo()) {
        the_custom_logo();
    } else {
        echo '<span class="site-title">' . esc_html(get_bloginfo('name')) . '</span>';
    }
}

/**
 * Disable srcset for images
 */
function komik_starter_disable_srcset($sources) {
    return false;
}
add_filter('wp_calculate_image_srcset', 'komik_starter_disable_srcset');

/**
 * Disable Gutenberg widget block editor
 */
add_filter('gutenberg_use_widgets_block_editor', '__return_false');
add_filter('use_widgets_block_editor', '__return_false');

/**
 * Custom Search Form Filter for post types
 */
function komik_starter_search_filter($query) {
    if ($query->is_search && !is_admin()) {
        // Can be customized to filter specific post types
        // $query->set('post_type', array('post', 'page'));
    }
    return $query;
}
add_filter('pre_get_posts', 'komik_starter_search_filter');

/**
 * Estimated Reading Time
 */
function komik_starter_reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute

    if ($reading_time == 1) {
        return $reading_time . ' min read';
    } else {
        return $reading_time . ' mins read';
    }
}

/**
 * Custom Comment Callback (Legacy)
 */
function komik_starter_comment_callback($comment, $args, $depth) {
    komik_threaded_comment($comment, $args, $depth);
}

/**
 * Threaded Comment Callback with Reply Support
 */
function komik_threaded_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    $tag = ($args['style'] === 'div') ? 'div' : 'li';
    
    // Get user role badge and level badge
    $user_badge = '';
    $level_badge = '';
    $comment_author_id = $comment->user_id;
    
    if ($comment_author_id > 0) {
        $user = get_userdata($comment_author_id);
        if ($user) {
            // Role badges for staff
            $roles = $user->roles;
            if (in_array('administrator', $roles)) {
                $user_badge = '<span class="comment-badge badge-admin"><i class="fas fa-shield-alt"></i> Admin</span>';
            } elseif (in_array('editor', $roles)) {
                $user_badge = '<span class="comment-badge badge-editor"><i class="fas fa-pen"></i> Editor</span>';
            } elseif (in_array('author', $roles)) {
                $user_badge = '<span class="comment-badge badge-author"><i class="fas fa-user-edit"></i> Author</span>';
            }
            
            // Check if this is the post author (OP)
            $post = get_post($comment->comment_post_ID);
            if ($post && $comment_author_id == $post->post_author) {
                $user_badge = '<span class="comment-badge badge-op"><i class="fas fa-crown"></i> OP</span>';
            }
            
            // Level badge for all users
            $level_badge = komik_get_level_badge($comment_author_id, 'small');
        }
    }
    ?>
    <<?php echo $tag; ?> <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?> id="comment-<?php comment_ID(); ?>">
        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            <header class="comment-header">
                <div class="comment-author vcard">
                    <?php echo get_avatar($comment, $args['avatar_size'], '', '', array('class' => 'comment-avatar')); ?>
                    <div class="comment-author-info">
                        <div class="comment-author-name-wrapper">
                            <span class="comment-author-name"><?php comment_author_link(); ?></span>
                            <?php echo $level_badge; ?>
                            <?php echo $user_badge; ?>
                        </div>
                        <time class="comment-date" datetime="<?php echo get_comment_date('c'); ?>">
                            <i class="far fa-clock"></i>
                            <?php echo human_time_diff(get_comment_date('U'), current_time('timestamp')) . ' ' . __('ago', 'komik-starter'); ?>
                        </time>
                    </div>
                </div>
            </header>

            <div class="comment-content">
                <?php if ($comment->comment_approved == '0') : ?>
                    <p class="comment-awaiting-moderation">
                        <i class="fas fa-hourglass-half"></i>
                        <?php _e('Your comment is awaiting moderation.', 'komik-starter'); ?>
                    </p>
                <?php endif; ?>
                <?php comment_text(); ?>
            </div>

            <footer class="comment-footer">
                <div class="comment-actions">
                    <?php
                    comment_reply_link(array_merge($args, array(
                        'add_below' => 'div-comment',
                        'depth'     => $depth,
                        'max_depth' => $args['max_depth'],
                        'before'    => '<span class="reply-link">',
                        'after'     => '</span>',
                    )));
                    ?>
                    <?php edit_comment_link('<i class="fas fa-edit"></i> ' . __('Edit', 'komik-starter'), '<span class="edit-link">', '</span>'); ?>
                </div>
            </footer>
        </article>
    <?php
}

/**
 * Format Number Helper
 */
function komik_starter_format_number($num) {
    if ($num >= 1000000) {
        return round($num / 1000000, 1) . 'M';
    } elseif ($num >= 1000) {
        return round($num / 1000, 1) . 'K';
    }
    return $num;
}

/**
 * Time Ago Function
 */
function komik_starter_time_ago($time) {
    $time_difference = time() - strtotime($time);

    if ($time_difference < 60) {
        return 'just now';
    }

    $units = array(
        31536000 => 'year',
        2592000  => 'month',
        604800   => 'week',
        86400    => 'day',
        3600     => 'hour',
        60       => 'minute',
    );

    foreach ($units as $unit => $text) {
        if ($time_difference >= $unit) {
            $num = floor($time_difference / $unit);
            return $num . ' ' . $text . ($num > 1 ? 's' : '') . ' ago';
        }
    }
}

/**
 * =================================================================
 * MANHWA MANAGER PLUGIN INTEGRATION
 * =================================================================
 */

/**
 * Check if Manhwa Manager plugin is active
 */
function komik_starter_is_manhwa_manager_active() {
    return class_exists('Manhwa_Manager');
}

/**
 * Get latest manhwa for homepage
 */
function komik_starter_get_latest_manhwa($count = 12) {
    if (!komik_starter_is_manhwa_manager_active()) {
        return array();
    }
    
    return get_posts(array(
        'post_type'      => 'manhwa',
        'posts_per_page' => $count,
        'orderby'        => 'modified',
        'order'          => 'DESC',
    ));
}

/**
 * Get popular manhwa
 */
function komik_starter_get_popular_manhwa($count = 6) {
    if (!komik_starter_is_manhwa_manager_active()) {
        return array();
    }
    
    return get_posts(array(
        'post_type'      => 'manhwa',
        'posts_per_page' => $count,
        'meta_key'       => '_manhwa_views',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    ));
}

/**
 * Get manhwa chapter URL (SEO-friendly format)
 * Format: /manhwa-slug-chapter-XX/
 */
function komik_starter_get_chapter_url($manhwa_id, $chapter_num) {
    $post = get_post($manhwa_id);
    if (!$post) {
        return home_url('/');
    }
    $chapter_num = preg_replace('/[^0-9.]/', '', $chapter_num);
    return home_url('/' . $post->post_name . '-chapter-' . $chapter_num . '/');
}

/**
 * Get chapter number from title
 */
function komik_starter_get_chapter_number($title) {
    if (preg_match('/(\d+)/', $title, $matches)) {
        return $matches[1];
    }
    return '';
}

/**
 * Display manhwa card
 */
function komik_starter_manhwa_card($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $type = get_post_meta($post_id, '_manhwa_type', true);
    $rating = get_post_meta($post_id, '_manhwa_rating', true);
    $status = get_post_meta($post_id, '_manhwa_status', true);
    $chapters = get_post_meta($post_id, '_manhwa_chapters', true);
    $ch_count = is_array($chapters) ? count($chapters) : 0;
    $latest_ch = !empty($chapters) ? $chapters[0] : null;
    
    ob_start();
    ?>
    <div class="bs styletere">
        <div class="bsx">
            <a href="<?php echo get_permalink($post_id); ?>">
                <div class="limit">
                    <?php if (has_post_thumbnail($post_id)) : ?>
                        <?php echo get_the_post_thumbnail($post_id, 'medium'); ?>
                    <?php else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholder.jpg" alt="<?php echo esc_attr(get_the_title($post_id)); ?>" />
                    <?php endif; ?>
                    
                    <?php if ($type) : ?>
                        <span class="typename <?php echo esc_attr($type); ?>"><?php echo esc_html($type); ?></span>
                    <?php endif; ?>
                    
                    <?php if ($status == 'completed') : ?>
                        <span class="status-badge completed"><i class="fas fa-check-circle"></i></span>
                    <?php endif; ?>
                </div>
                <div class="tt"><?php echo esc_html(get_the_title($post_id)); ?></div>
            </a>
            <div class="adds">
                <?php if ($rating) : ?>
                <div class="rating">
                    <span class="rtg"><i class="fas fa-star" style="color: #f39c12;"></i> <?php echo esc_html($rating); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($latest_ch) : ?>
                <div class="epxs"><?php echo esc_html($latest_ch['title']); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Modify breadcrumb for manhwa post type
 */
add_action('komik_starter_breadcrumb_before_title', function() {
    if (is_singular('manhwa')) {
        $genres = get_the_terms(get_the_ID(), 'manhwa_genre');
        if (!empty($genres) && !is_wp_error($genres)) {
            echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<a itemprop="item" href="' . esc_url(get_term_link($genres[0])) . '"><span itemprop="name">' . esc_html($genres[0]->name) . '</span></a>';
            echo '<meta itemprop="position" content="2">';
            echo '</li>';
            echo ' › ';
        }
    }
});

/**
 * Add manhwa to search results
 */
function komik_starter_add_manhwa_to_search($query) {
    if ($query->is_search && !is_admin() && $query->is_main_query()) {
        $post_types = $query->get('post_type');
        if (empty($post_types) || $post_types == 'post') {
            $query->set('post_type', array('post', 'manhwa'));
        }
    }
    return $query;
}
add_filter('pre_get_posts', 'komik_starter_add_manhwa_to_search');

/**
 * Add body class for manhwa pages
 */
function komik_starter_manhwa_body_class($classes) {
    if (is_singular('manhwa')) {
        $classes[] = 'single-manhwa-page';
    }
    if (is_post_type_archive('manhwa')) {
        $classes[] = 'manhwa-archive';
    }
    return $classes;
}
add_filter('body_class', 'komik_starter_manhwa_body_class');

/**
 * Register sidebar widget for latest chapters
 */
function komik_starter_register_manhwa_widgets() {
    register_sidebar(array(
        'name'          => __('Manhwa Sidebar', 'komik-starter'),
        'id'            => 'manhwa-sidebar',
        'description'   => __('Sidebar for manhwa pages.', 'komik-starter'),
        'before_widget' => '<div class="section">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="releases"><h4>',
        'after_title'   => '</h4></div>',
    ));
}
add_action('widgets_init', 'komik_starter_register_manhwa_widgets');

/**
 * Include SEO Functions
 */
require_once get_template_directory() . '/inc/seo.php';

/**
 * Include Theme Options (Popular Manhwa & Ads)
 */
require_once get_template_directory() . '/inc/theme-options.php';

/**
 * Include User Account System
 */
require_once get_template_directory() . '/inc/user-account.php';

/**
 * Auto-apply PDF Downloader template for page with slug 'download-pdf'
 */
function komik_auto_pdf_downloader_template($template) {
    global $post;
    
    if ($post && is_page() && $post->post_name === 'download-pdf') {
        $new_template = locate_template('page-download-pdf.php');
        if ($new_template) {
            return $new_template;
        }
    }
    
    return $template;
}
add_filter('template_include', 'komik_auto_pdf_downloader_template');

/**
 * Auto-apply Login template for page with slug 'login'
 */
function komik_auto_login_template($template) {
    global $post;
    
    if ($post && is_page() && $post->post_name === 'login') {
        $new_template = locate_template('page-login.php');
        if ($new_template) {
            return $new_template;
        }
    }
    
    return $template;
}
add_filter('template_include', 'komik_auto_login_template');

/**
 * Auto-apply Register template for page with slug 'register'
 */
function komik_auto_register_template($template) {
    global $post;
    
    if ($post && is_page() && $post->post_name === 'register') {
        $new_template = locate_template('page-register.php');
        if ($new_template) {
            return $new_template;
        }
    }
    
    return $template;
}
add_filter('template_include', 'komik_auto_register_template');

/**
 * Auto-apply Profile template for page with slug 'profile'
 */
function komik_auto_profile_template($template) {
    global $post;
    
    if ($post && is_page() && $post->post_name === 'profile') {
        $new_template = locate_template('page-profile.php');
        if ($new_template) {
            return $new_template;
        }
    }
    
    return $template;
}
add_filter('template_include', 'komik_auto_profile_template');

/**
 * Add login/profile link to header menu
 */
function komik_add_account_menu_item($items, $args) {
    if ($args->theme_location === 'main') {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $avatar = komik_get_user_avatar($user->ID, 32);
            $items .= '<li class="menu-item user-menu-item">';
            $items .= '<a href="' . home_url('/profile/') . '" class="user-menu-link">';
            $items .= '<span class="user-avatar">' . $avatar . '</span>';
            $items .= '<span class="user-name">' . esc_html($user->display_name) . '</span>';
            $items .= '</a></li>';
        } else {
            $items .= '<li class="menu-item login-menu-item">';
            $items .= '<a href="' . home_url('/login/') . '" class="login-btn">';
            $items .= '<i class="fas fa-user"></i> ' . __('Login', 'komik-starter');
            $items .= '</a></li>';
        }
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'komik_add_account_menu_item', 10, 2);

/**
 * AJAX Comment Handler
 */
function komik_ajax_comment() {
    // Get comment data from POST
    $comment_post_ID = isset($_POST['comment_post_ID']) ? intval($_POST['comment_post_ID']) : 0;
    $comment_content = isset($_POST['comment']) ? wp_unslash($_POST['comment']) : '';
    $comment_parent = isset($_POST['comment_parent']) ? intval($_POST['comment_parent']) : 0;
    
    // Don't sanitize - allow emoji and special characters
    $comment_content = trim($comment_content);
    
    // Validate
    if (!$comment_post_ID) {
        wp_send_json_error(['message' => __('Invalid post.', 'komik-starter')]);
    }
    
    if (empty($comment_content)) {
        wp_send_json_error(['message' => __('Please enter a comment.', 'komik-starter')]);
    }
    
    // Check if post exists and is open for comments
    $post = get_post($comment_post_ID);
    if (!$post) {
        wp_send_json_error(['message' => __('Post not found.', 'komik-starter')]);
    }
    
    if ($post->comment_status !== 'open') {
        wp_send_json_error(['message' => __('Comments are closed for this post.', 'komik-starter')]);
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        // Guest comment - get name and email
        $author = isset($_POST['author']) ? sanitize_text_field($_POST['author']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        
        if (get_option('require_name_email')) {
            if (empty($author) || empty($email)) {
                wp_send_json_error(['message' => __('Name and email are required.', 'komik-starter')]);
            }
        }
        
        $commentdata = [
            'comment_post_ID' => $comment_post_ID,
            'comment_content' => $comment_content,
            'comment_parent' => $comment_parent,
            'comment_author' => $author,
            'comment_author_email' => $email,
            'comment_author_url' => '',
            'comment_type' => 'comment',
        ];
    } else {
        // Logged in user
        $user = wp_get_current_user();
        
        $commentdata = [
            'comment_post_ID' => $comment_post_ID,
            'comment_content' => $comment_content,
            'comment_parent' => $comment_parent,
            'user_id' => $user->ID,
            'comment_author' => $user->display_name,
            'comment_author_email' => $user->user_email,
            'comment_author_url' => $user->user_url,
            'comment_type' => 'comment',
        ];
    }
    
    // Add comment author IP
    $commentdata['comment_author_IP'] = $_SERVER['REMOTE_ADDR'];
    $commentdata['comment_agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    
    // Insert comment
    $comment_id = wp_new_comment($commentdata, true);
    
    if (is_wp_error($comment_id)) {
        wp_send_json_error(['message' => $comment_id->get_error_message()]);
    }
    
    // Get the comment
    $comment = get_comment($comment_id);
    
    // Render comment HTML
    ob_start();
    $GLOBALS['comment'] = $comment;
    komik_threaded_comment($comment, [
        'style' => 'ol',
        'avatar_size' => 50,
        'max_depth' => 5,
    ], 1);
    $comment_html = ob_get_clean();
    
    wp_send_json_success([
        'message' => __('Comment posted successfully!', 'komik-starter'),
        'comment_id' => $comment_id,
        'comment_html' => $comment_html,
    ]);
}
add_action('wp_ajax_komik_ajax_comment', 'komik_ajax_comment');
add_action('wp_ajax_nopriv_komik_ajax_comment', 'komik_ajax_comment');

/**
 * Allow img tags in comments for GIF support
 */
function komik_allow_img_in_comments($tags) {
    $tags['img'] = [
        'src' => true,
        'alt' => true,
        'class' => true,
        'width' => true,
        'height' => true,
        'loading' => true,
    ];
    return $tags;
}
add_filter('wp_kses_allowed_html', function($tags, $context) {
    if ($context === 'pre_comment_content' || $context === 'post') {
        $tags['img'] = [
            'src' => true,
            'alt' => true,
            'class' => true,
            'width' => true,
            'height' => true,
            'loading' => true,
        ];
    }
    return $tags;
}, 10, 2);

/**
 * Also add to comment allowed tags
 */
add_filter('comment_text', function($comment_text) {
    // Convert GIF image tags that might have been stripped
    $comment_text = preg_replace(
        '/&lt;img src="(https:\/\/[^"]+\.gif[^"]*)" alt="GIF" class="comment-gif"&gt;/i',
        '<img src="$1" alt="GIF" class="comment-gif">',
        $comment_text
    );
    return $comment_text;
}, 20);



