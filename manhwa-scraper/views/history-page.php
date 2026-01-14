<?php
/**
 * History Page View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap mws-wrap">
    <h1 class="mws-title">
        <span class="dashicons dashicons-backup"></span>
        <?php esc_html_e('Scrape History', 'manhwa-scraper'); ?>
    </h1>
    
    <?php settings_errors('mws_history'); ?>
    
    <div class="mws-history-page">
        <!-- Filters -->
        <div class="mws-card mws-filters">
            <form method="get" action="">
                <input type="hidden" name="page" value="manhwa-scraper-history">
                
                <label for="mws-filter-status"><?php esc_html_e('Status:', 'manhwa-scraper'); ?></label>
                <select name="status" id="mws-filter-status">
                    <option value=""><?php esc_html_e('All', 'manhwa-scraper'); ?></option>
                    <option value="auto_update" <?php selected($status_filter, 'auto_update'); ?>><?php esc_html_e('🔄 Auto Update', 'manhwa-scraper'); ?></option>
                    <option value="success" <?php selected($status_filter, 'success'); ?>><?php esc_html_e('✓ Success', 'manhwa-scraper'); ?></option>
                    <option value="error" <?php selected($status_filter, 'error'); ?>><?php esc_html_e('✗ Error', 'manhwa-scraper'); ?></option>
                    <option value="updated" <?php selected($status_filter, 'updated'); ?>><?php esc_html_e('↑ Updated', 'manhwa-scraper'); ?></option>
                </select>
                
                <label for="mws-filter-source"><?php esc_html_e('Source:', 'manhwa-scraper'); ?></label>
                <select name="source" id="mws-filter-source">
                    <option value=""><?php esc_html_e('All', 'manhwa-scraper'); ?></option>
                    <?php foreach ($sources as $source): ?>
                    <option value="<?php echo esc_attr($source); ?>" <?php selected($source_filter, $source); ?>>
                        <?php echo esc_html($source); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="button"><?php esc_html_e('Filter', 'manhwa-scraper'); ?></button>
                <?php if (!empty($status_filter) || !empty($source_filter)): ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=manhwa-scraper-history')); ?>" class="button">
                    <?php esc_html_e('Clear', 'manhwa-scraper'); ?>
                </a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Logs Table -->
        <div class="mws-card">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 140px;"><?php esc_html_e('Date/Time', 'manhwa-scraper'); ?></th>
                        <th style="width: 90px;"><?php esc_html_e('Source', 'manhwa-scraper'); ?></th>
                        <th style="width: 80px;"><?php esc_html_e('Type', 'manhwa-scraper'); ?></th>
                        <th style="width: 90px;"><?php esc_html_e('Status', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Message', 'manhwa-scraper'); ?></th>
                        <th style="width: 70px;"><?php esc_html_e('Duration', 'manhwa-scraper'); ?></th>
                        <th style="width: 90px;"><?php esc_html_e('Actions', 'manhwa-scraper'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="7"><?php esc_html_e('No logs found', 'manhwa-scraper'); ?></td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                    <?php 
                    $status_class = '';
                    $status_icon = '';
                    switch ($log->status) {
                        case 'auto_update':
                            $status_class = 'mws-status-auto';
                            $status_icon = '🔄';
                            break;
                        case 'success':
                            $status_class = 'mws-status-success';
                            $status_icon = '✓';
                            break;
                        case 'error':
                            $status_class = 'mws-status-error';
                            $status_icon = '✗';
                            break;
                        case 'updated':
                            $status_class = 'mws-status-updated';
                            $status_icon = '↑';
                            break;
                        default:
                            $status_class = 'mws-status-default';
                            $status_icon = '•';
                    }
                    
                    // Get type with fallback
                    $log_type = isset($log->type) ? $log->type : 'scrape';
                    $type_colors = [
                        'scrape' => '#2196F3',
                        'download' => '#4CAF50', 
                        'update' => '#FF9800',
                        'import' => '#9C27B0',
                        'auto' => '#607D8B'
                    ];
                    $type_color = $type_colors[$log_type] ?? '#666';
                    
                    // Format duration
                    $duration_ms = isset($log->duration_ms) ? intval($log->duration_ms) : 0;
                    if ($duration_ms > 0) {
                        if ($duration_ms >= 60000) {
                            $duration_str = round($duration_ms / 60000, 1) . 'm';
                        } elseif ($duration_ms >= 1000) {
                            $duration_str = round($duration_ms / 1000, 1) . 's';
                        } else {
                            $duration_str = $duration_ms . 'ms';
                        }
                    } else {
                        $duration_str = '-';
                    }
                    ?>
                    <tr>
                        <td>
                            <?php 
                            // Convert to WIB (Asia/Jakarta)
                            $date = new DateTime($log->created_at, new DateTimeZone('UTC'));
                            $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
                            echo esc_html($date->format('d M Y H:i:s')) . ' <small style="color:#888;">WIB</small>';
                            ?>
                            <br>
                            <small style="color: #666;"><?php echo esc_html(human_time_diff(strtotime($log->created_at)) . ' ago'); ?></small>
                        </td>
                        <td><span class="mws-badge"><?php echo esc_html($log->source); ?></span></td>
                        <td>
                            <span style="background: <?php echo esc_attr($type_color); ?>; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; text-transform: uppercase;">
                                <?php echo esc_html($log_type); ?>
                            </span>
                        </td>
                        <td>
                            <span class="mws-status-badge <?php echo esc_attr($status_class); ?>">
                                <?php echo $status_icon; ?> <?php echo esc_html(ucfirst(str_replace('_', ' ', $log->status))); ?>
                            </span>
                        </td>
                        <td>
                            <?php echo esc_html($log->message); ?>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-family: monospace; color: <?php echo $duration_ms > 5000 ? '#dc3232' : ($duration_ms > 2000 ? '#ffb900' : '#46b450'); ?>;">
                                <?php echo esc_html($duration_str); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($log->data)): ?>
                            <button type="button" class="button button-small mws-view-data" data-log-id="<?php echo esc_attr($log->id); ?>" data-data="<?php echo esc_attr($log->data); ?>">
                                <?php esc_html_e('Details', 'manhwa-scraper'); ?>
                            </button>
                            <?php endif; ?>
                            <?php 
                            $data = json_decode($log->data, true);
                            if (!empty($data['post_id'])): 
                            ?>
                            <a href="<?php echo get_edit_post_link($data['post_id']); ?>" class="button button-small" target="_blank" title="Edit Post">
                                <span class="dashicons dashicons-edit" style="font-size: 14px;"></span>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="mws-pagination">
                <?php
                $pagination_args = [
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'current' => $current_page,
                    'total' => $total_pages,
                    'prev_text' => '&laquo; ' . __('Previous', 'manhwa-scraper'),
                    'next_text' => __('Next', 'manhwa-scraper') . ' &raquo;',
                ];
                echo paginate_links($pagination_args);
                ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Clear Logs -->
        <div class="mws-card">
            <h3><?php esc_html_e('Clear Logs', 'manhwa-scraper'); ?></h3>
            <p class="description">
                <?php esc_html_e('Remove old log entries to free up database space.', 'manhwa-scraper'); ?>
            </p>
            <form method="post" action="" id="mws-clear-logs-form" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <?php wp_nonce_field('mws_clear_logs', 'mws_clear_logs_nonce'); ?>
                <select name="clear_period">
                    <option value="7"><?php esc_html_e('Older than 7 days', 'manhwa-scraper'); ?></option>
                    <option value="30"><?php esc_html_e('Older than 30 days', 'manhwa-scraper'); ?></option>
                    <option value="90"><?php esc_html_e('Older than 90 days', 'manhwa-scraper'); ?></option>
                </select>
                <button type="submit" class="button button-secondary" onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete these logs?', 'manhwa-scraper'); ?>');">
                    <?php esc_html_e('Clear Old Logs', 'manhwa-scraper'); ?>
                </button>
                
                <span style="color: #ccc; margin: 0 5px;">|</span>
                
                <button type="submit" name="clear_period" value="all" class="button" style="background: #dc3232; color: #fff; border-color: #dc3232;" 
                    onclick="return confirm('<?php esc_attr_e('⚠️ WARNING: This will DELETE ALL LOG DATA permanently!\n\nAre you absolutely sure?', 'manhwa-scraper'); ?>');">
                    <span class="dashicons dashicons-trash" style="font-size: 16px; line-height: 1.3;"></span>
                    <?php esc_html_e('Delete All Logs', 'manhwa-scraper'); ?>
                </button>
            </form>
            
            <!-- Total logs info -->
            <?php 
            global $wpdb;
            $table_name = $wpdb->prefix . 'mws_logs';
            $total_logs = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
            ?>
            <p style="margin-top: 10px; font-size: 12px; color: #666;">
                <?php printf(esc_html__('Total logs in database: %s', 'manhwa-scraper'), '<strong>' . number_format($total_logs) . '</strong>'); ?>
            </p>
        </div>
    </div>
</div>

<!-- Data Modal -->
<div id="mws-data-modal" class="mws-modal" style="display: none;">
    <div class="mws-modal-content" style="max-width: 700px;">
        <span class="mws-modal-close">&times;</span>
        <h2><?php esc_html_e('Update Details', 'manhwa-scraper'); ?></h2>
        <div id="mws-modal-data-formatted"></div>
        <details style="margin-top: 15px;">
            <summary style="cursor: pointer; color: #666;"><?php esc_html_e('Raw JSON Data', 'manhwa-scraper'); ?></summary>
            <pre id="mws-modal-data" class="mws-json-display" style="margin-top: 10px;"></pre>
        </details>
    </div>
</div>

<style>
/* Status Badges */
.mws-status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.mws-status-auto {
    background: #e0f2fe;
    color: #0369a1;
}

.mws-status-success {
    background: #dcfce7;
    color: #16a34a;
}

.mws-status-error {
    background: #fee2e2;
    color: #dc2626;
}

.mws-status-updated {
    background: #fef3c7;
    color: #d97706;
}

.mws-status-default {
    background: #f3f4f6;
    color: #6b7280;
}

/* Modal Improvements */
.mws-modal-content {
    background: #fff;
    padding: 25px;
    border-radius: 8px;
    position: relative;
}

.mws-modal-close {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 24px;
    cursor: pointer;
    color: #999;
}

.mws-detail-card {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 15px;
}

.mws-detail-card h4 {
    margin: 0 0 10px;
    font-size: 14px;
    color: #333;
}

.mws-chapter-list {
    max-height: 200px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
}

.mws-chapter-item {
    padding: 8px 12px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;
}

.mws-chapter-item:last-child {
    border-bottom: none;
}

.mws-chapter-item a {
    color: #2271b1;
    text-decoration: none;
}

.mws-chapter-item a:hover {
    text-decoration: underline;
}

.mws-stat-row {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

.mws-stat-item {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 12px 16px;
    text-align: center;
    min-width: 100px;
}

.mws-stat-item .label {
    font-size: 11px;
    color: #666;
    text-transform: uppercase;
}

.mws-stat-item .value {
    font-size: 24px;
    font-weight: 600;
    color: #333;
}

.mws-stat-item.highlight .value {
    color: #16a34a;
}
</style>

<script>
jQuery(document).ready(function($) {
    // View data modal
    $('.mws-view-data').on('click', function() {
        var data = $(this).data('data');
        var parsed = typeof data === 'string' ? JSON.parse(data) : data;
        
        // Format JSON for display
        $('#mws-modal-data').text(JSON.stringify(parsed, null, 2));
        
        // Create formatted view
        var html = '';
        
        if (parsed.title) {
            html += '<h3 style="margin: 0 0 15px;">' + parsed.title + '</h3>';
        }
        
        // Stats row
        html += '<div class="mws-stat-row">';
        if (parsed.old_chapters !== undefined) {
            html += '<div class="mws-stat-item"><div class="label">Before</div><div class="value">' + parsed.old_chapters + '</div></div>';
        }
        if (parsed.new_chapters !== undefined) {
            html += '<div class="mws-stat-item"><div class="label">After</div><div class="value">' + parsed.new_chapters + '</div></div>';
        }
        if (parsed.chapters_added !== undefined) {
            html += '<div class="mws-stat-item highlight"><div class="label">Added</div><div class="value">+' + parsed.chapters_added + '</div></div>';
        }
        html += '</div>';
        
        // New chapters list
        if (parsed.new_chapter_list && parsed.new_chapter_list.length > 0) {
            html += '<div class="mws-detail-card">';
            html += '<h4>📖 New Chapters Added:</h4>';
            html += '<div class="mws-chapter-list">';
            parsed.new_chapter_list.forEach(function(ch) {
                html += '<div class="mws-chapter-item">';
                if (ch.url) {
                    html += '<a href="' + ch.url + '" target="_blank">' + (ch.title || 'Chapter ' + ch.number) + '</a>';
                } else {
                    html += (ch.title || 'Chapter ' + ch.number);
                }
                html += '</div>';
            });
            html += '</div></div>';
        }
        
        // Additional info
        if (parsed.auto_download !== undefined) {
            html += '<p style="font-size: 12px; color: #666;"><strong>Auto Download:</strong> ' + (parsed.auto_download ? '✓ Enabled' : '✗ Disabled') + '</p>';
        }
        
        if (parsed.auto_scrape_images !== undefined) {
            html += '<p style="font-size: 12px; color: #666;"><strong>Auto Scrape Images:</strong> ' + (parsed.auto_scrape_images ? '✓ Enabled' : '✗ Disabled') + '</p>';
        }
        
        if (parsed.updated_at) {
            html += '<p style="font-size: 12px; color: #666;"><strong>Updated:</strong> ' + parsed.updated_at + '</p>';
        }
        
        $('#mws-modal-data-formatted').html(html);
        $('#mws-data-modal').fadeIn(200);
    });
    
    // Close modal
    $('.mws-modal-close, .mws-modal').on('click', function(e) {
        if (e.target === this) {
            $('#mws-data-modal').fadeOut(200);
        }
    });
    
    // Prevent modal content click from closing
    $('.mws-modal-content').on('click', function(e) {
        e.stopPropagation();
    });
});
</script>
