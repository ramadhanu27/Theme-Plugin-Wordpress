<?php
/**
 * Dashboard Page View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap mws-wrap">
    <h1 class="mws-title">
        <span class="dashicons dashicons-download"></span>
        <?php esc_html_e('Manhwa Metadata Scraper', 'manhwa-scraper'); ?>
    </h1>
    
    <div class="mws-dashboard">
        <!-- Stats Cards -->
        <div class="mws-stats-grid">
            <div class="mws-stat-card">
                <div class="mws-stat-icon success">
                    <span class="dashicons dashicons-yes-alt"></span>
                </div>
                <div class="mws-stat-content">
                    <h3><?php echo esc_html($stats['total_scraped']); ?></h3>
                    <p><?php esc_html_e('Total Scraped', 'manhwa-scraper'); ?></p>
                </div>
            </div>
            
            <div class="mws-stat-card">
                <div class="mws-stat-icon primary">
                    <span class="dashicons dashicons-calendar-alt"></span>
                </div>
                <div class="mws-stat-content">
                    <h3><?php echo esc_html($stats['today_scraped']); ?></h3>
                    <p><?php esc_html_e('Scraped Today', 'manhwa-scraper'); ?></p>
                </div>
            </div>
            
            <div class="mws-stat-card">
                <div class="mws-stat-icon warning">
                    <span class="dashicons dashicons-warning"></span>
                </div>
                <div class="mws-stat-content">
                    <h3><?php echo esc_html($stats['total_errors']); ?></h3>
                    <p><?php esc_html_e('Errors', 'manhwa-scraper'); ?></p>
                </div>
            </div>
            
            <div class="mws-stat-card">
                <div class="mws-stat-icon info">
                    <span class="dashicons dashicons-clock"></span>
                </div>
                <div class="mws-stat-content">
                    <h3><?php echo $next_cron ? esc_html(human_time_diff($next_cron)) : '-'; ?></h3>
                    <p><?php esc_html_e('Next Update', 'manhwa-scraper'); ?></p>
                </div>
            </div>
        </div>
        
        <div class="mws-row">
            <!-- Quick Actions -->
            <div class="mws-col-6">
                <div class="mws-card">
                    <h2><?php esc_html_e('Quick Actions', 'manhwa-scraper'); ?></h2>
                    <div class="mws-quick-actions">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=manhwa-scraper-import')); ?>" class="button button-primary button-hero">
                            <span class="dashicons dashicons-upload"></span>
                            <?php esc_html_e('Import Single', 'manhwa-scraper'); ?>
                        </a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=manhwa-scraper-bulk')); ?>" class="button button-secondary button-hero">
                            <span class="dashicons dashicons-download"></span>
                            <?php esc_html_e('Bulk Scrape', 'manhwa-scraper'); ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Source Status -->
            <div class="mws-col-6">
                <div class="mws-card">
                    <h2><?php esc_html_e('Source Status', 'manhwa-scraper'); ?></h2>
                    <div class="mws-source-list">
                        <?php foreach ($sources as $source): ?>
                        <div class="mws-source-item" data-source="<?php echo esc_attr($source['id']); ?>">
                            <div class="mws-source-info">
                                <strong><?php echo esc_html($source['name']); ?></strong>
                                <span class="mws-source-url"><?php echo esc_html($source['url']); ?></span>
                            </div>
                            <button type="button" class="button mws-test-connection" data-source="<?php echo esc_attr($source['id']); ?>">
                                <?php esc_html_e('Test', 'manhwa-scraper'); ?>
                            </button>
                            <span class="mws-status-badge pending"><?php esc_html_e('Unknown', 'manhwa-scraper'); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="button mws-test-all-connections" style="margin-top: 10px;">
                        <?php esc_html_e('Test All Connections', 'manhwa-scraper'); ?>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="mws-card">
            <h2><?php esc_html_e('Recent Activity', 'manhwa-scraper'); ?></h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Time', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Source', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('URL', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Status', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Message', 'manhwa-scraper'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_logs)): ?>
                    <tr>
                        <td colspan="5"><?php esc_html_e('No activity yet', 'manhwa-scraper'); ?></td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recent_logs as $log): ?>
                    <tr>
                        <td><?php echo esc_html(human_time_diff(strtotime($log->created_at)) . ' ago'); ?></td>
                        <td><span class="mws-badge"><?php echo esc_html($log->source); ?></span></td>
                        <td class="mws-truncate" title="<?php echo esc_attr($log->url); ?>">
                            <?php echo esc_html(strlen($log->url) > 50 ? substr($log->url, 0, 50) . '...' : $log->url); ?>
                        </td>
                        <td>
                            <span class="mws-status-badge <?php echo esc_attr($log->status); ?>">
                                <?php echo esc_html(ucfirst($log->status)); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html($log->message); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <p style="margin-top: 10px;">
                <a href="<?php echo esc_url(admin_url('admin.php?page=manhwa-scraper-history')); ?>">
                    <?php esc_html_e('View All History →', 'manhwa-scraper'); ?>
                </a>
            </p>
        </div>
    </div>
</div>
