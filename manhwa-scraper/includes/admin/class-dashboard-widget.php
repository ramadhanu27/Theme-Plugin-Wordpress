<?php
/**
 * Dashboard Widget Class
 * Displays scraper statistics on WordPress Dashboard
 */

if (!defined('ABSPATH')) {
    exit;
}

class MWS_Dashboard_Widget {
    
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
        add_action('wp_dashboard_setup', [$this, 'register_widget']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
    }
    
    /**
     * Register dashboard widget
     */
    public function register_widget() {
        wp_add_dashboard_widget(
            'mws_dashboard_widget',
            '📚 Manhwa Scraper',
            [$this, 'render_widget'],
            null,
            null,
            'normal',
            'high'
        );
    }
    
    /**
     * Enqueue widget styles
     */
    public function enqueue_styles($hook) {
        if ($hook !== 'index.php') {
            return;
        }
        
        wp_add_inline_style('dashboard', $this->get_widget_css());
    }
    
    /**
     * Get widget CSS
     */
    private function get_widget_css() {
        return '
            .mws-widget-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
                margin-bottom: 20px;
            }
            .mws-stat-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 10px;
                padding: 18px;
                text-align: center;
                color: #fff;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            }
            .mws-stat-card.green {
                background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
            }
            .mws-stat-card.orange {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
            }
            .mws-stat-icon-mini {
                font-size: 20px;
                margin-bottom: 8px;
                opacity: 0.9;
            }
            .mws-stat-number {
                font-size: 28px;
                font-weight: 700;
                line-height: 1;
                margin-bottom: 5px;
            }
            .mws-stat-label {
                font-size: 11px;
                opacity: 0.9;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .mws-activity-list {
                margin: 0;
                padding: 0;
                list-style: none;
            }
            .mws-activity-item {
                display: flex;
                align-items: flex-start;
                padding: 12px 0;
                border-bottom: 1px solid #f0f0f1;
            }
            .mws-activity-item:last-child {
                border-bottom: none;
            }
            .mws-activity-icon {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 12px;
                flex-shrink: 0;
                font-size: 16px;
            }
            .mws-activity-icon.success {
                background: #d1fae5;
                color: #059669;
            }
            .mws-activity-icon.error {
                background: #fee2e2;
                color: #dc2626;
            }
            .mws-activity-icon.info {
                background: #dbeafe;
                color: #2563eb;
            }
            .mws-activity-content {
                flex: 1;
                min-width: 0;
            }
            .mws-activity-title {
                font-weight: 500;
                color: #1e1e1e;
                margin-bottom: 2px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .mws-activity-meta {
                font-size: 12px;
                color: #646970;
            }
            .mws-widget-section {
                margin-bottom: 20px;
            }
            .mws-section-title {
                font-size: 13px;
                font-weight: 600;
                color: #1e1e1e;
                margin-bottom: 12px;
                padding-bottom: 8px;
                border-bottom: 2px solid #667eea;
                display: inline-block;
            }
            .mws-quick-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }
            .mws-quick-action {
                display: inline-flex;
                align-items: center;
                padding: 8px 14px;
                background: #f6f7f7;
                border: 1px solid #c3c4c7;
                border-radius: 6px;
                color: #1e1e1e;
                text-decoration: none;
                font-size: 13px;
                transition: all 0.2s;
            }
            .mws-quick-action:hover {
                background: #667eea;
                color: #fff;
                border-color: #667eea;
            }
            .mws-quick-action .dashicons {
                margin-right: 6px;
                font-size: 16px;
                width: 16px;
                height: 16px;
            }
            .mws-empty-state {
                text-align: center;
                padding: 30px 20px;
                color: #646970;
            }
            .mws-empty-state .dashicons {
                font-size: 48px;
                width: 48px;
                height: 48px;
                margin-bottom: 10px;
                opacity: 0.5;
            }
            .mws-sources-list {
                display: flex;
                gap: 8px;
                margin-top: 10px;
            }
            .mws-source-badge {
                display: inline-flex;
                align-items: center;
                padding: 4px 10px;
                background: #f0f0f1;
                border-radius: 20px;
                font-size: 11px;
                color: #1e1e1e;
            }
            .mws-source-badge.active {
                background: #d1fae5;
                color: #059669;
            }
            .mws-source-badge .dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: currentColor;
                margin-right: 6px;
            }
            .mws-source-counter {
                font-size: 11px;
                font-weight: 600;
                color: #667eea;
                background: #eef2ff;
                padding: 2px 8px;
                border-radius: 10px;
                margin-left: 8px;
            }
            .mws-no-source {
                color: #6b7280;
                font-size: 13px;
            }
            .mws-no-source a {
                color: #667eea;
                text-decoration: none;
            }
            .mws-no-source a:hover {
                text-decoration: underline;
            }
        ';
    }
    
    /**
     * Render widget content
     */
    public function render_widget() {
        $stats = $this->get_stats();
        $recent_activity = $this->get_recent_activity();
        $sources = $this->get_sources_status();
        ?>
        
        <!-- Stats Grid -->
        <div class="mws-widget-grid">
            <div class="mws-stat-card">
                <div class="mws-stat-icon-mini">📚</div>
                <div class="mws-stat-number"><?php echo number_format($stats['total_manhwa']); ?></div>
                <div class="mws-stat-label">Total Manhwa</div>
            </div>
            <div class="mws-stat-card green">
                <div class="mws-stat-icon-mini">📖</div>
                <div class="mws-stat-number"><?php echo number_format($stats['new_chapters_today']); ?></div>
                <div class="mws-stat-label">Chapters Today</div>
            </div>
            <div class="mws-stat-card orange">
                <div class="mws-stat-icon-mini">🔄</div>
                <div class="mws-stat-number"><?php echo number_format($stats['scrapes_today']); ?></div>
                <div class="mws-stat-label">Scrapes Today</div>
            </div>
        </div>
        
        <!-- Active Sources -->
        <div class="mws-widget-section">
            <?php 
            $active_count = count(array_filter($sources, function($s) { return $s['active']; }));
            $total_count = count($sources);
            ?>
            <div class="mws-section-title">
                Active Sources 
                <span class="mws-source-counter"><?php echo $active_count; ?>/<?php echo $total_count; ?></span>
            </div>
            <div class="mws-sources-list">
                <?php 
                $has_active = false;
                foreach ($sources as $source): 
                    if ($source['active']): 
                        $has_active = true;
                ?>
                    <span class="mws-source-badge active">
                        <span class="dot"></span>
                        <?php echo esc_html($source['name']); ?>
                    </span>
                <?php 
                    endif;
                endforeach; 
                
                if (!$has_active): 
                ?>
                    <span class="mws-no-source">No active sources. <a href="<?php echo admin_url('admin.php?page=manhwa-scraper-settings'); ?>">Configure →</a></span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="mws-widget-section">
            <div class="mws-section-title">Recent Activity</div>
            <?php if (!empty($recent_activity)): ?>
                <ul class="mws-activity-list">
                    <?php foreach ($recent_activity as $activity): ?>
                        <li class="mws-activity-item">
                            <div class="mws-activity-icon <?php echo esc_attr($activity['type']); ?>">
                                <?php echo $this->get_activity_icon($activity['type']); ?>
                            </div>
                            <div class="mws-activity-content">
                                <div class="mws-activity-title"><?php echo esc_html($activity['title']); ?></div>
                                <div class="mws-activity-meta">
                                    <?php echo esc_html($activity['source']); ?> • 
                                    <?php echo esc_html($this->time_ago($activity['time'])); ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="mws-empty-state">
                    <span class="dashicons dashicons-clipboard"></span>
                    <p>No recent activity</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Quick Actions -->
        <div class="mws-widget-section">
            <div class="mws-section-title">Quick Actions</div>
            <div class="mws-quick-actions">
                <a href="<?php echo admin_url('admin.php?page=manhwa-scraper'); ?>" class="mws-quick-action">
                    <span class="dashicons dashicons-download"></span>
                    Import New
                </a>
                <a href="<?php echo admin_url('admin.php?page=manhwa-scraper-bulk'); ?>" class="mws-quick-action">
                    <span class="dashicons dashicons-list-view"></span>
                    Bulk Scrape
                </a>
                <a href="<?php echo admin_url('admin.php?page=manhwa-scraper-history'); ?>" class="mws-quick-action">
                    <span class="dashicons dashicons-backup"></span>
                    View History
                </a>
                <a href="<?php echo admin_url('edit.php?post_type=manhwa'); ?>" class="mws-quick-action">
                    <span class="dashicons dashicons-book"></span>
                    All Manhwa
                </a>
            </div>
        </div>
        
        <?php
    }
    
    /**
     * Get statistics
     */
    private function get_stats() {
        global $wpdb;
        
        // Total manhwa tracked (with source URL)
        $total_manhwa = $wpdb->get_var(
            "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} 
             WHERE meta_key = '_mws_source_url' AND meta_value != ''"
        );
        
        // Also count manhwa post type
        $total_manhwa_posts = wp_count_posts('manhwa');
        $total_manhwa = max((int)$total_manhwa, (int)($total_manhwa_posts->publish ?? 0));
        
        // New chapters today - check logs table
        $today = current_time('Y-m-d');
        $new_chapters_today = 0;
        
        $logs_table = $wpdb->prefix . 'mws_logs';
        if ($wpdb->get_var("SHOW TABLES LIKE '$logs_table'")) {
            $new_chapters_today = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$logs_table} 
                 WHERE status = 'success' AND DATE(created_at) = %s 
                 AND message LIKE '%chapter%'",
                $today
            ));
        }
        
        // Scrapes today
        $scrapes_today = 0;
        if ($wpdb->get_var("SHOW TABLES LIKE '$logs_table'")) {
            $scrapes_today = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$logs_table} 
                 WHERE DATE(created_at) = %s",
                $today
            ));
        }
        
        return [
            'total_manhwa' => (int) $total_manhwa,
            'new_chapters_today' => (int) $new_chapters_today,
            'scrapes_today' => (int) $scrapes_today,
        ];
    }
    
    /**
     * Get recent activity from logs
     */
    private function get_recent_activity() {
        global $wpdb;
        
        $logs_table = $wpdb->prefix . 'mws_logs';
        
        // Check if table exists
        if (!$wpdb->get_var("SHOW TABLES LIKE '$logs_table'")) {
            return [];
        }
        
        $logs = $wpdb->get_results(
            "SELECT * FROM {$logs_table} 
             ORDER BY created_at DESC 
             LIMIT 5"
        );
        
        if (empty($logs)) {
            return [];
        }
        
        $activities = [];
        foreach ($logs as $log) {
            $data = json_decode($log->data, true);
            $title = $data['title'] ?? $this->extract_title_from_url($log->url);
            
            $activities[] = [
                'type' => $log->status === 'success' ? 'success' : 'error',
                'title' => $title ?: 'Scrape operation',
                'source' => ucfirst($log->source),
                'time' => $log->created_at,
                'message' => $log->message,
            ];
        }
        
        return $activities;
    }
    
    /**
     * Extract title from URL
     */
    private function extract_title_from_url($url) {
        $path = parse_url($url, PHP_URL_PATH);
        $segments = array_filter(explode('/', $path));
        $last = end($segments);
        
        // Clean up slug
        $title = str_replace(['-', '_'], ' ', $last);
        $title = ucwords($title);
        
        return $title;
    }
    
    /**
     * Get sources status - fetch from scraper manager
     */
    private function get_sources_status() {
        $enabled = get_option('mws_enabled_sources', ['manhwaku', 'asura']);
        if (!is_array($enabled)) {
            $enabled = [];
        }
        
        // Get all available sources from scraper manager
        $all_sources = [];
        if (class_exists('MWS_Scraper_Manager')) {
            $scraper_manager = MWS_Scraper_Manager::get_instance();
            $available = $scraper_manager->get_available_sources();
            foreach ($available as $id => $name) {
                $all_sources[$id] = $name;
            }
        }
        
        // Fallback if scraper manager not available
        if (empty($all_sources)) {
            $all_sources = [
                'manhwaku' => 'Manhwaku.id',
                'asura' => 'Asura Scans',
                'komikcast' => 'Komikcast',
                'kiryuu' => 'Kiryuu',
                'komikindo' => 'Komikindo',
                'mangaplus' => 'MangaPlus',
            ];
        }
        
        $sources = [];
        
        // First add enabled sources
        foreach ($all_sources as $id => $name) {
            if (in_array($id, $enabled)) {
                $sources[] = [
                    'id' => $id,
                    'name' => $name,
                    'active' => true,
                ];
            }
        }
        
        // Then add disabled sources
        foreach ($all_sources as $id => $name) {
            if (!in_array($id, $enabled)) {
                $sources[] = [
                    'id' => $id,
                    'name' => $name,
                    'active' => false,
                ];
            }
        }
        
        return $sources;
    }
    
    /**
     * Get activity icon
     */
    private function get_activity_icon($type) {
        switch ($type) {
            case 'success':
                return '✓';
            case 'error':
                return '✕';
            case 'info':
            default:
                return 'ℹ';
        }
    }
    
    /**
     * Format time ago
     */
    private function time_ago($datetime) {
        $timestamp = strtotime($datetime);
        $diff = current_time('timestamp') - $timestamp;
        
        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' min' . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', $timestamp);
        }
    }
}
