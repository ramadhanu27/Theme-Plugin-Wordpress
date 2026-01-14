<?php
/**
 * Chapter Scraper Page View
 */

if (!defined('ABSPATH')) {
    exit;
}

$manhwa_manager_active = post_type_exists('manhwa');
?>

<div class="wrap mws-wrap">
    <h1 class="mws-title">
        <span class="dashicons dashicons-images-alt2"></span>
        <?php esc_html_e('Chapter Image Scraper', 'manhwa-scraper'); ?>
    </h1>
    
    <div class="mws-chapter-page">
        <div class="mws-row">
            <!-- Scrape Form -->
            <div class="mws-col-6">
                <div class="mws-card">
                    <h2><?php esc_html_e('Scrape Chapter Images', 'manhwa-scraper'); ?></h2>
                    <p class="description">
                        <?php esc_html_e('Enter a chapter URL to scrape all images from that chapter.', 'manhwa-scraper'); ?>
                    </p>
                    
                    <form id="mws-chapter-form">
                        <table class="form-table">
                            <tr>
                                <th><label for="mws-chapter-url"><?php esc_html_e('Chapter URL', 'manhwa-scraper'); ?></label></th>
                                <td>
                                    <input type="url" id="mws-chapter-url" name="url" class="large-text" 
                                           placeholder="https://manhwaku.id/manga-title-chapter-1/" required>
                                    <p class="description">
                                        <?php esc_html_e('Example: https://manhwaku.id/solo-leveling-chapter-1/', 'manhwa-scraper'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <button type="submit" class="button button-primary" id="mws-chapter-scrape-btn">
                                <span class="dashicons dashicons-search" style="margin-top: 4px;"></span>
                                <?php esc_html_e('Scrape Chapter Images', 'manhwa-scraper'); ?>
                            </button>
                            <span class="spinner" id="mws-chapter-spinner"></span>
                        </p>
                    </form>
                </div>
            </div>
            
            <!-- Info Card -->
            <div class="mws-col-6">
                <div class="mws-card">
                    <h2><?php esc_html_e('How it works', 'manhwa-scraper'); ?></h2>
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li><?php esc_html_e('Enter a chapter URL from a supported source', 'manhwa-scraper'); ?></li>
                        <li><?php esc_html_e('The scraper will extract all page images', 'manhwa-scraper'); ?></li>
                        <li><?php esc_html_e('Preview the images before saving', 'manhwa-scraper'); ?></li>
                        <?php if ($manhwa_manager_active): ?>
                        <li><strong><?php esc_html_e('Save directly to Manhwa Manager post', 'manhwa-scraper'); ?></strong></li>
                        <?php endif; ?>
                    </ul>
                    
                    <?php if (!$manhwa_manager_active): ?>
                    <p class="notice notice-warning" style="padding: 10px;">
                        <?php esc_html_e('Manhwa Manager plugin is not active. Install and activate it to save chapters to posts.', 'manhwa-scraper'); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Bulk Chapter Scraping Section -->
        <?php if ($manhwa_manager_active): ?>
        <div class="mws-card" style="margin-top: 20px;">
            <h2>
                <span class="dashicons dashicons-download" style="color: #2271b1;"></span>
                <?php esc_html_e('Bulk Scrape All Chapters', 'manhwa-scraper'); ?>
            </h2>
            <p class="description">
                <?php esc_html_e('Select a manhwa and scrape all chapters at once. This will fetch images for every chapter that has a source URL.', 'manhwa-scraper'); ?>
            </p>
            
            <div class="mws-row" style="margin-top: 15px;">
                <div class="mws-col-6">
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;">
                        <?php esc_html_e('Filter & Select Manhwa:', 'manhwa-scraper'); ?>
                    </label>
                    
                    <!-- Filter Controls -->
                    <div style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                        <select id="mws-bulk-chapter-status-filter" style="min-width: 180px;">
                            <option value="all"><?php esc_html_e('📋 All Manhwa', 'manhwa-scraper'); ?></option>
                            <option value="need-images"><?php esc_html_e('🔴 Need Images (has ch, no img)', 'manhwa-scraper'); ?></option>
                            <option value="has-external"><?php esc_html_e('🟡 Has External Images', 'manhwa-scraper'); ?></option>
                            <option value="partial-download"><?php esc_html_e('🟠 Partial Download', 'manhwa-scraper'); ?></option>
                            <option value="all-downloaded"><?php esc_html_e('🟢 All Downloaded', 'manhwa-scraper'); ?></option>
                            <option value="no-chapters"><?php esc_html_e('⚪ No Chapters Yet', 'manhwa-scraper'); ?></option>
                        </select>
                        <span id="mws-filter-count" style="color: #666; font-size: 12px;"></span>
                    </div>
                    
                    <!-- Search Input -->
                    <input type="text" id="mws-bulk-manhwa-filter" placeholder="<?php esc_attr_e('🔍 Search by title...', 'manhwa-scraper'); ?>" style="width: 100%; max-width: 450px; margin-bottom: 10px; padding: 8px 12px;">
                    
                    <!-- Manhwa Dropdown -->
                    <select id="mws-bulk-manhwa" style="width: 100%; max-width: 450px; padding: 8px;">
                        <option value=""><?php esc_html_e('-- Select Manhwa --', 'manhwa-scraper'); ?></option>
                        <?php
                        $upload_dir = wp_upload_dir();
                        
                        $manhwa_posts = get_posts([
                            'post_type' => 'manhwa',
                            'posts_per_page' => -1,
                            'orderby' => 'title',
                            'order' => 'ASC'
                        ]);
                        
                        $stats = ['total' => 0, 'need-images' => 0, 'has-external' => 0, 'partial-download' => 0, 'all-downloaded' => 0, 'no-chapters' => 0];
                        
                        foreach ($manhwa_posts as $manhwa) {
                            $chapters = get_post_meta($manhwa->ID, '_manhwa_chapters', true);
                            $ch_count = is_array($chapters) ? count($chapters) : 0;
                            
                            // Count chapters with images and downloaded images
                            $with_images = 0;
                            $with_local = 0;
                            
                            if (is_array($chapters)) {
                                foreach ($chapters as $ch) {
                                    if (!empty($ch['images']) && is_array($ch['images'])) {
                                        $with_images++;
                                        // Check if first image is local
                                        $first_img = $ch['images'][0];
                                        $img_url = is_array($first_img) ? ($first_img['url'] ?? $first_img['src'] ?? '') : $first_img;
                                        if (strpos($img_url, '/wp-content/uploads/manhwa/') !== false) {
                                            $with_local++;
                                        }
                                    }
                                }
                            }
                            
                            // Determine status type for filtering
                            $status_type = 'no-chapters';
                            $status_icon = '⚪';
                            $status_text = 'No Chapters';
                            
                            if ($ch_count > 0) {
                                if ($with_local == $ch_count && $ch_count > 0) {
                                    $status_type = 'all-downloaded';
                                    $status_icon = '🟢';
                                    $status_text = 'All Downloaded';
                                } elseif ($with_local > 0) {
                                    $status_type = 'partial-download';
                                    $status_icon = '🟠';
                                    $status_text = $with_local . '/' . $ch_count . ' Downloaded';
                                } elseif ($with_images > 0) {
                                    $status_type = 'has-external';
                                    $status_icon = '🟡';
                                    $status_text = $with_images . '/' . $ch_count . ' External';
                                } else {
                                    $status_type = 'need-images';
                                    $status_icon = '🔴';
                                    $status_text = $ch_count . ' ch - Need Images';
                                }
                            }
                            
                            $stats['total']++;
                            $stats[$status_type]++;
                            
                            echo '<option value="' . $manhwa->ID . '" data-title="' . esc_attr(strtolower($manhwa->post_title)) . '" data-local="' . $with_local . '" data-total="' . $ch_count . '" data-images="' . $with_images . '" data-status="' . $status_type . '">';
                            echo $status_icon . ' ' . esc_html($manhwa->post_title) . ' (' . $ch_count . ' ch) - ' . $status_text;
                            echo '</option>';
                        }
                        ?>
                    </select>
                    
                    <!-- Stats Summary -->
                    <div style="margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px; font-size: 12px;">
                        <strong><?php esc_html_e('Summary:', 'manhwa-scraper'); ?></strong>
                        <span style="margin-left: 10px;">🔴 <?php echo $stats['need-images']; ?> need images</span>
                        <span style="margin-left: 10px;">🟡 <?php echo $stats['has-external']; ?> external</span>
                        <span style="margin-left: 10px;">🟠 <?php echo $stats['partial-download']; ?> partial</span>
                        <span style="margin-left: 10px;">🟢 <?php echo $stats['all-downloaded']; ?> complete</span>
                        <span style="margin-left: 10px;">⚪ <?php echo $stats['no-chapters']; ?> no ch</span>
                    </div>
                </div>
                <div class="mws-col-6">
                    <label style="font-weight: 600; display: block; margin-bottom: 5px;">
                        <?php esc_html_e('Options:', 'manhwa-scraper'); ?>
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 5px; margin-right: 15px;">
                        <input type="checkbox" id="mws-bulk-skip-existing" checked>
                        <?php esc_html_e('Skip chapters that already have images', 'manhwa-scraper'); ?>
                    </label>
                    <br>
                    <label style="display: inline-flex; align-items: center; gap: 5px; margin-top: 10px;">
                        <input type="checkbox" id="mws-bulk-download-local">
                        <strong style="color: #2271b1;"><?php esc_html_e('Download images to local server', 'manhwa-scraper'); ?></strong>
                        <span class="dashicons dashicons-info" title="<?php esc_attr_e('Images will be saved to /wp-content/uploads/manhwa/chapters/', 'manhwa-scraper'); ?>" style="color: #999; cursor: help;"></span>
                    </label>
                    <br>
                    <label style="display: inline-flex; align-items: center; gap: 5px; margin-top: 10px;">
                        <input type="checkbox" id="mws-bulk-skip-downloaded">
                        <?php esc_html_e('Skip chapters that already downloaded to local', 'manhwa-scraper'); ?>
                    </label>
                    <br>
                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px; background: #e7f3ff; padding: 10px 12px; border-radius: 4px; border: 1px solid #2271b1; flex-wrap: wrap;">
                        <label style="display: inline-flex; align-items: center; gap: 5px;">
                            <input type="checkbox" id="mws-bulk-parallel" checked>
                            <strong style="color: #2271b1;"><?php esc_html_e('Parallel Download', 'manhwa-scraper'); ?></strong>
                        </label>
                        <label style="display: inline-flex; align-items: center; gap: 5px;">
                            <input type="number" id="mws-bulk-batch-size" value="10" min="1" max="100" style="width: 60px; text-align: center;">
                            <span style="color: #2271b1;"><?php esc_html_e('chapters at once', 'manhwa-scraper'); ?></span>
                        </label>
                        <span class="dashicons dashicons-performance" style="color: #2271b1;"></span>
                    </div>
                    <br>
                    <label style="display: inline-flex; align-items: center; gap: 5px; margin-top: 5px;">
                        <input type="number" id="mws-bulk-delay" value="2" min="1" max="10" style="width: 60px;">
                        <?php esc_html_e('seconds delay between batches', 'manhwa-scraper'); ?>
                    </label>
                </div>
            </div>
            
            <!-- Chapter List Preview -->
            <div id="mws-bulk-chapter-list" style="display: none; margin-top: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h3 style="margin: 0;"><?php esc_html_e('Chapters to Scrape:', 'manhwa-scraper'); ?></h3>
                    <div>
                        <label style="margin-right: 15px;">
                            <input type="checkbox" id="mws-bulk-select-all" checked>
                            <?php esc_html_e('Select All', 'manhwa-scraper'); ?>
                        </label>
                        <select id="mws-bulk-filter-status" style="min-width: 150px;">
                            <option value="all"><?php esc_html_e('Show All', 'manhwa-scraper'); ?></option>
                            <option value="no-images"><?php esc_html_e('No Images', 'manhwa-scraper'); ?></option>
                            <option value="external"><?php esc_html_e('External Only', 'manhwa-scraper'); ?></option>
                            <option value="local"><?php esc_html_e('Downloaded', 'manhwa-scraper'); ?></option>
                        </select>
                    </div>
                </div>
                <div id="mws-bulk-chapters-preview" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 10px; background: #f9f9f9;">
                    <!-- Chapter list will be loaded here -->
                </div>
                <p style="margin-top: 10px;">
                    <strong><?php esc_html_e('Total:', 'manhwa-scraper'); ?></strong> 
                    <span id="mws-bulk-total-count">0</span> <?php esc_html_e('chapters', 'manhwa-scraper'); ?>
                    | <strong><?php esc_html_e('To Scrape:', 'manhwa-scraper'); ?></strong> 
                    <span id="mws-bulk-scrape-count">0</span>
                    | <span style="color: green;">[OK] Downloaded: <span id="mws-bulk-downloaded-count">0</span></span>
                    | <span style="color: #2271b1;">[EXT] External: <span id="mws-bulk-external-count">0</span></span>
                    | <span style="color: #999;">[!] No Images: <span id="mws-bulk-noimages-count">0</span></span>
                </p>
            </div>
            
            <!-- Progress Section -->
            <div id="mws-bulk-progress" style="display: none; margin-top: 15px;">
                <h3><?php esc_html_e('Scraping Progress:', 'manhwa-scraper'); ?></h3>
                <div style="background: #f0f0f0; border-radius: 4px; height: 30px; overflow: hidden; margin-bottom: 10px;">
                    <div id="mws-bulk-progress-bar" style="background: linear-gradient(90deg, #2271b1, #135e96); height: 100%; width: 0%; transition: width 0.3s; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                        0%
                    </div>
                </div>
                <div id="mws-bulk-status" style="padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 4px; max-height: 200px; overflow-y: auto;">
                    <!-- Status messages will appear here -->
                </div>
            </div>
            
            <p class="submit" style="margin-top: 15px;">
                <button type="button" class="button button-secondary" id="mws-bulk-load-chapters">
                    <span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Load Chapters', 'manhwa-scraper'); ?>
                </button>
                <button type="button" class="button button-primary" id="mws-bulk-start-scrape" disabled>
                    <span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Start Bulk Scraping', 'manhwa-scraper'); ?>
                </button>
                <button type="button" class="button button-secondary" id="mws-bulk-download-external" disabled style="background: #ff9800; border-color: #e68900; color: white;" title="<?php esc_attr_e('Download all external images to local server (chapters with [EXT] status)', 'manhwa-scraper'); ?>">
                    <span class="dashicons dashicons-cloud-saved" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Download External to Local', 'manhwa-scraper'); ?>
                </button>
                <button type="button" class="button button-secondary" id="mws-bulk-stop-scrape" style="display: none;">
                    <span class="dashicons dashicons-no" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Stop', 'manhwa-scraper'); ?>
                </button>
            </p>
        </div>
        
        <!-- Scrape All Manhwa Chapters Section -->
        <div class="mws-card" style="margin-top: 20px; border: 2px solid #8B5CF6;">
            <h2 style="color: #8B5CF6;">
                <span class="dashicons dashicons-database" style="color: #8B5CF6;"></span>
                <?php esc_html_e('Scrape All Manhwa Chapters (Bulk All)', 'manhwa-scraper'); ?>
            </h2>
            <p class="description">
                <?php esc_html_e('Scrape chapters from ALL manhwa at once. This will process all manhwa that match the selected filter.', 'manhwa-scraper'); ?>
            </p>
            
            <div class="mws-row" style="margin-top: 15px;">
                <div class="mws-col-6">
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;">
                        <?php esc_html_e('Filter Manhwa to Process:', 'manhwa-scraper'); ?>
                    </label>
                    <select id="mws-all-manhwa-filter" style="min-width: 250px;">
                        <option value="need-images"><?php esc_html_e('🔴 Need Images (chapters without images)', 'manhwa-scraper'); ?></option>
                        <option value="has-external"><?php esc_html_e('🟡 Has External (download to local)', 'manhwa-scraper'); ?></option>
                        <option value="partial-download"><?php esc_html_e('🟠 Partial Download', 'manhwa-scraper'); ?></option>
                        <option value="all"><?php esc_html_e('📋 All Manhwa (not recommended)', 'manhwa-scraper'); ?></option>
                    </select>
                    
                    <div style="margin-top: 15px;">
                        <label style="display: inline-flex; align-items: center; gap: 5px;">
                            <input type="checkbox" id="mws-all-download-local" checked>
                            <strong style="color: #8B5CF6;"><?php esc_html_e('Download images to local server', 'manhwa-scraper'); ?></strong>
                        </label>
                    </div>
                    
                    <div style="margin-top: 10px;">
                        <label style="display: inline-flex; align-items: center; gap: 5px;">
                            <input type="number" id="mws-all-delay" value="3" min="1" max="30" style="width: 60px;">
                            <?php esc_html_e('seconds delay between manhwa', 'manhwa-scraper'); ?>
                        </label>
                    </div>
                    
                    <div style="margin-top: 10px;">
                        <label style="display: inline-flex; align-items: center; gap: 5px;">
                            <input type="number" id="mws-all-max-manhwa" value="50" min="1" max="500" style="width: 70px;">
                        <?php esc_html_e('max manhwa per batch', 'manhwa-scraper'); ?>
                    </label>
                </div>
                
                <div style="margin-top: 10px;">
                    <label style="display: inline-flex; align-items: center; gap: 5px;">
                        <input type="number" id="mws-all-parallel-chapters" value="10" min="1" max="100" style="width: 60px;">
                        <?php esc_html_e('parallel chapters (speed)', 'manhwa-scraper'); ?>
                    </label>
                    <span style="color: #666; font-size: 11px; display: block; margin-top: 3px;">
                        <?php esc_html_e('Higher = faster but uses more server resources', 'manhwa-scraper'); ?>
                    </span>
                </div>
                </div>
                
                <div class="mws-col-6">
                    <div id="mws-all-preview" style="padding: 15px; background: #f9f9f9; border-radius: 4px; border: 1px solid #ddd;">
                        <p style="margin: 0; color: #666;"><?php esc_html_e('Click "Preview" to see which manhwa will be processed.', 'manhwa-scraper'); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Progress for All Manhwa -->
            <div id="mws-all-progress" style="display: none; margin-top: 15px;">
                <h3><?php esc_html_e('Processing All Manhwa:', 'manhwa-scraper'); ?></h3>
                <div style="display: flex; gap: 20px; margin-bottom: 10px;">
                    <div>
                        <strong><?php esc_html_e('Current Manhwa:', 'manhwa-scraper'); ?></strong>
                        <span id="mws-all-current-manhwa">-</span>
                    </div>
                    <div>
                        <strong><?php esc_html_e('Progress:', 'manhwa-scraper'); ?></strong>
                        <span id="mws-all-manhwa-progress">0/0</span>
                    </div>
                </div>
                <div style="background: #f0f0f0; border-radius: 4px; height: 30px; overflow: hidden; margin-bottom: 10px;">
                    <div id="mws-all-progress-bar" style="background: linear-gradient(90deg, #8B5CF6, #6D28D9); height: 100%; width: 0%; transition: width 0.3s; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                        0%
                    </div>
                </div>
                <div id="mws-all-status" style="padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 4px; max-height: 300px; overflow-y: auto; font-size: 12px;">
                    <!-- Status messages will appear here -->
                </div>
            </div>
            
            <p class="submit" style="margin-top: 15px;">
                <button type="button" class="button button-secondary" id="mws-all-preview-btn">
                    <span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Preview', 'manhwa-scraper'); ?>
                </button>
                <button type="button" class="button" id="mws-all-start-btn" disabled style="background: #8B5CF6; border-color: #7C3AED; color: white;">
                    <span class="dashicons dashicons-controls-play" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Start Scraping All', 'manhwa-scraper'); ?>
                </button>
                <button type="button" class="button button-secondary" id="mws-all-stop-btn" style="display: none;">
                    <span class="dashicons dashicons-no" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Stop', 'manhwa-scraper'); ?>
                </button>
                <span class="spinner" id="mws-all-spinner"></span>
            </p>
        </div>
        <?php endif; ?>
        
        <!-- Results Section -->
        <div class="mws-card" id="mws-chapter-results" style="display: none;">
            <div class="mws-chapter-header">
                <div>
                    <h2 id="mws-chapter-title" style="margin-bottom: 5px;"></h2>
                    <div class="mws-chapter-meta">
                        <span class="mws-badge mws-badge-primary" id="mws-chapter-number"></span>
                        <span id="mws-chapter-image-count"></span>
                    </div>
                </div>
                
                <!-- Save to Manhwa Section -->
                <?php if ($manhwa_manager_active): ?>
                <div class="mws-save-section" style="display: flex; gap: 10px; align-items: center;">
                    <label for="mws-select-manhwa" style="white-space: nowrap; font-weight: 600;">
                        <?php esc_html_e('Save to:', 'manhwa-scraper'); ?>
                    </label>
                    <select id="mws-select-manhwa" style="min-width: 250px;">
                        <option value=""><?php esc_html_e('-- Select Manhwa --', 'manhwa-scraper'); ?></option>
                    </select>
                    <button type="button" class="button button-primary" id="mws-save-chapter-btn">
                        <span class="dashicons dashicons-saved" style="margin-top: 4px;"></span>
                        <?php esc_html_e('Save to Manhwa', 'manhwa-scraper'); ?>
                    </button>
                    <span class="spinner" id="mws-save-spinner"></span>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Auto-detected manhwa notice -->
            <div id="mws-detected-manhwa" class="notice notice-info" style="display: none; margin: 15px 0; padding: 10px;">
                <strong><?php esc_html_e('Detected Manhwa:', 'manhwa-scraper'); ?></strong>
                <span id="mws-detected-manhwa-title"></span>
                <a href="#" id="mws-detected-manhwa-link" target="_blank"><?php esc_html_e('Edit', 'manhwa-scraper'); ?></a>
            </div>
            
            <!-- Success message -->
            <div id="mws-save-success" class="notice notice-success" style="display: none; margin: 15px 0; padding: 10px;">
                <span id="mws-save-success-message"></span>
            </div>
            
            <!-- Navigation -->
            <div class="mws-chapter-nav" style="margin-bottom: 20px;">
                <button type="button" class="button" id="mws-prev-chapter" disabled>
                    <span class="dashicons dashicons-arrow-left-alt2" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Previous Chapter', 'manhwa-scraper'); ?>
                </button>
                <button type="button" class="button" id="mws-next-chapter" disabled>
                    <?php esc_html_e('Next Chapter', 'manhwa-scraper'); ?>
                    <span class="dashicons dashicons-arrow-right-alt2" style="margin-top: 4px;"></span>
                </button>
            </div>
            
            <!-- Images Grid -->
            <div class="mws-images-grid" id="mws-images-grid">
                <!-- Images will be loaded here -->
            </div>
            
            <!-- Actions -->
            <div class="mws-chapter-actions" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
                <button type="button" class="button button-primary" id="mws-export-images-json">
                    <span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Export JSON', 'manhwa-scraper'); ?>
                </button>
                <button type="button" class="button" id="mws-copy-images-urls">
                    <span class="dashicons dashicons-clipboard" style="margin-top: 4px;"></span>
                    <?php esc_html_e('Copy All URLs', 'manhwa-scraper'); ?>
                </button>
            </div>
        </div>
        
        <!-- JSON Preview -->
        <div class="mws-card" id="mws-chapter-json-section" style="display: none;">
            <h2><?php esc_html_e('Image URLs JSON', 'manhwa-scraper'); ?></h2>
            <textarea id="mws-chapter-json-output" class="large-text code" rows="10" readonly></textarea>
        </div>
    </div>
</div>

<style>
.mws-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
}
.mws-image-item {
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
    background: #f9f9f9;
}
.mws-image-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    display: block;
}
.mws-image-item .mws-image-info {
    padding: 8px;
    font-size: 12px;
    background: #fff;
    border-top: 1px solid #eee;
}
.mws-chapter-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}
.mws-chapter-nav {
    display: flex;
    gap: 10px;
}
.mws-badge-primary {
    background: #2271b1;
    color: #fff;
    padding: 3px 10px;
    border-radius: 3px;
    font-size: 12px;
}
</style>

<script>
var mwsChapterData = null;

jQuery(document).ready(function($) {
    // Chapter scrape form
    $('#mws-chapter-form').on('submit', function(e) {
        e.preventDefault();
        
        var url = $('#mws-chapter-url').val();
        var $spinner = $('#mws-chapter-spinner');
        var $btn = $('#mws-chapter-scrape-btn');
        
        if (!url) return;
        
        $btn.prop('disabled', true);
        $spinner.addClass('is-active');
        $('#mws-chapter-results').hide();
        $('#mws-chapter-json-section').hide();
        $('#mws-save-success').hide();
        
        $.ajax({
            url: mwsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mws_scrape_chapter_images',
                nonce: mwsData.nonce,
                url: url
            },
            success: function(response) {
                if (response.success) {
                    mwsChapterData = response.data.data;
                    displayChapterImages(mwsChapterData);
                    populateManhwaSelect(mwsChapterData);
                    $('#mws-chapter-results').show();
                } else {
                    alert(response.data.message || 'Error scraping chapter');
                }
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.removeClass('is-active');
            }
        });
    });
    
    function displayChapterImages(data) {
        $('#mws-chapter-title').text(data.chapter_title);
        $('#mws-chapter-number').text('Chapter ' + (data.chapter_number || 'N/A'));
        $('#mws-chapter-image-count').text(data.total_images + ' images');
        
        var $grid = $('#mws-images-grid').empty();
        
        data.images.forEach(function(img, index) {
            $grid.append(
                '<div class="mws-image-item">' +
                    '<img src="' + img.url + '" alt="Page ' + (index + 1) + '" loading="lazy" onerror="this.style.display=\'none\'">' +
                    '<div class="mws-image-info">Page ' + (index + 1) + '</div>' +
                '</div>'
            );
        });
        
        // Navigation buttons
        if (data.prev_chapter) {
            $('#mws-prev-chapter').prop('disabled', false).data('url', data.prev_chapter);
        } else {
            $('#mws-prev-chapter').prop('disabled', true);
        }
        
        if (data.next_chapter) {
            $('#mws-next-chapter').prop('disabled', false).data('url', data.next_chapter);
        } else {
            $('#mws-next-chapter').prop('disabled', true);
        }
    }
    
    function populateManhwaSelect(data) {
        var $select = $('#mws-select-manhwa');
        $select.find('option:not(:first)').remove();
        
        // Add available manhwa options
        if (data.available_manhwa) {
            data.available_manhwa.forEach(function(manhwa) {
                $select.append('<option value="' + manhwa.id + '">' + manhwa.title + '</option>');
            });
        }
        
        // Auto-select detected manhwa
        if (data.manhwa_post) {
            $select.val(data.manhwa_post.id);
            $('#mws-detected-manhwa-title').text(data.manhwa_post.title);
            $('#mws-detected-manhwa-link').attr('href', data.manhwa_post.edit_url);
            $('#mws-detected-manhwa').show();
        } else {
            $('#mws-detected-manhwa').hide();
        }
    }
    
    // Save chapter to manhwa post
    $('#mws-save-chapter-btn').on('click', function() {
        if (!mwsChapterData) return;
        
        var postId = $('#mws-select-manhwa').val();
        if (!postId) {
            alert('<?php esc_html_e('Please select a manhwa post', 'manhwa-scraper'); ?>');
            return;
        }
        
        var $btn = $(this);
        var $spinner = $('#mws-save-spinner');
        
        $btn.prop('disabled', true);
        $spinner.addClass('is-active');
        $('#mws-save-success').hide();
        
        $.ajax({
            url: mwsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mws_save_chapter_to_post',
                nonce: mwsData.nonce,
                post_id: postId,
                chapter_data: JSON.stringify(mwsChapterData)
            },
            success: function(response) {
                if (response.success) {
                    $('#mws-save-success-message').html(
                        response.data.message + 
                        ' <a href="' + response.data.edit_url + '" target="_blank"><?php esc_html_e('Edit Manhwa', 'manhwa-scraper'); ?></a>'
                    );
                    $('#mws-save-success').show();
                } else {
                    alert(response.data.message || 'Error saving chapter');
                }
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.removeClass('is-active');
            }
        });
    });
    
    // Navigation buttons
    $('#mws-prev-chapter, #mws-next-chapter').on('click', function() {
        var url = $(this).data('url');
        if (url) {
            $('#mws-chapter-url').val(url);
            $('#mws-chapter-form').submit();
        }
    });
    
    // Export JSON
    $('#mws-export-images-json').on('click', function() {
        if (!mwsChapterData) return;
        
        var json = JSON.stringify(mwsChapterData, null, 2);
        var blob = new Blob([json], {type: 'application/json'});
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'chapter-images-' + (mwsChapterData.chapter_number || 'unknown') + '.json';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
    
    // Copy URLs
    $('#mws-copy-images-urls').on('click', function() {
        if (!mwsChapterData) return;
        
        var urls = mwsChapterData.images.map(function(img) { return img.url; }).join('\n');
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(urls).then(function() {
                alert('<?php esc_html_e('URLs copied to clipboard!', 'manhwa-scraper'); ?>');
            });
        } else {
            $('#mws-chapter-json-output').val(urls);
            $('#mws-chapter-json-section').show();
        }
    });
    
    // ========== BULK SCRAPING FUNCTIONALITY ==========
    var bulkChaptersData = [];
    var bulkScrapeRunning = false;
    var bulkScrapeAborted = false;
    
    // Load chapters for selected manhwa
    $('#mws-bulk-load-chapters').on('click', function() {
        var postId = $('#mws-bulk-manhwa').val();
        if (!postId) {
            alert('<?php esc_html_e('Please select a manhwa first', 'manhwa-scraper'); ?>');
            return;
        }
        
        var $spinner = $('#mws-bulk-spinner');
        $spinner.addClass('is-active');
        
        $.ajax({
            url: mwsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mws_get_manhwa_chapters',
                nonce: mwsData.nonce,
                post_id: postId
            },
            success: function(response) {
                if (response.success) {
                    bulkChaptersData = response.data.chapters;
                    displayBulkChapters(bulkChaptersData);
                    $('#mws-bulk-chapter-list').show();
                    $('#mws-bulk-start-scrape').prop('disabled', false);
                } else {
                    alert(response.data.message || 'Error loading chapters');
                }
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            },
            complete: function() {
                $spinner.removeClass('is-active');
            }
        });
    });
    
    function displayBulkChapters(chapters, filterStatus) {
        var $preview = $('#mws-bulk-chapters-preview').empty();
        var skipExisting = $('#mws-bulk-skip-existing').is(':checked');
        var skipDownloaded = $('#mws-bulk-skip-downloaded').is(':checked');
        var toScrape = 0;
        var downloadedCount = 0;
        var externalCount = 0;
        var noImagesCount = 0;
        
        filterStatus = filterStatus || $('#mws-bulk-filter-status').val() || 'all';
        
        chapters.forEach(function(ch, index) {
            var hasImages = ch.images && ch.images.length > 0;
            var hasUrl = ch.url && ch.url.trim() !== '';
            var isLocal = false;
            
            // Check if images are downloaded to local server
            if (hasImages) {
                var firstImg = ch.images[0];
                var imgUrl = typeof firstImg === 'object' ? (firstImg.url || firstImg.src || '') : firstImg;
                isLocal = imgUrl.indexOf('/wp-content/uploads/manhwa/') !== -1;
            }
            
            // Count stats
            if (isLocal) {
                downloadedCount++;
            } else if (hasImages) {
                externalCount++;
            } else {
                noImagesCount++;
            }
            
            // Determine chapter status type
            var statusType = isLocal ? 'local' : (hasImages ? 'external' : 'no-images');
            
            // Filter visibility
            var showItem = filterStatus === 'all' || filterStatus === statusType;
            if (!showItem) return;
            
            // Determine if should be checked for scraping
            var willScrape = hasUrl && !isLocal; // Don't scrape if already downloaded
            if (skipExisting && hasImages) willScrape = false;
            if (skipDownloaded && isLocal) willScrape = false;
            
            if (willScrape) toScrape++;
            
            // Status display
            var statusClass, statusText, statusIcon;
            if (isLocal) {
                statusClass = 'color: green;';
                statusIcon = '[OK]';
                statusText = 'Downloaded (' + ch.images.length + ' images)';
            } else if (hasImages) {
                statusClass = 'color: #2271b1;';
                statusIcon = '[EXT]';
                statusText = 'External (' + ch.images.length + ' images)';
            } else if (hasUrl) {
                statusClass = 'color: orange;';
                statusIcon = '[~]';
                statusText = 'Ready to scrape';
            } else {
                statusClass = 'color: #999;';
                statusIcon = '[!]';
                statusText = 'No URL';
            }
            
            $preview.append(
                '<div class="mws-bulk-chapter-row" style="display: flex; justify-content: space-between; padding: 8px 5px; border-bottom: 1px solid #eee;" data-index="' + index + '" data-status="' + statusType + '">' +
                    '<span><input type="checkbox" class="mws-bulk-chapter-check" ' + (willScrape ? 'checked' : '') + ' data-index="' + index + '"> ' + 
                    (ch.title || ch.number || 'Chapter ' + (index + 1)) + '</span>' +
                    '<span style="' + statusClass + ' font-size: 12px;">' + statusIcon + ' ' + statusText + '</span>' +
                '</div>'
            );
        });
        
        $('#mws-bulk-total-count').text(chapters.length);
        $('#mws-bulk-scrape-count').text(toScrape);
        $('#mws-bulk-downloaded-count').text(downloadedCount);
        $('#mws-bulk-external-count').text(externalCount);
        $('#mws-bulk-noimages-count').text(noImagesCount);
        
        // Enable/disable Download External button based on external count
        if (externalCount > 0) {
            $('#mws-bulk-download-external').prop('disabled', false);
        } else {
            $('#mws-bulk-download-external').prop('disabled', true);
        }
    }
    
    // Combined filter function for manhwa list
    function filterManhwaList() {
        var textFilter = $('#mws-bulk-manhwa-filter').val().toLowerCase();
        var statusFilter = $('#mws-bulk-chapter-status-filter').val();
        var visibleCount = 0;
        
        $('#mws-bulk-manhwa option').each(function() {
            var $opt = $(this);
            if ($opt.val() === '') {
                return; // Keep placeholder always visible
            }
            
            var title = $opt.data('title') || $opt.text().toLowerCase();
            var status = $opt.data('status') || '';
            
            // Text filter
            var matchesText = textFilter === '' || title.indexOf(textFilter) !== -1;
            
            // Status filter - direct match
            var matchesStatus = (statusFilter === 'all') || (status === statusFilter);
            
            if (matchesText && matchesStatus) {
                $opt.show();
                visibleCount++;
            } else {
                $opt.hide();
            }
        });
        
        // Update count display
        $('#mws-filter-count').text('(' + visibleCount + ' manhwa)');
    }
    
    // Manhwa title filter
    $('#mws-bulk-manhwa-filter').on('input', filterManhwaList);
    
    // Status filter change
    $('#mws-bulk-chapter-status-filter').on('change', function() {
        filterManhwaList();
        // Reset manhwa selection
        $('#mws-bulk-manhwa').val('');
    });
    
    // Filter status change
    $('#mws-bulk-filter-status').on('change', function() {
        if (bulkChaptersData.length > 0) {
            displayBulkChapters(bulkChaptersData, $(this).val());
        }
    });
    
    // Select all checkbox
    $('#mws-bulk-select-all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.mws-bulk-chapter-check:visible').prop('checked', isChecked);
        var checked = $('.mws-bulk-chapter-check:checked').length;
        $('#mws-bulk-scrape-count').text(checked);
    });
    
    // Update count when checkboxes change
    $(document).on('change', '.mws-bulk-chapter-check', function() {
        var checked = $('.mws-bulk-chapter-check:checked').length;
        $('#mws-bulk-scrape-count').text(checked);
    });
    
    // Skip existing checkbox change
    $('#mws-bulk-skip-existing, #mws-bulk-skip-downloaded').on('change', function() {
        if (bulkChaptersData.length > 0) {
            displayBulkChapters(bulkChaptersData);
        }
    });
    
    // Start bulk scraping
    $('#mws-bulk-start-scrape').on('click', function() {
        var postId = $('#mws-bulk-manhwa').val();
        if (!postId) return;
        
        var selectedIndices = [];
        $('.mws-bulk-chapter-check:checked').each(function() {
            selectedIndices.push(parseInt($(this).data('index')));
        });
        
        if (selectedIndices.length === 0) {
            alert('<?php esc_html_e('No chapters selected for scraping', 'manhwa-scraper'); ?>');
            return;
        }
        
        var isParallel = $('#mws-bulk-parallel').is(':checked');
        var batchSizeVal = parseInt($('#mws-bulk-batch-size').val()) || 5;
        var parallelText = isParallel ? ' (' + batchSizeVal + ' at a time)' : ' (sequential)';
        
        if (!confirm('<?php esc_html_e('Start scraping', 'manhwa-scraper'); ?> ' + selectedIndices.length + ' <?php esc_html_e('chapters', 'manhwa-scraper'); ?>' + parallelText + '? <?php esc_html_e('This may take a while.', 'manhwa-scraper'); ?>')) {
            return;
        }
        
        bulkScrapeRunning = true;
        bulkScrapeAborted = false;
        
        $('#mws-bulk-start-scrape').hide();
        $('#mws-bulk-stop-scrape').show();
        $('#mws-bulk-progress').show();
        $('#mws-bulk-status').empty();
        
        var delay = parseInt($('#mws-bulk-delay').val()) * 1000 || 2000;
        var batchSize = parseInt($('#mws-bulk-batch-size').val()) || 5;
        batchSize = Math.max(1, Math.min(10, batchSize)); // Clamp between 1-10
        
        if (isParallel) {
            // Parallel processing - custom batch size
            addBulkStatus('⚡ <?php esc_html_e('Mode: Parallel', 'manhwa-scraper'); ?> (' + batchSize + ' <?php esc_html_e('chapters at a time', 'manhwa-scraper'); ?>)', 'info');
            processBulkChaptersParallel(postId, selectedIndices, 0, delay, batchSize);
        } else {
            // Sequential processing - 1 at a time
            addBulkStatus('📝 <?php esc_html_e('Mode: Sequential (1 at a time)', 'manhwa-scraper'); ?>', 'info');
            processBulkChapters(postId, selectedIndices, 0, delay);
        }
    });
    
    // Stop bulk scraping
    $('#mws-bulk-stop-scrape').on('click', function() {
        bulkScrapeAborted = true;
        addBulkStatus('<?php esc_html_e('Stopping...', 'manhwa-scraper'); ?>', 'warning');
    });
    
    // Download External Images to Local
    $('#mws-bulk-download-external').on('click', function() {
        var postId = $('#mws-bulk-manhwa').val();
        if (!postId) return;
        
        // Get chapters with external images
        var externalChapters = [];
        bulkChaptersData.forEach(function(ch, index) {
            if (ch.images && ch.images.length > 0) {
                var firstImg = ch.images[0];
                var imgUrl = typeof firstImg === 'object' ? (firstImg.url || firstImg.src || '') : firstImg;
                var isLocal = imgUrl.indexOf('/wp-content/uploads/manhwa/') !== -1;
                
                if (!isLocal) {
                    externalChapters.push({
                        index: index,
                        chapter: ch
                    });
                }
            }
        });
        
        if (externalChapters.length === 0) {
            alert('<?php esc_html_e('No external images found to download', 'manhwa-scraper'); ?>');
            return;
        }
        
        if (!confirm('<?php esc_html_e('Download', 'manhwa-scraper'); ?> ' + externalChapters.length + ' <?php esc_html_e('chapters with external images to local server?', 'manhwa-scraper'); ?>\n<?php esc_html_e('This may take a while.', 'manhwa-scraper'); ?>')) {
            return;
        }
        
        bulkScrapeRunning = true;
        bulkScrapeAborted = false;
        
        $('#mws-bulk-start-scrape').hide();
        $('#mws-bulk-download-external').hide();
        $('#mws-bulk-stop-scrape').show();
        $('#mws-bulk-progress').show();
        $('#mws-bulk-status').empty();
        
        addBulkStatus('🔄 <?php esc_html_e('Downloading external images to local server...', 'manhwa-scraper'); ?>', 'info');
        addBulkStatus('📊 <?php esc_html_e('Total chapters to download:', 'manhwa-scraper'); ?> ' + externalChapters.length, 'info');
        
        processExternalDownloads(postId, externalChapters, 0);
    });
    
    // Process external downloads sequentially
    function processExternalDownloads(postId, chapters, currentIndex) {
        if (bulkScrapeAborted || currentIndex >= chapters.length) {
            bulkScrapeRunning = false;
            $('#mws-bulk-stop-scrape').hide();
            $('#mws-bulk-start-scrape').show();
            $('#mws-bulk-download-external').show();
            
            if (bulkScrapeAborted) {
                addBulkStatus('<?php esc_html_e('Download stopped by user', 'manhwa-scraper'); ?>', 'warning');
            } else {
                addBulkStatus('✅ <?php esc_html_e('All external images downloaded successfully!', 'manhwa-scraper'); ?>', 'success');
                addBulkStatus('🔄 <?php esc_html_e('Refreshing chapter list...', 'manhwa-scraper'); ?>', 'info');
                
                // Reload chapters from database to show updated local URLs
                $.ajax({
                    url: mwsData.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'mws_get_manhwa_chapters',
                        nonce: mwsData.nonce,
                        post_id: postId
                    },
                    success: function(response) {
                        if (response.success) {
                            bulkChaptersData = response.data.chapters;
                            displayBulkChapters(bulkChaptersData);
                            addBulkStatus('✅ <?php esc_html_e('Chapter list refreshed!', 'manhwa-scraper'); ?>', 'success');
                        }
                    }
                });
            }
            return;
        }
        
        var item = chapters[currentIndex];
        var chapter = item.chapter;
        var chapterTitle = chapter.title || chapter.number || 'Chapter ' + (item.index + 1);
        
        // Update progress
        var progress = Math.round(((currentIndex + 1) / chapters.length) * 100);
        $('#mws-bulk-progress-bar').css('width', progress + '%').text(progress + '%');
        
        // Build chapter data for download
        var chapterData = {
            chapter_number: chapter.number || (item.index + 1),
            chapter_title: chapterTitle,
            images: chapter.images.map(function(img, idx) {
                return {
                    index: idx,
                    url: typeof img === 'object' ? (img.url || img.src || img) : img,
                    alt: 'Page ' + (idx + 1)
                };
            })
        };
        
        $.ajax({
            url: mwsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mws_download_chapter_images',
                nonce: mwsData.nonce,
                post_id: postId,
                chapter_data: JSON.stringify(chapterData)
            },
            success: function(response) {
                if (response.success) {
                    var r = response.data.result;
                    addBulkStatus('✓ ' + chapterTitle + ' - <?php esc_html_e('Downloaded', 'manhwa-scraper'); ?> ' + r.success + '/' + r.total + ' <?php esc_html_e('images', 'manhwa-scraper'); ?>', 'success');
                    
                    // Update local chapter data
                    if (response.data.local_images) {
                        bulkChaptersData[item.index].images = response.data.local_images;
                    }
                } else {
                    addBulkStatus('✗ ' + chapterTitle + ' - ' + (response.data.message || 'Error'), 'error');
                }
                
                // Continue with next
                setTimeout(function() {
                    processExternalDownloads(postId, chapters, currentIndex + 1);
                }, 500);
            },
            error: function() {
                addBulkStatus('✗ ' + chapterTitle + ' - <?php esc_html_e('Request failed', 'manhwa-scraper'); ?>', 'error');
                setTimeout(function() {
                    processExternalDownloads(postId, chapters, currentIndex + 1);
                }, 500);
            }
        });
    }
    
    // Parallel processing function
    function processBulkChaptersParallel(postId, indices, startIndex, delay, batchSize) {
        if (bulkScrapeAborted || startIndex >= indices.length) {
            // Done
            bulkScrapeRunning = false;
            $('#mws-bulk-stop-scrape').hide();
            $('#mws-bulk-start-scrape').show();
            
            if (bulkScrapeAborted) {
                addBulkStatus('<?php esc_html_e('Scraping stopped by user', 'manhwa-scraper'); ?>', 'warning');
            } else {
                addBulkStatus('<?php esc_html_e('All chapters scraped successfully!', 'manhwa-scraper'); ?>', 'success');
            }
            return;
        }
        
        // Get batch of chapters to process
        var batch = indices.slice(startIndex, startIndex + batchSize);
        var batchNum = Math.floor(startIndex / batchSize) + 1;
        var totalBatches = Math.ceil(indices.length / batchSize);
        
        addBulkStatus('📦 <?php esc_html_e('Processing batch', 'manhwa-scraper'); ?> ' + batchNum + '/' + totalBatches + ' (' + batch.length + ' <?php esc_html_e('chapters', 'manhwa-scraper'); ?>)', 'info');
        
        // Update progress
        var progress = Math.round(((startIndex + batch.length) / indices.length) * 100);
        $('#mws-bulk-progress-bar').css('width', progress + '%').text(progress + '%');
        
        // Process all chapters in batch simultaneously
        var promises = batch.map(function(chapterIndex) {
            return scrapeChapterAsync(postId, chapterIndex);
        });
        
        Promise.all(promises).then(function() {
            // All batch items complete, continue with next batch
            setTimeout(function() {
                processBulkChaptersParallel(postId, indices, startIndex + batchSize, delay, batchSize);
            }, delay);
        });
    }
    
    // Async chapter scrape function for parallel processing
    function scrapeChapterAsync(postId, chapterIndex) {
        return new Promise(function(resolve) {
            var chapter = bulkChaptersData[chapterIndex];
            var chapterTitle = chapter.title || chapter.number || 'Chapter ' + (chapterIndex + 1);
            
            if (!chapter.url) {
                addBulkStatus('⏭️ <?php esc_html_e('Skipped (no URL):', 'manhwa-scraper'); ?> ' + chapterTitle, 'warning');
                resolve();
                return;
            }
            
            $.ajax({
                url: mwsData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'mws_scrape_chapter_images',
                    nonce: mwsData.nonce,
                    url: chapter.url
                },
                success: function(response) {
                    if (response.success && response.data.data) {
                        var imgCount = response.data.data.images ? response.data.data.images.length : 0;
                        addBulkStatus('✓ ' + chapterTitle + ' - ' + imgCount + ' <?php esc_html_e('images', 'manhwa-scraper'); ?>', 'success');
                        
                        // Check if should download to local FIRST
                        var downloadLocal = $('#mws-bulk-download-local').is(':checked');
                        if (downloadLocal && response.data.data.images && response.data.data.images.length > 0) {
                            // Download first
                            $.ajax({
                                url: mwsData.ajaxUrl,
                                type: 'POST',
                                data: {
                                    action: 'mws_download_chapter_images',
                                    nonce: mwsData.nonce,
                                    post_id: postId,
                                    chapter_data: JSON.stringify(response.data.data)
                                },
                                success: function(dlResponse) {
                                    if (dlResponse.success) {
                                        var r = dlResponse.data.result;
                                        addBulkStatus('  ⬇ ' + chapterTitle + ' - <?php esc_html_e('Downloaded', 'manhwa-scraper'); ?> ' + r.success + ' <?php esc_html_e('images', 'manhwa-scraper'); ?>', 'success');
                                        
                                        // Now save with LOCAL URLs from download result
                                        var chapterDataWithLocal = Object.assign({}, response.data.data);
                                        if (dlResponse.data.local_images && dlResponse.data.local_images.length > 0) {
                                            chapterDataWithLocal.images = dlResponse.data.local_images;
                                        }
                                        
                                        $.ajax({
                                            url: mwsData.ajaxUrl,
                                            type: 'POST',
                                            data: {
                                                action: 'mws_save_chapter_to_post',
                                                nonce: mwsData.nonce,
                                                post_id: postId,
                                                chapter_data: JSON.stringify(chapterDataWithLocal)
                                            },
                                            success: function(saveResponse) {
                                                if (saveResponse.success) {
                                                    bulkChaptersData[chapterIndex].images = chapterDataWithLocal.images;
                                                    bulkChaptersData[chapterIndex].images_local = true;
                                                }
                                                resolve();
                                            },
                                            error: function() {
                                                addBulkStatus('✗ <?php esc_html_e('Save error:', 'manhwa-scraper'); ?> ' + chapterTitle, 'error');
                                                resolve();
                                            }
                                        });
                                    } else {
                                        // Download failed, save with external URLs
                                        saveChapterData(response.data.data, postId, chapterIndex, chapterTitle, resolve);
                                    }
                                },
                                error: function() {
                                    // Download failed, save with external URLs
                                    saveChapterData(response.data.data, postId, chapterIndex, chapterTitle, resolve);
                                }
                            });
                        } else {
                            // No download, save with external URLs
                            saveChapterData(response.data.data, postId, chapterIndex, chapterTitle, resolve);
                        }
                    } else {
                        var errorMsg = response.data && response.data.message ? response.data.message : 'Unknown error';
                        addBulkStatus('✗ <?php esc_html_e('Error:', 'manhwa-scraper'); ?> ' + chapterTitle + ' - ' + errorMsg, 'error');
                        console.error('Scrape error for ' + chapterTitle + ':', response);
                        resolve();
                    }
                },
                error: function(xhr, status, error) {
                    var errorMsg = error || status || 'Request failed';
                    if (xhr.responseText && xhr.responseText.indexOf('Fatal error') !== -1) {
                        errorMsg = 'PHP Fatal Error - check server logs';
                    }
                    addBulkStatus('✗ <?php esc_html_e('Error:', 'manhwa-scraper'); ?> ' + chapterTitle + ' - ' + errorMsg, 'error');
                    console.error('AJAX error for ' + chapterTitle + ':', {status: status, error: error, xhr: xhr});
                    resolve();
                }
            });
        });
    }
    
    // Sequential processing function (original)
    function processBulkChapters(postId, indices, currentIndex, delay) {
        if (bulkScrapeAborted || currentIndex >= indices.length) {
            // Done
            bulkScrapeRunning = false;
            $('#mws-bulk-stop-scrape').hide();
            $('#mws-bulk-start-scrape').show();
            
            if (bulkScrapeAborted) {
                addBulkStatus('<?php esc_html_e('Scraping stopped by user', 'manhwa-scraper'); ?>', 'warning');
            } else {
                addBulkStatus('<?php esc_html_e('All chapters scraped successfully!', 'manhwa-scraper'); ?>', 'success');
            }
            return;
        }
        
        var chapterIndex = indices[currentIndex];
        var chapter = bulkChaptersData[chapterIndex];
        var chapterTitle = chapter.title || chapter.number || 'Chapter ' + (chapterIndex + 1);
        
        // Update progress
        var progress = Math.round(((currentIndex + 1) / indices.length) * 100);
        $('#mws-bulk-progress-bar').css('width', progress + '%').text(progress + '%');
        
        addBulkStatus('<?php esc_html_e('Scraping:', 'manhwa-scraper'); ?> ' + chapterTitle + '...', 'info');
        console.log('Scraping URL:', chapter.url); // Debug log
        
        if (!chapter.url) {
            addBulkStatus('<?php esc_html_e('Skipped (no URL):', 'manhwa-scraper'); ?> ' + chapterTitle, 'warning');
            setTimeout(function() {
                processBulkChapters(postId, indices, currentIndex + 1, delay);
            }, 100);
            return;
        }
        
        $.ajax({
            url: mwsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mws_scrape_chapter_images',
                nonce: mwsData.nonce,
                url: chapter.url
            },
            success: function(response) {
                if (response.success && response.data.data) {
                    var imgCount = response.data.data.images ? response.data.data.images.length : 0;
                    addBulkStatus('✓ ' + chapterTitle + ' - ' + imgCount + ' <?php esc_html_e('images found', 'manhwa-scraper'); ?>', 'success');
                    
                    // Save to post
                    $.ajax({
                        url: mwsData.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'mws_save_chapter_to_post',
                            nonce: mwsData.nonce,
                            post_id: postId,
                            chapter_data: JSON.stringify(response.data.data)
                        },
                        success: function(saveResponse) {
                            if (saveResponse.success) {
                                addBulkStatus('  ✓ <?php esc_html_e('Saved to manhwa post', 'manhwa-scraper'); ?>', 'success');
                                // Update local data
                                bulkChaptersData[chapterIndex].images = response.data.data.images;
                                
                                // Check if should download to local
                                var downloadLocal = $('#mws-bulk-download-local').is(':checked');
                                if (downloadLocal && response.data.data.images && response.data.data.images.length > 0) {
                                    addBulkStatus('  ⬇ <?php esc_html_e('Downloading images to local server...', 'manhwa-scraper'); ?>', 'info');
                                    
                                    $.ajax({
                                        url: mwsData.ajaxUrl,
                                        type: 'POST',
                                        data: {
                                            action: 'mws_download_chapter_images',
                                            nonce: mwsData.nonce,
                                            post_id: postId,
                                            chapter_data: JSON.stringify(response.data.data)
                                        },
                                        success: function(dlResponse) {
                                            if (dlResponse.success) {
                                                var r = dlResponse.data.result;
                                                addBulkStatus('  ✓ <?php esc_html_e('Downloaded:', 'manhwa-scraper'); ?> ' + r.success + ' <?php esc_html_e('images', 'manhwa-scraper'); ?>' + (r.skipped > 0 ? ' (' + r.skipped + ' <?php esc_html_e('skipped', 'manhwa-scraper'); ?>)' : ''), 'success');
                                            } else {
                                                addBulkStatus('  ✗ <?php esc_html_e('Download failed:', 'manhwa-scraper'); ?> ' + (dlResponse.data.message || 'Unknown error'), 'error');
                                            }
                                        },
                                        error: function() {
                                            addBulkStatus('  ✗ <?php esc_html_e('Download error', 'manhwa-scraper'); ?>', 'error');
                                        },
                                        complete: function() {
                                            // Continue with next chapter after delay
                                            setTimeout(function() {
                                                processBulkChapters(postId, indices, currentIndex + 1, delay);
                                            }, delay);
                                        }
                                    });
                                } else {
                                    // Continue with next chapter after delay
                                    setTimeout(function() {
                                        processBulkChapters(postId, indices, currentIndex + 1, delay);
                                    }, delay);
                                }
                            } else {
                                addBulkStatus('  ✗ <?php esc_html_e('Error saving:', 'manhwa-scraper'); ?> ' + (saveResponse.data.message || 'Unknown error'), 'error');
                                setTimeout(function() {
                                    processBulkChapters(postId, indices, currentIndex + 1, delay);
                                }, delay);
                            }
                        },
                        error: function() {
                            addBulkStatus('  ✗ <?php esc_html_e('Save error', 'manhwa-scraper'); ?>', 'error');
                            // Continue with next chapter after delay
                            setTimeout(function() {
                                processBulkChapters(postId, indices, currentIndex + 1, delay);
                            }, delay);
                        }
                    });
                } else {
                    var errorUrl = response.data.url ? ' [' + response.data.url.substring(0, 50) + '...]' : '';
                    addBulkStatus('✗ <?php esc_html_e('Error scraping:', 'manhwa-scraper'); ?> ' + chapterTitle + ' - ' + (response.data.message || 'Unknown error') + errorUrl, 'error');
                    setTimeout(function() {
                        processBulkChapters(postId, indices, currentIndex + 1, delay);
                    }, delay);
                }
            },
            error: function(xhr, status, error) {
                var errorMsg = error;
                
                // Check if response is HTML (server returned error page/captcha instead of JSON)
                if (xhr.responseText && xhr.responseText.trim().charAt(0) === '<') {
                    if (xhr.responseText.indexOf('403') !== -1 || xhr.responseText.indexOf('Forbidden') !== -1) {
                        errorMsg = '<?php esc_html_e('Website blocked request (403). Try increasing delay.', 'manhwa-scraper'); ?>';
                    } else if (xhr.responseText.indexOf('captcha') !== -1 || xhr.responseText.indexOf('Cloudflare') !== -1) {
                        errorMsg = '<?php esc_html_e('Website showing captcha. Cannot scrape.', 'manhwa-scraper'); ?>';
                    } else if (xhr.responseText.indexOf('fatal error') !== -1 || xhr.responseText.indexOf('Fatal error') !== -1) {
                        errorMsg = '<?php esc_html_e('PHP Fatal Error. Check server logs.', 'manhwa-scraper'); ?>';
                    } else {
                        errorMsg = '<?php esc_html_e('Server returned HTML. Rate limited or blocked.', 'manhwa-scraper'); ?>';
                        // Log first 500 chars of response for debugging
                        console.log('Server Response (first 500 chars):', xhr.responseText.substring(0, 500));
                    }
                } else if (status === 'timeout') {
                    errorMsg = '<?php esc_html_e('Request timeout.', 'manhwa-scraper'); ?>';
                }
                
                addBulkStatus('✗ <?php esc_html_e('Error:', 'manhwa-scraper'); ?> ' + chapterTitle + ' - ' + errorMsg, 'error');
                console.log('Full error details:', {status: status, error: error, responseText: xhr.responseText ? xhr.responseText.substring(0, 1000) : 'empty'});
                setTimeout(function() {
                    processBulkChapters(postId, indices, currentIndex + 1, delay);
                }, delay);
            }
        });
    }
    
    // Helper function to save chapter data
    function saveChapterData(chapterData, postId, chapterIndex, chapterTitle, callback) {
        $.ajax({
            url: mwsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mws_save_chapter_to_post',
                nonce: mwsData.nonce,
                post_id: postId,
                chapter_data: JSON.stringify(chapterData)
            },
            success: function(saveResponse) {
                if (saveResponse.success) {
                    bulkChaptersData[chapterIndex].images = chapterData.images;
                }
                callback();
            },
            error: function() {
                addBulkStatus('✗ <?php esc_html_e('Save error:', 'manhwa-scraper'); ?> ' + chapterTitle, 'error');
                callback();
            }
        });
    }
    
    // Helper function to save chapter (for Scrape All)
    function saveChapterToPost(chapterData, postId, callback) {
        $.ajax({
            url: mwsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mws_save_chapter_to_post',
                nonce: mwsData.nonce,
                post_id: postId,
                chapter_data: JSON.stringify(chapterData)
            },
            success: function() {
                callback();
            },
            error: function() {
                callback();
            }
        });
    }
    
    function addBulkStatus(message, type) {
        var color = type === 'success' ? '#46b450' : (type === 'error' ? '#dc3232' : (type === 'warning' ? '#ffb900' : '#0073aa'));
        $('#mws-bulk-status').append('<div style="color: ' + color + '; margin-bottom: 5px;">' + message + '</div>');
        $('#mws-bulk-status').scrollTop($('#mws-bulk-status')[0].scrollHeight);
    }
    
    // ========== SCRAPE ALL MANHWA FUNCTIONALITY ==========
    var allManhwaData = [];
    var allManhwaRunning = false;
    var allManhwaAborted = false;
    
    // Preview button
    $('#mws-all-preview-btn').on('click', function() {
        var filterType = $('#mws-all-manhwa-filter').val();
        var maxManhwa = parseInt($('#mws-all-max-manhwa').val()) || 10;
        
        allManhwaData = [];
        
        // Get manhwa from the existing dropdown
        $('#mws-bulk-manhwa option').each(function() {
            var $opt = $(this);
            if ($opt.val() === '') return;
            
            var status = $opt.data('status') || '';
            var postId = $opt.val();
            var title = $opt.text();
            
            // Filter by status
            if (filterType === 'all' || status === filterType) {
                allManhwaData.push({
                    id: postId,
                    title: title,
                    status: status
                });
            }
        });
        
        // Limit to max
        if (allManhwaData.length > maxManhwa) {
            allManhwaData = allManhwaData.slice(0, maxManhwa);
        }
        
        // Update preview
        var $preview = $('#mws-all-preview').empty();
        if (allManhwaData.length === 0) {
            $preview.html('<p style="color: #dc3232;"><?php esc_html_e('No manhwa found matching this filter.', 'manhwa-scraper'); ?></p>');
            $('#mws-all-start-btn').prop('disabled', true);
        } else {
            var html = '<p style="margin-bottom: 10px;"><strong>' + allManhwaData.length + ' <?php esc_html_e('manhwa will be processed:', 'manhwa-scraper'); ?></strong></p>';
            html += '<div style="max-height: 150px; overflow-y: auto; font-size: 12px;">';
            allManhwaData.forEach(function(m, i) {
                html += '<div>' + (i+1) + '. ' + m.title + '</div>';
            });
            html += '</div>';
            $preview.html(html);
            $('#mws-all-start-btn').prop('disabled', false);
        }
    });
    
    // Start Scraping All
    $('#mws-all-start-btn').on('click', function() {
        if (allManhwaData.length === 0) {
            alert('<?php esc_html_e('No manhwa selected. Click Preview first.', 'manhwa-scraper'); ?>');
            return;
        }
        
        var filterType = $('#mws-all-manhwa-filter').val();
        var downloadLocal = $('#mws-all-download-local').is(':checked');
        
        var msg = '<?php esc_html_e('Start scraping', 'manhwa-scraper'); ?> ' + allManhwaData.length + ' <?php esc_html_e('manhwa?', 'manhwa-scraper'); ?>';
        msg += '\n\n<?php esc_html_e('Mode:', 'manhwa-scraper'); ?> ' + (filterType === 'has-external' ? '<?php esc_html_e('Download External to Local', 'manhwa-scraper'); ?>' : '<?php esc_html_e('Scrape New Chapters', 'manhwa-scraper'); ?>');
        msg += '\n<?php esc_html_e('Download Local:', 'manhwa-scraper'); ?> ' + (downloadLocal ? '<?php esc_html_e('Yes', 'manhwa-scraper'); ?>' : '<?php esc_html_e('No', 'manhwa-scraper'); ?>');
        msg += '\n\n<?php esc_html_e('This may take a long time!', 'manhwa-scraper'); ?>';
        
        if (!confirm(msg)) return;
        
        allManhwaRunning = true;
        allManhwaAborted = false;
        
        $('#mws-all-start-btn').hide();
        $('#mws-all-preview-btn').hide();
        $('#mws-all-stop-btn').show();
        $('#mws-all-progress').show();
        $('#mws-all-status').empty();
        
        addAllStatus('🚀 <?php esc_html_e('Starting scrape for', 'manhwa-scraper'); ?> ' + allManhwaData.length + ' <?php esc_html_e('manhwa...', 'manhwa-scraper'); ?>', 'info');
        
        var delay = parseInt($('#mws-all-delay').val()) * 1000 || 3000;
        
        processAllManhwa(0, delay, downloadLocal, filterType);
    });
    
    // Stop button
    $('#mws-all-stop-btn').on('click', function() {
        allManhwaAborted = true;
        addAllStatus('⏹ <?php esc_html_e('Stopping...', 'manhwa-scraper'); ?>', 'warning');
    });
    
    // Process all manhwa one by one
    function processAllManhwa(index, delay, downloadLocal, filterType) {
        if (allManhwaAborted || index >= allManhwaData.length) {
            allManhwaRunning = false;
            $('#mws-all-stop-btn').hide();
            $('#mws-all-start-btn').show();
            $('#mws-all-preview-btn').show();
            
            if (allManhwaAborted) {
                addAllStatus('⏹ <?php esc_html_e('Stopped by user', 'manhwa-scraper'); ?>', 'warning');
            } else {
                addAllStatus('✅ <?php esc_html_e('All manhwa processed successfully!', 'manhwa-scraper'); ?>', 'success');
            }
            return;
        }
        
        var manhwa = allManhwaData[index];
        var progress = Math.round(((index + 1) / allManhwaData.length) * 100);
        
        $('#mws-all-current-manhwa').text(manhwa.title);
        $('#mws-all-manhwa-progress').text((index + 1) + '/' + allManhwaData.length);
        $('#mws-all-progress-bar').css('width', progress + '%').text(progress + '%');
        
        addAllStatus('📚 <?php esc_html_e('Processing:', 'manhwa-scraper'); ?> ' + manhwa.title, 'info');
        
        // First, get chapters for this manhwa
        $.ajax({
            url: mwsData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mws_get_manhwa_chapters',
                nonce: mwsData.nonce,
                post_id: manhwa.id
            },
            success: function(response) {
                if (response.success && response.data.chapters) {
                    var chapters = response.data.chapters;
                    
                    // Filter chapters based on mode
                    var chaptersToProcess = [];
                    
                    chapters.forEach(function(ch, i) {
                        var hasImages = ch.images && ch.images.length > 0;
                        var hasUrl = ch.url && ch.url.trim() !== '';
                        var isLocal = false;
                        
                        if (hasImages) {
                            var firstImg = ch.images[0];
                            var imgUrl = typeof firstImg === 'object' ? (firstImg.url || firstImg.src || '') : firstImg;
                            isLocal = imgUrl.indexOf('/wp-content/uploads/manhwa/') !== -1;
                        }
                        
                        // Determine which chapters to process based on filter
                        if (filterType === 'has-external' && hasImages && !isLocal) {
                            // Download external to local
                            chaptersToProcess.push({
                                index: i,
                                chapter: ch,
                                mode: 'download'
                            });
                        } else if (filterType === 'need-images' && !hasImages && hasUrl) {
                            // Scrape new
                            chaptersToProcess.push({
                                index: i,
                                chapter: ch,
                                mode: 'scrape'
                            });
                        } else if (filterType === 'partial-download' || filterType === 'all') {
                            if (!isLocal && hasUrl) {
                                chaptersToProcess.push({
                                    index: i,
                                    chapter: ch,
                                    mode: hasImages ? 'download' : 'scrape'
                                });
                            }
                        }
                    });
                    
                    if (chaptersToProcess.length === 0) {
                        addAllStatus('  ⏭ <?php esc_html_e('No chapters to process', 'manhwa-scraper'); ?>', 'info');
                        setTimeout(function() {
                            processAllManhwa(index + 1, delay, downloadLocal, filterType);
                        }, delay);
                    } else {
                        addAllStatus('  📑 ' + chaptersToProcess.length + ' <?php esc_html_e('chapters to process', 'manhwa-scraper'); ?>', 'info');
                        processAllChapters(manhwa.id, chaptersToProcess, 0, downloadLocal, function() {
                            setTimeout(function() {
                                processAllManhwa(index + 1, delay, downloadLocal, filterType);
                            }, delay);
                        });
                    }
                } else {
                    addAllStatus('  ✗ <?php esc_html_e('Failed to get chapters', 'manhwa-scraper'); ?>', 'error');
                    setTimeout(function() {
                        processAllManhwa(index + 1, delay, downloadLocal, filterType);
                    }, delay);
                }
            },
            error: function() {
                addAllStatus('  ✗ <?php esc_html_e('Error loading chapters', 'manhwa-scraper'); ?>', 'error');
                setTimeout(function() {
                    processAllManhwa(index + 1, delay, downloadLocal, filterType);
                }, delay);
            }
        });
    }
    
    // Process chapters for a single manhwa (PARALLEL version)
    function processAllChapters(postId, chapters, startIndex, downloadLocal, callback) {
        if (allManhwaAborted || startIndex >= chapters.length) {
            callback();
            return;
        }
        
        var parallelCount = parseInt($('#mws-all-parallel-chapters').val()) || 3;
        var endIndex = Math.min(startIndex + parallelCount, chapters.length);
        var batch = chapters.slice(startIndex, endIndex);
        var completed = 0;
        
        batch.forEach(function(item) {
            processSingleChapter(postId, item, downloadLocal, function() {
                completed++;
                if (completed >= batch.length) {
                    // All in batch completed, process next batch
                    if (!allManhwaAborted) {
                        setTimeout(function() {
                            processAllChapters(postId, chapters, endIndex, downloadLocal, callback);
                        }, 200);
                    } else {
                        callback();
                    }
                }
            });
        });
    }
    
    // Process a single chapter
    function processSingleChapter(postId, item, downloadLocal, callback) {
        var chapter = item.chapter;
        var chapterTitle = chapter.title || chapter.number || 'Chapter ' + (item.index + 1);
        
        if (item.mode === 'download') {
            // Download external to local
            var chapterData = {
                chapter_number: chapter.number || (item.index + 1),
                chapter_title: chapterTitle,
                title: chapter.title,
                url: chapter.url,
                images: chapter.images.map(function(img, idx) {
                    return {
                        index: idx,
                        url: typeof img === 'object' ? (img.url || img.src || img) : img,
                        alt: 'Page ' + (idx + 1)
                    };
                })
            };
            
            $.ajax({
                url: mwsData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'mws_download_chapter_images',
                    nonce: mwsData.nonce,
                    post_id: postId,
                    chapter_data: JSON.stringify(chapterData)
                },
                success: function(response) {
                    if (response.success) {
                        var r = response.data.result;
                        addAllStatus('    ⬇ ' + chapterTitle + ' - ' + r.success + ' <?php esc_html_e('downloaded', 'manhwa-scraper'); ?>', 'success');
                    } else {
                        addAllStatus('    ✗ ' + chapterTitle + ' - <?php esc_html_e('Download failed', 'manhwa-scraper'); ?>', 'error');
                    }
                    callback();
                },
                error: function() {
                    addAllStatus('    ✗ ' + chapterTitle + ' - <?php esc_html_e('Error', 'manhwa-scraper'); ?>', 'error');
                    callback();
                }
            });
        } else {
            // Scrape new chapter
            $.ajax({
                url: mwsData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'mws_scrape_chapter_images',
                    nonce: mwsData.nonce,
                    url: chapter.url
                },
                success: function(response) {
                    if (response.success && response.data.data) {
                        var imgCount = response.data.data.images ? response.data.data.images.length : 0;
                        addAllStatus('    ✓ ' + chapterTitle + ' - ' + imgCount + ' <?php esc_html_e('images scraped', 'manhwa-scraper'); ?>', 'success');
                        
                        // Download first if enabled
                        if (downloadLocal && imgCount > 0) {
                            $.ajax({
                                url: mwsData.ajaxUrl,
                                type: 'POST',
                                data: {
                                    action: 'mws_download_chapter_images',
                                    nonce: mwsData.nonce,
                                    post_id: postId,
                                    chapter_data: JSON.stringify(response.data.data)
                                },
                                success: function(dlResponse) {
                                    if (dlResponse.success) {
                                        addAllStatus('      ⬇ <?php esc_html_e('Downloaded to local', 'manhwa-scraper'); ?>', 'success');
                                        
                                        // Save with local URLs
                                        var chapterDataWithLocal = Object.assign({}, response.data.data);
                                        if (dlResponse.data.local_images && dlResponse.data.local_images.length > 0) {
                                            chapterDataWithLocal.images = dlResponse.data.local_images;
                                        }
                                        
                                        $.ajax({
                                            url: mwsData.ajaxUrl,
                                            type: 'POST',
                                            data: {
                                                action: 'mws_save_chapter_to_post',
                                                nonce: mwsData.nonce,
                                                post_id: postId,
                                                chapter_data: JSON.stringify(chapterDataWithLocal)
                                            },
                                            success: function() {
                                                callback();
                                            },
                                            error: function() {
                                                callback();
                                            }
                                        });
                                    } else {
                                        // Download failed, save with external URLs
                                        saveChapterToPost(response.data.data, postId, callback);
                                    }
                                },
                                error: function() {
                                    // Download failed, save with external URLs
                                    saveChapterToPost(response.data.data, postId, callback);
                                }
                            });
                        } else {
                            // No download, save with external URLs
                            saveChapterToPost(response.data.data, postId, callback);
                        }
                    } else {
                        addAllStatus('    ✗ ' + chapterTitle + ' - <?php esc_html_e('Scrape failed', 'manhwa-scraper'); ?>', 'error');
                        callback();
                    }
                },
                error: function() {
                    addAllStatus('    ✗ ' + chapterTitle + ' - <?php esc_html_e('Error', 'manhwa-scraper'); ?>', 'error');
                    callback();
                }
            });
        }
    }
    
    function addAllStatus(message, type) {
        var color = type === 'success' ? '#46b450' : (type === 'error' ? '#dc3232' : (type === 'warning' ? '#ffb900' : '#8B5CF6'));
        $('#mws-all-status').append('<div style="color: ' + color + '; margin-bottom: 3px;">' + message + '</div>');
        $('#mws-all-status').scrollTop($('#mws-all-status')[0].scrollHeight);
    }
});
</script>
