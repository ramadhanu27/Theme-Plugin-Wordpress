<?php
/**
 * Admin Pages and Menus
 */

if (!defined('ABSPATH')) {
    exit;
}

class Manhwa_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_form_submissions'));
        
        // Add delete all button on manhwa list page
        add_action('restrict_manage_posts', array($this, 'add_delete_all_button'), 10, 1);
        add_action('admin_head-edit.php', array($this, 'add_delete_all_styles'));
        
        // AJAX handler for delete all manhwa
        add_action('wp_ajax_delete_all_manhwa', array($this, 'ajax_delete_all_manhwa'));
        
        // Custom columns for manhwa list
        add_filter('manage_manhwa_posts_columns', array($this, 'add_manhwa_columns'));
        add_action('manage_manhwa_posts_custom_column', array($this, 'render_manhwa_columns'), 10, 2);
        add_filter('manage_edit-manhwa_sortable_columns', array($this, 'make_manhwa_columns_sortable'));
        add_action('admin_head-edit.php', array($this, 'add_manhwa_list_styles'));
        
        // Enqueue admin styles for edit page
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        
        // Quick Edit customization
        add_action('quick_edit_custom_box', array($this, 'add_quick_edit_fields'), 10, 2);
        add_action('save_post_manhwa', array($this, 'save_quick_edit_fields'));
        add_action('admin_footer-edit.php', array($this, 'quick_edit_javascript'));
    }
    
    /**
     * Add Admin Menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'Manhwa Manager',
            'Manhwa Manager',
            'manage_options',
            'manhwa-manager',
            array($this, 'render_dashboard_page'),
            'dashicons-book',
            30
        );
        
        add_submenu_page(
            'manhwa-manager',
            'Upload List',
            'Upload List',
            'manage_options',
            'manhwa-manager-upload',
            array($this, 'render_upload_page')
        );
        
        add_submenu_page(
            'manhwa-manager',
            'Export/Import',
            'Export/Import',
            'manage_options',
            'manhwa-manager-export',
            array($this, 'render_export_page')
        );
        
        add_submenu_page(
            'manhwa-manager',
            'Bulk Actions',
            'Bulk Actions',
            'manage_options',
            'manhwa-manager-bulk',
            array($this, 'render_bulk_actions_page')
        );
        
        add_submenu_page(
            'manhwa-manager',
            'Upload Detail',
            'Upload Detail',
            'manage_options',
            'manhwa-manager-detail',
            array($this, 'render_detail_upload_page')
        );
        
        add_submenu_page(
            'manhwa-manager',
            'Featured Manhwa',
            'Featured Manhwa',
            'manage_options',
            'manhwa-manager-featured',
            array($this, 'render_featured_page')
        );
    }
    
    /**
     * Render Dashboard Page
     */
    public function render_dashboard_page() {
        $manhwa_count = wp_count_posts('manhwa');
        $total_chapters = $this->get_total_chapters();
        $total_views = $this->get_total_views();
        ?>
        <style>
            .mm-dashboard { max-width: 1400px; margin: 20px 0; }
            .mm-header { margin-bottom: 30px; }
            .mm-header h1 { font-size: 28px; font-weight: 600; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 12px; }
            .mm-header p { color: #64748b; margin: 8px 0 0 0; font-size: 15px; }
            
            .mm-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
            .mm-stat-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; transition: box-shadow 0.2s, border-color 0.2s; }
            .mm-stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-color: #cbd5e1; }
            .mm-stat-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
            .mm-stat-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
            .mm-stat-icon.blue { background: #eff6ff; color: #3b82f6; }
            .mm-stat-icon.green { background: #f0fdf4; color: #22c55e; }
            .mm-stat-icon.amber { background: #fffbeb; color: #f59e0b; }
            .mm-stat-icon.purple { background: #faf5ff; color: #a855f7; }
            .mm-stat-value { font-size: 32px; font-weight: 700; color: #1e293b; line-height: 1; }
            .mm-stat-label { font-size: 14px; color: #64748b; margin-top: 6px; }
            .mm-stat-change { font-size: 12px; padding: 4px 8px; border-radius: 6px; font-weight: 500; }
            .mm-stat-change.up { background: #dcfce7; color: #16a34a; }
            
            .mm-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
            .mm-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
            .mm-card-header { padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; }
            .mm-card-title { font-size: 16px; font-weight: 600; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 8px; }
            .mm-card-body { padding: 0; }
            
            .mm-quick-actions { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; padding: 20px; }
            .mm-action-btn { display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 20px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; text-decoration: none; color: #475569; font-size: 13px; font-weight: 500; transition: all 0.2s; }
            .mm-action-btn:hover { background: #f1f5f9; border-color: #3b82f6; color: #3b82f6; text-decoration: none; }
            .mm-action-btn .icon { font-size: 24px; }
            
            .mm-table { width: 100%; border-collapse: collapse; }
            .mm-table th { padding: 12px 20px; text-align: left; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
            .mm-table td { padding: 14px 20px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
            .mm-table tr:last-child td { border-bottom: none; }
            .mm-table tr:hover { background: #f8fafc; }
            
            .mm-badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }
            .mm-badge.ongoing { background: #dbeafe; color: #2563eb; }
            .mm-badge.completed { background: #dcfce7; color: #16a34a; }
            .mm-badge.hiatus { background: #fef3c7; color: #d97706; }
            
            .mm-empty { text-align: center; padding: 50px 20px; color: #94a3b8; }
            .mm-empty-icon { font-size: 48px; margin-bottom: 16px; opacity: 0.5; }
            
            .mm-btn-sm { padding: 6px 12px; font-size: 12px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; color: #475569; text-decoration: none; transition: all 0.2s; }
            .mm-btn-sm:hover { background: #e2e8f0; color: #1e293b; text-decoration: none; }
            
            @media (max-width: 1200px) { .mm-stats { grid-template-columns: repeat(2, 1fr); } .mm-grid { grid-template-columns: 1fr; } }
            @media (max-width: 600px) { .mm-stats { grid-template-columns: 1fr; } .mm-quick-actions { grid-template-columns: 1fr; } }
        </style>
        
        <div class="wrap mm-dashboard">
            <div class="mm-header">
                <h1><span style="font-size: 32px;">📚</span> Manhwa Manager</h1>
                <p>Manage your manhwa collection, chapters, and metadata</p>
            </div>
            
            <!-- Stats Cards -->
            <div class="mm-stats">
                <div class="mm-stat-card">
                    <div class="mm-stat-header">
                        <div class="mm-stat-icon blue">📖</div>
                    </div>
                    <div class="mm-stat-value"><?php echo number_format($manhwa_count->publish); ?></div>
                    <div class="mm-stat-label">Published Manhwa</div>
                </div>
                
                <div class="mm-stat-card">
                    <div class="mm-stat-header">
                        <div class="mm-stat-icon green">📑</div>
                    </div>
                    <div class="mm-stat-value"><?php echo number_format($total_chapters); ?></div>
                    <div class="mm-stat-label">Total Chapters</div>
                </div>
                
                <div class="mm-stat-card">
                    <div class="mm-stat-header">
                        <div class="mm-stat-icon amber">✏️</div>
                    </div>
                    <div class="mm-stat-value"><?php echo number_format($manhwa_count->draft); ?></div>
                    <div class="mm-stat-label">Draft Posts</div>
                </div>
                
                <div class="mm-stat-card">
                    <div class="mm-stat-header">
                        <div class="mm-stat-icon purple">👁️</div>
                    </div>
                    <div class="mm-stat-value"><?php echo number_format($total_views); ?></div>
                    <div class="mm-stat-label">Total Views</div>
                </div>
            </div>
            
            <!-- Main Grid -->
            <div class="mm-grid">
                <!-- Recent Manhwa -->
                <div class="mm-card">
                    <div class="mm-card-header">
                        <h2 class="mm-card-title">📋 Recent Manhwa</h2>
                        <a href="<?php echo admin_url('edit.php?post_type=manhwa'); ?>" class="mm-btn-sm">View All</a>
                    </div>
                    <div class="mm-card-body">
                        <?php
                        $recent_manhwa = get_posts(array(
                            'post_type' => 'manhwa',
                            'posts_per_page' => 6,
                            'orderby' => 'date',
                            'order' => 'DESC'
                        ));
                        
                        if ($recent_manhwa) :
                        ?>
                            <table class="mm-table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Chapters</th>
                                        <th>Date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_manhwa as $manhwa) : 
                                        $status = get_post_meta($manhwa->ID, '_manhwa_status', true) ?: 'ongoing';
                                        $chapters = get_post_meta($manhwa->ID, '_manhwa_chapters', true);
                                        $chapter_count = is_array($chapters) ? count($chapters) : 0;
                                    ?>
                                        <tr>
                                            <td><strong><?php echo esc_html(wp_trim_words($manhwa->post_title, 5)); ?></strong></td>
                                            <td><span class="mm-badge <?php echo esc_attr($status); ?>"><?php echo esc_html($status); ?></span></td>
                                            <td><?php echo $chapter_count; ?></td>
                                            <td><?php echo get_the_date('M d', $manhwa->ID); ?></td>
                                            <td><a href="<?php echo get_edit_post_link($manhwa->ID); ?>" class="mm-btn-sm">Edit</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <div class="mm-empty">
                                <div class="mm-empty-icon">📖</div>
                                <p>No manhwa found. Start by adding your first one!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="mm-card">
                    <div class="mm-card-header">
                        <h2 class="mm-card-title">⚡ Quick Actions</h2>
                    </div>
                    <div class="mm-card-body">
                        <div class="mm-quick-actions">
                            <a href="<?php echo admin_url('post-new.php?post_type=manhwa'); ?>" class="mm-action-btn">
                                <span class="icon">➕</span>
                                Add New
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=manhwa-manager-upload'); ?>" class="mm-action-btn">
                                <span class="icon">📤</span>
                                Upload JSON
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=manhwa-manager-detail'); ?>" class="mm-action-btn">
                                <span class="icon">📖</span>
                                Upload Detail
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=manhwa-manager-export'); ?>" class="mm-action-btn">
                                <span class="icon">💾</span>
                                Export/Import
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=manhwa-manager-bulk'); ?>" class="mm-action-btn">
                                <span class="icon">🔧</span>
                                Bulk Actions
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=manhwa-manager-featured'); ?>" class="mm-action-btn">
                                <span class="icon">⭐</span>
                                Featured
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Settings Card -->
            <div class="mm-card" style="margin-top: 24px;">
                <div class="mm-card-header">
                    <h2 class="mm-card-title">⚙️ Settings</h2>
                </div>
                <div class="mm-card-body" style="padding: 20px;">
                    <form method="post" action="">
                        <?php wp_nonce_field('manhwa_manager_settings', 'mm_settings_nonce'); ?>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                                <input type="checkbox" name="use_standard_editor" value="1" 
                                    <?php checked(get_option('manhwa_use_standard_editor', false)); ?>
                                    style="width: 20px; height: 20px;">
                                <div>
                                    <strong>Use Standard WordPress Editor</strong>
                                    <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">
                                        Enable this to use standard WordPress editor instead of custom editor.<br>
                                        <span style="color: #16a34a;">✓ Required for Yoast SEO Premium support</span>
                                    </p>
                                </div>
                            </label>
                        </div>
                        
                        <button type="submit" name="save_mm_settings" class="button button-primary">
                            💾 Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        
        // Handle settings save
        if (isset($_POST['save_mm_settings']) && wp_verify_nonce($_POST['mm_settings_nonce'], 'manhwa_manager_settings')) {
            update_option('manhwa_use_standard_editor', isset($_POST['use_standard_editor']));
            echo '<script>location.reload();</script>';
        }
    }
    
    /**
     * Render Upload Page
     */
    public function render_upload_page() {
        ?>
        <div class="wrap">
            <h1>📤 Upload Manhwa List (JSON)</h1>
            
            <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-top: 20px;">
                <form method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field('manhwa_upload_json', 'manhwa_upload_nonce'); ?>
                    
                    <h2>Upload JSON File</h2>
                    <p>Upload a JSON file containing manhwa list data. Plugin supports multiple formats:</p>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;">
                        <!-- Format 1: Standard -->
                        <div>
                            <h3 style="margin-bottom: 10px;">📋 Format 1: Standard</h3>
                            <div style="background: #f5f5f5; padding: 15px; border-radius: 10px; font-family: monospace; font-size: 12px; overflow-x: auto;">
<pre>{
  "manhwa": [
    {
      "title": "Solo Leveling",
      "description": "...",
      "author": "Chugong",
      "genres": ["Action"],
      "status": "completed",
      "rating": 4.8,
      "thumbnail_url": "..."
    }
  ]
}</pre>
                            </div>
                        </div>
                        
                        <!-- Format 2: Komiku -->
                        <div>
                            <h3 style="margin-bottom: 10px;">📋 Format 2: Komiku/Scraper</h3>
                            <div style="background: #f5f5f5; padding: 15px; border-radius: 10px; font-family: monospace; font-size: 12px; overflow-x: auto;">
<pre>[
  {
    "slug": "solo-leveling",
    "title": "Solo Leveling",
    "image": "...",
    "synopsis": "...",
    "genres": ["Action"],
    "status": "Ongoing",
    "type": "Manhwa",
    "totalChapters": 179
  }
]</pre>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin: 30px 0;">
                        <label for="manhwa_json_files" style="display: block; font-weight: bold; margin-bottom: 10px;">
                            Select JSON Files (Multiple):
                        </label>
                        <input type="file" name="manhwa_json_files[]" id="manhwa_json_files" accept=".json" multiple required style="padding: 10px; width: 100%;">
                        <p style="margin-top: 10px; color: #666; font-size: 13px;">
                            💡 Tip: Tekan <strong>Ctrl</strong> atau <strong>Shift</strong> untuk select multiple files sekaligus
                        </p>
                    </div>
                    
                    <div style="margin: 20px 0;">
                        <label>
                            <input type="checkbox" name="overwrite_existing" value="1">
                            Overwrite existing manhwa with same title
                        </label>
                    </div>
                    
                    <div style="margin: 20px 0;">
                        <label>
                            <input type="checkbox" name="remove_komik_word" value="1" checked>
                            Hapus kata "Komik" dari semua judul
                        </label>
                    </div>
                    
                    <button type="submit" name="upload_json" class="button button-primary button-large">
                        📤 Upload and Import
                    </button>
                    
                    <div id="upload-progress" style="display: none; margin-top: 20px;">
                        <div style="background: #f0f0f0; border-radius: 10px; padding: 20px;">
                            <h3 style="margin-top: 0;">⏳ Processing...</h3>
                            <div class="manhwa-progress">
                                <div class="manhwa-progress-bar" id="progress-bar" style="width: 0%;">0%</div>
                            </div>
                            <p id="progress-text" style="margin-top: 10px; color: #666;">Uploading files...</p>
                        </div>
                    </div>
                </form>
            </div>
            
            <div style="background: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 10px; margin-top: 30px;">
                <h3 style="margin-top: 0;">💡 Tips:</h3>
                <ul style="margin: 0;">
                    <li>Make sure your JSON file is properly formatted</li>
                    <li>Thumbnail URLs should be publicly accessible</li>
                    <li>Genres will be created automatically if they don't exist</li>
                    <li><strong>Large files:</strong> Plugin akan process secara batch (100 items per batch)</li>
                    <li><strong>Max file size:</strong> <?php echo ini_get('upload_max_filesize'); ?></li>
                    <li><strong>Max execution time:</strong> <?php echo ini_get('max_execution_time'); ?>s</li>
                </ul>
            </div>
            
            <div style="background: #d1ecf1; border: 2px solid #0c5460; padding: 20px; border-radius: 10px; margin-top: 20px;">
                <h3 style="margin-top: 0;">⚙️ Server Configuration:</h3>
                <ul style="margin: 0;">
                    <li><strong>PHP Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?></li>
                    <li><strong>Post Max Size:</strong> <?php echo ini_get('post_max_size'); ?></li>
                    <li><strong>Upload Max Filesize:</strong> <?php echo ini_get('upload_max_filesize'); ?></li>
                </ul>
                <p style="margin-bottom: 0; margin-top: 10px;"><em>Jika file terlalu besar, split menjadi beberapa file lebih kecil.</em></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render Export Page
     */
    public function render_export_page() {
        ?>
        <div class="wrap">
            <h1>💾 Export / Import Manhwa Data</h1>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">
                <!-- Export Section -->
                <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                    <h2>📥 Export Data</h2>
                    <p>Export all manhwa data to JSON format for backup or migration.</p>
                    
                    <form method="post">
                        <?php wp_nonce_field('manhwa_export_json', 'manhwa_export_nonce'); ?>
                        
                        <div style="margin: 20px 0;">
                            <label>
                                <input type="checkbox" name="include_chapters" value="1" checked>
                                Include chapters data
                            </label>
                        </div>
                        
                        <div style="margin: 20px 0;">
                            <label>
                                <input type="checkbox" name="include_meta" value="1" checked>
                                Include metadata (author, rating, etc.)
                            </label>
                        </div>
                        
                        <button type="submit" name="export_json" class="button button-primary button-large" style="width: 100%;">
                            📥 Export to JSON
                        </button>
                    </form>
                </div>
                
                <!-- Import Section -->
                <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                    <h2>📤 Import Data</h2>
                    <p>Import manhwa data from a previously exported JSON file.</p>
                    
                    <form method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field('manhwa_import_json', 'manhwa_import_nonce'); ?>
                        
                        <div style="margin: 20px 0;">
                            <label for="import_json_file" style="display: block; font-weight: bold; margin-bottom: 10px;">
                                Select JSON File:
                            </label>
                            <input type="file" name="import_json_file" id="import_json_file" accept=".json" required style="width: 100%; padding: 10px;">
                        </div>
                        
                        <div style="margin: 20px 0;">
                            <label>
                                <input type="checkbox" name="skip_existing" value="1" checked>
                                Skip existing manhwa
                            </label>
                        </div>
                        
                        <button type="submit" name="import_json" class="button button-primary button-large" style="width: 100%;">
                            📤 Import from JSON
                        </button>
                    </form>
                </div>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-top: 30px;">
                <h2>📊 Export Statistics</h2>
                <?php
                $manhwa_count = wp_count_posts('manhwa');
                $total_chapters = $this->get_total_chapters();
                ?>
                <p>Total Manhwa: <strong><?php echo $manhwa_count->publish; ?></strong></p>
                <p>Total Chapters: <strong><?php echo $total_chapters; ?></strong></p>
                <p>Estimated File Size: <strong><?php echo $this->estimate_export_size(); ?></strong></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render Bulk Actions Page
     */
    public function render_bulk_actions_page() {
        $manhwa_count = wp_count_posts('manhwa');
        ?>
        <div class="wrap">
            <h1>🔧 Bulk Actions</h1>
            
            <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-top: 20px;">
                <h2>Hapus Kata "Komik" dari Semua Judul</h2>
                <p>Tool ini akan menghapus kata "Komik" dari semua judul manhwa yang sudah ada di database.</p>
                
                <form method="post">
                    <?php wp_nonce_field('manhwa_bulk_remove_komik', 'manhwa_bulk_nonce'); ?>
                    
                    <div style="background: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 10px; margin: 20px 0;">
                        <h3 style="margin-top: 0;">⚠️ Peringatan:</h3>
                        <ul style="margin: 0;">
                            <li>Proses ini akan mengubah semua judul manhwa</li>
                            <li>Perubahan bersifat permanen</li>
                            <li>Disarankan untuk backup database terlebih dahulu</li>
                        </ul>
                    </div>
                    
                    <div style="margin: 20px 0;">
                        <label>
                            <input type="checkbox" name="confirm_bulk_action" value="1" required>
                            <strong>Saya mengerti dan ingin melanjutkan</strong>
                        </label>
                    </div>
                    
                    <button type="submit" name="bulk_remove_komik" class="button button-primary button-large">
                        🗑️ Hapus Kata "Komik" dari Semua Judul
                    </button>
                </form>
            </div>
            
            <!-- DELETE ALL MANHWA SECTION -->
            <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-top: 30px; border: 3px solid #dc3545;">
                <h2 style="color: #dc3545;">�️ Hapus Semua Data Manhwa</h2>
                <p>Tool ini akan menghapus <strong style="color: #dc3545;">SEMUA</strong> data manhwa dari database, termasuk chapters, images, dan metadata.</p>
                
                <div style="background: #f8d7da; border: 2px solid #dc3545; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <h3 style="margin-top: 0; color: #721c24;">🚨 BAHAYA - TIDAK BISA DIKEMBALIKAN!</h3>
                    <ul style="margin: 0; color: #721c24;">
                        <li>Semua <strong><?php echo $manhwa_count->publish + $manhwa_count->draft; ?></strong> manhwa akan dihapus permanen</li>
                        <li>Semua chapters dan images akan hilang</li>
                        <li>Proses ini <strong>TIDAK BISA DIKEMBALIKAN</strong></li>
                        <li>Pastikan Anda sudah export/backup data terlebih dahulu!</li>
                    </ul>
                </div>
                
                <form method="post" onsubmit="return confirmDeleteAll();">
                    <?php wp_nonce_field('manhwa_delete_all', 'manhwa_delete_all_nonce'); ?>
                    
                    <div style="margin: 20px 0;">
                        <label style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" name="confirm_delete_all" value="1" required style="width: 20px; height: 20px;">
                            <strong style="color: #dc3545;">Saya mengerti bahwa semua data akan dihapus permanen</strong>
                        </label>
                    </div>
                    
                    <div style="margin: 20px 0;">
                        <label style="display: block; margin-bottom: 10px;">
                            <strong>Ketik "HAPUS SEMUA" untuk konfirmasi:</strong>
                        </label>
                        <input type="text" name="delete_confirmation" id="delete-confirmation" placeholder="HAPUS SEMUA" required 
                               pattern="HAPUS SEMUA" title="Ketik 'HAPUS SEMUA' untuk melanjutkan"
                               style="padding: 10px; width: 300px; border: 2px solid #dc3545; border-radius: 5px;">
                    </div>
                    
                    <button type="submit" name="delete_all_manhwa" class="button button-large" 
                            style="background: #dc3545; color: white; border: none; padding: 15px 30px;">
                        🗑️ HAPUS SEMUA MANHWA
                    </button>
                </form>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-top: 30px;">
                <h2>�📊 Preview Perubahan</h2>
                <p>Berikut adalah contoh perubahan yang akan terjadi:</p>
                
                <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
                    <thead>
                        <tr>
                            <th>Judul Sebelum</th>
                            <th>Judul Sesudah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Komik Solo Leveling</td>
                            <td><strong>Solo Leveling</strong></td>
                        </tr>
                        <tr>
                            <td>Komik Tower of God</td>
                            <td><strong>Tower of God</strong></td>
                        </tr>
                        <tr>
                            <td>Komik Zero Kill Assassin</td>
                            <td><strong>Zero Kill Assassin</strong></td>
                        </tr>
                        <tr>
                            <td>Komik Zombie X Slasher</td>
                            <td><strong>Zombie X Slasher</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <script>
        function confirmDeleteAll() {
            var confirmation = document.getElementById('delete-confirmation').value;
            if (confirmation !== 'HAPUS SEMUA') {
                alert('Ketik "HAPUS SEMUA" untuk melanjutkan!');
                return false;
            }
            return confirm('PERINGATAN TERAKHIR!\n\nAnda yakin ingin menghapus SEMUA manhwa?\nAksi ini TIDAK BISA DIKEMBALIKAN!');
        }
        </script>
        <?php
    }
    
    /**
     * Render Detail Upload Page
     */
    public function render_detail_upload_page() {
        ?>
        <div class="wrap">
            <h1>📖 Upload Detail Manhwa</h1>
            
            <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-top: 20px;">
                <form method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field('manhwa_upload_detail', 'manhwa_detail_nonce'); ?>
                    
                    <h2>Upload Detail Manhwa dengan Chapters & Images</h2>
                    <p>Upload file JSON yang berisi detail lengkap manhwa termasuk chapters dan images.</p>
                    
                    <div style="background: #e7f3ff; border: 2px solid #2196F3; padding: 20px; border-radius: 10px; margin: 20px 0;">
                        <h3 style="margin-top: 0;">📋 Format JSON Detail Manhwa:</h3>
                        <div style="background: #f5f5f5; padding: 15px; border-radius: 10px; font-family: monospace; font-size: 12px; overflow-x: auto;">
<pre>{
  "manhwaTitle": "Komik Lookism",
  "alternativeTitle": "Penampilan",
  "manhwaUrl": "https://...",
  "slug": "lookism",
  "image": "https://...",
  "author": "Park Tae-jun",
  "type": "Manhwa",
  "status": "Ongoing",
  "released": "13 Tahun",
  "genres": ["Action", "Comedy"],
  "synopsis": "...",
  "totalChapters": 559,
  "chapters": [
    {
      "number": "1",
      "title": "Chapter 1",
      "url": "https://...",
      "date": "14/07/2025",
      "images": [
        {
          "page": 1,
          "url": "https://...",
          "filename": "page-001.jpg"
        }
      ]
    }
  ]
}</pre>
                        </div>
                    </div>
                    
                    <div style="margin: 30px 0;">
                        <label for="manhwa_detail_files" style="display: block; font-weight: bold; margin-bottom: 10px;">
                            Select Detail JSON Files (Multiple):
                        </label>
                        <input type="file" name="manhwa_detail_files[]" id="manhwa_detail_files" accept=".json" multiple required style="padding: 10px; width: 100%;">
                        <p style="margin-top: 10px; color: #666; font-size: 13px;">
                            💡 Tip: Tekan <strong>Ctrl</strong> atau <strong>Shift</strong> untuk select multiple files sekaligus
                        </p>
                    </div>
                    
                    <div style="background: #fff3cd; border: 2px solid #ffc107; padding: 15px; border-radius: 10px; margin: 20px 0;">
                        <h3 style="margin-top: 0;">⚙️ Options:</h3>
                        
                        <div style="margin: 15px 0;">
                            <label style="display: flex; align-items: center; font-size: 15px;">
                                <input type="checkbox" name="overwrite_detail" value="1" style="width: 20px; height: 20px; margin-right: 10px;">
                                <strong>Overwrite jika manhwa sudah ada</strong>
                            </label>
                            <p style="margin: 5px 0 0 30px; color: #666; font-size: 13px;">
                                ⚠️ Centang ini jika manhwa sudah ada dan ingin update data lengkap (image + chapters)
                            </p>
                        </div>
                        
                        <div style="margin: 15px 0;">
                            <label style="display: flex; align-items: center; font-size: 15px;">
                                <input type="checkbox" name="remove_komik_detail" value="1" checked style="width: 20px; height: 20px; margin-right: 10px;">
                                <strong>Hapus kata "Komik" dari judul</strong>
                            </label>
                            <p style="margin: 5px 0 0 30px; color: #666; font-size: 13px;">
                                "Komik Solo Leveling" → "Solo Leveling"
                            </p>
                        </div>
                        
                        <div style="margin: 15px 0;">
                            <label style="display: flex; align-items: center; font-size: 15px;">
                                <input type="checkbox" name="import_chapters" value="1" checked style="width: 20px; height: 20px; margin-right: 10px;">
                                <strong>Import chapters dan images</strong>
                            </label>
                            <p style="margin: 5px 0 0 30px; color: #666; font-size: 13px;">
                                Import semua chapter dengan images URLs (bisa ratusan chapter)
                            </p>
                        </div>
                    </div>
                    
                    <button type="submit" name="upload_detail" class="button button-primary button-large">
                        📖 Upload Detail Manhwa
                    </button>
                </form>
            </div>
            
            <div style="background: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 10px; margin-top: 30px;">
                <h3 style="margin-top: 0;">💡 Tips:</h3>
                <ul style="margin: 0;">
                    <li><strong>Detail format:</strong> Format ini untuk 1 manhwa dengan detail lengkap</li>
                    <li><strong>Chapters:</strong> Semua chapter akan disimpan dengan images</li>
                    <li><strong>Images:</strong> URL images akan disimpan untuk reader</li>
                    <li><strong>Alternative title:</strong> Akan disimpan sebagai metadata</li>
                    <li><strong>Released:</strong> Informasi tahun rilis</li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render Featured Manhwa Page
     */
    public function render_featured_page() {
        // Get current featured manhwa
        $featured_ids = get_option('manhwa_featured_ids', array());
        
        // Get all manhwa for selection
        $all_manhwa = get_posts(array(
            'post_type' => 'manhwa',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        ?>
        <div class="wrap">
            <h1>⭐ Featured Manhwa (Popular Today)</h1>
            
            <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-top: 20px;">
                <form method="post">
                    <?php wp_nonce_field('manhwa_save_featured', 'manhwa_featured_nonce'); ?>
                    
                    <h2>Pilih Manhwa untuk Ditampilkan di "Popular Today"</h2>
                    <p>Pilih hingga 10 manhwa yang akan ditampilkan di section "Popular Today" di homepage.</p>
                    
                    <div style="background: #e7f3ff; border: 2px solid #2196F3; padding: 20px; border-radius: 10px; margin: 20px 0;">
                        <h3 style="margin-top: 0;">💡 Tips:</h3>
                        <ul style="margin: 0;">
                            <li>Drag & drop untuk mengubah urutan</li>
                            <li>Manhwa pertama akan mendapat badge "COMPLETED"</li>
                            <li>Maksimal 10 manhwa</li>
                            <li>Jika tidak ada yang dipilih, akan otomatis tampil berdasarkan views</li>
                        </ul>
                    </div>
                    
                    <!-- Selected Manhwa -->
                    <div style="margin: 30px 0;">
                        <h3>Manhwa Terpilih (<?php echo count($featured_ids); ?>/10)</h3>
                        <div id="selected-manhwa" style="min-height: 100px; background: #f9f9f9; padding: 20px; border-radius: 10px; border: 2px dashed #ddd;">
                            <?php if (!empty($featured_ids)) : ?>
                                <?php foreach ($featured_ids as $index => $post_id) : 
                                    $post = get_post($post_id);
                                    if ($post) :
                                        $thumbnail = get_the_post_thumbnail_url($post_id, 'thumbnail');
                                ?>
                                    <div class="featured-item" data-id="<?php echo $post_id; ?>" style="background: white; padding: 15px; margin-bottom: 10px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; cursor: move; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                        <div style="display: flex; align-items: center; gap: 15px;">
                                            <span style="font-size: 24px; color: #999;">☰</span>
                                            <?php if ($thumbnail) : ?>
                                                <img src="<?php echo esc_url($thumbnail); ?>" style="width: 50px; height: 70px; object-fit: cover; border-radius: 5px;">
                                            <?php else : ?>
                                                <div style="width: 50px; height: 70px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 5px; display: flex; align-items: center; justify-content: center; font-size: 24px;">📖</div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?php echo esc_html($post->post_title); ?></strong>
                                                <?php if ($index === 0) : ?>
                                                    <span style="background: #dc3545; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 10px;">COMPLETED BADGE</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <button type="button" class="remove-featured button" data-id="<?php echo $post_id; ?>" style="background: #dc3545; color: white; border: none;">Remove</button>
                                    </div>
                                <?php 
                                    endif;
                                endforeach; ?>
                            <?php else : ?>
                                <p style="text-align: center; color: #999; margin: 40px 0;">Belum ada manhwa terpilih. Pilih dari daftar di bawah.</p>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="featured_manhwa_ids" id="featured-ids" value="<?php echo esc_attr(implode(',', $featured_ids)); ?>">
                    </div>
                    
                    <!-- Available Manhwa -->
                    <div style="margin: 30px 0;">
                        <h3>Pilih Manhwa</h3>
                        <input type="text" id="search-manhwa" placeholder="Search manhwa..." style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; margin-bottom: 15px;">
                        
                        <div id="available-manhwa" style="max-height: 400px; overflow-y: auto; background: #f9f9f9; padding: 20px; border-radius: 10px; border: 2px solid #ddd;">
                            <?php foreach ($all_manhwa as $manhwa) : 
                                if (in_array($manhwa->ID, $featured_ids)) continue;
                                $thumbnail = get_the_post_thumbnail_url($manhwa->ID, 'thumbnail');
                            ?>
                                <div class="available-item" data-id="<?php echo $manhwa->ID; ?>" data-title="<?php echo esc_attr(strtolower($manhwa->post_title)); ?>" style="background: white; padding: 15px; margin-bottom: 10px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: all 0.3s;">
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <?php if ($thumbnail) : ?>
                                            <img src="<?php echo esc_url($thumbnail); ?>" style="width: 50px; height: 70px; object-fit: cover; border-radius: 5px;">
                                        <?php else : ?>
                                            <div style="width: 50px; height: 70px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 5px; display: flex; align-items: center; justify-content: center; font-size: 24px;">📖</div>
                                        <?php endif; ?>
                                        <strong><?php echo esc_html($manhwa->post_title); ?></strong>
                                    </div>
                                    <button type="button" class="add-featured button button-primary" data-id="<?php echo $manhwa->ID; ?>">Add</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button type="submit" name="save_featured" class="button button-primary button-large">
                        💾 Save Featured Manhwa
                    </button>
                </form>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Search functionality
            $('#search-manhwa').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.available-item').each(function() {
                    const title = $(this).data('title');
                    if (title.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
            
            // Add to featured
            $(document).on('click', '.add-featured', function() {
                const id = $(this).data('id');
                const item = $(this).closest('.available-item');
                
                // Check limit
                if ($('.featured-item').length >= 10) {
                    alert('Maksimal 10 manhwa!');
                    return;
                }
                
                // Clone and modify
                const clone = item.clone();
                clone.removeClass('available-item').addClass('featured-item');
                clone.attr('style', 'background: white; padding: 15px; margin-bottom: 10px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; cursor: move; box-shadow: 0 2px 5px rgba(0,0,0,0.1);');
                clone.find('button').removeClass('add-featured button-primary').addClass('remove-featured').text('Remove').css({'background': '#dc3545', 'color': 'white', 'border': 'none'});
                clone.prepend('<span style="font-size: 24px; color: #999; margin-right: 15px;">☰</span>');
                
                $('#selected-manhwa').append(clone);
                item.hide();
                
                updateFeaturedIds();
            });
            
            // Remove from featured
            $(document).on('click', '.remove-featured', function() {
                const id = $(this).data('id');
                $(this).closest('.featured-item').remove();
                $(`.available-item[data-id="${id}"]`).show();
                updateFeaturedIds();
            });
            
            // Update hidden input
            function updateFeaturedIds() {
                const ids = [];
                $('.featured-item').each(function() {
                    ids.push($(this).data('id'));
                });
                $('#featured-ids').val(ids.join(','));
                
                // Update badges
                $('.featured-item').each(function(index) {
                    $(this).find('.badge').remove();
                    if (index === 0) {
                        $(this).find('strong').after('<span class="badge" style="background: #dc3545; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 10px;">COMPLETED BADGE</span>');
                    }
                });
            }
            
            // Sortable
            if (typeof $.fn.sortable !== 'undefined') {
                $('#selected-manhwa').sortable({
                    update: function() {
                        updateFeaturedIds();
                    }
                });
            }
        });
        </script>
        
        <style>
            .available-item:hover {
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                transform: translateY(-2px);
            }
        </style>
        <?php
    }
    
    /**
     * Handle Form Submissions
     */
    public function handle_form_submissions() {
        // Handle JSON Upload
        if (isset($_POST['upload_json']) && check_admin_referer('manhwa_upload_json', 'manhwa_upload_nonce')) {
            $this->handle_json_upload();
        }
        
        // Handle Export
        if (isset($_POST['export_json']) && check_admin_referer('manhwa_export_json', 'manhwa_export_nonce')) {
            $this->handle_json_export();
        }
        
        // Handle Import
        if (isset($_POST['import_json']) && check_admin_referer('manhwa_import_json', 'manhwa_import_nonce')) {
            $this->handle_json_import();
        }
        
        // Handle Bulk Remove Komik
        if (isset($_POST['bulk_remove_komik']) && check_admin_referer('manhwa_bulk_remove_komik', 'manhwa_bulk_nonce')) {
            $this->handle_bulk_remove_komik();
        }
        
        // Handle Detail Upload
        if (isset($_POST['upload_detail']) && check_admin_referer('manhwa_upload_detail', 'manhwa_detail_nonce')) {
            $this->handle_detail_upload();
        }
        
        // Handle Save Featured
        if (isset($_POST['save_featured']) && check_admin_referer('manhwa_save_featured', 'manhwa_featured_nonce')) {
            $this->handle_save_featured();
        }
        
        // Handle Delete All Manhwa
        if (isset($_POST['delete_all_manhwa']) && check_admin_referer('manhwa_delete_all', 'manhwa_delete_all_nonce')) {
            $this->handle_delete_all_manhwa();
        }
    }
    
    /**
     * Handle JSON Upload (Multiple Files)
     */
    private function handle_json_upload() {
        if (!isset($_FILES['manhwa_json_files'])) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>No files uploaded.</p></div>';
            });
            return;
        }
        
        $files = $_FILES['manhwa_json_files'];
        $file_count = count($files['name']);
        
        if ($file_count === 0) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>No files selected.</p></div>';
            });
            return;
        }
        
        $total_imported = 0;
        $total_skipped = 0;
        $total_errors = 0;
        $processed_files = 0;
        $failed_files = array();
        
        $remove_komik = isset($_POST['remove_komik_word']);
        $overwrite = isset($_POST['overwrite_existing']);
        
        // Process each file
        for ($i = 0; $i < $file_count; $i++) {
            $file_name = $files['name'][$i];
            $file_tmp = $files['tmp_name'][$i];
            $file_error = $files['error'][$i];
            
            // Skip if upload error
            if ($file_error !== UPLOAD_ERR_OK) {
                $failed_files[] = $file_name . ' (Upload error)';
                continue;
            }
            
            // Read and parse JSON
            $json_content = file_get_contents($file_tmp);
            $data = json_decode($json_content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $failed_files[] = $file_name . ' (Invalid JSON)';
                continue;
            }
            
            // Import data
            $json_handler = new Manhwa_JSON_Handler();
            $result = $json_handler->import_from_json($data, $overwrite, $remove_komik);
            
            if ($result['success']) {
                $total_imported += $result['imported'];
                $total_skipped += $result['skipped'];
                $total_errors += $result['errors'];
                $processed_files++;
            } else {
                $failed_files[] = $file_name . ' (' . $result['message'] . ')';
            }
        }
        
        // Show results
        add_action('admin_notices', function() use ($processed_files, $file_count, $total_imported, $total_skipped, $total_errors, $failed_files) {
            $message = sprintf(
                '<strong>Bulk Import Completed!</strong><br>' .
                'Files processed: %d/%d<br>' .
                'Total imported: %d<br>' .
                'Total skipped: %d<br>' .
                'Total errors: %d',
                $processed_files,
                $file_count,
                $total_imported,
                $total_skipped,
                $total_errors
            );
            
            if (!empty($failed_files)) {
                $message .= '<br><br><strong>Failed files:</strong><br>' . implode('<br>', $failed_files);
            }
            
            $class = $processed_files > 0 ? 'notice-success' : 'notice-error';
            echo '<div class="notice ' . $class . '"><p>' . $message . '</p></div>';
        });
    }
    
    /**
     * Handle JSON Export
     */
    private function handle_json_export() {
        $json_handler = new Manhwa_JSON_Handler();
        $include_chapters = isset($_POST['include_chapters']);
        $include_meta = isset($_POST['include_meta']);
        
        $json_handler->export_to_json($include_chapters, $include_meta);
    }
    
    /**
     * Handle JSON Import
     */
    private function handle_json_import() {
        if (!isset($_FILES['import_json_file'])) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>No file uploaded.</p></div>';
            });
            return;
        }
        
        $file = $_FILES['import_json_file'];
        $json_content = file_get_contents($file['tmp_name']);
        $data = json_decode($json_content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>Invalid JSON file.</p></div>';
            });
            return;
        }
        
        $json_handler = new Manhwa_JSON_Handler();
        $result = $json_handler->import_from_json($data, !isset($_POST['skip_existing']));
        
        add_action('admin_notices', function() use ($result) {
            echo '<div class="notice notice-success"><p>' . $result['message'] . '</p></div>';
        });
    }
    
    /**
     * Handle Detail Upload (Multiple Files)
     */
    private function handle_detail_upload() {
        if (!isset($_FILES['manhwa_detail_files'])) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>No files uploaded.</p></div>';
            });
            return;
        }
        
        $files = $_FILES['manhwa_detail_files'];
        $file_count = count($files['name']);
        
        if ($file_count === 0) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>No files selected.</p></div>';
            });
            return;
        }
        
        $total_imported = 0;
        $total_skipped = 0;
        $total_errors = 0;
        $processed_files = 0;
        $failed_files = array();
        
        $remove_komik = isset($_POST['remove_komik_detail']);
        $overwrite = isset($_POST['overwrite_detail']);
        $import_chapters = isset($_POST['import_chapters']);
        
        // Process each file
        for ($i = 0; $i < $file_count; $i++) {
            $file_name = $files['name'][$i];
            $file_tmp = $files['tmp_name'][$i];
            $file_error = $files['error'][$i];
            
            // Skip if upload error
            if ($file_error !== UPLOAD_ERR_OK) {
                $failed_files[] = $file_name . ' (Upload error)';
                $total_errors++;
                continue;
            }
            
            // Read and parse JSON
            $json_content = file_get_contents($file_tmp);
            $data = json_decode($json_content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $failed_files[] = $file_name . ' (Invalid JSON)';
                $total_errors++;
                continue;
            }
            
            // Import detail
            $json_handler = new Manhwa_JSON_Handler();
            $result = $json_handler->import_detail_manhwa($data, $overwrite, $remove_komik, $import_chapters);
            
            if ($result['success']) {
                $total_imported++;
                $processed_files++;
            } else {
                $failed_files[] = $file_name . ' (' . strip_tags($result['message']) . ')';
                if (strpos($result['message'], 'already exists') !== false) {
                    $total_skipped++;
                } else {
                    $total_errors++;
                }
            }
        }
        
        // Show results
        add_action('admin_notices', function() use ($processed_files, $file_count, $total_imported, $total_skipped, $total_errors, $failed_files) {
            $message = sprintf(
                '<strong>Bulk Detail Import Completed!</strong><br>' .
                'Files processed: %d/%d<br>' .
                'Successfully imported: %d manhwa<br>' .
                'Skipped (already exists): %d<br>' .
                'Errors: %d',
                $processed_files + count($failed_files),
                $file_count,
                $total_imported,
                $total_skipped,
                $total_errors
            );
            
            if (!empty($failed_files)) {
                $message .= '<br><br><strong>Failed/Skipped files:</strong><br>' . implode('<br>', array_slice($failed_files, 0, 10));
                if (count($failed_files) > 10) {
                    $message .= '<br>... and ' . (count($failed_files) - 10) . ' more';
                }
            }
            
            $class = $total_imported > 0 ? 'notice-success' : 'notice-warning';
            echo '<div class="notice ' . $class . '"><p>' . $message . '</p></div>';
        });
    }
    
    /**
     * Handle Save Featured
     */
    private function handle_save_featured() {
        if (!isset($_POST['featured_manhwa_ids'])) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>No data received.</p></div>';
            });
            return;
        }
        
        $ids_string = sanitize_text_field($_POST['featured_manhwa_ids']);
        $ids = array_filter(array_map('intval', explode(',', $ids_string)));
        
        // Limit to 10
        $ids = array_slice($ids, 0, 10);
        
        update_option('manhwa_featured_ids', $ids);
        
        add_action('admin_notices', function() use ($ids) {
            $count = count($ids);
            echo '<div class="notice notice-success"><p>✅ Featured manhwa saved! Total: ' . $count . ' manhwa.</p></div>';
        });
    }
    
    /**
     * Handle Bulk Remove Komik
     */
    private function handle_bulk_remove_komik() {
        $manhwa_posts = get_posts(array(
            'post_type' => 'manhwa',
            'posts_per_page' => -1,
            'post_status' => 'any'
        ));
        
        $updated = 0;
        $skipped = 0;
        
        foreach ($manhwa_posts as $post) {
            $old_title = $post->post_title;
            
            // Remove "Komik" from title
            $new_title = preg_replace('/^Komik\s+/i', '', $old_title);
            $new_title = preg_replace('/\s+Komik$/i', '', $new_title);
            $new_title = preg_replace('/\s+Komik\s+/i', ' ', $new_title);
            $new_title = trim($new_title);
            
            // Only update if title changed
            if ($old_title !== $new_title) {
                wp_update_post(array(
                    'ID' => $post->ID,
                    'post_title' => $new_title
                ));
                $updated++;
            } else {
                $skipped++;
            }
        }
        
        add_action('admin_notices', function() use ($updated, $skipped) {
            $message = sprintf(
                '<strong>Bulk Remove "Komik" Completed!</strong><br>' .
                'Updated: %d manhwa<br>' .
                'Skipped: %d manhwa (no "Komik" word found)',
                $updated,
                $skipped
            );
            echo '<div class="notice notice-success"><p>' . $message . '</p></div>';
        });
    }
    
    /**
     * Get Total Chapters
     */
    private function get_total_chapters() {
        global $wpdb;
        $result = $wpdb->get_var("
            SELECT SUM(LENGTH(meta_value) - LENGTH(REPLACE(meta_value, 'title', ''))) / LENGTH('title')
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_manhwa_chapters'
        ");
        return intval($result);
    }
    
    /**
     * Get Total Views
     */
    private function get_total_views() {
        global $wpdb;
        $result = $wpdb->get_var("
            SELECT SUM(CAST(meta_value AS UNSIGNED))
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_manhwa_views'
        ");
        return intval($result);
    }
    
    /**
     * Estimate Export Size
     */
    private function estimate_export_size() {
        $manhwa_count = wp_count_posts('manhwa')->publish;
        $estimated_kb = $manhwa_count * 5; // Rough estimate: 5KB per manhwa
        
        if ($estimated_kb < 1024) {
            return $estimated_kb . ' KB';
        } else {
            return round($estimated_kb / 1024, 2) . ' MB';
        }
    }
    
    /**
     * Handle Delete All Manhwa
     */
    private function handle_delete_all_manhwa() {
        // Verify confirmation checkbox
        if (!isset($_POST['confirm_delete_all']) || $_POST['confirm_delete_all'] !== '1') {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>Anda harus mencentang kotak konfirmasi untuk menghapus semua manhwa.</p></div>';
            });
            return;
        }
        
        // Verify text confirmation
        if (!isset($_POST['confirm_text']) || strtoupper(trim($_POST['confirm_text'])) !== 'HAPUS SEMUA') {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>Ketik "HAPUS SEMUA" dengan benar untuk konfirmasi penghapusan.</p></div>';
            });
            return;
        }
        
        global $wpdb;
        
        // Get all manhwa posts
        $manhwa_posts = get_posts(array(
            'post_type' => 'manhwa',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'fields' => 'ids'
        ));
        
        if (empty($manhwa_posts)) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-warning"><p>Tidak ada post manhwa yang ditemukan untuk dihapus.</p></div>';
            });
            return;
        }
        
        $deleted_count = 0;
        $deleted_attachments = 0;
        
        foreach ($manhwa_posts as $post_id) {
            // Delete featured image/attachment
            $thumbnail_id = get_post_thumbnail_id($post_id);
            if ($thumbnail_id) {
                wp_delete_attachment($thumbnail_id, true);
                $deleted_attachments++;
            }
            
            // Delete any other attachments linked to this post
            $attachments = get_posts(array(
                'post_type' => 'attachment',
                'posts_per_page' => -1,
                'post_parent' => $post_id,
                'fields' => 'ids'
            ));
            
            foreach ($attachments as $attachment_id) {
                wp_delete_attachment($attachment_id, true);
                $deleted_attachments++;
            }
            
            // Delete the post (this also deletes post meta)
            $result = wp_delete_post($post_id, true);
            if ($result) {
                $deleted_count++;
            }
        }
        
        // Clean up any orphaned meta data
        $wpdb->query("
            DELETE pm FROM {$wpdb->postmeta} pm
            LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID
            WHERE p.ID IS NULL
        ");
        
        // Clean up term relationships
        $wpdb->query("
            DELETE tr FROM {$wpdb->term_relationships} tr
            LEFT JOIN {$wpdb->posts} p ON tr.object_id = p.ID
            WHERE p.ID IS NULL
        ");
        
        $final_deleted = $deleted_count;
        $final_attachments = $deleted_attachments;
        
        add_action('admin_notices', function() use ($final_deleted, $final_attachments) {
            echo '<div class="notice notice-success"><p>';
            echo '<strong>✅ Penghapusan Berhasil!</strong><br>';
            echo sprintf('• %d post manhwa telah dihapus.<br>', $final_deleted);
            echo sprintf('• %d attachment/gambar telah dihapus.<br>', $final_attachments);
            echo '• Meta data dan term relationships yang orphan telah dibersihkan.';
            echo '</p></div>';
        });
    }
    
    /**
     * Add Delete All Button on Manhwa List Page
     */
    public function add_delete_all_button($post_type) {
        if ($post_type !== 'manhwa') {
            return;
        }
        
        $count = wp_count_posts('manhwa');
        $total = isset($count->publish) ? $count->publish : 0;
        $total += isset($count->draft) ? $count->draft : 0;
        $total += isset($count->pending) ? $count->pending : 0;
        $total += isset($count->private) ? $count->private : 0;
        
        if ($total > 0) {
            $nonce = wp_create_nonce('delete_all_manhwa_nonce');
            ?>
            <button type="button" id="delete-all-manhwa-btn" class="button delete-all-manhwa-btn">
                🗑️ Hapus Semua (<?php echo number_format($total); ?>)
            </button>
            
            <!-- Delete Confirmation Modal -->
            <div id="delete-all-modal" class="delete-all-modal" style="display: none;">
                <div class="delete-all-modal-content">
                    <div class="delete-all-modal-header">
                        <span class="delete-all-modal-icon">⚠️</span>
                        <h2>Hapus Semua Manhwa?</h2>
                    </div>
                    <div class="delete-all-modal-body">
                        <p class="delete-warning">
                            <strong>PERINGATAN!</strong> Anda akan menghapus <strong><?php echo number_format($total); ?> manhwa</strong> beserta semua data terkait:
                        </p>
                        <ul>
                            <li>Semua post manhwa</li>
                            <li>Gambar cover/thumbnail</li>
                            <li>Data chapter</li>
                            <li>Semua meta data</li>
                        </ul>
                        <p class="delete-permanent">Tindakan ini <strong>TIDAK DAPAT DIBATALKAN!</strong></p>
                        
                        <div id="delete-form-container">
                            <div class="confirm-field">
                                <label>
                                    <input type="checkbox" id="confirm-checkbox" value="1">
                                    Saya mengerti dan ingin menghapus semua data manhwa
                                </label>
                            </div>
                            
                            <div class="confirm-field">
                                <label>Ketik <strong>HAPUS SEMUA</strong> untuk konfirmasi:</label>
                                <input type="text" id="confirm-text-input" placeholder="HAPUS SEMUA" autocomplete="off">
                            </div>
                            
                            <div class="modal-buttons">
                                <button type="button" class="button cancel-btn" onclick="closeDeleteModal()">Batal</button>
                                <button type="button" class="button button-danger" id="confirm-delete-btn" disabled>
                                    🗑️ Hapus Semua Manhwa
                                </button>
                            </div>
                        </div>
                        
                        <div id="delete-progress" style="display: none;">
                            <div class="progress-message">
                                <span class="spinner is-active" style="float: none; margin: 0 10px 0 0;"></span>
                                <span id="progress-text">Menghapus data...</span>
                            </div>
                        </div>
                        
                        <div id="delete-result" style="display: none;"></div>
                    </div>
                </div>
            </div>
            
            <script>
            (function() {
                var deleteBtn = document.getElementById('delete-all-manhwa-btn');
                var modal = document.getElementById('delete-all-modal');
                var confirmBtn = document.getElementById('confirm-delete-btn');
                var confirmText = document.getElementById('confirm-text-input');
                var confirmCheckbox = document.getElementById('confirm-checkbox');
                var formContainer = document.getElementById('delete-form-container');
                var progressContainer = document.getElementById('delete-progress');
                var resultContainer = document.getElementById('delete-result');
                var progressText = document.getElementById('progress-text');
                
                deleteBtn.addEventListener('click', function() {
                    modal.style.display = 'flex';
                });
                
                window.closeDeleteModal = function() {
                    modal.style.display = 'none';
                    confirmText.value = '';
                    confirmCheckbox.checked = false;
                    confirmBtn.disabled = true;
                    formContainer.style.display = 'block';
                    progressContainer.style.display = 'none';
                    resultContainer.style.display = 'none';
                };
                
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeDeleteModal();
                    }
                });
                
                function validateForm() {
                    var isTextValid = confirmText.value.toUpperCase().trim() === 'HAPUS SEMUA';
                    var isChecked = confirmCheckbox.checked;
                    confirmBtn.disabled = !(isTextValid && isChecked);
                }
                
                confirmText.addEventListener('input', validateForm);
                confirmCheckbox.addEventListener('change', validateForm);
                
                confirmBtn.addEventListener('click', function() {
                    var isTextValid = confirmText.value.toUpperCase().trim() === 'HAPUS SEMUA';
                    var isChecked = confirmCheckbox.checked;
                    
                    if (!isTextValid || !isChecked) {
                        alert('Mohon lengkapi konfirmasi dengan benar.');
                        return;
                    }
                    
                    // Show progress
                    formContainer.style.display = 'none';
                    progressContainer.style.display = 'block';
                    
                    // Send AJAX request
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', ajaxurl, true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            progressContainer.style.display = 'none';
                            resultContainer.style.display = 'block';
                            
                            if (xhr.status === 200) {
                                try {
                                    var response = JSON.parse(xhr.responseText);
                                    if (response.success) {
                                        resultContainer.innerHTML = '<div class="delete-success">' +
                                            '<span style="font-size: 48px; display: block; margin-bottom: 10px;">✅</span>' +
                                            '<h3>Penghapusan Berhasil!</h3>' +
                                            '<p>• ' + response.data.deleted_posts + ' post manhwa dihapus</p>' +
                                            '<p>• ' + response.data.deleted_attachments + ' attachment dihapus</p>' +
                                            '<button type="button" class="button button-primary" onclick="location.reload()">Refresh Halaman</button>' +
                                            '</div>';
                                    } else {
                                        resultContainer.innerHTML = '<div class="delete-error">' +
                                            '<span style="font-size: 48px; display: block; margin-bottom: 10px;">❌</span>' +
                                            '<h3>Error!</h3>' +
                                            '<p>' + (response.data ? response.data.message : 'Terjadi kesalahan') + '</p>' +
                                            '<button type="button" class="button" onclick="closeDeleteModal()">Tutup</button>' +
                                            '</div>';
                                    }
                                } catch (e) {
                                    resultContainer.innerHTML = '<div class="delete-error">' +
                                        '<span style="font-size: 48px; display: block; margin-bottom: 10px;">❌</span>' +
                                        '<h3>Error!</h3>' +
                                        '<p>Gagal memproses response: ' + e.message + '</p>' +
                                        '<button type="button" class="button" onclick="closeDeleteModal()">Tutup</button>' +
                                        '</div>';
                                }
                            } else {
                                resultContainer.innerHTML = '<div class="delete-error">' +
                                    '<span style="font-size: 48px; display: block; margin-bottom: 10px;">❌</span>' +
                                    '<h3>Error!</h3>' +
                                    '<p>Request gagal dengan status: ' + xhr.status + '</p>' +
                                    '<button type="button" class="button" onclick="closeDeleteModal()">Tutup</button>' +
                                    '</div>';
                            }
                        }
                    };
                    
                    xhr.send('action=delete_all_manhwa&nonce=<?php echo $nonce; ?>&confirm=1');
                });
            })();
            </script>
            <?php
        }
    }
    
    /**
     * Add Delete All Button Styles
     */
    public function add_delete_all_styles() {
        global $typenow;
        if ($typenow !== 'manhwa') {
            return;
        }
        ?>
        <style>
        /* Clean table layout for manhwa list */
        .post-type-manhwa .wp-list-table {
            table-layout: auto !important;
        }
        
        .post-type-manhwa .wp-list-table th,
        .post-type-manhwa .wp-list-table td {
            padding: 8px 10px;
        }
        
        /* Checkbox column */
        .post-type-manhwa .column-cb {
            width: 30px !important;
        }
        
        /* Title column takes remaining space */
        .post-type-manhwa .column-title {
            width: auto;
        }
        
        /* Date column */
        .post-type-manhwa .column-date {
            width: 120px !important;
        }
        
        /* Yoast SEO columns */
        .post-type-manhwa .column-wpseo-score {
            width: 50px !important;
            text-align: center;
        }
        
        .post-type-manhwa .column-wpseo-title {
            width: 180px !important;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .post-type-manhwa .column-wpseo-links {
            width: 50px !important;
            text-align: center;
        }
        
        .post-type-manhwa .column-wpseo-score-readability {
            width: 50px !important;
            text-align: center;
        }
        
        .delete-all-manhwa-btn {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
            border-color: #bd2130 !important;
            color: #fff !important;
            margin-left: 8px !important;
            font-weight: 500 !important;
        }
        .delete-all-manhwa-btn:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71d2a 100%) !important;
            border-color: #a71d2a !important;
            color: #fff !important;
        }
        
        .delete-all-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 100000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .delete-all-modal-content {
            background: #fff;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .delete-all-modal-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        
        .delete-all-modal-icon {
            font-size: 48px;
            display: block;
            margin-bottom: 10px;
        }
        
        .delete-all-modal-header h2 {
            margin: 0;
            font-size: 24px;
            color: #fff;
        }
        
        .delete-all-modal-body {
            padding: 20px;
        }
        
        .delete-warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 12px;
            color: #856404;
        }
        
        .delete-all-modal-body ul {
            margin: 15px 0;
            padding-left: 25px;
        }
        
        .delete-all-modal-body li {
            margin-bottom: 5px;
            color: #666;
        }
        
        .delete-permanent {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 12px;
            color: #721c24;
            text-align: center;
        }
        
        .confirm-field {
            margin: 15px 0;
        }
        
        .confirm-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .confirm-field input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .confirm-field input[type="text"]:focus {
            border-color: #dc3545;
            outline: none;
        }
        
        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .modal-buttons .button {
            flex: 1;
            padding: 12px 20px !important;
            height: auto !important;
            font-size: 14px !important;
        }
        
        .cancel-btn {
            background: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
        }
        
        .cancel-btn:hover {
            background: #5a6268 !important;
            border-color: #545b62 !important;
            color: #fff !important;
        }
        
        .button-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
            border-color: #bd2130 !important;
            color: #fff !important;
        }
        
        .button-danger:hover:not(:disabled) {
            background: linear-gradient(135deg, #c82333 0%, #a71d2a 100%) !important;
        }
        
        .button-danger:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .progress-message {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-size: 16px;
        }
        
        .delete-success, .delete-error {
            text-align: center;
            padding: 20px;
        }
        
        .delete-success h3 {
            color: #28a745;
            margin: 10px 0;
        }
        
        .delete-error h3 {
            color: #dc3545;
            margin: 10px 0;
        }
        </style>
        <?php
    }
    
    /**
     * AJAX Handler for Delete All Manhwa
     */
    public function ajax_delete_all_manhwa() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'delete_all_manhwa_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed. Please refresh the page and try again.'));
        }
        
        // Check user capability
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'You do not have permission to perform this action.'));
        }
        
        // Check confirmation
        if (!isset($_POST['confirm']) || $_POST['confirm'] !== '1') {
            wp_send_json_error(array('message' => 'Confirmation required.'));
        }
        
        global $wpdb;
        
        // Get all manhwa posts
        $manhwa_posts = get_posts(array(
            'post_type' => 'manhwa',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'fields' => 'ids'
        ));
        
        if (empty($manhwa_posts)) {
            wp_send_json_error(array('message' => 'Tidak ada post manhwa yang ditemukan untuk dihapus.'));
        }
        
        $deleted_count = 0;
        $deleted_attachments = 0;
        
        foreach ($manhwa_posts as $post_id) {
            // Delete featured image/attachment
            $thumbnail_id = get_post_thumbnail_id($post_id);
            if ($thumbnail_id) {
                wp_delete_attachment($thumbnail_id, true);
                $deleted_attachments++;
            }
            
            // Delete any other attachments linked to this post
            $attachments = get_posts(array(
                'post_type' => 'attachment',
                'posts_per_page' => -1,
                'post_parent' => $post_id,
                'fields' => 'ids'
            ));
            
            foreach ($attachments as $attachment_id) {
                wp_delete_attachment($attachment_id, true);
                $deleted_attachments++;
            }
            
            // Delete the post (this also deletes post meta)
            $result = wp_delete_post($post_id, true);
            if ($result) {
                $deleted_count++;
            }
        }
        
        // Clean up any orphaned meta data
        $wpdb->query("
            DELETE pm FROM {$wpdb->postmeta} pm
            LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID
            WHERE p.ID IS NULL
        ");
        
        // Clean up term relationships
        $wpdb->query("
            DELETE tr FROM {$wpdb->term_relationships} tr
            LEFT JOIN {$wpdb->posts} p ON tr.object_id = p.ID
            WHERE p.ID IS NULL
        ");
        
        wp_send_json_success(array(
            'deleted_posts' => $deleted_count,
            'deleted_attachments' => $deleted_attachments,
            'message' => 'Berhasil menghapus semua data manhwa.'
        ));
    }
    
    /**
     * Add Custom Columns to Manhwa List
     * Disabled - Using default WordPress columns + Yoast SEO
     */
    public function add_manhwa_columns($columns) {
        // Just return default columns without adding custom ones
        // This allows Yoast SEO columns to show properly
        return $columns;
    }
    
    /**
     * Render Custom Column Content
     */
    public function render_manhwa_columns($column, $post_id) {
        switch ($column) {
            case 'thumbnail':
                if (has_post_thumbnail($post_id)) {
                    echo '<div class="manhwa-thumb-wrapper">';
                    echo get_the_post_thumbnail($post_id, array(50, 70), array(
                        'class' => 'manhwa-admin-thumb',
                        'style' => 'width: 50px; height: 70px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);'
                    ));
                    echo '</div>';
                } else {
                    echo '<div style="width: 50px; height: 70px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">📖</div>';
                }
                break;
                
            case 'status':
                $status = get_post_meta($post_id, '_manhwa_status', true);
                if (!$status) $status = 'ongoing';
                
                $status_colors = array(
                    'ongoing' => array('bg' => '#dbeafe', 'text' => '#1e40af', 'label' => 'Ongoing'),
                    'completed' => array('bg' => '#dcfce7', 'text' => '#15803d', 'label' => 'Completed'),
                    'hiatus' => array('bg' => '#fef3c7', 'text' => '#a16207', 'label' => 'Hiatus'),
                    'dropped' => array('bg' => '#fee2e2', 'text' => '#991b1b', 'label' => 'Dropped')
                );
                
                $color = isset($status_colors[$status]) ? $status_colors[$status] : $status_colors['ongoing'];
                
                echo sprintf(
                    '<span style="display: inline-block; padding: 4px 12px; background: %s; color: %s; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">%s</span>',
                    $color['bg'],
                    $color['text'],
                    $color['label']
                );
                break;
                
            case 'chapters':
                $chapters = get_post_meta($post_id, '_manhwa_chapters', true);
                $count = is_array($chapters) ? count($chapters) : 0;
                
                if ($count > 0) {
                    echo sprintf(
                        '<div style="display: flex; align-items: center; gap: 6px;"><span class="dashicons dashicons-book" style="color: #6366f1; font-size: 18px;"></span><strong style="color: #1e293b; font-size: 14px;">%d</strong></div>',
                        $count
                    );
                } else {
                    echo '<span style="color: #94a3b8; font-size: 13px;">—</span>';
                }
                break;
                
            case 'rating':
                $rating = get_post_meta($post_id, '_manhwa_rating', true);
                
                if ($rating) {
                    $rating_num = floatval($rating);
                    
                    // Normalize rating to 5-star scale if it's on 10-point scale
                    if ($rating_num > 5) {
                        $rating_num = $rating_num / 2; // Convert 10-point to 5-point
                    }
                    
                    // Calculate stars (max 5)
                    $full_stars = floor($rating_num);
                    $half_star = ($rating_num - $full_stars) >= 0.5 ? 1 : 0;
                    $empty_stars = 5 - $full_stars - $half_star;
                    
                    $stars = str_repeat('⭐', $full_stars);
                    if ($half_star) $stars .= '✨';
                    
                    echo sprintf(
                        '<div style="display: flex; align-items: center; gap: 6px;"><span style="font-size: 14px; line-height: 1;">%s</span><strong style="color: #f59e0b; font-size: 13px;">%.1f</strong></div>',
                        $stars ?: '⭐',
                        $rating_num
                    );
                } else {
                    echo '<span style="color: #94a3b8; font-size: 13px;">—</span>';
                }
                break;
                
            case 'views':
                $views = get_post_meta($post_id, '_manhwa_views', true);
                $views = $views ? intval($views) : 0;
                
                if ($views > 0) {
                    echo sprintf(
                        '<div style="display: flex; align-items: center; gap: 6px;"><span class="dashicons dashicons-visibility" style="color: #8b5cf6; font-size: 18px;"></span><strong style="color: #1e293b; font-size: 14px;">%s</strong></div>',
                        number_format($views)
                    );
                } else {
                    echo '<span style="color: #94a3b8; font-size: 13px;">0</span>';
                }
                
                // Hidden data for Quick Edit (only render once per row)
                $type = get_post_meta($post_id, '_manhwa_type', true);
                $status = get_post_meta($post_id, '_manhwa_status', true);
                $rating = get_post_meta($post_id, '_manhwa_rating', true);
                $year = get_post_meta($post_id, '_manhwa_release_year', true);
                $author = get_post_meta($post_id, '_manhwa_author', true);
                $artist = get_post_meta($post_id, '_manhwa_artist', true);
                
                echo '<span class="manhwa-data-type" style="display:none;">' . esc_html($type) . '</span>';
                echo '<span class="manhwa-data-status" style="display:none;">' . esc_html($status) . '</span>';
                echo '<span class="manhwa-data-rating" style="display:none;">' . esc_html($rating) . '</span>';
                echo '<span class="manhwa-data-views" style="display:none;">' . esc_html($views) . '</span>';
                echo '<span class="manhwa-data-year" style="display:none;">' . esc_html($year) . '</span>';
                echo '<span class="manhwa-data-author" style="display:none;">' . esc_html($author) . '</span>';
                echo '<span class="manhwa-data-artist" style="display:none;">' . esc_html($artist) . '</span>';
                break;
        }
    }
    
    /**
     * Make Columns Sortable
     */
    public function make_manhwa_columns_sortable($columns) {
        $columns['chapters'] = 'chapters';
        $columns['rating'] = 'rating';
        $columns['views'] = 'views';
        return $columns;
    }
    
    /**
     * Add Custom Styles for Manhwa List Page
     */
    public function add_manhwa_list_styles() {
        global $post_type;
        if ($post_type !== 'manhwa') return;
        ?>
        <style>
            /* Manhwa List Table Enhancements */
            .wp-list-table.posts #the-list tr {
                transition: background-color 0.2s ease;
            }
            
            .wp-list-table.posts #the-list tr:hover {
                background-color: #f8fafc !important;
            }
            
            .wp-list-table .column-thumbnail {
                width: 70px;
                padding: 8px !important;
            }
            
            .wp-list-table .column-status {
                width: 110px;
            }
            
            .wp-list-table .column-chapters {
                width: 100px;
            }
            
            .wp-list-table .column-rating {
                width: 100px;
            }
            
            .wp-list-table .column-views {
                width: 100px;
            }
            
            .wp-list-table .column-title {
                font-weight: 600;
            }
            
            .wp-list-table .column-title .row-title {
                font-size: 14px;
                color: #1e293b;
                font-weight: 600;
            }
            
            .wp-list-table .column-title .row-title:hover {
                color: #3b82f6;
            }
            
            .manhwa-thumb-wrapper {
                position: relative;
                display: inline-block;
            }
            
            .manhwa-thumb-wrapper:hover {
                transform: scale(1.05);
                transition: transform 0.2s ease;
            }
            
            /* Better spacing */
            .wp-list-table td {
                vertical-align: middle !important;
            }
            
            /* Yoast SEO Column Adjustments */
            .wp-list-table .column-wpseo-score {
                width: 60px !important;
                text-align: center;
            }
            
            .wp-list-table .column-wpseo-title {
                width: 80px !important;
            }
            
            .wp-list-table .column-wpseo-links {
                width: 60px !important;
                text-align: center;
            }
            
            /* Hide Yoast columns that were removed */
            .wp-list-table .column-wpseo-score-readability,
            .wp-list-table .column-wpseo-metadesc,
            .wp-list-table .column-wpseo-focuskw,
            .wp-list-table .column-wpseo-cornerstone,
            .wp-list-table .column-wpseo-orphaned {
                display: none !important;
            }
            
            /* Responsive adjustments */
            @media screen and (max-width: 1280px) {
                .wp-list-table .column-thumbnail,
                .wp-list-table .column-wpseo-title,
                .wp-list-table .column-wpseo-links {
                    display: none;
                }
            }
            
            @media screen and (max-width: 782px) {
                .wp-list-table .column-thumbnail,
                .wp-list-table .column-rating,
                .wp-list-table .column-wpseo-score,
                .wp-list-table .column-wpseo-title,
                .wp-list-table .column-wpseo-links {
                    display: none;
                }
            }
        </style>
        <?php
    }
    
    /**
     * Enqueue Admin Styles
     */
    public function enqueue_admin_styles($hook) {
        // Only load on manhwa edit page
        global $post_type;
        if (($hook === 'post.php' || $hook === 'post-new.php') && $post_type === 'manhwa') {
            wp_enqueue_style(
                'manhwa-admin-edit',
                plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin-edit.css',
                array(),
                '1.0.0'
            );
        }
    }
    
    /**
     * Add Quick Edit Custom Fields
     */
    public function add_quick_edit_fields($column_name, $post_type) {
        if ($post_type !== 'manhwa') return;
        
        // Only add once (on first custom column)
        static $printed = false;
        if ($printed) return;
        $printed = true;
        ?>
        <fieldset class="inline-edit-col-right inline-edit-manhwa">
            <div class="inline-edit-col">
                <span class="title" style="font-weight: 600; color: #1e40af; margin-bottom: 12px; display: block; font-size: 13px;">
                    📚 Manhwa Details
                </span>
                
                <div class="inline-edit-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                    <label style="display: block;">
                        <span class="title" style="display: block; margin-bottom: 4px; font-size: 12px; color: #64748b;">Type</span>
                        <select name="manhwa_type" style="width: 100%; padding: 6px 8px; border-radius: 6px; border: 1px solid #cbd5e1;">
                            <option value="">— Select —</option>
                            <option value="Manhwa">🇰🇷 Manhwa</option>
                            <option value="Manga">🇯🇵 Manga</option>
                            <option value="Manhua">🇨🇳 Manhua</option>
                            <option value="Comic">🌍 Comic</option>
                        </select>
                    </label>
                    
                    <label style="display: block;">
                        <span class="title" style="display: block; margin-bottom: 4px; font-size: 12px; color: #64748b;">Status</span>
                        <select name="manhwa_status" style="width: 100%; padding: 6px 8px; border-radius: 6px; border: 1px solid #cbd5e1;">
                            <option value="">— Select —</option>
                            <option value="ongoing">🔵 Ongoing</option>
                            <option value="completed">🟢 Completed</option>
                            <option value="hiatus">🟡 Hiatus</option>
                        </select>
                    </label>
                </div>
                
                <div class="inline-edit-group" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                    <label style="display: block;">
                        <span class="title" style="display: block; margin-bottom: 4px; font-size: 12px; color: #64748b;">⭐ Rating</span>
                        <input type="number" name="manhwa_rating" step="0.1" min="0" max="5" placeholder="4.5" style="width: 100%; padding: 6px 8px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    </label>
                    
                    <label style="display: block;">
                        <span class="title" style="display: block; margin-bottom: 4px; font-size: 12px; color: #64748b;">👁️ Views</span>
                        <input type="number" name="manhwa_views" min="0" placeholder="0" style="width: 100%; padding: 6px 8px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    </label>
                    
                    <label style="display: block;">
                        <span class="title" style="display: block; margin-bottom: 4px; font-size: 12px; color: #64748b;">📅 Year</span>
                        <input type="number" name="manhwa_release_year" min="1900" max="2099" placeholder="2024" style="width: 100%; padding: 6px 8px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    </label>
                </div>
                
                <div class="inline-edit-group" style="margin-bottom: 12px;">
                    <label style="display: block;">
                        <span class="title" style="display: block; margin-bottom: 4px; font-size: 12px; color: #64748b;">✍️ Author</span>
                        <input type="text" name="manhwa_author" placeholder="Author name" style="width: 100%; padding: 6px 8px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    </label>
                </div>
                
                <div class="inline-edit-group">
                    <label style="display: block;">
                        <span class="title" style="display: block; margin-bottom: 4px; font-size: 12px; color: #64748b;">🎨 Artist</span>
                        <input type="text" name="manhwa_artist" placeholder="Artist name" style="width: 100%; padding: 6px 8px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    </label>
                </div>
            </div>
        </fieldset>
        
        <style>
            .inline-edit-manhwa {
                background: #f8fafc;
                padding: 16px;
                border-radius: 8px;
                margin-top: 12px;
                border: 1px solid #e2e8f0;
            }
            
            .inline-edit-manhwa input:focus,
            .inline-edit-manhwa select:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
            }
        </style>
        <?php
    }
    
    /**
     * Save Quick Edit Fields
     */
    public function save_quick_edit_fields($post_id) {
        // Skip autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Verify this is a manhwa
        if (get_post_type($post_id) !== 'manhwa') {
            return;
        }
        
        // Check if this is quick edit (inline-save)
        if (!isset($_POST['_inline_edit'])) {
            return;
        }
        
        // Save fields
        $fields = array(
            'manhwa_type' => '_manhwa_type',
            'manhwa_status' => '_manhwa_status',
            'manhwa_rating' => '_manhwa_rating',
            'manhwa_views' => '_manhwa_views',
            'manhwa_release_year' => '_manhwa_release_year',
            'manhwa_author' => '_manhwa_author',
            'manhwa_artist' => '_manhwa_artist'
        );
        
        foreach ($fields as $field => $meta_key) {
            if (isset($_POST[$field])) {
                $value = sanitize_text_field($_POST[$field]);
                if (!empty($value)) {
                    update_post_meta($post_id, $meta_key, $value);
                }
            }
        }
    }
    
    /**
     * Quick Edit JavaScript - Populate Fields
     */
    public function quick_edit_javascript() {
        global $post_type;
        if ($post_type !== 'manhwa') return;
        ?>
        <script type="text/javascript">
        jQuery(function($) {
            // Store original Quick Edit function
            const $originalInlineEdit = inlineEditPost.edit;
            
            // Override Quick Edit function
            inlineEditPost.edit = function(id) {
                // Call original function
                $originalInlineEdit.apply(this, arguments);
                
                // Get post ID
                let postId = 0;
                if (typeof(id) === 'object') {
                    postId = parseInt(this.getId(id));
                }
                
                if (postId > 0) {
                    // Get the row
                    const $row = $('#post-' + postId);
                    const $editRow = $('#edit-' + postId);
                    
                    // Get values from hidden data attributes in the row
                    const type = $row.find('.manhwa-data-type').text() || '';
                    const status = $row.find('.manhwa-data-status').text() || '';
                    const rating = $row.find('.manhwa-data-rating').text() || '';
                    const views = $row.find('.manhwa-data-views').text() || '';
                    const year = $row.find('.manhwa-data-year').text() || '';
                    const author = $row.find('.manhwa-data-author').text() || '';
                    const artist = $row.find('.manhwa-data-artist').text() || '';
                    
                    // Populate fields
                    $editRow.find('select[name="manhwa_type"]').val(type);
                    $editRow.find('select[name="manhwa_status"]').val(status);
                    $editRow.find('input[name="manhwa_rating"]').val(rating);
                    $editRow.find('input[name="manhwa_views"]').val(views);
                    $editRow.find('input[name="manhwa_release_year"]').val(year);
                    $editRow.find('input[name="manhwa_author"]').val(author);
                    $editRow.find('input[name="manhwa_artist"]').val(artist);
                }
                
                return this;
            };
        });
        </script>
        <?php
    }
}

