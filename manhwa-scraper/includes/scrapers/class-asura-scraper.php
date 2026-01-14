<?php
/**
 * Asura Scans Scraper
 * Scrapes manhwa data from asuracomic.net
 */

if (!defined('ABSPATH')) {
    exit;
}

class MWS_Asura_Scraper extends MWS_Scraper_Base {
    
    const SOURCE_ID = 'asura';
    const SOURCE_NAME = 'Asura Scans';
    const SOURCE_URL = 'https://asuracomic.net';
    
    /**
     * Get source identifier
     */
    public function get_source_id() {
        return self::SOURCE_ID;
    }
    
    /**
     * Get source display name
     */
    public function get_source_name() {
        return self::SOURCE_NAME;
    }
    
    /**
     * Get source base URL
     */
    public function get_source_url() {
        return self::SOURCE_URL;
    }
    
    /**
     * Check if this scraper can handle the given URL
     */
    public function can_handle_url($url) {
        return strpos($url, 'asuracomic.net') !== false || 
               strpos($url, 'asurascans.com') !== false ||
               strpos($url, 'asura.gg') !== false;
    }
    
    /**
     * Scrape single manhwa from URL
     *
     * @param string $url
     * @return array|WP_Error
     */
    public function scrape_single($url) {
        $parser = $this->fetch_and_parse($url);
        
        if (is_wp_error($parser)) {
            $this->log($url, 'error', $parser->get_error_message());
            return $parser;
        }
        
        try {
            $data = $this->extract_manhwa_data($parser, $url);
            $this->log($url, 'success', 'Scraped successfully', ['title' => $data['title']]);
            return $data;
        } catch (Exception $e) {
            $this->log($url, 'error', $e->getMessage());
            return new WP_Error('scrape_error', $e->getMessage());
        }
    }
    
    /**
     * Extract manhwa data from parser
     *
     * @param MWS_Html_Parser $parser
     * @param string $url
     * @return array
     */
    private function extract_manhwa_data($parser, $url) {
        // Title - from og:title or h3
        $title = $parser->getMeta('og:title');
        if (!empty($title)) {
            // Remove site name suffix
            $title = preg_replace('/\s*[-–|]\s*Asura\s*Scans?\s*$/i', '', $title);
        }
        if (empty($title)) {
            $title = $parser->getText('h3');
        }
        $title = $this->clean_text($title);
        
        // Synopsis/Description from og:description
        $description = $parser->getMeta('og:description');
        $description = $this->clean_text($description);
        
        // Cover image from og:image
        $cover = $parser->getMeta('og:image');
        if (empty($cover)) {
            $cover = $parser->getAttribute('img[alt*="poster"]', 'src');
        }
        $cover = $this->normalize_url($cover);
        
        // Type detection from page content
        $type = 'Manhwa';
        $page_text = strtolower($parser->getText('body'));
        if (strpos($page_text, 'manga') !== false && strpos($page_text, 'manhwa') === false) {
            $type = 'Manga';
        } elseif (strpos($page_text, 'manhua') !== false) {
            $type = 'Manhua';
        } elseif (strpos($page_text, 'mangatoon') !== false) {
            $type = 'Mangatoon';
        }
        
        // Status - look for badges
        $status = 'ongoing';
        if (strpos($page_text, 'completed') !== false) {
            $status = 'completed';
        } elseif (strpos($page_text, 'hiatus') !== false) {
            $status = 'hiatus';
        }
        
        // Genres - Asura typically shows these in tags
        $genres = [];
        $genre_links = $parser->getAllText('a[href*="/genres/"]');
        foreach ($genre_links as $genre) {
            $genre = trim($genre);
            if (!empty($genre) && !in_array($genre, $genres)) {
                $genres[] = $genre;
            }
        }
        
        // Followers count
        $followers = 0;
        if (preg_match('/Followed by (\d+(?:,\d+)*)/i', $page_text, $matches)) {
            $followers = (int) str_replace(',', '', $matches[1]);
        }
        
        // Rating
        $rating = null;
        if (preg_match('/(\d+(?:\.\d+)?)\s*\/?\s*(?:10|5)/i', $page_text, $matches)) {
            $rating = (float) $matches[1];
            if ($rating > 5) {
                $rating = $rating / 2; // Convert 10-scale to 5-scale
            }
        }
        
        // Chapters
        $chapters = $this->extract_chapters($parser, $url);
        
        // Latest chapter
        $latest_chapter = null;
        if (!empty($chapters)) {
            $latest_chapter = $chapters[0]['title'] ?? null;
        }
        
        // Slug from URL
        $slug = $this->extract_slug($url);
        // Remove hash suffix if present
        $slug = preg_replace('/-[a-f0-9]{8}$/', '', $slug);
        
        return [
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'thumbnail_url' => $cover,
            'genres' => $genres,
            'status' => $status,
            'type' => $type,
            'rating' => $rating,
            'followers' => $followers,
            'chapters' => $chapters,
            'latest_chapter' => $latest_chapter,
            'total_chapters' => count($chapters),
            'source' => self::SOURCE_ID,
            'source_url' => $url,
            'scraped_at' => current_time('c'),
        ];
    }
    
    /**
     * Extract chapters list
     *
     * @param MWS_Html_Parser $parser
     * @param string $base_url
     * @return array
     */
    private function extract_chapters($parser, $base_url) {
        $chapters = [];
        
        // Get all links
        $links = $parser->getLinks('a');
        
        foreach ($links as $link) {
            $href = $link['href'];
            $text = $link['text'];
            
            // Filter chapter links
            if (preg_match('/\/chapter\/(\d+(?:\.\d+)?)/i', $href, $matches)) {
                $chapter_num = (float) $matches[1];
                
                // Try to extract date from text
                $date = null;
                if (preg_match('/([A-Z][a-z]+\s+\d+(?:st|nd|rd|th)?\s+\d{4})/i', $text, $date_matches)) {
                    $date = $this->parse_date($date_matches[1]);
                }
                
                // Clean chapter title
                $chapter_title = $this->clean_text($text);
                $chapter_title = preg_replace('/[A-Z][a-z]+\s+\d+(?:st|nd|rd|th)?\s+\d{4}/i', '', $chapter_title);
                $chapter_title = trim($chapter_title);
                
                if (empty($chapter_title)) {
                    $chapter_title = 'Chapter ' . $chapter_num;
                }
                
                $chapters[] = [
                    'number' => $chapter_num,
                    'title' => $chapter_title,
                    'url' => $this->normalize_url($href),
                    'date' => $date,
                ];
            }
        }
        
        // Remove duplicates and sort
        $unique = [];
        foreach ($chapters as $chapter) {
            $key = $chapter['number'];
            if (!isset($unique[$key])) {
                $unique[$key] = $chapter;
            }
        }
        
        $chapters = array_values($unique);
        usort($chapters, function($a, $b) {
            return $b['number'] <=> $a['number'];
        });
        
        return $chapters;
    }
    
    /**
     * Scrape manhwa list from page
     *
     * @param int $page
     * @return array|WP_Error
     */
    public function scrape_list($page = 1) {
        $url = self::SOURCE_URL . '/series';
        if ($page > 1) {
            $url .= '?page=' . $page;
        }
        
        $parser = $this->fetch_and_parse($url);
        
        if (is_wp_error($parser)) {
            return $parser;
        }
        
        $manhwa_list = [];
        
        // Find series links
        $links = $parser->getLinks('a[href*="/series/"]');
        $processed = [];
        
        foreach ($links as $link) {
            $href = $link['href'];
            $text = $link['text'];
            
            // Skip if already processed or if it's a chapter link
            if (in_array($href, $processed) || strpos($href, '/chapter/') !== false) {
                continue;
            }
            
            // Must be a series detail page
            if (preg_match('/\/series\/([^\/]+)$/', $href)) {
                $slug = $this->extract_slug($href);
                $title = $this->clean_text($text);
                
                // Skip short titles or status badges
                if (empty($title) || strlen($title) < 3 || in_array(strtolower($title), ['ongoing', 'completed', 'manhwa', 'manga'])) {
                    continue;
                }
                
                $manhwa_list[] = [
                    'slug' => $slug,
                    'title' => $title,
                    'url' => $this->normalize_url($href),
                    'source' => self::SOURCE_ID,
                ];
                
                $processed[] = $href;
            }
        }
        
        // Remove duplicates by slug
        $unique = [];
        foreach ($manhwa_list as $item) {
            $slug = preg_replace('/-[a-f0-9]{8}$/', '', $item['slug']);
            if (!isset($unique[$slug])) {
                $unique[$slug] = $item;
            }
        }
        
        return [
            'page' => $page,
            'items' => array_values($unique),
            'count' => count($unique),
        ];
    }
    
    /**
     * Get latest chapter info
     *
     * @param string $url
     * @return array|WP_Error
     */
    public function get_latest_chapter($url) {
        $data = $this->scrape_single($url);
        
        if (is_wp_error($data)) {
            return $data;
        }
        
        return [
            'title' => $data['title'],
            'latest_chapter' => $data['latest_chapter'],
            'total_chapters' => $data['total_chapters'],
            'chapters' => array_slice($data['chapters'], 0, 5),
        ];
    }
}
