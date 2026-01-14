<?php
/**
 * Custom Edit Page for Manhwa
 * No meta boxes, clean single-page editing experience
 */

if (!defined('ABSPATH')) {
    exit;
}

class Manhwa_Custom_Edit {
    
    public function __construct() {
        // Add custom edit menu
        add_action('admin_menu', array($this, 'add_edit_menu'));
        
        // Handle save
        add_action('admin_init', array($this, 'handle_save'));
        
        // Redirect default edit to custom edit
        add_action('load-post.php', array($this, 'maybe_redirect_to_custom_edit'));
        
        // Redirect default new post to custom edit
        add_action('load-post-new.php', array($this, 'maybe_redirect_new_to_custom_edit'));
        
        // AJAX handlers
        add_action('wp_ajax_manhwa_save_draft', array($this, 'ajax_save_draft'));
        add_action('wp_ajax_manhwa_delete_chapter', array($this, 'ajax_delete_chapter'));
        add_action('wp_ajax_manhwa_check_duplicate', array($this, 'ajax_check_duplicate'));
    }
    
    /**
     * Add hidden submenu for edit page
     */
    public function add_edit_menu() {
        add_submenu_page(
            null, // Hidden from menu
            'Edit Manhwa',
            'Edit Manhwa',
            'edit_posts',
            'manhwa-edit',
            array($this, 'render_edit_page')
        );
    }
    
    /**
     * Redirect default post edit to custom edit
     * Can be disabled via option 'manhwa_use_standard_editor'
     */
    public function maybe_redirect_to_custom_edit() {
        global $post_type;
        
        // Skip redirect if standard editor is enabled (for Yoast SEO support)
        if (get_option('manhwa_use_standard_editor', false)) {
            return;
        }
        
        if (isset($_GET['post']) && isset($_GET['action']) && $_GET['action'] === 'edit') {
            $post = get_post($_GET['post']);
            if ($post && $post->post_type === 'manhwa') {
                wp_redirect(admin_url('admin.php?page=manhwa-edit&id=' . $post->ID));
                exit;
            }
        }
    }
    
    /**
     * Redirect new post to custom edit
     * Can be disabled via option 'manhwa_use_standard_editor'
     */
    public function maybe_redirect_new_to_custom_edit() {
        // Skip redirect if standard editor is enabled (for Yoast SEO support)
        if (get_option('manhwa_use_standard_editor', false)) {
            return;
        }
        
        if (isset($_GET['post_type']) && $_GET['post_type'] === 'manhwa') {
            wp_redirect(admin_url('admin.php?page=manhwa-edit'));
            exit;
        }
    }
    
    /**
     * Handle form save
     */
    public function handle_save() {
        if (!isset($_POST['manhwa_custom_save']) || !isset($_POST['manhwa_edit_nonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_POST['manhwa_edit_nonce'], 'manhwa_edit_save')) {
            wp_die('Security check failed');
        }
        
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        
        // Update or create post
        $post_data = array(
            'post_title'   => sanitize_text_field($_POST['manhwa_title']),
            'post_content' => wp_kses_post($_POST['manhwa_synopsis']),
            'post_status'  => sanitize_text_field($_POST['post_status']),
            'post_type'    => 'manhwa'
        );
        
        if ($post_id > 0) {
            $post_data['ID'] = $post_id;
            wp_update_post($post_data);
        } else {
            $post_id = wp_insert_post($post_data);
        }
        
        if (is_wp_error($post_id)) {
            wp_die('Error saving manhwa');
        }
        
        // Save meta
        $meta_fields = array(
            'author' => '_manhwa_author',
            'artist' => '_manhwa_artist',
            'alternative_title' => '_manhwa_alternative_title',
            'rating' => '_manhwa_rating',
            'views' => '_manhwa_views',
            'status' => '_manhwa_status',
            'type' => '_manhwa_type',
            'release_year' => '_manhwa_release_year'
        );
        
        foreach ($meta_fields as $field => $meta_key) {
            if (isset($_POST['manhwa_' . $field])) {
                update_post_meta($post_id, $meta_key, sanitize_text_field($_POST['manhwa_' . $field]));
            }
        }
        
        // Save genres
        if (isset($_POST['manhwa_genres'])) {
            $genres = array_map('intval', $_POST['manhwa_genres']);
            wp_set_post_terms($post_id, $genres, 'manhwa_genre');
        } else {
            wp_set_post_terms($post_id, array(), 'manhwa_genre');
        }
        
        // Save chapters
        if (isset($_POST['chapters']) && is_array($_POST['chapters'])) {
            $chapters = array();
            foreach ($_POST['chapters'] as $chapter) {
                if (empty($chapter['title'])) continue;
                
                $images = array();
                if (!empty($chapter['images_text'])) {
                    $urls = explode("\n", $chapter['images_text']);
                    foreach ($urls as $url) {
                        $url = trim($url);
                        if (!empty($url)) {
                            $images[] = array('url' => esc_url_raw($url));
                        }
                    }
                }
                
                $chapters[] = array(
                    'title' => sanitize_text_field($chapter['title']),
                    'url' => sanitize_text_field($chapter['url']),
                    'date' => sanitize_text_field($chapter['date']),
                    'images' => $images
                );
            }
            update_post_meta($post_id, '_manhwa_chapters', $chapters);
        }
        
        // Handle cover upload
        if (isset($_POST['manhwa_cover_id']) && intval($_POST['manhwa_cover_id']) > 0) {
            set_post_thumbnail($post_id, intval($_POST['manhwa_cover_id']));
        }
        
        // Redirect with success message
        wp_redirect(admin_url('admin.php?page=manhwa-edit&id=' . $post_id . '&saved=1'));
        exit;
    }
    
    /**
     * Render the custom edit page
     */
    public function render_edit_page() {
        $post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $post = $post_id > 0 ? get_post($post_id) : null;
        $is_new = !$post;
        
        // Get meta data
        $title = $post ? $post->post_title : '';
        $synopsis = $post ? $post->post_content : '';
        $author = get_post_meta($post_id, '_manhwa_author', true);
        $artist = get_post_meta($post_id, '_manhwa_artist', true);
        $alt_title = get_post_meta($post_id, '_manhwa_alternative_title', true);
        $rating = get_post_meta($post_id, '_manhwa_rating', true);
        $views = get_post_meta($post_id, '_manhwa_views', true);
        $status = get_post_meta($post_id, '_manhwa_status', true);
        $type = get_post_meta($post_id, '_manhwa_type', true);
        $release_year = get_post_meta($post_id, '_manhwa_release_year', true);
        $chapters = get_post_meta($post_id, '_manhwa_chapters', true) ?: array();
        $post_status = $post ? $post->post_status : 'draft';
        $cover_id = get_post_thumbnail_id($post_id);
        $cover_url = $cover_id ? wp_get_attachment_url($cover_id) : '';
        
        // Get genres
        $all_genres = get_terms(array('taxonomy' => 'manhwa_genre', 'hide_empty' => false));
        $selected_genres = wp_get_post_terms($post_id, 'manhwa_genre', array('fields' => 'ids'));
        
        // Enqueue media uploader
        wp_enqueue_media();
        
        $saved = isset($_GET['saved']) && $_GET['saved'] == 1;
        ?>
        <style>
            :root {
                --primary: #3b82f6;
                --primary-hover: #2563eb;
                --success: #22c55e;
                --danger: #ef4444;
                --warning: #f59e0b;
                --gray-50: #f9fafb;
                --gray-100: #f3f4f6;
                --gray-200: #e5e7eb;
                --gray-300: #d1d5db;
                --gray-400: #9ca3af;
                --gray-500: #6b7280;
                --gray-600: #4b5563;
                --gray-700: #374151;
                --gray-800: #1f2937;
                --gray-900: #111827;
                --radius: 8px;
                --radius-lg: 12px;
            }
            
            .manhwa-edit-wrap {
                max-width: 1400px;
                margin: 20px auto 50px;
                padding: 0 20px;
            }
            
            /* Header */
            .edit-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 24px;
                padding: 20px 24px;
                background: #fff;
                border: 1px solid var(--gray-200);
                border-radius: var(--radius-lg);
            }
            
            .edit-header-left {
                display: flex;
                align-items: center;
                gap: 16px;
            }
            
            .back-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                background: var(--gray-100);
                border-radius: var(--radius);
                color: var(--gray-600);
                text-decoration: none;
                transition: all 0.2s;
            }
            
            .back-btn:hover {
                background: var(--gray-200);
                color: var(--gray-800);
            }
            
            .edit-title {
                font-size: 24px;
                font-weight: 700;
                color: var(--gray-900);
                margin: 0;
            }
            
            .edit-subtitle {
                font-size: 14px;
                color: var(--gray-500);
                margin: 4px 0 0 0;
            }
            
            .edit-header-right {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            
            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 10px 20px;
                font-size: 14px;
                font-weight: 600;
                border-radius: var(--radius);
                cursor: pointer;
                transition: all 0.2s;
                text-decoration: none;
                border: none;
            }
            
            .btn-primary {
                background: var(--primary);
                color: #fff;
            }
            
            .btn-primary:hover {
                background: var(--primary-hover);
                color: #fff;
            }
            
            .btn-success {
                background: var(--success);
                color: #fff;
            }
            
            .btn-outline {
                background: #fff;
                border: 1px solid var(--gray-300);
                color: var(--gray-700);
            }
            
            .btn-outline:hover {
                background: var(--gray-100);
                color: var(--gray-800);
            }
            
            /* Layout */
            .edit-layout {
                display: grid;
                grid-template-columns: 1fr 350px;
                gap: 24px;
            }
            
            @media (max-width: 1024px) {
                .edit-layout {
                    grid-template-columns: 1fr;
                }
            }
            
            /* Cards */
            .edit-card {
                background: #fff;
                border: 1px solid var(--gray-200);
                border-radius: var(--radius-lg);
                overflow: hidden;
                margin-bottom: 24px;
            }
            
            .card-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 20px;
                border-bottom: 1px solid var(--gray-200);
                background: var(--gray-50);
            }
            
            .card-title {
                font-size: 16px;
                font-weight: 600;
                color: var(--gray-800);
                margin: 0;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .card-body {
                padding: 20px;
            }
            
            /* Form Fields */
            .form-group {
                margin-bottom: 20px;
            }
            
            .form-group:last-child {
                margin-bottom: 0;
            }
            
            .form-label {
                display: block;
                font-size: 14px;
                font-weight: 600;
                color: var(--gray-700);
                margin-bottom: 8px;
            }
            
            .form-input,
            .form-select,
            .form-textarea {
                width: 100%;
                padding: 12px 16px;
                font-size: 14px;
                border: 1px solid var(--gray-300);
                border-radius: var(--radius);
                background: #fff;
                transition: all 0.2s;
                box-sizing: border-box;
            }
            
            .form-input:focus,
            .form-select:focus,
            .form-textarea:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }
            
            .form-textarea {
                min-height: 120px;
                resize: vertical;
            }
            
            .form-hint {
                font-size: 12px;
                color: var(--gray-500);
                margin-top: 6px;
            }
            
            .form-row {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }
            
            @media (max-width: 600px) {
                .form-row {
                    grid-template-columns: 1fr;
                }
            }
            
            /* Cover Upload */
            .cover-preview {
                width: 100%;
                aspect-ratio: 2/3;
                background: var(--gray-100);
                border-radius: var(--radius);
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                margin-bottom: 16px;
                border: 2px dashed var(--gray-300);
            }
            
            .cover-preview img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .cover-preview.has-image {
                border: none;
            }
            
            .cover-placeholder {
                text-align: center;
                color: var(--gray-400);
            }
            
            .cover-placeholder-icon {
                font-size: 48px;
                margin-bottom: 8px;
            }
            
            /* Genre Checkboxes */
            .genre-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                max-height: 200px;
                overflow-y: auto;
                padding: 4px;
            }
            
            .genre-item {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                background: var(--gray-50);
                border-radius: var(--radius);
                cursor: pointer;
                transition: all 0.2s;
            }
            
            .genre-item:hover {
                background: var(--gray-100);
            }
            
            .genre-item input {
                margin: 0;
            }
            
            .genre-item label {
                font-size: 13px;
                color: var(--gray-700);
                cursor: pointer;
            }
            
            /* Chapters */
            .chapters-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            
            .chapter-item {
                background: var(--gray-50);
                border: 1px solid var(--gray-200);
                border-radius: var(--radius);
                padding: 16px;
                transition: all 0.2s;
            }
            
            .chapter-item:hover {
                border-color: var(--primary);
            }
            
            .chapter-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 12px;
            }
            
            .chapter-number {
                font-size: 13px;
                font-weight: 600;
                color: var(--primary);
                background: rgba(59, 130, 246, 0.1);
                padding: 4px 10px;
                border-radius: 20px;
            }
            
            .chapter-remove {
                background: var(--danger);
                color: #fff;
                border: none;
                padding: 6px 12px;
                border-radius: var(--radius);
                cursor: pointer;
                font-size: 12px;
                font-weight: 500;
            }
            
            .chapter-remove:hover {
                background: #dc2626;
            }
            
            .chapter-fields {
                display: grid;
                grid-template-columns: 1fr 1fr 120px;
                gap: 12px;
            }
            
            @media (max-width: 768px) {
                .chapter-fields {
                    grid-template-columns: 1fr;
                }
            }
            
            .chapter-input {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid var(--gray-300);
                border-radius: var(--radius);
                font-size: 13px;
            }
            
            .chapter-images-toggle {
                margin-top: 12px;
                padding: 10px;
                background: #fff;
                border: 1px solid var(--gray-200);
                border-radius: var(--radius);
                cursor: pointer;
                width: 100%;
                text-align: left;
                font-size: 13px;
                color: var(--gray-600);
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .chapter-images-toggle:hover {
                background: var(--gray-50);
            }
            
            .chapter-images-content {
                display: none;
                margin-top: 12px;
            }
            
            .chapter-images-content.active {
                display: block;
            }
            
            .images-textarea {
                width: 100%;
                height: 100px;
                font-family: monospace;
                font-size: 12px;
                padding: 10px;
                border: 1px solid var(--gray-300);
                border-radius: var(--radius);
            }
            
            .images-preview {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
                gap: 8px;
                margin-top: 12px;
            }
            
            .images-preview img {
                width: 100%;
                height: 60px;
                object-fit: cover;
                border-radius: 4px;
                border: 1px solid var(--gray-200);
            }
            
            .add-chapter-btn {
                width: 100%;
                padding: 14px;
                background: var(--gray-50);
                border: 2px dashed var(--gray-300);
                border-radius: var(--radius);
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                color: var(--gray-600);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                transition: all 0.2s;
            }
            
            .add-chapter-btn:hover {
                background: var(--gray-100);
                border-color: var(--primary);
                color: var(--primary);
            }
            
            /* Status badges */
            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }
            
            .status-draft {
                background: var(--gray-100);
                color: var(--gray-600);
            }
            
            .status-publish {
                background: #dcfce7;
                color: #16a34a;
            }
            
            /* Alert */
            .alert {
                padding: 14px 18px;
                border-radius: var(--radius);
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .alert-success {
                background: #dcfce7;
                color: #166534;
                border: 1px solid #bbf7d0;
            }
            
            /* Duplicate Detector */
            .duplicate-detector {
                margin-top: 8px;
                display: none;
            }
            
            .duplicate-detector.active {
                display: block;
            }
            
            .duplicate-warning {
                background: #fef3c7;
                border: 1px solid #fcd34d;
                border-radius: var(--radius);
                padding: 12px 16px;
                margin-bottom: 12px;
            }
            
            .duplicate-warning-header {
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 600;
                color: #92400e;
                font-size: 14px;
                margin-bottom: 8px;
            }
            
            .duplicate-warning-text {
                font-size: 13px;
                color: #a16207;
            }
            
            .duplicate-list {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            
            .duplicate-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 12px;
                background: #fff;
                border: 1px solid var(--gray-200);
                border-radius: var(--radius);
                transition: all 0.2s;
            }
            
            .duplicate-item:hover {
                border-color: var(--warning);
            }
            
            .duplicate-item-cover {
                width: 40px;
                height: 56px;
                border-radius: 4px;
                object-fit: cover;
                background: var(--gray-100);
            }
            
            .duplicate-item-info {
                flex: 1;
                min-width: 0;
            }
            
            .duplicate-item-title {
                font-size: 13px;
                font-weight: 600;
                color: var(--gray-800);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .duplicate-item-meta {
                font-size: 11px;
                color: var(--gray-500);
                margin-top: 2px;
            }
            
            .duplicate-item-similarity {
                font-size: 12px;
                font-weight: 600;
                padding: 4px 8px;
                border-radius: 20px;
                background: #fef3c7;
                color: #92400e;
            }
            
            .duplicate-item-similarity.high {
                background: #fee2e2;
                color: #b91c1c;
            }
            
            .duplicate-item-actions {
                display: flex;
                gap: 6px;
            }
            
            .duplicate-item-btn {
                padding: 4px 10px;
                font-size: 11px;
                border-radius: 4px;
                text-decoration: none;
                transition: all 0.2s;
            }
            
            .duplicate-item-btn.view {
                background: var(--gray-100);
                color: var(--gray-600);
            }
            
            .duplicate-item-btn.edit {
                background: var(--primary);
                color: #fff;
            }
            
            .duplicate-checking {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                background: var(--gray-50);
                border-radius: var(--radius);
                font-size: 13px;
                color: var(--gray-500);
            }
            
            .duplicate-checking .spinner {
                width: 16px;
                height: 16px;
                border: 2px solid var(--gray-200);
                border-top-color: var(--primary);
                border-radius: 50%;
                animation: spin 0.8s linear infinite;
            }
            
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
            
            /* Sticky sidebar */
            .edit-sidebar {
                position: sticky;
                top: 32px;
            }
            
            @media (max-width: 1024px) {
                .edit-sidebar {
                    position: static;
                }
            }
        </style>
        
        <div class="manhwa-edit-wrap">
            <!-- Header -->
            <div class="edit-header">
                <div class="edit-header-left">
                    <a href="<?php echo admin_url('edit.php?post_type=manhwa'); ?>" class="back-btn">
                        ←
                    </a>
                    <div>
                        <h1 class="edit-title"><?php echo $is_new ? '➕ Add New Manhwa' : '✏️ Edit Manhwa'; ?></h1>
                        <p class="edit-subtitle">
                            <?php if (!$is_new): ?>
                                ID: <?php echo $post_id; ?> • 
                                <span class="status-badge status-<?php echo $post_status; ?>">
                                    <?php echo ucfirst($post_status); ?>
                                </span>
                            <?php else: ?>
                                Create a new manhwa entry
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="edit-header-right">
                    <?php if (!$is_new): ?>
                        <a href="<?php echo get_permalink($post_id); ?>" target="_blank" class="btn btn-outline">
                            👁️ View
                        </a>
                    <?php endif; ?>
                    <button type="submit" form="manhwa-edit-form" name="manhwa_custom_save" value="1" class="btn btn-primary">
                        💾 Save Changes
                    </button>
                </div>
            </div>
            
            <?php if ($saved): ?>
                <div class="alert alert-success">
                    ✅ Manhwa saved successfully!
                </div>
            <?php endif; ?>
            
            <form id="manhwa-edit-form" method="post" action="">
                <?php wp_nonce_field('manhwa_edit_save', 'manhwa_edit_nonce'); ?>
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <input type="hidden" name="manhwa_cover_id" id="manhwa_cover_id" value="<?php echo $cover_id; ?>">
                
                <div class="edit-layout">
                    <!-- Main Content -->
                    <div class="edit-main">
                        <!-- Basic Info -->
                        <div class="edit-card">
                            <div class="card-header">
                                <h2 class="card-title">📝 Basic Information</h2>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label">Title *</label>
                                    <input type="text" name="manhwa_title" id="manhwa_title" class="form-input" value="<?php echo esc_attr($title); ?>" placeholder="Enter manhwa title" required>
                                    
                                    <!-- Duplicate Detector -->
                                    <div id="duplicate-detector" class="duplicate-detector">
                                        <div id="duplicate-checking" class="duplicate-checking" style="display: none;">
                                            <div class="spinner"></div>
                                            <span>Checking for duplicates...</span>
                                        </div>
                                        <div id="duplicate-results"></div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Alternative Title</label>
                                    <input type="text" name="manhwa_alternative_title" class="form-input" value="<?php echo esc_attr($alt_title); ?>" placeholder="Other names (optional)">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Synopsis</label>
                                    <textarea name="manhwa_synopsis" class="form-textarea" placeholder="Enter synopsis/description"><?php echo esc_textarea($synopsis); ?></textarea>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Author</label>
                                        <input type="text" name="manhwa_author" class="form-input" value="<?php echo esc_attr($author); ?>" placeholder="Author name">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Artist</label>
                                        <input type="text" name="manhwa_artist" class="form-input" value="<?php echo esc_attr($artist); ?>" placeholder="Artist name">
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Type</label>
                                        <select name="manhwa_type" class="form-select">
                                            <option value="">Select type...</option>
                                            <option value="Manhwa" <?php selected($type, 'Manhwa'); ?>>🇰🇷 Manhwa</option>
                                            <option value="Manga" <?php selected($type, 'Manga'); ?>>🇯🇵 Manga</option>
                                            <option value="Manhua" <?php selected($type, 'Manhua'); ?>>🇨🇳 Manhua</option>
                                            <option value="Comic" <?php selected($type, 'Comic'); ?>>🌍 Comic</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Status</label>
                                        <select name="manhwa_status" class="form-select">
                                            <option value="">Select status...</option>
                                            <option value="ongoing" <?php selected($status, 'ongoing'); ?>>🔵 Ongoing</option>
                                            <option value="completed" <?php selected($status, 'completed'); ?>>🟢 Completed</option>
                                            <option value="hiatus" <?php selected($status, 'hiatus'); ?>>🟡 Hiatus</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Rating</label>
                                        <input type="number" name="manhwa_rating" class="form-input" value="<?php echo esc_attr($rating ?: '7.0'); ?>" step="0.1" min="0" max="10" placeholder="7.0">
                                        <span class="form-hint">Rating from 0 to 10</span>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Release Year</label>
                                        <input type="number" name="manhwa_release_year" class="form-input" value="<?php echo esc_attr($release_year); ?>" placeholder="2025">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Views</label>
                                    <input type="number" name="manhwa_views" class="form-input" value="<?php echo esc_attr($views ?: '0'); ?>" placeholder="0">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Chapters -->
                        <div class="edit-card">
                            <div class="card-header">
                                <h2 class="card-title">📚 Chapters <span style="color: var(--gray-400); font-weight: normal;">(<?php echo count($chapters); ?>)</span></h2>
                            </div>
                            <div class="card-body">
                                <div id="chapters-list" class="chapters-list">
                                    <?php foreach ($chapters as $index => $chapter): 
                                        $images = isset($chapter['images']) ? $chapter['images'] : array();
                                        $images_count = is_array($images) ? count($images) : 0;
                                        $images_text = '';
                                        if ($images_count > 0) {
                                            $urls = array_map(function($img) {
                                                return is_array($img) ? ($img['url'] ?? '') : $img;
                                            }, $images);
                                            $images_text = implode("\n", $urls);
                                        }
                                    ?>
                                        <div class="chapter-item">
                                            <div class="chapter-header">
                                                <span class="chapter-number">#<?php echo $index + 1; ?></span>
                                                <button type="button" class="chapter-remove" onclick="removeChapter(this)">✕ Remove</button>
                                            </div>
                                            <div class="chapter-fields">
                                                <input type="text" name="chapters[<?php echo $index; ?>][title]" class="chapter-input" value="<?php echo esc_attr($chapter['title']); ?>" placeholder="Chapter title">
                                                <input type="text" name="chapters[<?php echo $index; ?>][url]" class="chapter-input" value="<?php echo esc_attr($chapter['url']); ?>" placeholder="URL/Slug">
                                                <input type="date" name="chapters[<?php echo $index; ?>][date]" class="chapter-input" value="<?php echo esc_attr($chapter['date']); ?>">
                                            </div>
                                            <button type="button" class="chapter-images-toggle" onclick="toggleChapterImages(this)">
                                                📷 Images <?php if ($images_count > 0): ?><strong>(<?php echo $images_count; ?>)</strong><?php endif; ?>
                                            </button>
                                            <div class="chapter-images-content">
                                                <?php if ($images_count > 0): ?>
                                                    <div class="images-preview">
                                                        <?php foreach (array_slice($images, 0, 10) as $img): 
                                                            $img_url = is_array($img) ? ($img['url'] ?? '') : $img;
                                                        ?>
                                                            <img src="<?php echo esc_url($img_url); ?>" loading="lazy" onerror="this.style.display='none'">
                                                        <?php endforeach; ?>
                                                        <?php if ($images_count > 10): ?>
                                                            <div style="display:flex;align-items:center;justify-content:center;background:var(--gray-100);border-radius:4px;font-size:12px;color:var(--gray-500);">+<?php echo $images_count - 10; ?> more</div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                                <textarea name="chapters[<?php echo $index; ?>][images_text]" class="images-textarea" placeholder="One image URL per line"><?php echo esc_textarea($images_text); ?></textarea>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <button type="button" class="add-chapter-btn" onclick="addChapter()">
                                    ➕ Add Chapter
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="edit-sidebar">
                        <!-- Publish -->
                        <div class="edit-card">
                            <div class="card-header">
                                <h2 class="card-title">📤 Publish</h2>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <select name="post_status" class="form-select">
                                        <option value="draft" <?php selected($post_status, 'draft'); ?>>📝 Draft</option>
                                        <option value="publish" <?php selected($post_status, 'publish'); ?>>✅ Published</option>
                                        <option value="pending" <?php selected($post_status, 'pending'); ?>>⏳ Pending Review</option>
                                    </select>
                                </div>
                                <button type="submit" name="manhwa_custom_save" value="1" class="btn btn-primary" style="width: 100%;">
                                    💾 Save Changes
                                </button>
                            </div>
                        </div>
                        
                        <!-- Cover -->
                        <div class="edit-card">
                            <div class="card-header">
                                <h2 class="card-title">📸 Cover Image</h2>
                            </div>
                            <div class="card-body">
                                <div id="cover-preview" class="cover-preview <?php echo $cover_url ? 'has-image' : ''; ?>">
                                    <?php if ($cover_url): ?>
                                        <img src="<?php echo esc_url($cover_url); ?>" alt="Cover">
                                    <?php else: ?>
                                        <div class="cover-placeholder">
                                            <div class="cover-placeholder-icon">📖</div>
                                            <div>No cover image</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button type="button" id="upload-cover-btn" class="btn btn-outline" style="width: 100%; margin-bottom: 8px;">
                                    📤 Upload Cover
                                </button>
                                <?php if ($cover_url): ?>
                                    <button type="button" id="remove-cover-btn" class="btn btn-outline" style="width: 100%; color: var(--danger);">
                                        🗑️ Remove
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Genres -->
                        <div class="edit-card">
                            <div class="card-header">
                                <h2 class="card-title">🏷️ Genres</h2>
                            </div>
                            <div class="card-body">
                                <div class="genre-grid">
                                    <?php foreach ($all_genres as $genre): ?>
                                        <label class="genre-item">
                                            <input type="checkbox" name="manhwa_genres[]" value="<?php echo $genre->term_id; ?>" <?php echo in_array($genre->term_id, $selected_genres) ? 'checked' : ''; ?>>
                                            <span><?php echo esc_html($genre->name); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <script>
        let chapterIndex = <?php echo count($chapters); ?>;
        
        function addChapter() {
            const html = `
                <div class="chapter-item">
                    <div class="chapter-header">
                        <span class="chapter-number">#${chapterIndex + 1}</span>
                        <button type="button" class="chapter-remove" onclick="removeChapter(this)">✕ Remove</button>
                    </div>
                    <div class="chapter-fields">
                        <input type="text" name="chapters[${chapterIndex}][title]" class="chapter-input" placeholder="Chapter title">
                        <input type="text" name="chapters[${chapterIndex}][url]" class="chapter-input" placeholder="URL/Slug">
                        <input type="date" name="chapters[${chapterIndex}][date]" class="chapter-input" value="${new Date().toISOString().split('T')[0]}">
                    </div>
                    <button type="button" class="chapter-images-toggle" onclick="toggleChapterImages(this)">
                        📷 Images
                    </button>
                    <div class="chapter-images-content">
                        <textarea name="chapters[${chapterIndex}][images_text]" class="images-textarea" placeholder="One image URL per line"></textarea>
                    </div>
                </div>
            `;
            document.getElementById('chapters-list').insertAdjacentHTML('beforeend', html);
            chapterIndex++;
        }
        
        function removeChapter(btn) {
            if (confirm('Remove this chapter?')) {
                btn.closest('.chapter-item').remove();
                renumberChapters();
            }
        }
        
        function renumberChapters() {
            document.querySelectorAll('.chapter-item').forEach((item, index) => {
                item.querySelector('.chapter-number').textContent = '#' + (index + 1);
            });
        }
        
        function toggleChapterImages(btn) {
            const content = btn.nextElementSibling;
            content.classList.toggle('active');
            btn.textContent = content.classList.contains('active') ? '📷 Hide Images' : '📷 Images';
            
            // Restore count if exists
            const textarea = content.querySelector('.images-textarea');
            if (textarea && textarea.value.trim()) {
                const count = textarea.value.trim().split('\n').filter(l => l.trim()).length;
                if (count > 0 && !content.classList.contains('active')) {
                    btn.innerHTML = `📷 Images <strong>(${count})</strong>`;
                }
            }
        }
        
        // Cover upload
        jQuery(document).ready(function($) {
            let mediaUploader;
            
            $('#upload-cover-btn').on('click', function(e) {
                e.preventDefault();
                
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                mediaUploader = wp.media({
                    title: 'Choose Cover Image',
                    button: { text: 'Set as Cover' },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#manhwa_cover_id').val(attachment.id);
                    $('#cover-preview').addClass('has-image').html('<img src="' + attachment.url + '" alt="Cover">');
                    
                    // Show remove button if not exists
                    if ($('#remove-cover-btn').length === 0) {
                        $('#upload-cover-btn').after('<button type="button" id="remove-cover-btn" class="btn btn-outline" style="width: 100%; margin-top: 8px; color: var(--danger);">🗑️ Remove</button>');
                    }
                });
                
                mediaUploader.open();
            });
            
            $(document).on('click', '#remove-cover-btn', function(e) {
                e.preventDefault();
                $('#manhwa_cover_id').val('');
                $('#cover-preview').removeClass('has-image').html('<div class="cover-placeholder"><div class="cover-placeholder-icon">📖</div><div>No cover image</div></div>');
                $(this).remove();
            });
            
            // Duplicate Detector
            let duplicateTimer;
            const postId = <?php echo $post_id ?: 0; ?>;
            
            $('#manhwa_title').on('input', function() {
                const title = $(this).val().trim();
                
                // Clear previous timer
                clearTimeout(duplicateTimer);
                
                // Hide if less than 3 chars
                if (title.length < 3) {
                    $('#duplicate-detector').removeClass('active');
                    $('#duplicate-results').html('');
                    return;
                }
                
                // Show detector area
                $('#duplicate-detector').addClass('active');
                
                // Debounce - wait 500ms after typing stops
                duplicateTimer = setTimeout(function() {
                    checkDuplicates(title);
                }, 500);
            });
            
            function checkDuplicates(title) {
                $('#duplicate-checking').show();
                $('#duplicate-results').html('');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'manhwa_check_duplicate',
                        title: title,
                        post_id: postId
                    },
                    success: function(response) {
                        $('#duplicate-checking').hide();
                        
                        if (response.success && response.data.duplicates.length > 0) {
                            let html = `
                                <div class="duplicate-warning">
                                    <div class="duplicate-warning-header">
                                        ⚠️ Possible Duplicates Found (${response.data.total})
                                    </div>
                                    <div class="duplicate-warning-text">
                                        Similar titles already exist. Please check before saving.
                                    </div>
                                </div>
                                <div class="duplicate-list">
                            `;
                            
                            response.data.duplicates.forEach(function(item) {
                                const similarityClass = item.similarity >= 80 ? 'high' : '';
                                const coverImg = item.cover ? 
                                    `<img src="${item.cover}" class="duplicate-item-cover">` : 
                                    `<div class="duplicate-item-cover" style="display:flex;align-items:center;justify-content:center;">📖</div>`;
                                
                                html += `
                                    <div class="duplicate-item">
                                        ${coverImg}
                                        <div class="duplicate-item-info">
                                            <div class="duplicate-item-title">${item.title}</div>
                                            <div class="duplicate-item-meta">
                                                ${item.status.toUpperCase()} • ${item.chapters} chapters
                                            </div>
                                        </div>
                                        <span class="duplicate-item-similarity ${similarityClass}">${item.similarity}%</span>
                                        <div class="duplicate-item-actions">
                                            <a href="${item.view_url}" target="_blank" class="duplicate-item-btn view">View</a>
                                            <a href="${item.edit_url}" class="duplicate-item-btn edit">Edit</a>
                                        </div>
                                    </div>
                                `;
                            });
                            
                            html += '</div>';
                            $('#duplicate-results').html(html);
                        } else {
                            $('#duplicate-detector').removeClass('active');
                        }
                    },
                    error: function() {
                        $('#duplicate-checking').hide();
                    }
                });
            }
        });
        </script>
        <?php
    }
    
    /**
     * AJAX: Save Draft
     */
    public function ajax_save_draft() {
        // Implementation for auto-save
    }
    
    /**
     * AJAX: Delete Chapter
     */
    public function ajax_delete_chapter() {
        // Implementation for AJAX chapter delete
    }
    
    /**
     * AJAX: Check for Duplicate Manhwa
     */
    public function ajax_check_duplicate() {
        // Verify request
        if (!isset($_POST['title']) || empty($_POST['title'])) {
            wp_send_json_error(array('message' => 'No title provided'));
        }
        
        $title = sanitize_text_field($_POST['title']);
        $current_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        
        // Minimum 3 characters to search
        if (strlen($title) < 3) {
            wp_send_json_success(array('duplicates' => array()));
        }
        
        // Search for similar titles
        $args = array(
            'post_type' => 'manhwa',
            'posts_per_page' => 10,
            'post_status' => array('publish', 'draft', 'pending'),
            's' => $title,
            'post__not_in' => $current_id > 0 ? array($current_id) : array()
        );
        
        $query = new WP_Query($args);
        $duplicates = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $post_title = get_the_title();
                
                // Calculate similarity
                $similarity = 0;
                similar_text(strtolower($title), strtolower($post_title), $similarity);
                
                // Also check if title contains search term
                $contains = stripos($post_title, $title) !== false;
                
                // Only include if similarity > 40% or contains
                if ($similarity > 40 || $contains) {
                    $status = get_post_status();
                    $cover = get_the_post_thumbnail_url($post_id, 'thumbnail') ?: '';
                    $chapters = get_post_meta($post_id, '_manhwa_chapters', true);
                    $chapter_count = is_array($chapters) ? count($chapters) : 0;
                    
                    $duplicates[] = array(
                        'id' => $post_id,
                        'title' => $post_title,
                        'status' => $status,
                        'similarity' => round($similarity),
                        'cover' => $cover,
                        'chapters' => $chapter_count,
                        'edit_url' => admin_url('admin.php?page=manhwa-edit&id=' . $post_id),
                        'view_url' => get_permalink($post_id)
                    );
                }
            }
            wp_reset_postdata();
        }
        
        // Sort by similarity (highest first)
        usort($duplicates, function($a, $b) {
            return $b['similarity'] - $a['similarity'];
        });
        
        wp_send_json_success(array(
            'duplicates' => array_slice($duplicates, 0, 5), // Max 5 results
            'total' => count($duplicates)
        ));
    }
}

// Initialize
new Manhwa_Custom_Edit();
