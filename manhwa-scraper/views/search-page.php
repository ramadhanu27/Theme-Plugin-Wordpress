<?php
/**
 * Search Page View
 * Search manhwa directly from sources
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap mws-wrap">
    <h1 class="mws-title">
        <span class="dashicons dashicons-search"></span>
        <?php esc_html_e('Search from Sources', 'manhwa-scraper'); ?>
    </h1>
    
    <div class="mws-search-page">
        <!-- Search Form -->
        <div class="mws-card mws-search-card">
            <form id="mws-search-form" class="mws-search-form">
                <div class="mws-search-row">
                    <div class="mws-search-input-wrap">
                        <input type="text" 
                               id="mws-search-keyword" 
                               name="keyword" 
                               placeholder="<?php esc_attr_e('Enter manhwa title to search...', 'manhwa-scraper'); ?>" 
                               class="mws-search-input"
                               autocomplete="off"
                               required>
                    </div>
                    <select id="mws-search-source" name="source" class="mws-search-source">
                        <option value="all"><?php esc_html_e('All Sources', 'manhwa-scraper'); ?></option>
                        <?php foreach ($sources as $source): ?>
                        <option value="<?php echo esc_attr($source['id']); ?>">
                            <?php echo esc_html($source['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="button button-primary mws-search-btn" id="mws-search-btn">
                        <span class="dashicons dashicons-search" style="margin-top: 4px;"></span>
                        <?php esc_html_e('Search', 'manhwa-scraper'); ?>
                    </button>
                </div>
            </form>
            <div class="mws-search-tips">
                <span class="dashicons dashicons-lightbulb"></span>
                <?php esc_html_e('Tip: Enter at least 3 characters for better results. Try searching by Korean/Japanese title for more matches.', 'manhwa-scraper'); ?>
            </div>
        </div>
        
        <!-- Loading State -->
        <div class="mws-search-loading" id="mws-search-loading" style="display: none;">
            <div class="mws-loading-spinner">
                <div class="spinner is-active"></div>
            </div>
            <p><?php esc_html_e('Searching sources...', 'manhwa-scraper'); ?></p>
        </div>
        
        <!-- Results Summary -->
        <div class="mws-results-summary" id="mws-results-summary" style="display: none;">
            <div class="mws-summary-text">
                <span class="dashicons dashicons-yes-alt"></span>
                <span id="mws-results-count">0</span> <?php esc_html_e('results found for', 'manhwa-scraper'); ?> "<span id="mws-results-keyword"></span>"
            </div>
            <div class="mws-results-actions">
                <button type="button" class="button" id="mws-import-selected-results" style="display: none;">
                    <span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Import Selected', 'manhwa-scraper'); ?> (<span id="mws-selected-count">0</span>)
                </button>
            </div>
        </div>
        
        <!-- Results Grid -->
        <div class="mws-results-grid" id="mws-results-grid">
            <!-- Results will be inserted here -->
        </div>
        
        <!-- Empty State -->
        <div class="mws-empty-state" id="mws-empty-state">
            <span class="dashicons dashicons-book-alt"></span>
            <h3><?php esc_html_e('Search for Manhwa', 'manhwa-scraper'); ?></h3>
            <p><?php esc_html_e('Enter a manhwa title above to search from enabled sources.', 'manhwa-scraper'); ?></p>
        </div>
        
        <!-- No Results State -->
        <div class="mws-no-results" id="mws-no-results" style="display: none;">
            <span class="dashicons dashicons-warning"></span>
            <h3><?php esc_html_e('No Results Found', 'manhwa-scraper'); ?></h3>
            <p><?php esc_html_e('Try different keywords or check if the source is available.', 'manhwa-scraper'); ?></p>
        </div>
        
        <!-- Duplicate Detection Modal -->
        <div class="mws-modal" id="mws-duplicate-modal" style="display: none;">
            <div class="mws-modal-content mws-duplicate-modal-content">
                <div class="mws-modal-header">
                    <h2><span class="dashicons dashicons-warning" style="color: #f59e0b;"></span> <?php esc_html_e('Duplicate Detected', 'manhwa-scraper'); ?></h2>
                    <button type="button" class="mws-modal-close">&times;</button>
                </div>
                <div class="mws-modal-body">
                    <div class="mws-duplicate-info">
                        <p class="mws-duplicate-message"></p>
                        
                        <div class="mws-duplicate-comparison">
                            <div class="mws-duplicate-new">
                                <h4><?php esc_html_e('New Manhwa', 'manhwa-scraper'); ?></h4>
                                <div class="mws-duplicate-item">
                                    <img src="" alt="" class="mws-dup-thumb-new">
                                    <div class="mws-dup-details">
                                        <div class="mws-dup-title-new"></div>
                                        <div class="mws-dup-source-new"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mws-duplicate-arrow">
                                <span class="dashicons dashicons-arrow-right-alt"></span>
                            </div>
                            <div class="mws-duplicate-existing">
                                <h4><?php esc_html_e('Existing Manhwa', 'manhwa-scraper'); ?></h4>
                                <div class="mws-duplicate-item">
                                    <img src="" alt="" class="mws-dup-thumb-existing">
                                    <div class="mws-dup-details">
                                        <div class="mws-dup-title-existing"></div>
                                        <div class="mws-dup-chapters-existing"></div>
                                        <a href="" target="_blank" class="mws-dup-edit-link"><?php esc_html_e('Edit Post', 'manhwa-scraper'); ?> →</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mws-duplicate-match-info">
                            <span class="mws-match-badge">
                                <span class="mws-match-type"></span>
                                <span class="mws-match-confidence"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mws-modal-footer">
                    <button type="button" class="button mws-dup-skip">
                        <span class="dashicons dashicons-no" style="margin-top:4px;"></span>
                        <?php esc_html_e('Skip', 'manhwa-scraper'); ?>
                    </button>
                    <button type="button" class="button mws-dup-update">
                        <span class="dashicons dashicons-update" style="margin-top:4px;"></span>
                        <?php esc_html_e('Update Existing', 'manhwa-scraper'); ?>
                    </button>
                    <button type="button" class="button button-primary mws-dup-create">
                        <span class="dashicons dashicons-plus" style="margin-top:4px;"></span>
                        <?php esc_html_e('Create New Anyway', 'manhwa-scraper'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.mws-search-page {
    max-width: 1400px;
}

.mws-search-card {
    margin-bottom: 30px;
}

.mws-search-form {
    margin-bottom: 15px;
}

.mws-search-row {
    display: flex;
    gap: 15px;
    align-items: stretch;
}

.mws-search-input-wrap {
    flex: 1;
    position: relative;
}

.mws-search-input-wrap .dashicons {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #646970;
    font-size: 20px;
}

.mws-search-input {
    width: 100%;
    padding: 12px 15px;
    font-size: 16px;
    border: 2px solid #dcdcde;
    border-radius: 8px;
    transition: all 0.3s;
}

.mws-search-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

.mws-search-source {
    min-width: 180px;
    padding: 12px 15px;
    font-size: 14px;
    border: 2px solid #dcdcde;
    border-radius: 8px;
    background: #fff;
    cursor: pointer;
}

.mws-search-btn {
    padding: 12px 25px !important;
    font-size: 14px !important;
    height: auto !important;
    display: flex !important;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
    border-radius: 8px !important;
}

.mws-search-btn:hover {
    opacity: 0.9;
}

.mws-search-tips {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #646970;
    font-size: 13px;
    background: #f0f6fc;
    padding: 10px 15px;
    border-radius: 6px;
}

.mws-search-tips .dashicons {
    color: #2271b1;
}

/* Loading State */
.mws-search-loading {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e0e0e0;
}

.mws-loading-spinner .spinner {
    float: none;
    margin: 0 auto 10px;
}

/* Results Summary */
.mws-results-summary {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: #d1fae5;
    border-radius: 8px;
    margin-bottom: 20px;
}

.mws-summary-text {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #065f46;
    font-weight: 500;
}

.mws-summary-text .dashicons {
    color: #10b981;
}

/* Results Grid */
.mws-results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 15px;
}

/* Result Card */
.mws-result-card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    transition: all 0.3s;
    position: relative;
}

.mws-result-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.mws-result-card.selected {
    border: 3px solid #667eea;
}

.mws-result-checkbox {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 10;
    width: 22px;
    height: 22px;
    cursor: pointer;
    accent-color: #667eea;
}

.mws-result-thumbnail {
    position: relative;
    padding-top: 130%;
    background: linear-gradient(135deg, #f0f0f1 0%, #e0e0e0 100%);
    overflow: hidden;
}

.mws-result-thumbnail img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.mws-result-card:hover .mws-result-thumbnail img {
    transform: scale(1.05);
}

.mws-result-source {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 4px 10px;
    background: rgba(0,0,0,0.7);
    color: #fff;
    font-size: 11px;
    border-radius: 20px;
    font-weight: 500;
}

.mws-result-content {
    padding: 10px;
}

.mws-result-title {
    font-size: 13px;
    font-weight: 600;
    color: #1e1e1e;
    margin-bottom: 8px;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 34px;
}

.mws-result-actions {
    display: flex;
    gap: 6px;
}

.mws-result-actions .button {
    flex: 1;
    text-align: center;
    justify-content: center;
    display: flex !important;
    align-items: center;
    gap: 4px;
    padding: 4px 8px !important;
    font-size: 11px !important;
    min-height: auto !important;
    height: auto !important;
}

.mws-result-actions .button-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
}

.mws-result-actions .button .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}

/* Empty & No Results States */
.mws-empty-state,
.mws-no-results {
    text-align: center;
    padding: 80px 20px;
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e0e0e0;
}

.mws-empty-state .dashicons,
.mws-no-results .dashicons {
    font-size: 64px;
    width: 64px;
    height: 64px;
    color: #646970;
    opacity: 0.3;
    margin-bottom: 15px;
}

.mws-empty-state h3,
.mws-no-results h3 {
    margin: 0 0 10px;
    color: #1e1e1e;
}

.mws-empty-state p,
.mws-no-results p {
    color: #646970;
    margin: 0;
}

/* Importing state */
.mws-result-card.importing {
    opacity: 0.6;
    pointer-events: none;
}

.mws-result-card.imported .mws-result-actions .button-primary {
    background: #4caf50 !important;
    pointer-events: none;
}

/* Already Exists state */
.mws-result-card.mws-exists {
    border: 2px solid #22c55e;
}

.mws-result-card.mws-exists .mws-result-thumbnail::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(34, 197, 94, 0.15);
    pointer-events: none;
}

.mws-exists-badge {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    padding: 5px 12px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: #fff;
    font-size: 10px;
    font-weight: 600;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(34, 197, 94, 0.4);
    white-space: nowrap;
}

.mws-result-card.mws-exists .mws-result-actions .button-primary {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%) !important;
}

.mws-result-actions a.button {
    text-decoration: none;
}

@media (max-width: 782px) {
    .mws-search-row {
        flex-direction: column;
    }
    .mws-search-source {
        min-width: 100%;
    }
    .mws-results-summary {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
}

/* Duplicate Modal */
.mws-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.6);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.mws-modal-content {
    background: #fff;
    border-radius: 12px;
    max-width: 700px;
    width: 100%;
    max-height: 90vh;
    overflow: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.mws-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid #f0f0f1;
}

.mws-modal-header h2 {
    margin: 0;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.mws-modal-close {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: #646970;
    line-height: 1;
    padding: 0;
}

.mws-modal-close:hover {
    color: #1e1e1e;
}

.mws-modal-body {
    padding: 25px;
}

.mws-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 20px 25px;
    border-top: 1px solid #f0f0f1;
    background: #f9f9f9;
    border-radius: 0 0 12px 12px;
}

.mws-modal-footer .button {
    display: flex;
    align-items: center;
    gap: 6px;
}

.mws-duplicate-message {
    font-size: 14px;
    color: #646970;
    margin-bottom: 20px;
}

.mws-duplicate-comparison {
    display: flex;
    gap: 20px;
    align-items: stretch;
}

.mws-duplicate-new,
.mws-duplicate-existing {
    flex: 1;
    background: #f9f9f9;
    border-radius: 8px;
    padding: 15px;
}

.mws-duplicate-new h4,
.mws-duplicate-existing h4 {
    margin: 0 0 12px;
    font-size: 12px;
    text-transform: uppercase;
    color: #646970;
    font-weight: 600;
}

.mws-duplicate-arrow {
    display: flex;
    align-items: center;
    color: #646970;
}

.mws-duplicate-arrow .dashicons {
    font-size: 30px;
    width: 30px;
    height: 30px;
}

.mws-duplicate-item {
    display: flex;
    gap: 12px;
}

.mws-duplicate-item img {
    width: 60px;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
    background: #e0e0e0;
}

.mws-dup-details {
    flex: 1;
    min-width: 0;
}

.mws-dup-title-new,
.mws-dup-title-existing {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 5px;
    line-height: 1.3;
}

.mws-dup-source-new,
.mws-dup-chapters-existing {
    font-size: 12px;
    color: #646970;
    margin-bottom: 5px;
}

.mws-dup-edit-link {
    font-size: 12px;
    color: #2271b1;
    text-decoration: none;
}

.mws-dup-edit-link:hover {
    text-decoration: underline;
}

.mws-duplicate-match-info {
    margin-top: 20px;
    text-align: center;
}

.mws-match-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 15px;
    background: #fef3c7;
    border-radius: 20px;
    font-size: 13px;
}

.mws-match-type {
    color: #92400e;
    font-weight: 500;
}

.mws-match-confidence {
    background: #f59e0b;
    color: #fff;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
}

@media (max-width: 600px) {
    .mws-duplicate-comparison {
        flex-direction: column;
    }
    .mws-duplicate-arrow {
        transform: rotate(90deg);
        justify-content: center;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    var searchResults = [];
    
    // Search form submit
    $('#mws-search-form').on('submit', function(e) {
        e.preventDefault();
        
        var keyword = $('#mws-search-keyword').val().trim();
        var source = $('#mws-search-source').val();
        
        if (keyword.length < 2) {
            alert('Please enter at least 2 characters to search');
            return;
        }
        
        performSearch(keyword, source);
    });
    
    function performSearch(keyword, source) {
        // Show loading
        $('#mws-empty-state').hide();
        $('#mws-no-results').hide();
        $('#mws-results-summary').hide();
        $('#mws-results-grid').empty();
        $('#mws-search-loading').show();
        $('#mws-search-btn').prop('disabled', true);
        
        $.ajax({
            url: mwsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mws_search_manhwa',
                nonce: mwsData.nonce,
                keyword: keyword,
                source: source
            },
            success: function(response) {
                if (response.success) {
                    searchResults = response.data.results;
                    displayResults(response.data);
                } else {
                    showError(response.data.message);
                }
            },
            error: function(xhr, status, error) {
                showError('Search failed: ' + error);
            },
            complete: function() {
                $('#mws-search-loading').hide();
                $('#mws-search-btn').prop('disabled', false);
            }
        });
    }
    
    function displayResults(data) {
        var $grid = $('#mws-results-grid');
        
        if (data.results.length === 0) {
            $('#mws-no-results').show();
            return;
        }
        
        // Show summary
        $('#mws-results-count').text(data.count);
        $('#mws-results-keyword').text(data.keyword);
        $('#mws-results-summary').show();
        
        // Render cards
        data.results.forEach(function(item, index) {
            var thumbnail = item.thumbnail_url || '';
            var thumbnailHtml = thumbnail 
                ? '<img src="' + thumbnail + '" alt="' + item.title + '" loading="lazy">'
                : '<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:48px;opacity:0.3;">📚</div>';
            
            // Check if exists in database
            var existsBadge = '';
            var actionButtons = '';
            
            if (item.exists_in_db) {
                existsBadge = '<span class="mws-exists-badge">✓ Already Exists</span>';
                actionButtons = 
                    '<a href="' + item.existing_view_url + '" target="_blank" class="button mws-view-existing" title="View on site">' +
                        '<span class="dashicons dashicons-visibility"></span> View' +
                    '</a>' +
                    '<a href="' + item.existing_edit_url + '" target="_blank" class="button button-primary mws-edit-existing" title="Edit post">' +
                        '<span class="dashicons dashicons-edit"></span> Edit' +
                    '</a>';
            } else {
                actionButtons = 
                    '<button type="button" class="button mws-view-details" data-index="' + index + '" data-url="' + item.url + '">' +
                        '<span class="dashicons dashicons-visibility"></span> View' +
                    '</button>' +
                    '<button type="button" class="button button-primary mws-quick-import" data-index="' + index + '">' +
                        '<span class="dashicons dashicons-download"></span> Import' +
                    '</button>';
            }
            
            var card = $('<div class="mws-result-card' + (item.exists_in_db ? ' mws-exists' : '') + '" data-index="' + index + '">' +
                '<input type="checkbox" class="mws-result-checkbox" data-index="' + index + '"' + (item.exists_in_db ? ' disabled' : '') + '>' +
                '<div class="mws-result-thumbnail">' +
                    thumbnailHtml +
                    '<span class="mws-result-source">' + (item.source_name || item.source) + '</span>' +
                    existsBadge +
                '</div>' +
                '<div class="mws-result-content">' +
                    '<div class="mws-result-title" title="' + item.title + '">' + item.title + '</div>' +
                    '<div class="mws-result-actions">' +
                        actionButtons +
                    '</div>' +
                '</div>' +
            '</div>');
            
            $grid.append(card);
        });
    }
    
    function showError(message) {
        $('#mws-no-results').show().find('p').text(message);
    }
    
    // View details
    $(document).on('click', '.mws-view-details', function() {
        var url = $(this).data('url');
        window.open(url, '_blank');
    });
    
    // Variables for duplicate handling
    var pendingImport = null;
    var pendingScrapedData = null;
    
    // Quick import with duplicate check
    $(document).on('click', '.mws-quick-import', function() {
        var $btn = $(this);
        var $card = $btn.closest('.mws-result-card');
        var index = $btn.data('index');
        var item = searchResults[index];
        
        if (!item) return;
        
        pendingImport = { btn: $btn, card: $card, item: item };
        
        $card.addClass('importing');
        $btn.prop('disabled', true).html('<span class="spinner is-active" style="float:none;margin:0;"></span>');
        
        // First check for duplicates
        $.ajax({
            url: mwsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mws_check_duplicate',
                nonce: mwsData.nonce,
                title: item.title,
                slug: item.slug,
                url: item.url
            },
            success: function(dupResponse) {
                if (dupResponse.success && dupResponse.data.is_duplicate) {
                    // Show duplicate modal
                    showDuplicateModal(item, dupResponse.data);
                } else {
                    // No duplicate, proceed with import
                    proceedWithImport(item, $card, $btn, false, null);
                }
            },
            error: function() {
                // If check fails, proceed anyway
                proceedWithImport(item, $card, $btn, false, null);
            }
        });
    });
    
    // Show duplicate modal
    function showDuplicateModal(newItem, duplicateData) {
        var $modal = $('#mws-duplicate-modal');
        var existing = duplicateData.existing;
        
        // Set new item info
        $('.mws-dup-thumb-new').attr('src', newItem.thumbnail_url || '');
        $('.mws-dup-title-new').text(newItem.title);
        $('.mws-dup-source-new').text('Source: ' + (newItem.source_name || newItem.source));
        
        // Set existing item info
        $('.mws-dup-thumb-existing').attr('src', existing.thumbnail_url || '');
        $('.mws-dup-title-existing').text(existing.title);
        $('.mws-dup-chapters-existing').text(existing.total_chapters + ' chapters');
        $('.mws-dup-edit-link').attr('href', existing.edit_url || '#');
        
        // Set match info
        var matchTypeLabels = {
            'source_url': 'Same source URL',
            'slug': 'Same slug',
            'title_exact': 'Exact title match',
            'title_similar': 'Similar title'
        };
        $('.mws-match-type').text(matchTypeLabels[duplicateData.match_type] || duplicateData.match_type);
        $('.mws-match-confidence').text(duplicateData.confidence + '% match');
        
        // Set message
        $('.mws-duplicate-message').text(
            'A similar manhwa already exists in your library. What would you like to do?'
        );
        
        // Store reference  
        $modal.data('existing-id', existing.id);
        
        $modal.show();
    }
    
    // Hide modal
    $(document).on('click', '.mws-modal-close, .mws-modal', function(e) {
        if (e.target === this || $(e.target).hasClass('mws-modal-close')) {
            $('#mws-duplicate-modal').hide();
            resetPendingImport();
        }
    });
    
    // Skip button
    $(document).on('click', '.mws-dup-skip', function() {
        $('#mws-duplicate-modal').hide();
        if (pendingImport) {
            pendingImport.card.removeClass('importing');
            pendingImport.btn.prop('disabled', false).html('<span class="dashicons dashicons-download"></span> Import');
            showNotice('Import skipped: ' + pendingImport.item.title, 'warning');
        }
        resetPendingImport();
    });
    
    // Update existing button
    $(document).on('click', '.mws-dup-update', function() {
        var existingId = $('#mws-duplicate-modal').data('existing-id');
        $('#mws-duplicate-modal').hide();
        
        if (pendingImport) {
            proceedWithImport(pendingImport.item, pendingImport.card, pendingImport.btn, true, existingId);
        }
    });
    
    // Create new anyway button
    $(document).on('click', '.mws-dup-create', function() {
        $('#mws-duplicate-modal').hide();
        
        if (pendingImport) {
            proceedWithImport(pendingImport.item, pendingImport.card, pendingImport.btn, false, null);
        }
    });
    
    function resetPendingImport() {
        pendingImport = null;
        pendingScrapedData = null;
    }
    
    // Proceed with import
    function proceedWithImport(item, $card, $btn, isUpdate, existingId) {
        // First scrape full details
        $.ajax({
            url: mwsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mws_scrape_single',
                nonce: mwsData.nonce,
                url: item.url
            },
            success: function(scrapeResponse) {
                if (scrapeResponse.success) {
                    var importData = scrapeResponse.data.data;
                    
                    // Add update flag if updating
                    if (isUpdate && existingId) {
                        importData.update_existing = true;
                        importData.existing_id = existingId;
                    }
                    
                    // Now import
                    $.ajax({
                        url: mwsData.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'mws_import_manhwa',
                            nonce: mwsData.nonce,
                            data: JSON.stringify([importData]),
                            download_cover: 'true',
                            create_post: 'true'
                        },
                        success: function(importResponse) {
                            if (importResponse.success) {
                                $card.addClass('imported').removeClass('importing');
                                var actionText = isUpdate ? 'Updated' : 'Imported';
                                $btn.html('<span class="dashicons dashicons-yes"></span> ' + actionText);
                                showNotice('Successfully ' + actionText.toLowerCase() + ': ' + item.title, 'success');
                            } else {
                                $card.removeClass('importing');
                                $btn.prop('disabled', false).html('<span class="dashicons dashicons-download"></span> Import');
                                showNotice(importResponse.data.message || 'Import failed', 'error');
                            }
                        },
                        error: function() {
                            $card.removeClass('importing');
                            $btn.prop('disabled', false).html('<span class="dashicons dashicons-download"></span> Import');
                            showNotice('Import failed', 'error');
                        },
                        complete: function() {
                            resetPendingImport();
                        }
                    });
                } else {
                    $card.removeClass('importing');
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-download"></span> Import');
                    showNotice(scrapeResponse.data.message || 'Failed to scrape details', 'error');
                    resetPendingImport();
                }
            },
            error: function() {
                $card.removeClass('importing');
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-download"></span> Import');
                showNotice('Scrape failed', 'error');
                resetPendingImport();
            }
        });
    }
    
    // Checkbox selection
    $(document).on('change', '.mws-result-checkbox', function() {
        var $card = $(this).closest('.mws-result-card');
        $card.toggleClass('selected', $(this).is(':checked'));
        updateSelectedCount();
    });
    
    function updateSelectedCount() {
        var count = $('.mws-result-checkbox:checked').length;
        $('#mws-selected-count').text(count);
        
        if (count > 0) {
            $('#mws-import-selected-results').show();
        } else {
            $('#mws-import-selected-results').hide();
        }
    }
    
    // Import selected
    $('#mws-import-selected-results').on('click', function() {
        var selectedItems = [];
        $('.mws-result-checkbox:checked').each(function() {
            var index = $(this).data('index');
            if (searchResults[index]) {
                selectedItems.push({
                    index: index,
                    item: searchResults[index]
                });
            }
        });
        
        if (selectedItems.length === 0) return;
        
        if (!confirm('Import ' + selectedItems.length + ' manhwa? This will scrape full details for each.')) {
            return;
        }
        
        // Import one by one
        var $btn = $(this);
        $btn.prop('disabled', true).text('Importing...');
        
        importSelectedSequentially(selectedItems, 0, function() {
            $btn.prop('disabled', false).html('<span class="dashicons dashicons-download" style="margin-top: 4px;"></span> Import Selected (<span id="mws-selected-count">0</span>)');
            $('.mws-result-checkbox').prop('checked', false);
            $('.mws-result-card').removeClass('selected');
            updateSelectedCount();
        });
    });
    
    function importSelectedSequentially(items, index, callback) {
        if (index >= items.length) {
            callback();
            return;
        }
        
        var item = items[index];
        var $card = $('.mws-result-card[data-index="' + item.index + '"]');
        var $btn = $card.find('.mws-quick-import');
        
        $btn.trigger('click');
        
        // Wait a bit before next import
        setTimeout(function() {
            importSelectedSequentially(items, index + 1, callback);
        }, 2000);
    }
    
    // Show notice helper
    function showNotice(message, type) {
        var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.mws-search-page').prepend($notice);
        
        setTimeout(function() {
            $notice.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
});
</script>
