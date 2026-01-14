<?php
/**
 * Statistics Page View
 * Displays scraper statistics and analytics with charts
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap mws-wrap">
    <h1 class="mws-title">
        <span class="dashicons dashicons-chart-area"></span>
        <?php esc_html_e('Statistics & Analytics', 'manhwa-scraper'); ?>
    </h1>
    
    <div class="mws-stats-page">
        <!-- Stats Summary Cards -->
        <div class="mws-stats-grid">
            <div class="mws-stat-box purple">
                <div class="mws-stat-icon"><span class="dashicons dashicons-book"></span></div>
                <div class="mws-stat-info">
                    <div class="mws-stat-value" id="stat-total-manhwa">--</div>
                    <div class="mws-stat-label">Total Manhwa</div>
                </div>
            </div>
            <div class="mws-stat-box blue">
                <div class="mws-stat-icon"><span class="dashicons dashicons-download"></span></div>
                <div class="mws-stat-info">
                    <div class="mws-stat-value" id="stat-total-scrapes">--</div>
                    <div class="mws-stat-label">Total Scrapes</div>
                </div>
            </div>
            <div class="mws-stat-box green">
                <div class="mws-stat-icon"><span class="dashicons dashicons-yes-alt"></span></div>
                <div class="mws-stat-info">
                    <div class="mws-stat-value" id="stat-success-rate">--%</div>
                    <div class="mws-stat-label">Success Rate</div>
                </div>
            </div>
            <div class="mws-stat-box orange">
                <div class="mws-stat-icon"><span class="dashicons dashicons-warning"></span></div>
                <div class="mws-stat-info">
                    <div class="mws-stat-value" id="stat-total-errors">--</div>
                    <div class="mws-stat-label">Total Errors</div>
                </div>
            </div>
        </div>
        
        <!-- Charts Row -->
        <div class="mws-charts-row">
            <!-- Activity Chart -->
            <div class="mws-card mws-chart-card">
                <div class="mws-card-header">
                    <h2><?php esc_html_e('Scrape Activity (Last 30 Days)', 'manhwa-scraper'); ?></h2>
                    <div class="mws-chart-legend">
                        <span class="legend-item success"><span class="dot"></span> Success</span>
                        <span class="legend-item error"><span class="dot"></span> Error</span>
                    </div>
                </div>
                <div class="mws-chart-container">
                    <canvas id="activityChart" height="300"></canvas>
                </div>
            </div>
            
            <!-- Source Distribution -->
            <div class="mws-card mws-chart-card mws-chart-small">
                <div class="mws-card-header">
                    <h2><?php esc_html_e('Source Distribution', 'manhwa-scraper'); ?></h2>
                </div>
                <div class="mws-chart-container">
                    <canvas id="sourceChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Source Stats Table -->
        <div class="mws-card">
            <div class="mws-card-header">
                <h2><?php esc_html_e('Source Performance', 'manhwa-scraper'); ?></h2>
            </div>
            <table class="wp-list-table widefat fixed striped" id="source-stats-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Source', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Total Scrapes', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Success', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Errors', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Success Rate', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Avg Response Time', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Status', 'manhwa-scraper'); ?></th>
                    </tr>
                </thead>
                <tbody id="source-stats-body">
                    <tr>
                        <td colspan="7" class="loading-row">
                            <span class="spinner is-active"></span>
                            <?php esc_html_e('Loading statistics...', 'manhwa-scraper'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Recent Errors -->
        <div class="mws-card">
            <div class="mws-card-header">
                <h2><?php esc_html_e('Recent Errors', 'manhwa-scraper'); ?></h2>
            </div>
            <div id="recent-errors-container">
                <div class="loading-row">
                    <span class="spinner is-active"></span>
                    <?php esc_html_e('Loading errors...', 'manhwa-scraper'); ?>
                </div>
            </div>
        </div>
        
        <!-- Top Manhwa -->
        <div class="mws-row">
            <div class="mws-col-6">
                <div class="mws-card">
                    <div class="mws-card-header">
                        <h2><?php esc_html_e('Most Scraped Manhwa', 'manhwa-scraper'); ?></h2>
                    </div>
                    <div id="top-manhwa-container">
                        <div class="loading-row">
                            <span class="spinner is-active"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mws-col-6">
                <div class="mws-card">
                    <div class="mws-card-header">
                        <h2><?php esc_html_e('Activity Timeline', 'manhwa-scraper'); ?></h2>
                    </div>
                    <div id="timeline-container">
                        <div class="loading-row">
                            <span class="spinner is-active"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.mws-stats-page {
    max-width: 1400px;
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
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-left: 4px solid #667eea;
}

.mws-stat-box.purple { border-color: #667eea; }
.mws-stat-box.blue { border-color: #2196f3; }
.mws-stat-box.green { border-color: #4caf50; }
.mws-stat-box.orange { border-color: #ff9800; }

.mws-stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}

.mws-stat-box.purple .mws-stat-icon { background: rgba(102, 126, 234, 0.1); color: #667eea; }
.mws-stat-box.blue .mws-stat-icon { background: rgba(33, 150, 243, 0.1); color: #2196f3; }
.mws-stat-box.green .mws-stat-icon { background: rgba(76, 175, 80, 0.1); color: #4caf50; }
.mws-stat-box.orange .mws-stat-icon { background: rgba(255, 152, 0, 0.1); color: #ff9800; }

.mws-stat-icon .dashicons {
    font-size: 28px;
    width: 28px;
    height: 28px;
}

.mws-stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #1e1e1e;
    line-height: 1;
}

.mws-stat-label {
    font-size: 14px;
    color: #646970;
    margin-top: 5px;
}

.mws-charts-row {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.mws-chart-card {
    min-height: 380px;
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

.mws-chart-legend {
    display: flex;
    gap: 15px;
}

.legend-item {
    display: flex;
    align-items: center;
    font-size: 13px;
    color: #646970;
}

.legend-item .dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 6px;
}

.legend-item.success .dot { background: #4caf50; }
.legend-item.error .dot { background: #f44336; }

.mws-chart-container {
    position: relative;
}

.loading-row {
    text-align: center;
    padding: 30px;
    color: #646970;
}

.loading-row .spinner {
    float: none;
    margin-right: 10px;
}

/* Source Stats Table */
#source-stats-table th {
    font-weight: 600;
}

.success-rate-bar {
    background: #f0f0f1;
    border-radius: 10px;
    height: 8px;
    overflow: hidden;
    min-width: 100px;
}

.success-rate-fill {
    height: 100%;
    border-radius: 10px;
    transition: width 0.3s;
}

.success-rate-fill.high { background: #4caf50; }
.success-rate-fill.medium { background: #ff9800; }
.success-rate-fill.low { background: #f44336; }

.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-badge.active {
    background: #e8f5e9;
    color: #2e7d32;
}

.status-badge.inactive {
    background: #f5f5f5;
    color: #757575;
}

/* Error List */
.error-item {
    display: flex;
    align-items: flex-start;
    padding: 15px;
    border-bottom: 1px solid #f0f0f1;
    gap: 15px;
}

.error-item:last-child {
    border-bottom: none;
}

.error-icon {
    width: 36px;
    height: 36px;
    background: #ffebee;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f44336;
    flex-shrink: 0;
}

.error-content {
    flex: 1;
    min-width: 0;
}

.error-title {
    font-weight: 500;
    color: #1e1e1e;
    margin-bottom: 4px;
}

.error-message {
    font-size: 13px;
    color: #f44336;
    margin-bottom: 4px;
}

.error-meta {
    font-size: 12px;
    color: #646970;
}

/* Top Manhwa List */
.top-manhwa-item {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f1;
    gap: 12px;
}

.top-manhwa-item:last-child {
    border-bottom: none;
}

.top-manhwa-rank {
    width: 28px;
    height: 28px;
    background: #667eea;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 13px;
    flex-shrink: 0;
}

.top-manhwa-rank.gold { background: #ffc107; color: #1e1e1e; }
.top-manhwa-rank.silver { background: #9e9e9e; }
.top-manhwa-rank.bronze { background: #cd7f32; }

.top-manhwa-info {
    flex: 1;
    min-width: 0;
}

.top-manhwa-title {
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.top-manhwa-count {
    font-size: 12px;
    color: #646970;
}

/* Timeline */
.timeline-item {
    display: flex;
    padding: 10px 0;
    gap: 12px;
}

.timeline-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #667eea;
    margin-top: 4px;
    flex-shrink: 0;
}

.timeline-dot.success { background: #4caf50; }
.timeline-dot.error { background: #f44336; }

.timeline-content {
    flex: 1;
}

.timeline-title {
    font-size: 13px;
    color: #1e1e1e;
}

.timeline-time {
    font-size: 12px;
    color: #646970;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #646970;
}

.empty-state .dashicons {
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
    .mws-charts-row {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 782px) {
    .mws-stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
jQuery(document).ready(function($) {
    // Load statistics
    loadStatistics();
    
    function loadStatistics() {
        $.ajax({
            url: mwsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mws_get_statistics',
                nonce: mwsData.nonce
            },
            success: function(response) {
                if (response.success) {
                    renderStatistics(response.data);
                } else {
                    showError('Failed to load statistics');
                }
            },
            error: function() {
                showError('Error loading statistics');
            }
        });
    }
    
    function renderStatistics(data) {
        // Update summary cards
        $('#stat-total-manhwa').text(data.summary.total_manhwa.toLocaleString());
        $('#stat-total-scrapes').text(data.summary.total_scrapes.toLocaleString());
        $('#stat-success-rate').text(data.summary.success_rate + '%');
        $('#stat-total-errors').text(data.summary.total_errors.toLocaleString());
        
        // Render activity chart
        renderActivityChart(data.activity);
        
        // Render source chart
        renderSourceChart(data.sources);
        
        // Render source table
        renderSourceTable(data.sources);
        
        // Render recent errors
        renderRecentErrors(data.recent_errors);
        
        // Render top manhwa
        renderTopManhwa(data.top_manhwa);
        
        // Render timeline
        renderTimeline(data.timeline);
    }
    
    function renderActivityChart(activity) {
        var ctx = document.getElementById('activityChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: activity.labels,
                datasets: [{
                    label: 'Success',
                    data: activity.success,
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 6
                }, {
                    label: 'Errors',
                    data: activity.errors,
                    borderColor: '#f44336',
                    backgroundColor: 'rgba(244, 67, 54, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1e1e1e',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }
    
    function renderSourceChart(sources) {
        var ctx = document.getElementById('sourceChart').getContext('2d');
        
        var labels = sources.map(function(s) { return s.name; });
        var data = sources.map(function(s) { return s.total; });
        var colors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe'];
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, sources.length),
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
    
    function renderSourceTable(sources) {
        var $tbody = $('#source-stats-body').empty();
        
        if (sources.length === 0) {
            $tbody.html('<tr><td colspan="7" class="empty-state">No source data available</td></tr>');
            return;
        }
        
        sources.forEach(function(source) {
            var rateClass = source.success_rate >= 80 ? 'high' : (source.success_rate >= 50 ? 'medium' : 'low');
            var statusClass = source.active ? 'active' : 'inactive';
            var statusText = source.active ? 'Active' : 'Inactive';
            
            $tbody.append(
                '<tr>' +
                    '<td><strong>' + source.name + '</strong></td>' +
                    '<td>' + source.total.toLocaleString() + '</td>' +
                    '<td style="color: #4caf50;">' + source.success.toLocaleString() + '</td>' +
                    '<td style="color: #f44336;">' + source.errors.toLocaleString() + '</td>' +
                    '<td>' +
                        '<div style="display: flex; align-items: center; gap: 10px;">' +
                            '<div class="success-rate-bar">' +
                                '<div class="success-rate-fill ' + rateClass + '" style="width: ' + source.success_rate + '%;"></div>' +
                            '</div>' +
                            '<span>' + source.success_rate + '%</span>' +
                        '</div>' +
                    '</td>' +
                    '<td>' + (source.avg_response_time || 'N/A') + '</td>' +
                    '<td><span class="status-badge ' + statusClass + '">' + statusText + '</span></td>' +
                '</tr>'
            );
        });
    }
    
    function renderRecentErrors(errors) {
        var $container = $('#recent-errors-container').empty();
        
        if (errors.length === 0) {
            $container.html(
                '<div class="empty-state">' +
                    '<span class="dashicons dashicons-yes-alt"></span>' +
                    '<p>No recent errors! Everything is working smoothly.</p>' +
                '</div>'
            );
            return;
        }
        
        errors.forEach(function(error) {
            $container.append(
                '<div class="error-item">' +
                    '<div class="error-icon"><span class="dashicons dashicons-warning"></span></div>' +
                    '<div class="error-content">' +
                        '<div class="error-title">' + error.title + '</div>' +
                        '<div class="error-message">' + error.message + '</div>' +
                        '<div class="error-meta">' + error.source + ' • ' + error.time_ago + '</div>' +
                    '</div>' +
                '</div>'
            );
        });
    }
    
    function renderTopManhwa(manhwa) {
        var $container = $('#top-manhwa-container').empty();
        
        if (manhwa.length === 0) {
            $container.html(
                '<div class="empty-state">' +
                    '<span class="dashicons dashicons-book"></span>' +
                    '<p>No scrape data yet</p>' +
                '</div>'
            );
            return;
        }
        
        manhwa.forEach(function(item, index) {
            var rankClass = index === 0 ? 'gold' : (index === 1 ? 'silver' : (index === 2 ? 'bronze' : ''));
            
            $container.append(
                '<div class="top-manhwa-item">' +
                    '<div class="top-manhwa-rank ' + rankClass + '">' + (index + 1) + '</div>' +
                    '<div class="top-manhwa-info">' +
                        '<div class="top-manhwa-title">' + item.title + '</div>' +
                        '<div class="top-manhwa-count">' + item.count + ' scrapes</div>' +
                    '</div>' +
                '</div>'
            );
        });
    }
    
    function renderTimeline(timeline) {
        var $container = $('#timeline-container').empty();
        
        if (timeline.length === 0) {
            $container.html(
                '<div class="empty-state">' +
                    '<span class="dashicons dashicons-clock"></span>' +
                    '<p>No activity yet</p>' +
                '</div>'
            );
            return;
        }
        
        timeline.forEach(function(item) {
            $container.append(
                '<div class="timeline-item">' +
                    '<div class="timeline-dot ' + item.status + '"></div>' +
                    '<div class="timeline-content">' +
                        '<div class="timeline-title">' + item.title + '</div>' +
                        '<div class="timeline-time">' + item.time_ago + '</div>' +
                    '</div>' +
                '</div>'
            );
        });
    }
    
    function showError(message) {
        $('#source-stats-body').html(
            '<tr><td colspan="7" style="color: #f44336; text-align: center;">' + message + '</td></tr>'
        );
    }
});
</script>
