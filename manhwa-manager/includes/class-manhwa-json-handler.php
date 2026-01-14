<?php
/**
 * JSON Handler for Export/Import
 */

if (!defined('ABSPATH')) {
    exit;
}

class Manhwa_JSON_Handler {
    
    /**
     * Export manhwa data to JSON
     */
    public function export_to_json($include_chapters = true, $include_meta = true) {
        $manhwa_posts = get_posts(array(
            'post_type' => 'manhwa',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        $export_data = array(
            'export_date' => current_time('mysql'),
            'version' => '1.0',
            'manhwa' => array()
        );
        
        foreach ($manhwa_posts as $post) {
            $manhwa_data = array(
                'title' => $post->post_title,
                'description' => $post->post_content,
                'excerpt' => $post->post_excerpt,
                'slug' => $post->post_name,
                'date' => $post->post_date,
            );
            
            // Add thumbnail
            if (has_post_thumbnail($post->ID)) {
                $manhwa_data['thumbnail_url'] = get_the_post_thumbnail_url($post->ID, 'full');
            }
            
            // Add genres
            $genres = wp_get_post_terms($post->ID, 'manhwa_genre', array('fields' => 'names'));
            if (!empty($genres)) {
                $manhwa_data['genres'] = $genres;
            }
            
            // Add metadata
            if ($include_meta) {
                $manhwa_data['author'] = get_post_meta($post->ID, '_manhwa_author', true);
                $manhwa_data['artist'] = get_post_meta($post->ID, '_manhwa_artist', true);
                $manhwa_data['rating'] = get_post_meta($post->ID, '_manhwa_rating', true);
                $manhwa_data['views'] = get_post_meta($post->ID, '_manhwa_views', true);
                $manhwa_data['status'] = get_post_meta($post->ID, '_manhwa_status', true);
                $manhwa_data['release_year'] = get_post_meta($post->ID, '_manhwa_release_year', true);
            }
            
            // Add chapters
            if ($include_chapters) {
                $chapters = get_post_meta($post->ID, '_manhwa_chapters', true);
                if (is_array($chapters) && !empty($chapters)) {
                    $manhwa_data['chapters'] = $chapters;
                }
            }
            
            $export_data['manhwa'][] = $manhwa_data;
        }
        
        // Generate filename
        $filename = 'manhwa-export-' . date('Y-m-d-His') . '.json';
        
        // Set headers for download
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Output JSON
        echo json_encode($export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Import manhwa data from JSON
     */
    public function import_from_json($data, $overwrite = false, $remove_komik = false) {
        // Increase limits for large imports
        @ini_set('max_execution_time', 300); // 5 minutes
        @ini_set('memory_limit', '512M');
        
        // Detect JSON format
        $manhwa_list = array();
        
        // Format 1: {"manhwa": [...]}
        if (isset($data['manhwa']) && is_array($data['manhwa'])) {
            $manhwa_list = $data['manhwa'];
        }
        // Format 2: Direct array [...]
        elseif (is_array($data) && isset($data[0])) {
            $manhwa_list = $data;
        }
        // Format 3: Single object {...}
        elseif (isset($data['title']) || isset($data['slug'])) {
            $manhwa_list = array($data);
        }
        else {
            return array(
                'success' => false,
                'message' => 'Invalid JSON format. Expected manhwa data.'
            );
        }
        
        $imported = 0;
        $skipped = 0;
        $errors = 0;
        $total = count($manhwa_list);
        
        // Process in batches to avoid timeout
        $batch_size = 50;
        $current_batch = 0;
        
        foreach ($manhwa_list as $index => $manhwa_data) {
            // Show progress every 10 items
            if ($index % 10 === 0) {
                $progress = round(($index / $total) * 100);
                error_log("Manhwa Import Progress: {$progress}% ({$index}/{$total})");
            }
            try {
                // Normalize data structure (support multiple formats)
                $manhwa_data = $this->normalize_manhwa_data($manhwa_data);
                
                // Remove "Komik" word from title if requested
                if ($remove_komik && isset($manhwa_data['title'])) {
                    $manhwa_data['title'] = $this->remove_komik_from_title($manhwa_data['title']);
                }
                
                // Check if manhwa already exists
                $existing_post = get_page_by_title($manhwa_data['title'], OBJECT, 'manhwa');
                
                if ($existing_post && !$overwrite) {
                    $skipped++;
                    continue;
                }
                
                // Prepare post data
                $post_data = array(
                    'post_title' => sanitize_text_field($manhwa_data['title']),
                    'post_content' => wp_kses_post($manhwa_data['description'] ?? $manhwa_data['synopsis'] ?? ''),
                    'post_excerpt' => sanitize_text_field($manhwa_data['excerpt'] ?? ''),
                    'post_type' => 'manhwa',
                    'post_status' => 'publish',
                );
                
                if (isset($manhwa_data['slug'])) {
                    $post_data['post_name'] = sanitize_title($manhwa_data['slug']);
                }
                
                if (isset($manhwa_data['date'])) {
                    $post_data['post_date'] = sanitize_text_field($manhwa_data['date']);
                }
                
                // Insert or update post
                if ($existing_post && $overwrite) {
                    $post_data['ID'] = $existing_post->ID;
                    $post_id = wp_update_post($post_data);
                } else {
                    $post_id = wp_insert_post($post_data);
                }
                
                if (is_wp_error($post_id)) {
                    $errors++;
                    continue;
                }
                
                // Handle thumbnail (support both 'thumbnail_url' and 'image')
                $thumbnail_url = $manhwa_data['thumbnail_url'] ?? $manhwa_data['image'] ?? null;
                if ($thumbnail_url && !empty($thumbnail_url)) {
                    $this->set_thumbnail_from_url($post_id, $thumbnail_url);
                }
                
                // Handle genres
                if (isset($manhwa_data['genres']) && is_array($manhwa_data['genres'])) {
                    wp_set_post_terms($post_id, $manhwa_data['genres'], 'manhwa_genre');
                }
                
                // Handle metadata
                if (isset($manhwa_data['author'])) {
                    update_post_meta($post_id, '_manhwa_author', sanitize_text_field($manhwa_data['author']));
                }
                
                if (isset($manhwa_data['artist'])) {
                    update_post_meta($post_id, '_manhwa_artist', sanitize_text_field($manhwa_data['artist']));
                }
                
                if (isset($manhwa_data['rating'])) {
                    update_post_meta($post_id, '_manhwa_rating', floatval($manhwa_data['rating']));
                }
                
                if (isset($manhwa_data['views'])) {
                    update_post_meta($post_id, '_manhwa_views', intval($manhwa_data['views']));
                }
                
                if (isset($manhwa_data['status'])) {
                    $status = strtolower(sanitize_text_field($manhwa_data['status']));
                    update_post_meta($post_id, '_manhwa_status', $status);
                }
                
                if (isset($manhwa_data['release_year'])) {
                    update_post_meta($post_id, '_manhwa_release_year', intval($manhwa_data['release_year']));
                }
                
                // Handle type (Manga/Manhwa/Manhua)
                if (isset($manhwa_data['type'])) {
                    update_post_meta($post_id, '_manhwa_type', sanitize_text_field($manhwa_data['type']));
                }
                
                // Handle totalChapters
                if (isset($manhwa_data['totalChapters'])) {
                    update_post_meta($post_id, '_manhwa_total_chapters', intval($manhwa_data['totalChapters']));
                }
                
                // Handle scrapedAt
                if (isset($manhwa_data['scrapedAt'])) {
                    update_post_meta($post_id, '_manhwa_scraped_at', sanitize_text_field($manhwa_data['scrapedAt']));
                }
                
                // Handle chapters
                if (isset($manhwa_data['chapters']) && is_array($manhwa_data['chapters'])) {
                    $chapters = array();
                    foreach ($manhwa_data['chapters'] as $chapter) {
                        $chapters[] = array(
                            'title' => sanitize_text_field($chapter['title'] ?? ''),
                            'url' => esc_url_raw($chapter['url'] ?? ''),
                            'date' => sanitize_text_field($chapter['date'] ?? ''),
                        );
                    }
                    update_post_meta($post_id, '_manhwa_chapters', $chapters);
                }
                
                $imported++;
                
            } catch (Exception $e) {
                $errors++;
                continue;
            }
        }
        
        $message = sprintf(
            'Import completed! Imported: %d, Skipped: %d, Errors: %d',
            $imported,
            $skipped,
            $errors
        );
        
        return array(
            'success' => true,
            'message' => $message,
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors
        );
    }
    
    /**
     * Set thumbnail from URL
     */
    private function set_thumbnail_from_url($post_id, $image_url) {
        // Check if URL is valid
        if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
            error_log("Invalid thumbnail URL: {$image_url}");
            return false;
        }
        
        // Use WordPress HTTP API for better handling
        $response = wp_remote_get($image_url, array(
            'timeout' => 30,
            'sslverify' => false
        ));
        
        if (is_wp_error($response)) {
            error_log("Failed to download thumbnail: " . $response->get_error_message());
            return false;
        }
        
        $image_data = wp_remote_retrieve_body($response);
        
        if (empty($image_data)) {
            error_log("Empty image data from URL: {$image_url}");
            return false;
        }
        
        // Get filename from URL
        $filename = basename(parse_url($image_url, PHP_URL_PATH));
        
        // Remove query parameters from filename
        $filename = preg_replace('/\?.*/', '', $filename);
        $filename = sanitize_file_name($filename);
        
        // If no extension, add .jpg
        if (!preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $filename)) {
            $filename .= '.jpg';
        }
        
        // Upload to WordPress
        $upload = wp_upload_bits($filename, null, $image_data);
        
        if ($upload['error']) {
            error_log("Upload error: " . $upload['error']);
            return false;
        }
        
        // Create attachment
        $attachment = array(
            'post_mime_type' => $upload['type'],
            'post_title' => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        $attachment_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
        
        if (is_wp_error($attachment_id)) {
            error_log("Attachment creation error: " . $attachment_id->get_error_message());
            return false;
        }
        
        // Generate metadata
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
        wp_update_attachment_metadata($attachment_id, $attachment_data);
        
        // Set as featured image
        set_post_thumbnail($post_id, $attachment_id);
        
        return true;
    }
    
    /**
     * Remove "Komik" word from title
     */
    private function remove_komik_from_title($title) {
        // Remove "Komik " at the beginning (case-insensitive)
        $title = preg_replace('/^Komik\s+/i', '', $title);
        
        // Remove " Komik" at the end (case-insensitive)
        $title = preg_replace('/\s+Komik$/i', '', $title);
        
        // Remove "Komik" in the middle if standalone (case-insensitive)
        $title = preg_replace('/\s+Komik\s+/i', ' ', $title);
        
        // Trim extra spaces
        $title = trim($title);
        
        return $title;
    }
    
    /**
     * Normalize manhwa data to standard format
     */
    private function normalize_manhwa_data($data) {
        $normalized = array();
        
        // Title
        $normalized['title'] = $data['title'] ?? '';
        
        // Description/Synopsis
        $normalized['description'] = $data['description'] ?? $data['synopsis'] ?? '';
        
        // Excerpt
        $normalized['excerpt'] = $data['excerpt'] ?? '';
        
        // Slug
        $normalized['slug'] = $data['slug'] ?? sanitize_title($normalized['title']);
        
        // Image/Thumbnail
        $normalized['thumbnail_url'] = $data['thumbnail_url'] ?? $data['image'] ?? '';
        
        // Genres
        $normalized['genres'] = $data['genres'] ?? array();
        
        // Status (normalize to lowercase)
        if (isset($data['status'])) {
            $normalized['status'] = strtolower($data['status']);
        }
        
        // Author
        $normalized['author'] = $data['author'] ?? '';
        
        // Artist
        $normalized['artist'] = $data['artist'] ?? '';
        
        // Rating
        $normalized['rating'] = $data['rating'] ?? null;
        
        // Views
        $normalized['views'] = $data['views'] ?? 0;
        
        // Release Year
        $normalized['release_year'] = $data['release_year'] ?? null;
        
        // Type (Manga/Manhwa/Manhua)
        $normalized['type'] = $data['type'] ?? 'Manhwa';
        
        // Total Chapters
        $normalized['totalChapters'] = $data['totalChapters'] ?? 0;
        
        // Scraped At
        $normalized['scrapedAt'] = $data['scrapedAt'] ?? '';
        
        // Chapters
        $normalized['chapters'] = $data['chapters'] ?? array();
        
        // Date
        $normalized['date'] = $data['date'] ?? '';
        
        return $normalized;
    }
    
    /**
     * Import detail manhwa (with chapters and images)
     */
    public function import_detail_manhwa($data, $overwrite = false, $remove_komik = false, $import_chapters = true) {
        // Increase limits
        @ini_set('max_execution_time', 600); // 10 minutes for detail import
        @ini_set('memory_limit', '512M');
        
        try {
            // Get title
            $title = $data['manhwaTitle'] ?? $data['title'] ?? '';
            
            if (empty($title)) {
                return array(
                    'success' => false,
                    'message' => 'Missing manhwa title'
                );
            }
            
            // Remove "Komik" if requested
            if ($remove_komik) {
                $title = $this->remove_komik_from_title($title);
            }
            
            // Check if exists
            $existing_post = get_page_by_title($title, OBJECT, 'manhwa');
            
            if ($existing_post && !$overwrite) {
                return array(
                    'success' => false,
                    'message' => 'Manhwa already exists: ' . $title . '<br><br>' .
                                '<strong>Solusi:</strong><br>' .
                                '1. Centang checkbox "Overwrite jika manhwa sudah ada"<br>' .
                                '2. Upload ulang untuk update data lengkap (image + chapters)'
                );
            }
            
            // Prepare post data
            $post_data = array(
                'post_title' => sanitize_text_field($title),
                'post_content' => wp_kses_post($data['synopsis'] ?? ''),
                'post_type' => 'manhwa',
                'post_status' => 'publish',
                'post_date' => current_time('mysql'),
                'post_modified' => current_time('mysql'),
                'post_modified_gmt' => current_time('mysql', 1),
            );
            
            if (isset($data['slug'])) {
                $post_data['post_name'] = sanitize_title($data['slug']);
            }
            
            // Insert or update post
            if ($existing_post && $overwrite) {
                $post_data['ID'] = $existing_post->ID;
                // Force update modified date
                $post_data['post_date'] = $existing_post->post_date; // Keep original date
                $post_id = wp_update_post($post_data);
            } else {
                $post_id = wp_insert_post($post_data);
            }
            
            if (is_wp_error($post_id)) {
                return array(
                    'success' => false,
                    'message' => 'Failed to create/update manhwa: ' . $post_id->get_error_message()
                );
            }
            
            // Set thumbnail
            if (isset($data['image']) && !empty($data['image'])) {
                $this->set_thumbnail_from_url($post_id, $data['image']);
            }
            
            // Set genres
            if (isset($data['genres']) && is_array($data['genres'])) {
                wp_set_post_terms($post_id, $data['genres'], 'manhwa_genre');
            }
            
            // Set metadata
            if (isset($data['author'])) {
                update_post_meta($post_id, '_manhwa_author', sanitize_text_field($data['author']));
            }
            
            if (isset($data['alternativeTitle'])) {
                update_post_meta($post_id, '_manhwa_alternative_title', sanitize_text_field($data['alternativeTitle']));
            }
            
            if (isset($data['type'])) {
                update_post_meta($post_id, '_manhwa_type', sanitize_text_field($data['type']));
            }
            
            if (isset($data['status'])) {
                $status = strtolower(sanitize_text_field($data['status']));
                update_post_meta($post_id, '_manhwa_status', $status);
            }
            
            if (isset($data['released'])) {
                update_post_meta($post_id, '_manhwa_released', sanitize_text_field($data['released']));
            }
            
            if (isset($data['manhwaUrl'])) {
                update_post_meta($post_id, '_manhwa_url', esc_url_raw($data['manhwaUrl']));
            }
            
            if (isset($data['totalChapters'])) {
                update_post_meta($post_id, '_manhwa_total_chapters', intval($data['totalChapters']));
            }
            
            if (isset($data['scrapedAt'])) {
                update_post_meta($post_id, '_manhwa_scraped_at', sanitize_text_field($data['scrapedAt']));
            }
            
            // Import chapters with images
            $chapters_imported = 0;
            if ($import_chapters && isset($data['chapters']) && is_array($data['chapters'])) {
                $chapters_data = array();
                
                foreach ($data['chapters'] as $chapter) {
                    $chapter_info = array(
                        'number' => sanitize_text_field($chapter['number'] ?? ''),
                        'title' => sanitize_text_field($chapter['title'] ?? ''),
                        'url' => esc_url_raw($chapter['url'] ?? ''),
                        'date' => sanitize_text_field($chapter['date'] ?? ''),
                        'images' => array()
                    );
                    
                    // Add images
                    if (isset($chapter['images']) && is_array($chapter['images'])) {
                        foreach ($chapter['images'] as $image) {
                            $chapter_info['images'][] = array(
                                'page' => intval($image['page'] ?? 0),
                                'url' => esc_url_raw($image['url'] ?? ''),
                                'filename' => sanitize_file_name($image['filename'] ?? '')
                            );
                        }
                    }
                    
                    $chapters_data[] = $chapter_info;
                    $chapters_imported++;
                }
                
                update_post_meta($post_id, '_manhwa_chapters', $chapters_data);
            }
            
            return array(
                'success' => true,
                'message' => sprintf(
                    'Detail manhwa imported successfully!<br>' .
                    'Title: %s<br>' .
                    'Chapters: %d<br>' .
                    'Post ID: %d',
                    $title,
                    $chapters_imported,
                    $post_id
                )
            );
            
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            );
        }
    }
    
    /**
     * Validate JSON structure
     */
    public function validate_json($data) {
        $errors = array();
        
        // Check if it's an array or has manhwa key
        if (!is_array($data) && !isset($data['manhwa'])) {
            $errors[] = 'Invalid JSON structure';
        }
        
        // Get manhwa list
        $manhwa_list = array();
        if (isset($data['manhwa'])) {
            $manhwa_list = $data['manhwa'];
        } elseif (is_array($data) && isset($data[0])) {
            $manhwa_list = $data;
        } elseif (isset($data['title'])) {
            $manhwa_list = array($data);
        }
        
        // Validate each manhwa
        foreach ($manhwa_list as $index => $manhwa) {
            if (!isset($manhwa['title']) && !isset($manhwa['slug'])) {
                $errors[] = "Manhwa at index {$index} is missing 'title' or 'slug'";
            }
        }
        
        return array(
            'valid' => empty($errors),
            'errors' => $errors
        );
    }
}
