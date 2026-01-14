<?php
/**
 * User Account System
 * Handles user registration, login, and profile
 * 
 * @package Komik_Starter
 */

if (!defined('ABSPATH')) {
    exit;
}

class Komik_User_Account {
    
    private static $instance = null;
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // AJAX handlers
        add_action('wp_ajax_nopriv_komik_login', [$this, 'ajax_login']);
        add_action('wp_ajax_nopriv_komik_register', [$this, 'ajax_register']);
        add_action('wp_ajax_komik_update_profile', [$this, 'ajax_update_profile']);
        add_action('wp_ajax_komik_update_password', [$this, 'ajax_update_password']);
        add_action('wp_ajax_komik_update_avatar', [$this, 'ajax_update_avatar']);
        
        // Bookmark AJAX handlers
        add_action('wp_ajax_komik_add_bookmark', [$this, 'ajax_add_bookmark']);
        add_action('wp_ajax_komik_remove_bookmark', [$this, 'ajax_remove_bookmark']);
        add_action('wp_ajax_komik_get_bookmarks', [$this, 'ajax_get_bookmarks']);
        add_action('wp_ajax_komik_check_bookmark', [$this, 'ajax_check_bookmark']);
        
        // Reading history AJAX handlers
        add_action('wp_ajax_komik_add_reading_history', [$this, 'ajax_add_reading_history']);
        add_action('wp_ajax_komik_get_reading_history', [$this, 'ajax_get_reading_history']);
        
        // Enqueue scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        
        // Redirect logged in users from login/register pages
        add_action('template_redirect', [$this, 'redirect_logged_in_users']);
        
        // Add user meta fields
        add_action('user_register', [$this, 'save_user_meta']);
        
        // Block subscribers from wp-admin
        add_action('admin_init', [$this, 'block_wp_admin_for_subscribers']);
        
        // Redirect subscribers from wp-login.php to frontend login
        add_action('login_init', [$this, 'redirect_login_page']);
        
        // Hide admin bar for subscribers
        add_action('after_setup_theme', [$this, 'hide_admin_bar_for_subscribers']);
        
        // Redirect after login based on role
        add_filter('login_redirect', [$this, 'login_redirect'], 10, 3);
        
        // Redirect after logout
        add_action('wp_logout', [$this, 'logout_redirect']);
        
        // Auto-approve comments from logged-in users
        add_filter('pre_comment_approved', [$this, 'auto_approve_comments'], 10, 2);
    }
    
    /**
     * AJAX Add Bookmark
     */
    public function ajax_add_bookmark() {
        check_ajax_referer('komik_account_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => __('Please login first.', 'komik-starter')]);
        }
        
        $user_id = get_current_user_id();
        $manhwa_id = intval($_POST['manhwa_id']);
        
        if (!$manhwa_id || get_post_type($manhwa_id) !== 'manhwa') {
            wp_send_json_error(['message' => __('Invalid manhwa.', 'komik-starter')]);
        }
        
        $bookmarks = get_user_meta($user_id, 'komik_bookmarks', true);
        if (!is_array($bookmarks)) {
            $bookmarks = [];
        }
        
        // Add to beginning if not already bookmarked
        if (!in_array($manhwa_id, $bookmarks)) {
            array_unshift($bookmarks, $manhwa_id);
            update_user_meta($user_id, 'komik_bookmarks', $bookmarks);
        }
        
        wp_send_json_success([
            'message' => __('Bookmark added!', 'komik-starter'),
            'count' => count($bookmarks)
        ]);
    }
    
    /**
     * AJAX Remove Bookmark
     */
    public function ajax_remove_bookmark() {
        check_ajax_referer('komik_account_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => __('Please login first.', 'komik-starter')]);
        }
        
        $user_id = get_current_user_id();
        $manhwa_id = intval($_POST['manhwa_id']);
        
        $bookmarks = get_user_meta($user_id, 'komik_bookmarks', true);
        if (!is_array($bookmarks)) {
            $bookmarks = [];
        }
        
        // Remove from bookmarks
        $bookmarks = array_filter($bookmarks, function($id) use ($manhwa_id) {
            return $id !== $manhwa_id;
        });
        $bookmarks = array_values($bookmarks); // Re-index
        
        update_user_meta($user_id, 'komik_bookmarks', $bookmarks);
        
        wp_send_json_success([
            'message' => __('Bookmark removed!', 'komik-starter'),
            'count' => count($bookmarks)
        ]);
    }
    
    /**
     * AJAX Get Bookmarks
     */
    public function ajax_get_bookmarks() {
        check_ajax_referer('komik_account_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => __('Please login first.', 'komik-starter')]);
        }
        
        $user_id = get_current_user_id();
        $bookmarks = get_user_meta($user_id, 'komik_bookmarks', true);
        
        if (!is_array($bookmarks)) {
            $bookmarks = [];
        }
        
        wp_send_json_success(['bookmarks' => $bookmarks]);
    }
    
    /**
     * AJAX Check if Bookmarked
     */
    public function ajax_check_bookmark() {
        check_ajax_referer('komik_account_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_success(['bookmarked' => false, 'logged_in' => false]);
        }
        
        $user_id = get_current_user_id();
        $manhwa_id = intval($_POST['manhwa_id']);
        
        $bookmarks = get_user_meta($user_id, 'komik_bookmarks', true);
        if (!is_array($bookmarks)) {
            $bookmarks = [];
        }
        
        wp_send_json_success([
            'bookmarked' => in_array($manhwa_id, $bookmarks),
            'logged_in' => true
        ]);
    }
    
    /**
     * AJAX Add Reading History
     */
    public function ajax_add_reading_history() {
        check_ajax_referer('komik_account_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => __('Please login first.', 'komik-starter')]);
        }
        
        $user_id = get_current_user_id();
        $manhwa_id = intval($_POST['manhwa_id']);
        $chapter = sanitize_text_field($_POST['chapter']);
        $chapter_url = esc_url_raw($_POST['chapter_url']);
        
        if (!$manhwa_id) {
            wp_send_json_error(['message' => __('Invalid data.', 'komik-starter')]);
        }
        
        $history = get_user_meta($user_id, 'komik_reading_history', true);
        if (!is_array($history)) {
            $history = [];
        }
        
        // Remove existing entry for this manhwa
        $history = array_filter($history, function($item) use ($manhwa_id) {
            return $item['manhwa_id'] !== $manhwa_id;
        });
        
        // Add new entry at the beginning
        array_unshift($history, [
            'manhwa_id' => $manhwa_id,
            'chapter' => $chapter,
            'chapter_url' => $chapter_url,
            'read_at' => current_time('mysql'),
        ]);
        
        // Keep only last 50 entries
        $history = array_slice($history, 0, 50);
        
        update_user_meta($user_id, 'komik_reading_history', $history);
        
        wp_send_json_success([
            'message' => __('History updated!', 'komik-starter'),
            'count' => count($history)
        ]);
    }
    
    /**
     * AJAX Get Reading History
     */
    public function ajax_get_reading_history() {
        check_ajax_referer('komik_account_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => __('Please login first.', 'komik-starter')]);
        }
        
        $user_id = get_current_user_id();
        $history = get_user_meta($user_id, 'komik_reading_history', true);
        
        if (!is_array($history)) {
            $history = [];
        }
        
        wp_send_json_success(['history' => $history]);
    }
    
    /**
     * Auto-approve comments from logged-in users
     */
    public function auto_approve_comments($approved, $commentdata) {
        // If user is logged in, auto-approve their comment
        if (is_user_logged_in()) {
            return 1; // 1 = approved
        }
        
        // For guests, use default WordPress behavior
        return $approved;
    }

    /**
     * Block subscribers from accessing wp-admin
     */
    public function block_wp_admin_for_subscribers() {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return; // Allow AJAX requests
        }
        
        if (current_user_can('subscriber') && !current_user_can('edit_posts')) {
            wp_redirect(home_url('/profile/'));
            exit;
        }
    }
    
    /**
     * Redirect wp-login.php to frontend login page for non-admins
     */
    public function redirect_login_page() {
        // Allow logout action
        if (isset($_GET['action']) && $_GET['action'] === 'logout') {
            return;
        }
        
        // Allow password reset
        if (isset($_GET['action']) && in_array($_GET['action'], ['lostpassword', 'rp', 'resetpass'])) {
            return;
        }
        
        // If user is already logged in, redirect to profile
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            if (in_array('subscriber', $user->roles)) {
                wp_redirect(home_url('/profile/'));
                exit;
            }
        }
        
        // Redirect non-logged in users to frontend login
        // But allow admins and editors to use wp-login
        if (!is_user_logged_in() && !isset($_GET['action'])) {
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            // Only redirect if not coming from wp-admin
            if (strpos($referer, 'wp-admin') === false) {
                wp_redirect(home_url('/login/'));
                exit;
            }
        }
    }
    
    /**
     * Hide admin bar for subscribers
     */
    public function hide_admin_bar_for_subscribers() {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            if (in_array('subscriber', $user->roles)) {
                show_admin_bar(false);
            }
        }
    }
    
    /**
     * Redirect users after login based on role
     */
    public function login_redirect($redirect_to, $requested_redirect_to, $user) {
        if (isset($user->roles) && is_array($user->roles)) {
            if (in_array('subscriber', $user->roles)) {
                return home_url('/profile/');
            }
        }
        return $redirect_to;
    }
    
    /**
     * Redirect after logout
     */
    public function logout_redirect() {
        wp_redirect(home_url('/'));
        exit;
    }
    
    /**
     * Enqueue scripts for account pages
     */
    public function enqueue_scripts() {
        // Localize account data for all pages (needed for bookmark/history)
        wp_localize_script('komik-starter-script', 'komikAccount', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('komik_account_nonce'),
            'homeUrl' => home_url('/'),
            'profileUrl' => home_url('/profile/'),
            'loginUrl' => home_url('/login/'),
            'isLoggedIn' => is_user_logged_in(),
            'strings' => [
                'loading' => __('Loading...', 'komik-starter'),
                'success' => __('Success!', 'komik-starter'),
                'error' => __('Error occurred', 'komik-starter'),
                'bookmarked' => __('Bookmarked', 'komik-starter'),
                'bookmark' => __('Bookmark', 'komik-starter'),
                'loginRequired' => __('Please login to bookmark', 'komik-starter'),
            ]
        ]);
        
        // Enqueue account.js only on account pages
        if (is_page(['login', 'register', 'profile', 'my-account'])) {
            wp_enqueue_script('komik-account', get_template_directory_uri() . '/assets/js/account.js', ['jquery'], '1.0.0', true);
        }
    }
    
    /**
     * Redirect logged in users from login/register pages
     */
    public function redirect_logged_in_users() {
        if (is_user_logged_in() && is_page(['login', 'register'])) {
            wp_redirect(home_url('/profile/'));
            exit;
        }
    }
    
    /**
     * AJAX Login Handler
     */
    public function ajax_login() {
        check_ajax_referer('komik_account_nonce', 'nonce');
        
        $username = sanitize_user($_POST['username']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']) && $_POST['remember'] === 'true';
        
        if (empty($username) || empty($password)) {
            wp_send_json_error(['message' => __('Please fill in all fields.', 'komik-starter')]);
        }
        
        // Check if username is email
        if (is_email($username)) {
            $user = get_user_by('email', $username);
            if ($user) {
                $username = $user->user_login;
            }
        }
        
        $creds = [
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => $remember,
        ];
        
        $user = wp_signon($creds, is_ssl());
        
        if (is_wp_error($user)) {
            wp_send_json_error(['message' => __('Invalid username or password.', 'komik-starter')]);
        }
        
        wp_send_json_success([
            'message' => __('Login successful! Redirecting...', 'komik-starter'),
            'redirect' => home_url('/profile/')
        ]);
    }
    
    /**
     * AJAX Register Handler
     */
    public function ajax_register() {
        check_ajax_referer('komik_account_nonce', 'nonce');
        
        // Check if registration is allowed
        if (!get_option('users_can_register')) {
            wp_send_json_error(['message' => __('Registration is currently disabled.', 'komik-starter')]);
        }
        
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        
        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            wp_send_json_error(['message' => __('Please fill in all fields.', 'komik-starter')]);
        }
        
        if (!is_email($email)) {
            wp_send_json_error(['message' => __('Please enter a valid email address.', 'komik-starter')]);
        }
        
        if (strlen($password) < 6) {
            wp_send_json_error(['message' => __('Password must be at least 6 characters.', 'komik-starter')]);
        }
        
        if ($password !== $password_confirm) {
            wp_send_json_error(['message' => __('Passwords do not match.', 'komik-starter')]);
        }
        
        if (username_exists($username)) {
            wp_send_json_error(['message' => __('Username already exists.', 'komik-starter')]);
        }
        
        if (email_exists($email)) {
            wp_send_json_error(['message' => __('Email already registered.', 'komik-starter')]);
        }
        
        // Validate username
        if (!validate_username($username)) {
            wp_send_json_error(['message' => __('Invalid username. Use only letters, numbers, and underscores.', 'komik-starter')]);
        }
        
        // Create user
        $user_id = wp_create_user($username, $password, $email);
        
        if (is_wp_error($user_id)) {
            wp_send_json_error(['message' => $user_id->get_error_message()]);
        }
        
        // Set user role
        wp_update_user([
            'ID' => $user_id,
            'role' => 'subscriber'
        ]);
        
        // Auto login
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);
        
        wp_send_json_success([
            'message' => __('Registration successful! Redirecting...', 'komik-starter'),
            'redirect' => home_url('/profile/')
        ]);
    }
    
    /**
     * AJAX Update Profile Handler
     */
    public function ajax_update_profile() {
        check_ajax_referer('komik_account_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => __('Please login first.', 'komik-starter')]);
        }
        
        $user_id = get_current_user_id();
        $display_name = sanitize_text_field($_POST['display_name']);
        $email = sanitize_email($_POST['email']);
        $bio = sanitize_textarea_field($_POST['bio']);
        
        // Validation
        if (empty($display_name) || empty($email)) {
            wp_send_json_error(['message' => __('Display name and email are required.', 'komik-starter')]);
        }
        
        if (!is_email($email)) {
            wp_send_json_error(['message' => __('Please enter a valid email address.', 'komik-starter')]);
        }
        
        // Check if email already used by another user
        $existing = get_user_by('email', $email);
        if ($existing && $existing->ID !== $user_id) {
            wp_send_json_error(['message' => __('Email already used by another account.', 'komik-starter')]);
        }
        
        // Update user
        $result = wp_update_user([
            'ID' => $user_id,
            'display_name' => $display_name,
            'user_email' => $email,
            'description' => $bio,
        ]);
        
        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }
        
        wp_send_json_success(['message' => __('Profile updated successfully!', 'komik-starter')]);
    }
    
    /**
     * AJAX Update Password Handler
     */
    public function ajax_update_password() {
        check_ajax_referer('komik_account_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => __('Please login first.', 'komik-starter')]);
        }
        
        $user_id = get_current_user_id();
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validation
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            wp_send_json_error(['message' => __('Please fill in all password fields.', 'komik-starter')]);
        }
        
        // Verify current password
        $user = get_user_by('id', $user_id);
        if (!wp_check_password($current_password, $user->user_pass, $user_id)) {
            wp_send_json_error(['message' => __('Current password is incorrect.', 'komik-starter')]);
        }
        
        if (strlen($new_password) < 6) {
            wp_send_json_error(['message' => __('New password must be at least 6 characters.', 'komik-starter')]);
        }
        
        if ($new_password !== $confirm_password) {
            wp_send_json_error(['message' => __('New passwords do not match.', 'komik-starter')]);
        }
        
        // Update password
        wp_set_password($new_password, $user_id);
        
        // Re-login user
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);
        
        wp_send_json_success(['message' => __('Password updated successfully!', 'komik-starter')]);
    }
    
    /**
     * AJAX Update Avatar Handler
     */
    public function ajax_update_avatar() {
        check_ajax_referer('komik_account_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => __('Please login first.', 'komik-starter')]);
        }
        
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(['message' => __('Please select an image to upload.', 'komik-starter')]);
        }
        
        $user_id = get_current_user_id();
        $file = $_FILES['avatar'];
        
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowed_types)) {
            wp_send_json_error(['message' => __('Invalid file type. Please upload JPG, PNG, GIF, or WebP.', 'komik-starter')]);
        }
        
        // Validate file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            wp_send_json_error(['message' => __('File too large. Maximum size is 2MB.', 'komik-starter')]);
        }
        
        // Upload file
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        
        $attachment_id = media_handle_upload('avatar', 0);
        
        if (is_wp_error($attachment_id)) {
            wp_send_json_error(['message' => $attachment_id->get_error_message()]);
        }
        
        // Save avatar ID to user meta
        update_user_meta($user_id, 'komik_avatar', $attachment_id);
        
        $avatar_url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
        
        wp_send_json_success([
            'message' => __('Avatar updated successfully!', 'komik-starter'),
            'avatar_url' => $avatar_url
        ]);
    }
    
    /**
     * Save user meta on registration
     */
    public function save_user_meta($user_id) {
        update_user_meta($user_id, 'komik_joined', current_time('mysql'));
        update_user_meta($user_id, 'komik_bookmarks', []);
        update_user_meta($user_id, 'komik_reading_history', []);
    }
    
    /**
     * Get user avatar
     */
    public static function get_avatar($user_id = null, $size = 96) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $custom_avatar = get_user_meta($user_id, 'komik_avatar', true);
        
        if ($custom_avatar) {
            $avatar_url = wp_get_attachment_image_url($custom_avatar, 'thumbnail');
            if ($avatar_url) {
                return '<img src="' . esc_url($avatar_url) . '" alt="Avatar" class="avatar" width="' . $size . '" height="' . $size . '" />';
            }
        }
        
        return get_avatar($user_id, $size);
    }
    
    /**
     * Get reading stats for user
     */
    public static function get_reading_stats($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $bookmarks = get_user_meta($user_id, 'komik_bookmarks', true);
        $history = get_user_meta($user_id, 'komik_reading_history', true);
        
        return [
            'bookmarks' => is_array($bookmarks) ? count($bookmarks) : 0,
            'chapters_read' => is_array($history) ? count($history) : 0,
        ];
    }
}

// Initialize
Komik_User_Account::get_instance();

/**
 * Helper function to get user avatar
 */
function komik_get_user_avatar($user_id = null, $size = 96) {
    return Komik_User_Account::get_avatar($user_id, $size);
}

/**
 * Helper function to get reading stats
 */
function komik_get_reading_stats($user_id = null) {
    return Komik_User_Account::get_reading_stats($user_id);
}
