<?php
/**
 * Bulk Scrape Page View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap mws-wrap">
    <h1 class="mws-title">
        <span class="dashicons dashicons-download"></span>
        <?php esc_html_e('Bulk Scrape', 'manhwa-scraper'); ?>
    </h1>
    
    <div class="mws-bulk-page">
        <div class="mws-row">
            <!-- Bulk Scrape Form -->
            <div class="mws-col-6">
                <div class="mws-card">
                    <h2><?php esc_html_e('Scrape Settings', 'manhwa-scraper'); ?></h2>
                    <p class="description">
                        <?php esc_html_e('Scrape multiple manhwa from a source list page.', 'manhwa-scraper'); ?>
                    </p>
                    
                    <form id="mws-bulk-form">
                        <table class="form-table">
                            <tr>
                                <th><label for="mws-source"><?php esc_html_e('Source', 'manhwa-scraper'); ?></label></th>
                                <td>
                                    <select id="mws-source" name="source" class="regular-text" required>
                                        <option value=""><?php esc_html_e('Select a source...', 'manhwa-scraper'); ?></option>
                                        <?php foreach ($sources as $source): ?>
                                        <option value="<?php echo esc_attr($source['id']); ?>">
                                            <?php echo esc_html($source['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Scrape Mode', 'manhwa-scraper'); ?></th>
                                <td>
                                    <label style="display: inline-flex; align-items: center; margin-right: 20px; cursor: pointer;">
                                        <input type="radio" name="scrape_mode" value="range" checked style="margin-right: 6px;">
                                        <?php esc_html_e('Page Range', 'manhwa-scraper'); ?>
                                    </label>
                                    <label style="display: inline-flex; align-items: center; cursor: pointer;">
                                        <input type="radio" name="scrape_mode" value="single" style="margin-right: 6px;">
                                        <?php esc_html_e('Single Page Only', 'manhwa-scraper'); ?>
                                    </label>
                                    <p class="description">
                                        <?php esc_html_e('Choose to scrape multiple pages or just a single specific page.', 'manhwa-scraper'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr id="mws-page-number-row">
                                <th><label for="mws-start-page"><?php esc_html_e('Page Number', 'manhwa-scraper'); ?></label></th>
                                <td>
                                    <input type="number" id="mws-start-page" name="start_page" value="1" min="1" max="500" class="small-text">
                                    <p class="description" id="mws-page-desc-range">
                                        <?php esc_html_e('Start from this page number (e.g., enter 10 to start from page 10)', 'manhwa-scraper'); ?>
                                    </p>
                                    <p class="description" id="mws-page-desc-single" style="display: none;">
                                        <?php esc_html_e('Only scrape this specific page (e.g., enter 5 to scrape only page 5)', 'manhwa-scraper'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr id="mws-pages-count-row">
                                <th><label for="mws-pages"><?php esc_html_e('Pages to Scrape', 'manhwa-scraper'); ?></label></th>
                                <td>
                                    <input type="number" id="mws-pages" name="pages" value="1" min="1" max="50" class="small-text">
                                    <p class="description">
                                        <?php esc_html_e('Number of pages to scrape from start page (max 50)', 'manhwa-scraper'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Options', 'manhwa-scraper'); ?></th>
                                <td>
                                    <label style="display: block; margin-bottom: 10px;">
                                        <input type="checkbox" id="mws-scrape-details" name="scrape_details" value="1">
                                        <?php esc_html_e('Scrape full details for each (slower)', 'manhwa-scraper'); ?>
                                    </label>
                                    <label style="display: block;">
                                        <input type="checkbox" id="mws-scrape-chapters" name="scrape_chapters" value="1">
                                        <?php esc_html_e('Also scrape all chapters for each manhwa', 'manhwa-scraper'); ?>
                                    </label>
                                    <p class="description">
                                        <?php esc_html_e('When "Scrape all chapters" is enabled, chapter images will be scraped for each manhwa. This may take a long time.', 'manhwa-scraper'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <button type="submit" class="button button-primary" id="mws-bulk-scrape-btn">
                                <span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
                                <?php esc_html_e('Start Bulk Scrape', 'manhwa-scraper'); ?>
                            </button>
                            <span class="spinner" id="mws-bulk-spinner"></span>
                        </p>
                    </form>
                </div>
            </div>
            
            <!-- Info -->
            <div class="mws-col-6">
                <div class="mws-card">
                    <h2><?php esc_html_e('Information', 'manhwa-scraper'); ?></h2>
                    <div class="mws-notice notice-info">
                        <p>
                            <strong><?php esc_html_e('Parallel Scraping', 'manhwa-scraper'); ?></strong><br>
                            <?php esc_html_e('Bulk scraping uses parallel requests (5 at a time) for faster performance when "Scrape full details" is enabled.', 'manhwa-scraper'); ?>
                        </p>
                    </div>
                    <div class="mws-notice notice-warning">
                        <p>
                            <strong><?php esc_html_e('Scrape Details Option', 'manhwa-scraper'); ?></strong><br>
                            <?php esc_html_e('Enable "Scrape full details" to get complete metadata including description, genres, chapters, and rating.', 'manhwa-scraper'); ?>
                        </p>
                    </div>
                    <div class="mws-notice notice-success">
                        <p>
                            <strong><?php esc_html_e('Speed Comparison', 'manhwa-scraper'); ?></strong><br>
                            <?php esc_html_e('• Without parallel: ~30 seconds per manhwa', 'manhwa-scraper'); ?><br>
                            <?php esc_html_e('• With parallel (5x): ~6 seconds per manhwa', 'manhwa-scraper'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Progress -->
        <div class="mws-card" id="mws-progress-section" style="display: none;">
            <h2><?php esc_html_e('Progress', 'manhwa-scraper'); ?></h2>
            <div class="mws-progress-bar">
                <div class="mws-progress-fill" id="mws-progress-fill" style="width: 0%"></div>
            </div>
            <p class="mws-progress-text" id="mws-progress-text">
                <?php esc_html_e('Starting...', 'manhwa-scraper'); ?>
            </p>
        </div>
        
        <!-- Results -->
        <div class="mws-card" id="mws-bulk-results" style="display: none;">
            <h2><?php esc_html_e('Results', 'manhwa-scraper'); ?></h2>
            
            <div class="mws-results-summary" id="mws-results-summary"></div>
            
            <div class="mws-results-actions" style="margin-bottom: 20px;">
                <button type="button" class="button button-primary" id="mws-export-all-json">
                    <span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Export All as JSON', 'manhwa-scraper'); ?>
                </button>
                <button type="button" class="button" id="mws-import-selected">
                    <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Import Selected', 'manhwa-scraper'); ?>
                </button>
                <button type="button" class="button button-secondary" id="mws-scrape-all-chapters-btn" style="background: #8b5cf6; border-color: #7c3aed; color: #fff;">
                    <span class="dashicons dashicons-images-alt2" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Scrape All Chapters (Selected)', 'manhwa-scraper'); ?>
                </button>
                <label style="margin-left: 20px;">
                    <input type="checkbox" id="mws-select-all-results">
                    <?php esc_html_e('Select All', 'manhwa-scraper'); ?>
                </label>
            </div>
            
            <table class="wp-list-table widefat fixed striped" id="mws-results-table">
                <thead>
                    <tr>
                        <th class="check-column"><input type="checkbox" id="mws-check-all"></th>
                        <th><?php esc_html_e('Cover', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Title', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Type', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Chapters', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Status', 'manhwa-scraper'); ?></th>
                        <th><?php esc_html_e('Actions', 'manhwa-scraper'); ?></th>
                    </tr>
                </thead>
                <tbody id="mws-results-body"></tbody>
            </table>
        </div>
        
        <!-- JSON Output -->
        <div class="mws-card" id="mws-bulk-json-section" style="display: none;">
            <h2>
                <?php esc_html_e('JSON Export', 'manhwa-scraper'); ?>
                <button type="button" class="button button-small" id="mws-copy-bulk-json" style="margin-left: 10px;">
                    <span class="dashicons dashicons-clipboard" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Copy', 'manhwa-scraper'); ?>
                </button>
            </h2>
            <textarea id="mws-bulk-json-output" class="large-text code" rows="15" readonly></textarea>
        </div>
        
        <!-- Chapter Scraping Progress -->
        <div class="mws-card" id="mws-chapter-progress-section" style="display: none;">
            <h2>
                <span class="dashicons dashicons-images-alt2" style="color: #8b5cf6;"></span>
                <?php esc_html_e('Scraping Chapters', 'manhwa-scraper'); ?>
            </h2>
            <div class="mws-chapter-progress-info" style="margin-bottom: 15px;">
                <p><strong><?php esc_html_e('Current Manhwa:', 'manhwa-scraper'); ?></strong> <span id="mws-chapter-current-manhwa">-</span></p>
                <p><strong><?php esc_html_e('Current Chapter:', 'manhwa-scraper'); ?></strong> <span id="mws-chapter-current-chapter">-</span></p>
            </div>
            <div class="mws-progress-bar">
                <div class="mws-progress-fill" id="mws-chapter-progress-fill" style="width: 0%; background: linear-gradient(90deg, #8b5cf6, #a78bfa);"></div>
            </div>
            <p class="mws-progress-text" id="mws-chapter-progress-text">
                <?php esc_html_e('Preparing...', 'manhwa-scraper'); ?>
            </p>
            <div id="mws-chapter-log" style="max-height: 200px; overflow-y: auto; background: #1a1a2e; padding: 10px; border-radius: 6px; margin-top: 15px; font-family: monospace; font-size: 12px; color: #888;">
                <!-- Log entries will appear here -->
            </div>
        </div>
    </div>
</div>

<script>
// Store bulk scraped data globally
var mwsBulkData = [];

// Scrape Mode Toggle
jQuery(document).ready(function($) {
    // Handle mode toggle
    $('input[name="scrape_mode"]').on('change', function() {
        var mode = $(this).val();
        
        if (mode === 'single') {
            // Single page mode
            $('#mws-pages-count-row').hide();
            $('#mws-page-desc-range').hide();
            $('#mws-page-desc-single').show();
            $('#mws-page-number-row th label').text('<?php esc_html_e('Page Number', 'manhwa-scraper'); ?>');
            // Set pages to 1 for single mode
            $('#mws-pages').val(1);
        } else {
            // Range mode
            $('#mws-pages-count-row').show();
            $('#mws-page-desc-range').show();
            $('#mws-page-desc-single').hide();
            $('#mws-page-number-row th label').text('<?php esc_html_e('Start Page', 'manhwa-scraper'); ?>');
        }
    });
    
    // Initialize on page load
    $('input[name="scrape_mode"]:checked').trigger('change');
    
    // Scrape All Chapters Handler
    $('#mws-scrape-all-chapters-btn').on('click', function() {
        // Get selected manhwa
        var selectedRows = $('#mws-results-body tr').filter(function() {
            return $(this).find('input[type="checkbox"]:checked').length > 0;
        });
        
        if (selectedRows.length === 0) {
            alert('<?php esc_html_e('Please select at least one manhwa to scrape chapters.', 'manhwa-scraper'); ?>');
            return;
        }
        
        // Collect manhwa data with chapters
        var manhwaToScrape = [];
        selectedRows.each(function() {
            var index = $(this).data('index');
            if (typeof mwsBulkData[index] !== 'undefined' && mwsBulkData[index].chapters) {
                manhwaToScrape.push({
                    index: index,
                    title: mwsBulkData[index].title,
                    chapters: mwsBulkData[index].chapters
                });
            }
        });
        
        if (manhwaToScrape.length === 0) {
            alert('<?php esc_html_e('No chapters found for selected manhwa. Make sure to enable "Scrape full details" when scraping.', 'manhwa-scraper'); ?>');
            return;
        }
        
        // Show progress section
        $('#mws-chapter-progress-section').show();
        $('#mws-chapter-log').html('');
        
        // Calculate total chapters
        var totalChapters = 0;
        manhwaToScrape.forEach(function(m) {
            totalChapters += m.chapters.length;
        });
        
        addChapterLog('Starting bulk chapter scrape...', 'info');
        addChapterLog('Total manhwa: ' + manhwaToScrape.length + ', Total chapters: ' + totalChapters, 'info');
        
        var processedChapters = 0;
        var currentManhwaIndex = 0;
        
        function processManhwa(mIndex) {
            if (mIndex >= manhwaToScrape.length) {
                // All done
                $('#mws-chapter-progress-text').text('<?php esc_html_e('Completed!', 'manhwa-scraper'); ?>');
                $('#mws-chapter-progress-fill').css('width', '100%');
                addChapterLog('All chapters scraped successfully!', 'success');
                return;
            }
            
            var manhwa = manhwaToScrape[mIndex];
            $('#mws-chapter-current-manhwa').text(manhwa.title + ' (' + (mIndex + 1) + '/' + manhwaToScrape.length + ')');
            addChapterLog('Processing: ' + manhwa.title + ' (' + manhwa.chapters.length + ' chapters)', 'info');
            
            processChapters(manhwa, 0, function() {
                // Move to next manhwa
                processManhwa(mIndex + 1);
            });
        }
        
        function processChapters(manhwa, cIndex, callback) {
            if (cIndex >= manhwa.chapters.length) {
                callback();
                return;
            }
            
            var chapter = manhwa.chapters[cIndex];
            var chapterTitle = chapter.title || ('Chapter ' + (cIndex + 1));
            var chapterUrl = chapter.url;
            
            $('#mws-chapter-current-chapter').text(chapterTitle + ' (' + (cIndex + 1) + '/' + manhwa.chapters.length + ')');
            
            // Make AJAX call to scrape chapter images
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'mws_scrape_chapter_images',
                    nonce: mwsAdmin.nonce,
                    url: chapterUrl
                },
                success: function(response) {
                    processedChapters++;
                    var pct = Math.round(processedChapters / totalChapters * 100);
                    $('#mws-chapter-progress-fill').css('width', pct + '%');
                    $('#mws-chapter-progress-text').text(processedChapters + ' / ' + totalChapters + ' chapters processed (' + pct + '%)');
                    
                    if (response.success) {
                        var imgCount = response.data.images ? response.data.images.length : 0;
                        addChapterLog('✓ ' + chapterTitle + ' - ' + imgCount + ' images', 'success');
                        
                        // Store images in chapters data
                        if (mwsBulkData[manhwa.index] && mwsBulkData[manhwa.index].chapters[cIndex]) {
                            mwsBulkData[manhwa.index].chapters[cIndex].images = response.data.images;
                        }
                    } else {
                        addChapterLog('✗ ' + chapterTitle + ' - ' + (response.data || 'Failed'), 'error');
                    }
                    
                    // Small delay before next chapter to avoid rate limiting
                    setTimeout(function() {
                        processChapters(manhwa, cIndex + 1, callback);
                    }, 500);
                },
                error: function() {
                    processedChapters++;
                    addChapterLog('✗ ' + chapterTitle + ' - Request failed', 'error');
                    
                    setTimeout(function() {
                        processChapters(manhwa, cIndex + 1, callback);
                    }, 500);
                }
            });
        }
        
        function addChapterLog(message, type) {
            var color = '#888';
            if (type === 'success') color = '#10b981';
            else if (type === 'error') color = '#ef4444';
            else if (type === 'info') color = '#8b5cf6';
            
            var time = new Date().toLocaleTimeString();
            $('#mws-chapter-log').append('<div style="color: ' + color + ';">[' + time + '] ' + message + '</div>');
            $('#mws-chapter-log').scrollTop($('#mws-chapter-log')[0].scrollHeight);
        }
        
        // Start processing
        processManhwa(0);
    });
    
    // Enable "Scrape chapters" checkbox only when "Scrape details" is checked
    $('#mws-scrape-details').on('change', function() {
        if ($(this).is(':checked')) {
            $('#mws-scrape-chapters').prop('disabled', false);
        } else {
            $('#mws-scrape-chapters').prop('disabled', true).prop('checked', false);
        }
    });
    $('#mws-scrape-details').trigger('change');
});
</script>
