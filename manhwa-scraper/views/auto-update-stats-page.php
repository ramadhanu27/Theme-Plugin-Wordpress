<?php
/**
 * Auto Update Statistics Page
 * Shows detailed statistics about auto update system
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get statistics
$last_index = get_option('mws_last_update_index', 0);
$last_stats = get_option('mws_last_batch_stats', []);
$last_time = get_option('mws_last_batch_time', '');

// Get total tracked manhwa
global $wpdb;
$total_tracked = $wpdb->get_var("
    SELECT COUNT(DISTINCT p.ID)
    FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
    WHERE p.post_type = 'manhwa'
    AND pm.meta_key = '_mws_source_url'
    AND pm.meta_value != ''
");

// Get status breakdown
$status_breakdown = $wpdb->get_results("
    SELECT 
        COALESCE(pm.meta_value, 'unknown') as status,
        COUNT(*) as count
    FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_mws_source_url'
    LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_manhwa_status'
    WHERE p.post_type = 'manhwa'
    GROUP BY status
    ORDER BY count DESC
", ARRAY_A);

// Get recently updated manhwa (last 7 days)
$recent_updates = $wpdb->get_results("
    SELECT 
        p.ID,
        p.post_title,
        pm1.meta_value as last_updated,
        pm2.meta_value as total_chapters,
        pm3.meta_value as latest_chapter,
        pm4.meta_value as status
    FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_mws_last_updated'
    LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_mws_total_chapters'
    LEFT JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_mws_latest_chapter'
    LEFT JOIN {$wpdb->postmeta} pm4 ON p.ID = pm4.post_id AND pm4.meta_key = '_manhwa_status'
    WHERE p.post_type = 'manhwa'
    AND pm1.meta_value >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ORDER BY pm1.meta_value DESC
    LIMIT 50
", ARRAY_A);

// Get update frequency stats
$update_frequency = $wpdb->get_results("
    SELECT 
        DATE(pm.meta_value) as update_date,
        COUNT(*) as updates_count
    FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_mws_last_updated'
    WHERE p.post_type = 'manhwa'
    AND pm.meta_value >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(pm.meta_value)
    ORDER BY update_date DESC
", ARRAY_A);

// Calculate progress
$progress_percent = $total_tracked > 0 ? ($last_index / $total_tracked) * 100 : 0;
$batch_size = $last_stats['batch_size'] ?? 20;
$batches_remaining = $total_tracked > 0 ? ceil(($total_tracked - $last_index) / $batch_size) : 0;
?>

<div class="wrap mws-wrap">
    <h1 class="mws-title">
        <span class="dashicons dashicons-update"></span>
        <?php esc_html_e('Auto Update Statistics', 'manhwa-scraper'); ?>
    </h1>
    
    <div class="mws-auto-update-stats">
        <!-- Current Batch Progress -->
        <div class="mws-card mws-progress-card">
            <div class="mws-card-header">
                <h2><?php esc_html_e('Current Batch Progress', 'manhwa-scraper'); ?></h2>
                <div class="mws-refresh-btn">
                    <button type="button" class="button" onclick="location.reload()">
                        <span class="dashicons dashicons-update"></span>
                        <?php esc_html_e('Refresh', 'manhwa-scraper'); ?>
                    </button>
                </div>
            </div>
            
            <div class="mws-progress-info">
                <div class="mws-progress-bar-container">
                    <div class="mws-progress-bar">
                        <div class="mws-progress-fill" style="width: <?php echo esc_attr($progress_percent); ?>%"></div>
                    </div>
                    <div class="mws-progress-text">
                        <?php echo esc_html($last_index); ?> / <?php echo esc_html($total_tracked); ?> 
                        (<?php echo number_format($progress_percent, 1); ?>%)
                    </div>
                </div>
                
                <?php
                // Calculate estimated time remaining
                $cron_interval_hours = 2; // Default interval
                $cron_schedule = get_option('mws_cron_schedule', 'twicedaily');
                $interval_map = [
                    'hourly' => 1,
                    'twicedaily' => 12,
                    'daily' => 24,
                    'mws_every_4_hours' => 4,
                    'mws_every_6_hours' => 6,
                ];
                $cron_interval_hours = isset($interval_map[$cron_schedule]) ? $interval_map[$cron_schedule] : 2;
                $estimated_hours = $batches_remaining * $cron_interval_hours;
                
                // Format last batch time to WIB
                $last_batch_wib = '';
                $next_batch_wib = '';
                if ($last_time) {
                    $dt = new DateTime($last_time, new DateTimeZone('UTC'));
                    $dt->setTimezone(new DateTimeZone('Asia/Jakarta'));
                    $last_batch_wib = $dt->format('d M Y H:i:s') . ' WIB';
                    
                    // Calculate next batch
                    $next_dt = clone $dt;
                    $next_dt->modify("+{$cron_interval_hours} hours");
                    $next_batch_wib = $next_dt->format('d M Y H:i:s') . ' WIB';
                }
                ?>
                
                <div class="mws-batch-info-grid">
                    <!-- Current Position -->
                    <div class="mws-batch-card">
                        <div class="mws-batch-icon blue">
                            <span class="dashicons dashicons-location"></span>
                        </div>
                        <div class="mws-batch-content">
                            <div class="mws-batch-label"><?php esc_html_e('Current Position', 'manhwa-scraper'); ?></div>
                            <div class="mws-batch-value"><?php echo esc_html($last_index); ?> / <?php echo esc_html($total_tracked); ?></div>
                            <div class="mws-batch-sub"><?php printf(__('Processing manhwa #%d', 'manhwa-scraper'), $last_index + 1); ?></div>
                        </div>
                    </div>
                    
                    <!-- Batch Size -->
                    <div class="mws-batch-card">
                        <div class="mws-batch-icon purple">
                            <span class="dashicons dashicons-database"></span>
                        </div>
                        <div class="mws-batch-content">
                            <div class="mws-batch-label"><?php esc_html_e('Batch Size', 'manhwa-scraper'); ?></div>
                            <div class="mws-batch-value"><?php echo esc_html($batch_size); ?> manhwa</div>
                            <div class="mws-batch-sub"><?php esc_html_e('per batch process', 'manhwa-scraper'); ?></div>
                        </div>
                    </div>
                    
                    <!-- Batches Remaining -->
                    <div class="mws-batch-card">
                        <div class="mws-batch-icon orange">
                            <span class="dashicons dashicons-backup"></span>
                        </div>
                        <div class="mws-batch-content">
                            <div class="mws-batch-label"><?php esc_html_e('Batches Remaining', 'manhwa-scraper'); ?></div>
                            <div class="mws-batch-value"><?php echo esc_html($batches_remaining); ?> batch</div>
                            <div class="mws-batch-sub">
                                <?php 
                                if ($estimated_hours > 24) {
                                    printf(__('~%d days to complete', 'manhwa-scraper'), ceil($estimated_hours / 24));
                                } elseif ($estimated_hours > 0) {
                                    printf(__('~%d hours to complete', 'manhwa-scraper'), $estimated_hours);
                                } else {
                                    esc_html_e('Almost done!', 'manhwa-scraper');
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Last Batch Time -->
                    <div class="mws-batch-card">
                        <div class="mws-batch-icon green">
                            <span class="dashicons dashicons-clock"></span>
                        </div>
                        <div class="mws-batch-content">
                            <div class="mws-batch-label"><?php esc_html_e('Last Batch', 'manhwa-scraper'); ?></div>
                            <div class="mws-batch-value"><?php echo $last_batch_wib ?: '-'; ?></div>
                            <div class="mws-batch-sub">
                                <?php 
                                if ($last_time) {
                                    echo human_time_diff(strtotime($last_time)) . ' ' . __('ago', 'manhwa-scraper');
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Next Batch Time -->
                    <div class="mws-batch-card">
                        <div class="mws-batch-icon teal">
                            <span class="dashicons dashicons-calendar-alt"></span>
                        </div>
                        <div class="mws-batch-content">
                            <div class="mws-batch-label"><?php esc_html_e('Next Batch (Est.)', 'manhwa-scraper'); ?></div>
                            <div class="mws-batch-value"><?php echo $next_batch_wib ?: '-'; ?></div>
                            <div class="mws-batch-sub">
                                <?php printf(__('Every %d hours', 'manhwa-scraper'), $cron_interval_hours); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Full Cycle Info -->
                    <div class="mws-batch-card">
                        <div class="mws-batch-icon pink">
                            <span class="dashicons dashicons-chart-bar"></span>
                        </div>
                        <div class="mws-batch-content">
                            <div class="mws-batch-label"><?php esc_html_e('Full Cycle', 'manhwa-scraper'); ?></div>
                            <div class="mws-batch-value"><?php echo ceil($total_tracked / $batch_size); ?> batches</div>
                            <div class="mws-batch-sub">
                                <?php printf(__('%d manhwa total', 'manhwa-scraper'), $total_tracked); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Last Batch Stats -->
        <?php if (!empty($last_stats)): ?>
        <div class="mws-stats-grid">
            <div class="mws-stat-box blue">
                <div class="mws-stat-icon"><span class="dashicons dashicons-search"></span></div>
                <div class="mws-stat-info">
                    <div class="mws-stat-value"><?php echo esc_html($last_stats['checked'] ?? 0); ?></div>
                    <div class="mws-stat-label"><?php esc_html_e('Checked', 'manhwa-scraper'); ?></div>
                </div>
            </div>
            <div class="mws-stat-box green">
                <div class="mws-stat-icon"><span class="dashicons dashicons-yes-alt"></span></div>
                <div class="mws-stat-info">
                    <div class="mws-stat-value"><?php echo esc_html($last_stats['updated'] ?? 0); ?></div>
                    <div class="mws-stat-label"><?php esc_html_e('Updated', 'manhwa-scraper'); ?></div>
                </div>
            </div>
            <div class="mws-stat-box orange">
                <div class="mws-stat-icon"><span class="dashicons dashicons-dismiss"></span></div>
                <div class="mws-stat-info">
                    <div class="mws-stat-value"><?php echo esc_html($last_stats['skipped'] ?? 0); ?></div>
                    <div class="mws-stat-label"><?php esc_html_e('Skipped', 'manhwa-scraper'); ?></div>
                </div>
            </div>
            <div class="mws-stat-box red">
                <div class="mws-stat-icon"><span class="dashicons dashicons-warning"></span></div>
                <div class="mws-stat-info">
                    <div class="mws-stat-value"><?php echo esc_html($last_stats['errors'] ?? 0); ?></div>
                    <div class="mws-stat-label"><?php esc_html_e('Errors', 'manhwa-scraper'); ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Status Breakdown -->
        <div class="mws-row">
            <div class="mws-col-6">
                <div class="mws-card">
                    <div class="mws-card-header">
                        <h2><?php esc_html_e('Manhwa by Status', 'manhwa-scraper'); ?></h2>
                    </div>
                    <div class="mws-chart-container">
                        <canvas id="statusChart" height="250"></canvas>
                    </div>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Status', 'manhwa-scraper'); ?></th>
                                <th><?php esc_html_e('Count', 'manhwa-scraper'); ?></th>
                                <th><?php esc_html_e('Percentage', 'manhwa-scraper'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($status_breakdown as $status): ?>
                            <tr>
                                <td><strong><?php echo esc_html(ucfirst($status['status'])); ?></strong></td>
                                <td><?php echo esc_html($status['count']); ?></td>
                                <td><?php echo number_format(($status['count'] / $total_tracked) * 100, 1); ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mws-col-6">
                <div class="mws-card">
                    <div class="mws-card-header">
                        <h2><?php esc_html_e('Update Activity (Last 30 Days)', 'manhwa-scraper'); ?></h2>
                    </div>
                    <div class="mws-chart-container">
                        <canvas id="activityChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recently Updated Manhwa -->
        <div class="mws-card">
            <div class="mws-card-header">
                <h2><?php esc_html_e('Recently Updated Manhwa (Last 7 Days)', 'manhwa-scraper'); ?></h2>
                <div class="mws-header-actions">
                    <span class="mws-count-badge"><?php echo count($recent_updates); ?> updates</span>
                </div>
            </div>
            
            <?php if (!empty($recent_updates)): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 40%;"><?php esc_html_e('Title', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Status', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Total Chapters', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Latest Chapter', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Last Updated', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Actions', 'manhwa-scraper'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_updates as $manhwa): ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($manhwa['post_title']); ?></strong>
                        </td>
                        <td>
                            <?php 
                            $status = $manhwa['status'] ?: 'unknown';
                            $status_class = $status === 'ongoing' ? 'success' : ($status === 'completed' ? 'info' : 'default');
                            ?>
                            <span class="mws-badge mws-badge-<?php echo esc_attr($status_class); ?>">
                                <?php echo esc_html(ucfirst($status)); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html($manhwa['total_chapters'] ?: '-'); ?></td>
                        <td><?php echo esc_html($manhwa['latest_chapter'] ?: '-'); ?></td>
                        <td>
                            <?php 
                            $time_ago = human_time_diff(strtotime($manhwa['last_updated']), current_time('timestamp'));
                            echo esc_html($time_ago . ' ago');
                            ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(get_edit_post_link($manhwa['ID'])); ?>" class="button button-small">
                                <?php esc_html_e('View', 'manhwa-scraper'); ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="mws-empty-state">
                <span class="dashicons dashicons-info"></span>
                <p><?php esc_html_e('No updates in the last 7 days', 'manhwa-scraper'); ?></p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- ==================== IMAGE SCRAPER HISTORY ==================== -->
        <?php
        // Get Image Scraper stats
        $image_scraper_stats = [];
        $image_scraper_logs = [];
        $top_scraped = [];
        $daily_scrape_stats = [];
        
        if (class_exists('MWS_Logger')) {
            $image_scraper_stats = MWS_Logger::get_summary_stats('auto_image_scraper', 7);
            $image_scraper_logs = MWS_Logger::get_logs([
                'type' => 'auto_image_scraper',
                'limit' => 50,
            ]);
            $top_scraped = MWS_Logger::get_top_scraped(10, 30);
            $daily_scrape_stats = MWS_Logger::get_daily_stats('auto_image_scraper', 14);
        }
        ?>
        
        <!-- Image Scraper Statistics -->
        <div class="mws-card mws-image-scraper-section">
            <div class="mws-card-header">
                <h2>
                    <span class="dashicons dashicons-images-alt2"></span>
                    <?php esc_html_e('Image Scraper Statistics (Last 7 Days)', 'manhwa-scraper'); ?>
                </h2>
                <div class="mws-header-actions">
                    <button type="button" id="run-image-scraper-btn" class="button button-primary">
                        <span class="dashicons dashicons-update"></span>
                        <?php esc_html_e('Run Now', 'manhwa-scraper'); ?>
                    </button>
                    <?php 
                    $image_scraper_enabled = get_option('mws_auto_image_scraper_enabled', false);
                    if ($image_scraper_enabled): 
                    ?>
                        <span class="mws-status-badge mws-status-active">
                            <span class="dashicons dashicons-yes"></span> <?php esc_html_e('Active', 'manhwa-scraper'); ?>
                        </span>
                    <?php else: ?>
                        <span class="mws-status-badge mws-status-inactive">
                            <span class="dashicons dashicons-no"></span> <?php esc_html_e('Disabled', 'manhwa-scraper'); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($image_scraper_stats) && $image_scraper_stats['total_scrapes'] > 0): ?>
            <div class="mws-stats-grid mws-stats-grid-5">
                <div class="mws-stat-box blue">
                    <div class="mws-stat-icon"><span class="dashicons dashicons-database"></span></div>
                    <div class="mws-stat-info">
                        <div class="mws-stat-value"><?php echo esc_html($image_scraper_stats['total_scrapes']); ?></div>
                        <div class="mws-stat-label"><?php esc_html_e('Total Scrapes', 'manhwa-scraper'); ?></div>
                    </div>
                </div>
                <div class="mws-stat-box purple">
                    <div class="mws-stat-icon"><span class="dashicons dashicons-book"></span></div>
                    <div class="mws-stat-info">
                        <div class="mws-stat-value"><?php echo esc_html($image_scraper_stats['total_chapters']); ?></div>
                        <div class="mws-stat-label"><?php esc_html_e('Chapters Scraped', 'manhwa-scraper'); ?></div>
                    </div>
                </div>
                <div class="mws-stat-box teal">
                    <div class="mws-stat-icon"><span class="dashicons dashicons-format-gallery"></span></div>
                    <div class="mws-stat-info">
                        <div class="mws-stat-value"><?php echo esc_html($image_scraper_stats['total_images']); ?></div>
                        <div class="mws-stat-label"><?php esc_html_e('Images Found', 'manhwa-scraper'); ?></div>
                    </div>
                </div>
                <div class="mws-stat-box orange">
                    <div class="mws-stat-icon"><span class="dashicons dashicons-download"></span></div>
                    <div class="mws-stat-info">
                        <div class="mws-stat-value"><?php echo esc_html(MWS_Logger::format_bytes($image_scraper_stats['total_size'])); ?></div>
                        <div class="mws-stat-label"><?php esc_html_e('Downloaded Size', 'manhwa-scraper'); ?></div>
                    </div>
                </div>
                <div class="mws-stat-box green">
                    <div class="mws-stat-icon"><span class="dashicons dashicons-yes-alt"></span></div>
                    <div class="mws-stat-info">
                        <div class="mws-stat-value"><?php echo esc_html($image_scraper_stats['success_count']); ?></div>
                        <div class="mws-stat-label"><?php esc_html_e('Successful', 'manhwa-scraper'); ?></div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="mws-empty-state mws-empty-small">
                <span class="dashicons dashicons-info"></span>
                <p><?php esc_html_e('No image scraping activity in the last 7 days', 'manhwa-scraper'); ?></p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Top Scraped Manhwa (by size) -->
        <?php if (!empty($top_scraped)): ?>
        <div class="mws-card">
            <div class="mws-card-header">
                <h2>
                    <span class="dashicons dashicons-chart-bar"></span>
                    <?php esc_html_e('Top Scraped Manhwa by Size (Last 30 Days)', 'manhwa-scraper'); ?>
                </h2>
            </div>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 35%;"><?php esc_html_e('Title', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Scrape Count', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Chapters', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Images', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Total Size', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Last Scraped', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Actions', 'manhwa-scraper'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_scraped as $item): ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($item['manhwa_title']); ?></strong>
                        </td>
                        <td>
                            <span class="mws-mini-badge"><?php echo esc_html($item['scrape_count']); ?>x</span>
                        </td>
                        <td><?php echo esc_html($item['total_chapters']); ?></td>
                        <td><?php echo esc_html($item['total_images']); ?></td>
                        <td>
                            <strong class="mws-size-badge">
                                <?php echo esc_html(MWS_Logger::format_bytes($item['total_size'])); ?>
                            </strong>
                        </td>
                        <td>
                            <?php 
                            $time_ago = human_time_diff(strtotime($item['last_scraped']), current_time('timestamp'));
                            echo esc_html($time_ago . ' ago');
                            ?>
                        </td>
                        <td>
                            <?php if ($item['manhwa_id'] > 0): ?>
                            <a href="<?php echo esc_url(get_edit_post_link($item['manhwa_id'])); ?>" class="button button-small">
                                <?php esc_html_e('Edit', 'manhwa-scraper'); ?>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <!-- Image Scraper History Log -->
        <?php if (!empty($image_scraper_logs)): ?>
        <div class="mws-card">
            <div class="mws-card-header">
                <h2>
                    <span class="dashicons dashicons-list-view"></span>
                    <?php esc_html_e('Image Scraper History Log', 'manhwa-scraper'); ?>
                </h2>
                <div class="mws-header-actions">
                    <span class="mws-count-badge"><?php echo count($image_scraper_logs); ?> entries</span>
                </div>
            </div>
            
            <div class="mws-history-table-wrapper">
                <table class="wp-list-table widefat fixed striped mws-history-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;"><?php esc_html_e('Date', 'manhwa-scraper'); ?></th>
                            <th style="width: 35%;"><?php esc_html_e('Title', 'manhwa-scraper'); ?></th>
                            <th><?php esc_html_e('Status', 'manhwa-scraper'); ?></th>
                            <th><?php esc_html_e('Chapters', 'manhwa-scraper'); ?></th>
                            <th><?php esc_html_e('Images', 'manhwa-scraper'); ?></th>
                            <th><?php esc_html_e('Size', 'manhwa-scraper'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($image_scraper_logs as $log): ?>
                        <tr>
                            <td>
                                <?php 
                                $log_time = new DateTime($log['created_at'], new DateTimeZone('UTC'));
                                $log_time->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                echo esc_html($log_time->format('d M H:i'));
                                ?>
                            </td>
                            <td>
                                <?php if ($log['manhwa_id'] > 0): ?>
                                    <a href="<?php echo esc_url(get_edit_post_link($log['manhwa_id'])); ?>">
                                        <?php echo esc_html($log['manhwa_title']); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo esc_html($log['manhwa_title'] ?: '-'); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $status_class = $log['status'] === 'success' ? 'success' : ($log['status'] === 'error' ? 'error' : 'info');
                                ?>
                                <span class="mws-badge mws-badge-<?php echo esc_attr($status_class); ?>">
                                    <?php echo esc_html(ucfirst($log['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($log['chapters_scraped']); ?></td>
                            <td><?php echo esc_html($log['images_found']); ?></td>
                            <td>
                                <?php if ($log['total_size'] > 0): ?>
                                    <strong><?php echo esc_html(MWS_Logger::format_bytes($log['total_size'])); ?></strong>
                                <?php else: ?>
                                    <span class="mws-text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
</div>

<style>
.mws-auto-update-stats {
    max-width: 1400px;
}

.mws-progress-card {
    margin-bottom: 30px;
}

.mws-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f1;
}

.mws-card-header h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.mws-progress-bar-container {
    margin-bottom: 20px;
}

.mws-progress-bar {
    height: 30px;
    background: #f0f0f1;
    border-radius: 15px;
    overflow: hidden;
    position: relative;
}

.mws-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    transition: width 0.3s ease;
    position: relative;
}

.mws-progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.mws-progress-text {
    text-align: center;
    margin-top: 10px;
    font-size: 14px;
    font-weight: 600;
    color: #1e1e1e;
}

/* New Batch Info Grid Styles */
.mws-batch-info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-top: 25px;
}

.mws-batch-card {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 18px;
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    transition: all 0.2s ease;
}

.mws-batch-card:hover {
    border-color: #c7d2fe;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.mws-batch-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.mws-batch-icon .dashicons {
    font-size: 22px;
    width: 22px;
    height: 22px;
}

.mws-batch-icon.blue { background: rgba(33, 150, 243, 0.1); color: #2196f3; }
.mws-batch-icon.purple { background: rgba(156, 39, 176, 0.1); color: #9c27b0; }
.mws-batch-icon.orange { background: rgba(255, 152, 0, 0.1); color: #ff9800; }
.mws-batch-icon.green { background: rgba(76, 175, 80, 0.1); color: #4caf50; }
.mws-batch-icon.teal { background: rgba(0, 150, 136, 0.1); color: #009688; }
.mws-batch-icon.pink { background: rgba(233, 30, 99, 0.1); color: #e91e63; }

.mws-batch-content {
    flex: 1;
    min-width: 0;
}

.mws-batch-label {
    font-size: 12px;
    font-weight: 500;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.mws-batch-value {
    font-size: 15px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 3px;
    word-break: break-word;
}

.mws-batch-sub {
    font-size: 12px;
    color: #9ca3af;
}

/* Old progress details (backward compat) */
.mws-progress-details {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
}

.mws-detail-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.mws-detail-item .label {
    font-size: 12px;
    color: #646970;
    font-weight: 500;
}

.mws-detail-item .value {
    font-size: 16px;
    color: #1e1e1e;
    font-weight: 600;
}

.mws-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.mws-stat-box {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-left: 4px solid;
}

.mws-stat-box.blue { border-color: #2196f3; }
.mws-stat-box.green { border-color: #4caf50; }
.mws-stat-box.orange { border-color: #ff9800; }
.mws-stat-box.red { border-color: #f44336; }

.mws-stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mws-stat-box.blue .mws-stat-icon { background: rgba(33, 150, 243, 0.1); color: #2196f3; }
.mws-stat-box.green .mws-stat-icon { background: rgba(76, 175, 80, 0.1); color: #4caf50; }
.mws-stat-box.orange .mws-stat-icon { background: rgba(255, 152, 0, 0.1); color: #ff9800; }
.mws-stat-box.red .mws-stat-icon { background: rgba(244, 67, 54, 0.1); color: #f44336; }

.mws-stat-icon .dashicons {
    font-size: 24px;
    width: 24px;
    height: 24px;
}

.mws-stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #1e1e1e;
    line-height: 1;
}

.mws-stat-label {
    font-size: 13px;
    color: #646970;
    margin-top: 5px;
}

.mws-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.mws-col-6 {
    min-width: 0;
}

.mws-chart-container {
    padding: 20px 0;
}

.mws-count-badge {
    background: #667eea;
    color: #fff;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.mws-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.mws-badge-success {
    background: #e8f5e9;
    color: #2e7d32;
}

.mws-badge-info {
    background: #e3f2fd;
    color: #1976d2;
}

.mws-badge-default {
    background: #f5f5f5;
    color: #757575;
}

.mws-empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #646970;
}

.mws-empty-state .dashicons {
    font-size: 48px;
    width: 48px;
    height: 48px;
    opacity: 0.3;
    margin-bottom: 10px;
}

@media (max-width: 1200px) {
    .mws-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .mws-row {
        grid-template-columns: 1fr;
    }
    .mws-batch-info-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 782px) {
    .mws-stats-grid {
        grid-template-columns: 1fr;
    }
    .mws-progress-details {
        grid-template-columns: 1fr;
    }
    .mws-batch-info-grid {
        grid-template-columns: 1fr;
    }
    .mws-batch-card {
        padding: 15px;
    }
    .mws-batch-icon {
        width: 40px;
        height: 40px;
    }
    .mws-batch-value {
        font-size: 14px;
    }
}

/* Image Scraper Section Styles */
.mws-image-scraper-section {
    margin-top: 30px;
    border-top: 3px solid #667eea;
}

.mws-image-scraper-section .mws-card-header h2 .dashicons {
    margin-right: 8px;
    color: #667eea;
}

.mws-stats-grid-5 {
    grid-template-columns: repeat(5, 1fr) !important;
}

@media (max-width: 1200px) {
    .mws-stats-grid-5 {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 782px) {
    .mws-stats-grid-5 {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

.mws-stat-box.purple {
    border-color: #9c27b0;
}

.mws-stat-box.purple .mws-stat-icon {
    background: rgba(156, 39, 176, 0.1);
    color: #9c27b0;
}

.mws-stat-box.teal {
    border-color: #009688;
}

.mws-stat-box.teal .mws-stat-icon {
    background: rgba(0, 150, 136, 0.1);
    color: #009688;
}

.mws-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.mws-status-active {
    background: #e8f5e9;
    color: #2e7d32;
}

.mws-status-inactive {
    background: #fce4ec;
    color: #c2185b;
}

.mws-status-badge .dashicons {
    font-size: 14px;
    width: 14px;
    height: 14px;
}

.mws-empty-small {
    padding: 30px 20px;
}

.mws-empty-small .dashicons {
    font-size: 36px;
    width: 36px;
    height: 36px;
}

.mws-mini-badge {
    display: inline-block;
    background: #e3f2fd;
    color: #1976d2;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
}

.mws-size-badge {
    color: #ff9800;
}

.mws-history-table-wrapper {
    max-height: 500px;
    overflow-y: auto;
    border-radius: 8px;
}

.mws-history-table {
    border-collapse: collapse;
}

.mws-history-table tbody tr:hover {
    background-color: #f8f9fa;
}

.mws-text-muted {
    color: #9ca3af;
}

.mws-badge-error {
    background: #ffebee;
    color: #c62828;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
jQuery(document).ready(function($) {
    // Status Chart
    var statusData = <?php echo json_encode($status_breakdown); ?>;
    var statusLabels = statusData.map(function(item) { return item.status.charAt(0).toUpperCase() + item.status.slice(1); });
    var statusCounts = statusData.map(function(item) { return parseInt(item.count); });
    
    var statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: ['#4caf50', '#ff9800', '#2196f3', '#f44336', '#9c27b0'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '60%'
        }
    });
    
    // Activity Chart
    var activityData = <?php echo json_encode(array_reverse($update_frequency)); ?>;
    var activityLabels = activityData.map(function(item) { 
        return new Date(item.update_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric'});
    });
    var activityCounts = activityData.map(function(item) { return parseInt(item.updates_count); });
    
    var activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: activityLabels,
            datasets: [{
                label: 'Updates',
                data: activityCounts,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Manual Image Scraper Trigger
    $('#run-image-scraper-btn').on('click', function() {
        var $btn = $(this);
        var originalText = $btn.html();
        
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Running...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'mws_run_image_scraper',
                nonce: '<?php echo wp_create_nonce('mws_ajax_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    var stats = response.data.stats;
                    var message = 'Scraping completed!\n';
                    message += 'Checked: ' + stats.total_checked + '\n';
                    message += 'Chapters: ' + stats.chapters_scraped + '\n';
                    message += 'Images: ' + stats.images_found + '\n';
                    message += 'Errors: ' + stats.errors;
                    alert(message);
                    location.reload();
                } else {
                    alert('Error: ' + (response.data.message || 'Unknown error'));
                }
            },
            error: function() {
                alert('Request failed. Please try again.');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>

<style>
.spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    100% { transform: rotate(360deg); }
}
#run-image-scraper-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-right: 10px;
}
#run-image-scraper-btn .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}
</style>

