<?php
/**
 * Plugin Name: Manhwa Manager
 * Plugin URI: http://localhost/wordpress/
 * Description: Plugin untuk mengelola list komik dan detail manhwa dengan export/import JSON
 * Version: 1.0.0
 * Author: Manhwa Reader
 * Author URI: http://localhost/
 * Text Domain: manhwa-manager
 * License: GPL v2 or later
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MANHWA_MANAGER_VERSION', '1.0.0');
define('MANHWA_MANAGER_PATH', plugin_dir_path(__FILE__));
define('MANHWA_MANAGER_URL', plugin_dir_url(__FILE__));

// Include required files
require_once MANHWA_MANAGER_PATH . 'includes/class-manhwa-post-type.php';
require_once MANHWA_MANAGER_PATH . 'includes/class-manhwa-admin.php';
require_once MANHWA_MANAGER_PATH . 'includes/class-manhwa-json-handler.php';
require_once MANHWA_MANAGER_PATH . 'includes/class-manhwa-chapter-reader.php';
require_once MANHWA_MANAGER_PATH . 'includes/class-manhwa-custom-edit.php';

/**
 * Main Plugin Class
 */
class Manhwa_Manager {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Activation & Deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Initialize components
        add_action('plugins_loaded', array($this, 'init'));
        
        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    public function init() {
        // Initialize custom post type
        new Manhwa_Post_Type();
        
        // Initialize admin pages
        new Manhwa_Admin();
        
        // Initialize JSON handler
        new Manhwa_JSON_Handler();
        
        // Initialize chapter reader
        new Manhwa_Chapter_Reader();
        
        // One-time flush for new rewrite rules (chapter SEO URLs)
        if (is_admin() && !get_option('manhwa_manager_flush_v2')) {
            add_action('admin_init', function() {
                flush_rewrite_rules();
                update_option('manhwa_manager_flush_v2', true);
            });
        }
    }
    
    public function activate() {
        // Create custom post type
        $post_type = new Manhwa_Post_Type();
        $post_type->register_post_type();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Create uploads directory for manhwa
        $upload_dir = wp_upload_dir();
        $manhwa_dir = $upload_dir['basedir'] . '/manhwa';
        
        if (!file_exists($manhwa_dir)) {
            wp_mkdir_p($manhwa_dir);
        }
    }
    
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    public function enqueue_admin_scripts($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'manhwa-manager') === false) {
            return;
        }
        
        // Enqueue WordPress media uploader
        wp_enqueue_media();
        
        // Enqueue custom CSS
        wp_enqueue_style(
            'manhwa-manager-admin',
            MANHWA_MANAGER_URL . 'assets/css/admin.css',
            array(),
            MANHWA_MANAGER_VERSION
        );
        
        // Enqueue custom JS
        wp_enqueue_script(
            'manhwa-manager-admin',
            MANHWA_MANAGER_URL . 'assets/js/admin.js',
            array('jquery'),
            MANHWA_MANAGER_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('manhwa-manager-admin', 'manhwaManager', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('manhwa_manager_nonce')
        ));
    }
}

// Initialize plugin
function manhwa_manager_init() {
    return Manhwa_Manager::get_instance();
}

// Start the plugin
manhwa_manager_init();
