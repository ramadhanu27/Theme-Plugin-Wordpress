<?php
/**
 * Chapter Reader Handler
 * Handles chapter display and reader endpoint
 */

if (!defined('ABSPATH')) {
    exit;
}

class Manhwa_Chapter_Reader {
    
    public function __construct() {
        // Add rewrite rules
        add_action('init', array($this, 'add_rewrite_rules'));
        
        // Add query vars
        add_filter('query_vars', array($this, 'add_query_vars'));
        
        // Handle chapter template
        add_action('template_redirect', array($this, 'handle_chapter_request'), 5);
        
        // Register shortcode
        add_shortcode('manhwa_chapter', array($this, 'chapter_shortcode'));
        
        // AJAX for chapter images
        add_action('wp_ajax_get_chapter_images', array($this, 'ajax_get_chapter_images'));
        add_action('wp_ajax_nopriv_get_chapter_images', array($this, 'ajax_get_chapter_images'));
    }
    
    /**
     * Add rewrite rules for chapter URLs
     * SEO-friendly URL format: /manhwa-slug-chapter-XX/
     */
    public function add_rewrite_rules() {
        // Primary format: /manhwa-slug-chapter-XX/ (SEO-friendly, like manhwaindo.my)
        add_rewrite_rule(
            '([^/]+)-chapter-([0-9]+(?:\.[0-9]+)?)/?$',
            'index.php?chapter_reader=1&manhwa_slug=$matches[1]&chapter_num=$matches[2]',
            'top'
        );
        
        // Alternative format: /read/manhwa-slug/chapter-XX/
        add_rewrite_rule(
            'read/([^/]+)/chapter-([0-9]+(?:\.[0-9]+)?)/?$',
            'index.php?chapter_reader=1&manhwa_slug=$matches[1]&chapter_num=$matches[2]',
            'top'
        );
        
        // Legacy format: /chapter/manhwa-slug/chapter-number/
        add_rewrite_rule(
            'chapter/([^/]+)/([^/]+)/?$',
            'index.php?chapter_reader=1&manhwa_slug=$matches[1]&chapter_num=$matches[2]',
            'top'
        );
        
        // Legacy query string format: /chapter/?manhwa=ID&chapter=NUM
        add_rewrite_rule(
            'chapter/?$',
            'index.php?chapter_reader=1',
            'top'
        );
    }
    
    /**
     * Add query vars
     */
    public function add_query_vars($vars) {
        $vars[] = 'chapter_reader';
        $vars[] = 'manhwa_slug';
        $vars[] = 'chapter_num';
        return $vars;
    }
    
    /**
     * Generate SEO-friendly chapter URL
     * Format: /manhwa-slug-chapter-XX/
     */
    public static function get_chapter_url($manhwa_id, $chapter_number) {
        $manhwa = get_post($manhwa_id);
        if (!$manhwa) {
            return home_url('/');
        }
        
        $slug = $manhwa->post_name;
        $chapter_num = preg_replace('/[^0-9.]/', '', $chapter_number);
        
        return home_url('/' . $slug . '-chapter-' . $chapter_num . '/');
    }
    
    /**
     * Generate legacy chapter URL (for backward compatibility)
     */
    public static function get_legacy_chapter_url($manhwa_id, $chapter_number) {
        return add_query_arg(array(
            'manhwa' => $manhwa_id,
            'chapter' => $chapter_number
        ), home_url('/chapter/'));
    }
    
    /**
     * Handle chapter request
     */
    public function handle_chapter_request() {
        // Check if this is a chapter reader request
        if (!get_query_var('chapter_reader') && !isset($_GET['manhwa'])) {
            return;
        }
        
        // Get chapter data
        $chapter_data = $this->get_chapter_data();
        
        if (!$chapter_data) {
            // Return proper 404 status for SEO bots
            status_header(404);
            nocache_headers();
            $this->display_404_page();
            exit;
        }
        
        // Display chapter reader
        $this->display_chapter_reader($chapter_data);
        exit;
    }
    
    /**
     * Display 404 page for chapter not found
     */
    private function display_404_page() {
        $manhwa_id = isset($_GET['manhwa']) ? intval($_GET['manhwa']) : 0;
        $chapter_num = isset($_GET['chapter']) ? sanitize_text_field($_GET['chapter']) : '';
        $manhwa = $manhwa_id ? get_post($manhwa_id) : null;
        $site_name = get_bloginfo('name');
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="robots" content="noindex, follow">
            <title>Chapter Not Found - <?php echo esc_html($site_name); ?></title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { background: #16151d; color: #fff; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
                .error-box { background: #1c1b24; padding: 40px; border-radius: 16px; text-align: center; max-width: 500px; }
                .error-icon { font-size: 72px; margin-bottom: 20px; }
                h1 { font-size: 24px; margin-bottom: 10px; }
                p { color: #888; margin-bottom: 20px; }
                .btn { display: inline-block; padding: 12px 24px; background: #366ad3; color: #fff; text-decoration: none; border-radius: 8px; }
                .btn:hover { background: #4a7ee0; }
            </style>
        </head>
        <body>
            <div class="error-box">
                <div class="error-icon">📖</div>
                <h1>Chapter Not Found</h1>
                <?php if ($manhwa): ?>
                    <p>Chapter "<?php echo esc_html($chapter_num); ?>" not found in "<?php echo esc_html($manhwa->post_title); ?>"</p>
                    <a href="<?php echo esc_url(get_permalink($manhwa_id)); ?>" class="btn">Back to <?php echo esc_html($manhwa->post_title); ?></a>
                <?php else: ?>
                    <p>The requested chapter could not be found.</p>
                    <a href="<?php echo home_url(); ?>" class="btn">Go to Homepage</a>
                <?php endif; ?>
            </div>
        </body>
        </html>
        <?php
    }
    
    /**
     * Get chapter data from request
     */
    private function get_chapter_data() {
        $manhwa_id = 0;
        $chapter_num = '';
        
        // Get from query vars
        if (get_query_var('manhwa_slug')) {
            $manhwa_slug = sanitize_title(get_query_var('manhwa_slug'));
            $posts = get_posts(array(
                'post_type' => 'manhwa',
                'name' => $manhwa_slug,
                'posts_per_page' => 1,
            ));
            if (!empty($posts)) {
                $manhwa_id = $posts[0]->ID;
            }
            $chapter_num = sanitize_text_field(get_query_var('chapter_num'));
        }
        
        // Get from GET params
        if (isset($_GET['manhwa'])) {
            $manhwa_id = intval($_GET['manhwa']);
        }
        if (isset($_GET['chapter'])) {
            $chapter_num = sanitize_text_field($_GET['chapter']);
        }
        
        if (!$manhwa_id) {
            return null;
        }
        
        // Get manhwa post
        $manhwa = get_post($manhwa_id);
        if (!$manhwa || $manhwa->post_type !== 'manhwa') {
            return null;
        }
        
        // Get chapters
        $chapters = get_post_meta($manhwa_id, '_manhwa_chapters', true);
        if (!is_array($chapters) || empty($chapters)) {
            return null;
        }
        
        // Find the chapter
        $current_chapter = null;
        $chapter_index = -1;
        
        foreach ($chapters as $index => $chapter) {
            // Match by chapter number in title or by index
            if (!empty($chapter_num)) {
                if (preg_match('/chapter\s*' . preg_quote($chapter_num, '/') . '\b/i', $chapter['title'])) {
                    $current_chapter = $chapter;
                    $chapter_index = $index;
                    break;
                }
                // Also try matching just the number
                if (preg_match('/(\d+)/', $chapter['title'], $matches)) {
                    if ($matches[1] == $chapter_num) {
                        $current_chapter = $chapter;
                        $chapter_index = $index;
                        break;
                    }
                }
            }
        }
        
        // If not found by number, try first chapter
        if (!$current_chapter && empty($chapter_num)) {
            $current_chapter = $chapters[0];
            $chapter_index = 0;
        }
        
        if (!$current_chapter) {
            return null;
        }
        
        // Get images
        $images = isset($current_chapter['images']) ? $current_chapter['images'] : array();
        
        // Get prev/next chapters
        $prev_chapter = ($chapter_index > 0) ? $chapters[$chapter_index - 1] : null;
        $next_chapter = ($chapter_index < count($chapters) - 1) ? $chapters[$chapter_index + 1] : null;
        
        return array(
            'manhwa_id' => $manhwa_id,
            'manhwa_title' => $manhwa->post_title,
            'manhwa_url' => get_permalink($manhwa_id),
            'chapter' => $current_chapter,
            'chapter_index' => $chapter_index,
            'images' => $images,
            'prev_chapter' => $prev_chapter,
            'next_chapter' => $next_chapter,
            'total_chapters' => count($chapters),
        );
    }
    
    /**
     * Display chapter reader
     */
    private function display_chapter_reader($data) {
        $manhwa_id = $data['manhwa_id'];
        $manhwa_title = $data['manhwa_title'];
        $chapter = $data['chapter'];
        $images = $data['images'];
        $prev_chapter = $data['prev_chapter'];
        $next_chapter = $data['next_chapter'];
        
        // Get manhwa post for slug
        $manhwa_post = get_post($manhwa_id);
        $manhwa_slug = $manhwa_post ? $manhwa_post->post_name : '';
        
        // Extract chapter number for navigation URLs
        $get_chapter_num = function($ch) {
            if (preg_match('/([\d.]+)/', $ch['title'], $m)) {
                return $m[1];
            }
            return '1';
        };
        
        // Use SEO-friendly URLs
        $prev_url = $prev_chapter ? self::get_chapter_url($manhwa_id, $get_chapter_num($prev_chapter)) : null;
        $next_url = $next_chapter ? self::get_chapter_url($manhwa_id, $get_chapter_num($next_chapter)) : null;
        
        // Get all chapters for dropdown
        $all_chapters = get_post_meta($manhwa_id, '_manhwa_chapters', true);
        $current_ch_num = $get_chapter_num($chapter);
        
        // Get site name
        $site_name = get_bloginfo('name');
        
        // Get current URL safely (PHP 8.x compatible - prevent null value)
        $current_url = self::get_chapter_url($manhwa_id, $current_ch_num);
        
        // SEO data - use clean URL format
        $canonical_url = self::get_chapter_url($manhwa_id, $current_ch_num);
        $seo_title = $manhwa_title . ' ' . $chapter['title'] . ' - ' . $site_name;
        $seo_description = sprintf('Read %s %s online for free. %d pages available. Read the latest chapters of %s on %s.', 
            $manhwa_title, 
            $chapter['title'],
            count($images),
            $manhwa_title,
            $site_name
        );
        $cover_url = get_the_post_thumbnail_url($manhwa_id, 'large');
        
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="robots" content="index, follow, max-image-preview:large">
            <meta name="description" content="<?php echo esc_attr($seo_description); ?>">
            <link rel="canonical" href="<?php echo esc_url($canonical_url); ?>">
            
            <!-- Open Graph -->
            <meta property="og:type" content="article">
            <meta property="og:title" content="<?php echo esc_attr($seo_title); ?>">
            <meta property="og:description" content="<?php echo esc_attr($seo_description); ?>">
            <meta property="og:url" content="<?php echo esc_url($canonical_url); ?>">
            <meta property="og:site_name" content="<?php echo esc_attr($site_name); ?>">
            <?php if ($cover_url): ?>
            <meta property="og:image" content="<?php echo esc_url($cover_url); ?>">
            <?php endif; ?>
            
            <!-- Twitter Card -->
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="<?php echo esc_attr($seo_title); ?>">
            <meta name="twitter:description" content="<?php echo esc_attr($seo_description); ?>">
            <?php if ($cover_url): ?>
            <meta name="twitter:image" content="<?php echo esc_url($cover_url); ?>">
            <?php endif; ?>
            
            <!-- Structured Data -->
            <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "Article",
                "headline": "<?php echo esc_js($manhwa_title . ' ' . $chapter['title']); ?>",
                "description": "<?php echo esc_js($seo_description); ?>",
                "url": "<?php echo esc_url($canonical_url); ?>",
                "mainEntityOfPage": {
                    "@type": "WebPage",
                    "@id": "<?php echo esc_url($canonical_url); ?>"
                },
                "isPartOf": {
                    "@type": "ComicSeries",
                    "name": "<?php echo esc_js($manhwa_title); ?>",
                    "url": "<?php echo esc_url($data['manhwa_url']); ?>"
                },
                "image": "<?php echo $cover_url ? esc_url($cover_url) : ''; ?>",
                "publisher": {
                    "@type": "Organization",
                    "name": "<?php echo esc_js($site_name); ?>",
                    "url": "<?php echo esc_url(home_url()); ?>"
                }
            }
            </script>
            
            <title><?php echo esc_html($seo_title); ?></title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <script>
                // Inline komikAccount for reading history tracking
                var komikAccount = {
                    ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
                    nonce: '<?php echo wp_create_nonce('komik_account_nonce'); ?>',
                    isLoggedIn: <?php echo is_user_logged_in() ? 'true' : 'false'; ?>
                };
            </script>
            <style>
                /* Base Reset */
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                html, body {
                    width: 100%;
                    min-height: 100%;
                    background: #16151d;
                    color: #fff;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                }
                
                /* Header Section */
                .reader-header {
                    background: #1c1b24;
                    padding: 30px 20px;
                    text-align: center;
                }
                .reader-header-inner {
                    max-width: 900px;
                    margin: 0 auto;
                }
                .reader-title h1 {
                    font-size: 24px;
                    font-weight: 600;
                    margin-bottom: 8px;
                    color: #fff;
                }
                .reader-subtitle {
                    font-size: 14px;
                    color: #888;
                    margin-bottom: 20px;
                }
                .reader-subtitle a {
                    color: #366ad3;
                    text-decoration: none;
                }
                .reader-subtitle a:hover {
                    text-decoration: underline;
                }
                
                /* Social Share */
                .reader-share {
                    display: flex;
                    justify-content: center;
                    flex-wrap: wrap;
                    gap: 10px;
                    margin-bottom: 20px;
                }
                .share-btn {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 8px 16px;
                    border-radius: 6px;
                    font-size: 13px;
                    font-weight: 500;
                    text-decoration: none;
                    color: #fff;
                    transition: opacity 0.2s;
                }
                .share-btn:hover {
                    opacity: 0.85;
                }
                .share-btn.facebook { background: #1877f2; }
                .share-btn.twitter { background: #1da1f2; }
                .share-btn.whatsapp { background: #25d366; }
                .share-btn.pinterest { background: #bd081c; }
                .share-btn.telegram { background: #0088cc; }
                
                /* Navigation Bar */
                .reader-nav {
                    max-width: 900px;
                    margin: 20px auto;
                    padding: 0 20px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    gap: 15px;
                }
                .chapter-select {
                    position: relative;
                }
                .chapter-select select {
                    appearance: none;
                    background: #252430;
                    color: #fff;
                    border: 1px solid #444;
                    padding: 12px 40px 12px 15px;
                    border-radius: 8px;
                    font-size: 14px;
                    cursor: pointer;
                    min-width: 150px;
                }
                .chapter-select::after {
                    content: '▼';
                    position: absolute;
                    right: 15px;
                    top: 50%;
                    transform: translateY(-50%);
                    font-size: 10px;
                    color: #888;
                    pointer-events: none;
                }
                .nav-buttons {
                    display: flex;
                    gap: 10px;
                }
                .nav-btn {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 12px 20px;
                    border-radius: 20px;
                    font-size: 13px;
                    font-weight: 500;
                    text-decoration: none;
                    color: #fff;
                    transition: all 0.2s;
                }
                .nav-btn.prev {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                }
                .nav-btn.next {
                    background: linear-gradient(135deg, #444 0%, #333 100%);
                }
                .nav-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                }
                .nav-btn.disabled {
                    opacity: 0.5;
                    pointer-events: none;
                }
                .nav-btn.download {
                    background: linear-gradient(135deg, #c0392b, #e74c3c);
                    border: none;
                    cursor: pointer;
                }
                .nav-btn.download:hover {
                    background: linear-gradient(135deg, #e74c3c, #c0392b);
                }
                
                /* Images */
                .chapter-images {
                    max-width: 900px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .chapter-images img {
                    width: 100%;
                    height: auto;
                    display: block;
                    margin-bottom: 0;
                }
                .no-images {
                    text-align: center;
                    padding: 60px 20px;
                    color: #888;
                }
                .no-images h2 {
                    margin-bottom: 15px;
                    font-size: 24px;
                }
                .no-images a {
                    color: #366ad3;
                }
                
                /* Footer Navigation */
                .reader-footer {
                    background: #1c1b24;
                    padding: 30px 20px;
                }
                .reader-footer-inner {
                    max-width: 900px;
                    margin: 0 auto;
                    display: flex;
                    justify-content: center;
                    flex-wrap: wrap;
                    gap: 15px;
                }
                
                /* PDF Modal */
                .pdf-modal{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.85);display:flex;align-items:center;justify-content:center;z-index:99999;}
                .pdf-modal-box{background:#1c1b24;padding:30px 40px;border-radius:16px;text-align:center;min-width:280px;}
                .pdf-modal-title{font-size:20px;font-weight:700;margin-bottom:15px;}
                .pdf-modal-title i{color:#e74c3c;margin-right:8px;}
                .pdf-modal-text{color:#888;margin-bottom:20px;}
                .pdf-bar{height:10px;background:#333;border-radius:5px;overflow:hidden;}
                .pdf-fill{height:100%;background:linear-gradient(90deg,#3498db,#2ecc71);width:0;transition:width 0.3s;}
                .pdf-count{margin-top:12px;font-size:13px;}
                
                /* Auto-Scroll Controls */
                .autoscroll-panel {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    background: rgba(28, 27, 36, 0.95);
                    border: 1px solid #444;
                    border-radius: 12px;
                    padding: 12px 16px;
                    z-index: 9999;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.4);
                    backdrop-filter: blur(10px);
                }
                .autoscroll-btn {
                    width: 44px;
                    height: 44px;
                    border-radius: 50%;
                    border: none;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: #fff;
                    font-size: 16px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.2s;
                }
                .autoscroll-btn:hover {
                    transform: scale(1.1);
                    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.5);
                }
                .autoscroll-btn.active {
                    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
                }
                .autoscroll-speed {
                    display: flex;
                    flex-direction: column;
                    gap: 4px;
                }
                .autoscroll-speed label {
                    font-size: 11px;
                    color: #888;
                    text-transform: uppercase;
                }
                .autoscroll-speed input[type="range"] {
                    width: 100px;
                    height: 6px;
                    -webkit-appearance: none;
                    background: #333;
                    border-radius: 3px;
                    outline: none;
                }
                .autoscroll-speed input[type="range"]::-webkit-slider-thumb {
                    -webkit-appearance: none;
                    width: 16px;
                    height: 16px;
                    background: #667eea;
                    border-radius: 50%;
                    cursor: pointer;
                }
                .autoscroll-speed-value {
                    font-size: 12px;
                    color: #fff;
                    text-align: center;
                }
                .autoscroll-close {
                    background: transparent;
                    border: none;
                    color: #888;
                    font-size: 18px;
                    cursor: pointer;
                    padding: 4px;
                }
                .autoscroll-close:hover {
                    color: #fff;
                }
                .autoscroll-toggle {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    border: none;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: #fff;
                    font-size: 20px;
                    cursor: pointer;
                    z-index: 9998;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.2s;
                }
                .autoscroll-toggle:hover {
                    transform: scale(1.1);
                }
                .autoscroll-toggle.hidden {
                    display: none;
                }
                .autoscroll-panel.hidden {
                    display: none;
                }
                
                /* Reading Progress Bar */
                .reading-progress {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 0%;
                    height: 3px;
                    background: linear-gradient(90deg, #667eea, #764ba2);
                    z-index: 99999;
                    transition: width 0.1s;
                }
                
                /* Back to Top */
                .back-to-top {
                    position: fixed;
                    bottom: 80px;
                    right: 20px;
                    width: 44px;
                    height: 44px;
                    border-radius: 50%;
                    border: none;
                    background: #333;
                    color: #fff;
                    font-size: 18px;
                    cursor: pointer;
                    z-index: 9997;
                    opacity: 0;
                    visibility: hidden;
                    transition: all 0.2s;
                }
                .back-to-top.visible {
                    opacity: 1;
                    visibility: visible;
                }
                .back-to-top:hover {
                    background: #444;
                }
                
                /* Responsive */
                @media (max-width: 600px) {
                    .reader-title h1 {
                        font-size: 18px;
                    }
                    .reader-share {
                        gap: 6px;
                    }
                    .share-btn {
                        padding: 6px 10px;
                        font-size: 12px;
                    }
                    .share-btn span {
                        display: none;
                    }
                    .reader-nav {
                        flex-direction: column;
                    }
                    .nav-buttons {
                        width: 100%;
                        justify-content: center;
                    }
                }
            </style>
        </head>
        <body>
            <!-- Header -->
            <header class="reader-header">
                <div class="reader-header-inner">
                    <div class="reader-title">
                        <h1><?php echo esc_html($manhwa_title . ' ' . $chapter['title']); ?></h1>
                        <p class="reader-subtitle">All chapters are in <a href="<?php echo esc_url($data['manhwa_url']); ?>"><?php echo esc_html($manhwa_title); ?></a></p>
                    </div>
                    
                    <!-- Social Share -->
                    <div class="reader-share">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($current_url); ?>" target="_blank" class="share-btn facebook"><i class="fab fa-facebook-f"></i> <span>Facebook</span></a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($current_url); ?>&text=<?php echo urlencode($manhwa_title . ' ' . $chapter['title']); ?>" target="_blank" class="share-btn twitter"><i class="fab fa-twitter"></i> <span>Twitter</span></a>
                        <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($manhwa_title . ' ' . $chapter['title'] . ' ' . $current_url); ?>" target="_blank" class="share-btn whatsapp"><i class="fab fa-whatsapp"></i> <span>WhatsApp</span></a>
                        <a href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode($current_url); ?>&description=<?php echo urlencode($manhwa_title . ' ' . $chapter['title']); ?>" target="_blank" class="share-btn pinterest"><i class="fab fa-pinterest"></i> <span>Pinterest</span></a>
                        <a href="https://t.me/share/url?url=<?php echo urlencode($current_url); ?>&text=<?php echo urlencode($manhwa_title . ' ' . $chapter['title']); ?>" target="_blank" class="share-btn telegram"><i class="fab fa-telegram"></i> <span>Telegram</span></a>
                    </div>
                </div>
            </header>
            
            <!-- Navigation -->
            <nav class="reader-nav">
                <div class="chapter-select">
                    <select id="chapterSelect" onchange="if(this.value) window.location.href=this.value;">
                        <?php if (!empty($all_chapters)): foreach ($all_chapters as $ch): 
                            $ch_num = $get_chapter_num($ch);
                            $ch_url = self::get_chapter_url($manhwa_id, $ch_num);
                        ?>
                            <option value="<?php echo esc_url($ch_url); ?>" <?php echo ($ch_num == $current_ch_num) ? 'selected' : ''; ?>><?php echo esc_html($ch['title']); ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="nav-buttons">
                    <a href="<?php echo $prev_url ? esc_url($prev_url) : '#'; ?>" class="nav-btn prev <?php echo !$prev_url ? 'disabled' : ''; ?>"><i class="fas fa-chevron-left"></i> Prev</a>
                    <button type="button" class="nav-btn download" id="dlPdfBtn"><i class="fas fa-file-pdf"></i> PDF</button>
                    <a href="<?php echo $next_url ? esc_url($next_url) : '#'; ?>" class="nav-btn next <?php echo !$next_url ? 'disabled' : ''; ?>">Next <i class="fas fa-chevron-right"></i></a>
                </div>
            </nav>
            
            <!-- Chapter Images -->
            <main class="chapter-images" id="chapterImgs">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $img): 
                        $img_url = is_array($img) ? ($img['url'] ?? '') : $img;
                        if (!empty($img_url)):
                    ?>
                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($chapter['title']); ?>" loading="lazy">
                    <?php endif; endforeach; ?>
                <?php else: ?>
                    <div class="no-images">
                        <h2>📖 Maaf, Gambar Belum Diupload</h2>
                        <p>Chapter ini belum memiliki gambar.</p>
                        <p>Silakan kembali lagi nanti atau baca chapter lainnya.</p>
                    </div>
                <?php endif; ?>
            </main>
            
            <!-- Footer Navigation -->
            <footer class="reader-footer">
                <div class="reader-footer-inner">
                    <a href="<?php echo $prev_url ? esc_url($prev_url) : '#'; ?>" class="nav-btn prev <?php echo !$prev_url ? 'disabled' : ''; ?>"><i class="fas fa-chevron-left"></i> Prev</a>
                    <button type="button" class="nav-btn download" id="dlPdfBtn2"><i class="fas fa-file-pdf"></i> PDF</button>
                    <a href="<?php echo esc_url($data['manhwa_url']); ?>" class="nav-btn" style="background: #444;">List</a>
                    <a href="<?php echo $next_url ? esc_url($next_url) : '#'; ?>" class="nav-btn next <?php echo !$next_url ? 'disabled' : ''; ?>">Next Chapter <i class="fas fa-chevron-right"></i></a>
                </div>
            </footer>
            
            <!-- Reading Progress Bar -->
            <div class="reading-progress" id="readingProgress"></div>
            
            <!-- Back to Top Button -->
            <button class="back-to-top" id="backToTop" title="Back to Top">
                <i class="fas fa-chevron-up"></i>
            </button>
            
            <!-- Auto-Scroll Toggle Button -->
            <button class="autoscroll-toggle" id="autoscrollToggle" title="Auto Scroll">
                <i class="fas fa-scroll"></i>
            </button>
            
            <!-- Auto-Scroll Control Panel -->
            <div class="autoscroll-panel hidden" id="autoscrollPanel">
                <button class="autoscroll-btn" id="autoscrollBtn" title="Play/Pause">
                    <i class="fas fa-play"></i>
                </button>
                <div class="autoscroll-speed">
                    <label>Speed</label>
                    <input type="range" id="autoscrollSpeed" min="1" max="10" value="3">
                    <span class="autoscroll-speed-value" id="speedValue">3x</span>
                </div>
                <button class="autoscroll-close" id="autoscrollClose" title="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
            <script>
            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                <?php if ($prev_url): ?>
                if (e.keyCode === 37) window.location.href = '<?php echo esc_url($prev_url); ?>';
                <?php endif; ?>
                <?php if ($next_url): ?>
                if (e.keyCode === 39) window.location.href = '<?php echo esc_url($next_url); ?>';
                <?php endif; ?>
                // Spacebar to toggle auto-scroll
                if (e.keyCode === 32 && e.target.tagName !== 'INPUT') {
                    e.preventDefault();
                    document.getElementById('autoscrollBtn').click();
                }
            });
            
            // ===== AUTO-SCROLL FUNCTIONALITY =====
            (function() {
                var isScrolling = false;
                var scrollSpeed = 3;
                var scrollInterval = null;
                
                var toggleBtn = document.getElementById('autoscrollToggle');
                var panel = document.getElementById('autoscrollPanel');
                var playBtn = document.getElementById('autoscrollBtn');
                var closeBtn = document.getElementById('autoscrollClose');
                var speedSlider = document.getElementById('autoscrollSpeed');
                var speedValue = document.getElementById('speedValue');
                var progressBar = document.getElementById('readingProgress');
                var backToTop = document.getElementById('backToTop');
                
                // Toggle panel
                toggleBtn.addEventListener('click', function() {
                    panel.classList.remove('hidden');
                    toggleBtn.classList.add('hidden');
                });
                
                closeBtn.addEventListener('click', function() {
                    panel.classList.add('hidden');
                    toggleBtn.classList.remove('hidden');
                    stopScroll();
                });
                
                // Play/Pause
                playBtn.addEventListener('click', function() {
                    if (isScrolling) {
                        stopScroll();
                    } else {
                        startScroll();
                    }
                });
                
                // Speed control
                speedSlider.addEventListener('input', function() {
                    scrollSpeed = parseInt(this.value);
                    speedValue.textContent = scrollSpeed + 'x';
                    if (isScrolling) {
                        stopScroll();
                        startScroll();
                    }
                });
                
                function startScroll() {
                    isScrolling = true;
                    playBtn.classList.add('active');
                    playBtn.innerHTML = '<i class="fas fa-pause"></i>';
                    
                    // Calculate scroll amount based on speed (1-10)
                    var scrollAmount = scrollSpeed * 0.5;
                    
                    scrollInterval = setInterval(function() {
                        window.scrollBy(0, scrollAmount);
                        
                        // Stop at bottom
                        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
                            stopScroll();
                        }
                    }, 16); // ~60fps
                }
                
                function stopScroll() {
                    isScrolling = false;
                    playBtn.classList.remove('active');
                    playBtn.innerHTML = '<i class="fas fa-play"></i>';
                    if (scrollInterval) {
                        clearInterval(scrollInterval);
                        scrollInterval = null;
                    }
                }
                
                // Reading Progress Bar
                function updateProgress() {
                    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    var scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
                    var progress = (scrollTop / scrollHeight) * 100;
                    progressBar.style.width = Math.min(progress, 100) + '%';
                    
                    // Show/hide back to top button
                    if (scrollTop > 500) {
                        backToTop.classList.add('visible');
                    } else {
                        backToTop.classList.remove('visible');
                    }
                }
                
                window.addEventListener('scroll', updateProgress);
                updateProgress();
                
                // Back to Top
                backToTop.addEventListener('click', function() {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
                
                // Stop auto-scroll on manual scroll (touch/mouse)
                var lastScrollTop = window.pageYOffset;
                window.addEventListener('wheel', function() {
                    if (isScrolling) stopScroll();
                });
                window.addEventListener('touchmove', function() {
                    if (isScrolling) stopScroll();
                });
            })();
            
            // PDF Download - Webtoon Format (vertical continuous)
            (function(){
                var title = <?php echo json_encode($manhwa_title . ' ' . $chapter['title']); ?>;
                var siteHost = window.location.hostname;
                var busy = false;
                
                function dlPDF() {
                    if(busy) return;
                    busy = true;
                    var imgs = document.querySelectorAll('#chapterImgs img');
                    if(!imgs.length){alert('No images!');busy=false;return;}
                    
                    // Check if images are local or external
                    var externalCount = 0;
                    var localCount = 0;
                    imgs.forEach(function(img) {
                        try {
                            var imgHost = new URL(img.src).hostname;
                            if (imgHost === siteHost || img.src.startsWith('/') || img.src.startsWith(window.location.origin)) {
                                localCount++;
                            } else {
                                externalCount++;
                            }
                        } catch(e) {
                            localCount++; // Relative URLs are local
                        }
                    });
                    
                    // If all images are external, show warning and cancel
                    if (externalCount > 0 && localCount === 0) {
                        alert('⚠️ Maaf, Download PDF belum tersedia untuk chapter ini.\n\nSilakan baca chapter secara online atau coba lagi nanti.');
                        busy = false;
                        return;
                    }
                    
                    // If mixed (some local, some external), show warning but continue
                    if (externalCount > 0 && localCount > 0) {
                        var proceed = confirm('⚠️ Peringatan!\n\nBeberapa gambar mungkin tidak bisa dimuat ke PDF.\nPDF akan dibuat dengan gambar yang tersedia.\n\nLanjutkan?');
                        if (!proceed) {
                            busy = false;
                            return;
                        }
                    }
                    
                    var m = document.createElement('div');
                    m.className = 'pdf-modal';
                    m.innerHTML = '<div class="pdf-modal-box"><div class="pdf-modal-title"><i class="fas fa-file-pdf"></i> Creating Webtoon PDF</div><div class="pdf-modal-text">Loading images...</div><div class="pdf-bar"><div class="pdf-fill" id="pFill"></div></div><div class="pdf-count" id="pCnt">0/'+imgs.length+'</div></div>';
                    document.body.appendChild(m);
                    
                    var arr = Array.from(imgs), cnt = 0;
                    
                    // Load all images first
                    function load(el,i){
                        return new Promise(function(r){
                            var im=new Image();
                            im.crossOrigin='anonymous';
                            im.onload=function(){
                                cnt++;
                                document.getElementById('pFill').style.width=(cnt/arr.length*50)+'%';
                                document.getElementById('pCnt').textContent='Loading: '+cnt+'/'+arr.length;
                                r({img:im,i:i,w:im.width,h:im.height});
                            };
                            im.onerror=function(){cnt++;r(null);};
                            im.src=el.src;
                        });
                    }
                    
                    Promise.all(arr.map(function(e,i){return load(e,i);})).then(function(res){
                        var ld = res.filter(function(x){return x;}).sort(function(a,b){return a.i-b.i;});
                        if(!ld.length){alert('Load failed');m.remove();busy=false;return;}
                        
                        document.querySelector('.pdf-modal-text').textContent='Creating webtoon PDF...';
                        
                        // Webtoon format: fixed width, variable height per page
                        var pdfWidth = 210; // A4 width in mm
                        var margin = 5;
                        var contentWidth = pdfWidth - (margin * 2);
                        
                        // Calculate total height needed
                        var totalHeight = 0;
                        var imgData = [];
                        
                        ld.forEach(function(item) {
                            var ratio = item.h / item.w;
                            var imgWidth = contentWidth;
                            var imgHeight = imgWidth * ratio;
                            imgData.push({img: item.img, width: imgWidth, height: imgHeight});
                            totalHeight += imgHeight;
                        });
                        
                        // Create PDF with webtoon style - multiple pages if needed
                        // Max page height = 3000mm (jsPDF limit is around 14400mm)
                        var maxPageHeight = 2000;
                        var {jsPDF} = window.jspdf;
                        
                        var currentY = margin;
                        var pageNum = 0;
                        var pages = [[]]; // Array of pages, each containing image positions
                        
                        // Distribute images across pages
                        imgData.forEach(function(item, i) {
                            if (currentY + item.height > maxPageHeight && pages[pageNum].length > 0) {
                                // Start new page
                                pageNum++;
                                pages[pageNum] = [];
                                currentY = margin;
                            }
                            pages[pageNum].push({
                                img: item.img,
                                y: currentY,
                                width: item.width,
                                height: item.height
                            });
                            currentY += item.height;
                        });
                        
                        // Create PDF
                        var pdf = null;
                        var ad = 0;
                        
                        pages.forEach(function(pageItems, pIdx) {
                            // Calculate page height
                            var pageHeight = margin;
                            pageItems.forEach(function(item) {
                                pageHeight = Math.max(pageHeight, item.y + item.height + margin);
                            });
                            
                            if (pIdx === 0) {
                                pdf = new jsPDF({
                                    orientation: 'portrait',
                                    unit: 'mm',
                                    format: [pdfWidth, pageHeight]
                                });
                            } else {
                                pdf.addPage([pdfWidth, pageHeight], 'portrait');
                            }
                            
                            // Add images to this page
                            pageItems.forEach(function(item) {
                                try {
                                    pdf.addImage(item.img, 'JPEG', margin, item.y, item.width, item.height);
                                    ad++;
                                } catch(e) {
                                    console.log('Error adding image:', e);
                                }
                                document.getElementById('pFill').style.width=(50+ad/ld.length*50)+'%';
                                document.getElementById('pCnt').textContent='Processing: '+ad+'/'+ld.length;
                            });
                        });
                        
                        pdf.save(title.replace(/[^a-z0-9]/gi,'_').substring(0,50)+'.pdf');
                        document.querySelector('.pdf-modal-text').textContent='Done!';
                        document.getElementById('pFill').style.width='100%';
                        document.getElementById('pCnt').textContent='Webtoon PDF ready!';
                        setTimeout(function(){m.remove();busy=false;},1500);
                    });
                }
                document.getElementById('dlPdfBtn').onclick = dlPDF;
                document.getElementById('dlPdfBtn2').onclick = dlPDF;
            })();
            
            // Track reading history for logged-in users
            (function() {
                var komikAccount = window.komikAccount || null;
                
                // If user is logged in and komikAccount is available, track reading
                if (komikAccount && komikAccount.isLoggedIn) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', komikAccount.ajaxUrl, true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.send(
                        'action=komik_add_reading_history' +
                        '&nonce=' + encodeURIComponent(komikAccount.nonce) +
                        '&manhwa_id=<?php echo $manhwa_id; ?>' +
                        '&chapter=' + encodeURIComponent('<?php echo esc_js($chapter['title']); ?>') +
                        '&chapter_url=' + encodeURIComponent(window.location.href)
                    );
                }
                
                // Also save to localStorage for guests/backup
                try {
                    var history = JSON.parse(localStorage.getItem('komik_reading_history') || '[]');
                    // Remove existing entry for this manhwa
                    history = history.filter(function(item) {
                        return item.manhwa_id !== <?php echo $manhwa_id; ?>;
                    });
                    // Add new entry
                    history.unshift({
                        manhwa_id: <?php echo $manhwa_id; ?>,
                        manhwa_title: <?php echo json_encode($manhwa_title); ?>,
                        manhwa_url: <?php echo json_encode($data['manhwa_url']); ?>,
                        chapter: <?php echo json_encode($chapter['title']); ?>,
                        chapter_url: window.location.href,
                        read_at: new Date().toISOString()
                    });
                    // Keep only last 50
                    history = history.slice(0, 50);
                    localStorage.setItem('komik_reading_history', JSON.stringify(history));
                } catch(e) {}
            })();
            </script>
            
            <?php wp_footer(); ?>
            
        </body>
        </html>
        <?php
    }
    
    /**
     * Chapter shortcode
     */
    public function chapter_shortcode($atts) {
        $atts = shortcode_atts(array(
            'manhwa_id' => 0,
            'chapter' => '',
        ), $atts);
        
        if (!$atts['manhwa_id']) {
            return '<p>No manhwa specified.</p>';
        }
        
        $_GET['manhwa'] = $atts['manhwa_id'];
        $_GET['chapter'] = $atts['chapter'];
        
        $data = $this->get_chapter_data();
        
        if (!$data) {
            return '<p>Chapter not found.</p>';
        }
        
        ob_start();
        ?>
        <div class="manhwa-chapter-reader">
            <?php foreach ($data['images'] as $img): 
                $img_url = is_array($img) ? ($img['url'] ?? '') : $img;
            ?>
                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($data['chapter']['title']); ?>" style="width: 100%; height: auto;">
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * AJAX get chapter images
     */
    public function ajax_get_chapter_images() {
        $manhwa_id = isset($_GET['manhwa_id']) ? intval($_GET['manhwa_id']) : 0;
        $chapter_num = isset($_GET['chapter']) ? sanitize_text_field($_GET['chapter']) : '';
        
        if (!$manhwa_id) {
            wp_send_json_error(array('message' => 'Invalid manhwa ID'));
        }
        
        $_GET['manhwa'] = $manhwa_id;
        $_GET['chapter'] = $chapter_num;
        
        $data = $this->get_chapter_data();
        
        if (!$data) {
            wp_send_json_error(array('message' => 'Chapter not found'));
        }
        
        wp_send_json_success(array(
            'chapter' => $data['chapter'],
            'images' => $data['images'],
            'prev' => $data['prev_chapter'],
            'next' => $data['next_chapter'],
        ));
    }
}
