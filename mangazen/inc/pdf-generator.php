<?php
/**
 * AJAX Handler for External PDF/ZIP Download
 * Generates PDF/ZIP on server from external images
 */

// Check for WordPress
if (!defined('ABSPATH')) {
    // Direct access - try to load WordPress
    $wp_load_paths = [
        dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-load.php',
        dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php',
    ];
    
    $loaded = false;
    foreach ($wp_load_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $loaded = true;
            break;
        }
    }
    
    if (!$loaded) {
        die('WordPress not found');
    }
}

// Include TCPDF if not exists, use simple image concat
require_once ABSPATH . 'wp-admin/includes/file.php';

class KS_External_PDF_Generator {
    
    private $temp_dir;
    private $manhwa_slug;
    private $chapter_info;
    
    public function __construct() {
        $upload_dir = wp_upload_dir();
        $this->temp_dir = $upload_dir['basedir'] . '/pdf-temp';
        
        if (!file_exists($this->temp_dir)) {
            wp_mkdir_p($this->temp_dir);
        }
    }
    
    /**
     * Download image from URL
     */
    private function download_image($url, $filename) {
        $response = wp_remote_get($url, [
            'timeout' => 60,
            'sslverify' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Referer' => parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST) . '/',
            ],
        ]);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $code = wp_remote_retrieve_response_code($response);
        
        if ($code !== 200 || empty($body) || strlen($body) < 1000) {
            return false;
        }
        
        $filepath = $this->temp_dir . '/' . $filename;
        file_put_contents($filepath, $body);
        
        return file_exists($filepath) ? $filepath : false;
    }
    
    /**
     * Generate PDF from images using pure PHP (GD + basic PDF structure)
     */
    public function generate_pdf_from_images($images, $title) {
        // Download all images first
        $local_images = [];
        $batch_id = uniqid('pdf_');
        
        foreach ($images as $idx => $url) {
            $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = $batch_id . '_' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT) . '.' . $ext;
            
            $local_path = $this->download_image($url, $filename);
            if ($local_path) {
                $local_images[] = $local_path;
            }
        }
        
        if (empty($local_images)) {
            return false;
        }
        
        // Try to use TCPDF if available
        $tcpdf_path = ABSPATH . 'wp-content/plugins/tcpdf/tcpdf.php';
        $fpdf_path = ABSPATH . 'wp-content/themes/komik-starter/inc/fpdf/fpdf.php';
        
        // Use simple FPDF-like approach with inline class
        $pdf_content = $this->create_simple_pdf($local_images, $title);
        
        // Cleanup temp images
        foreach ($local_images as $img) {
            @unlink($img);
        }
        
        return $pdf_content;
    }
    
    /**
     * Create simple PDF with images
     */
    private function create_simple_pdf($images, $title) {
        $pdf_file = $this->temp_dir . '/' . sanitize_file_name($title) . '_' . uniqid() . '.pdf';
        
        // Method 1: Try using Imagick if available
        if (class_exists('Imagick')) {
            try {
                $pdf = new Imagick();
                $pdf->setResolution(150, 150);
                
                foreach ($images as $img_path) {
                    $img = new Imagick($img_path);
                    $img->setImageFormat('pdf');
                    $pdf->addImage($img);
                    $img->clear();
                }
                
                $pdf->writeImages($pdf_file, true);
                $pdf->clear();
                
                if (file_exists($pdf_file) && filesize($pdf_file) > 100) {
                    return $pdf_file;
                }
            } catch (Exception $e) {
                // Continue to next method
            }
        }
        
        // Method 2: Use our simple FPDF class
        require_once dirname(__FILE__) . '/simple-fpdf.php';
        
        try {
            $pdf = new FPDF('P', 'mm', 'A4');
            $pdfWidth = 210;
            $margin = 5;
            $contentWidth = $pdfWidth - ($margin * 2);
            $currentY = $margin;
            $maxPageHeight = 2000;
            
            $pdf->AddPage();
            
            foreach ($images as $img_path) {
                if (!file_exists($img_path)) continue;
                
                $size = @getimagesize($img_path);
                if (!$size) continue;
                
                $imgWidth = $size[0];
                $imgHeight = $size[1];
                $ratio = $imgHeight / $imgWidth;
                
                $displayWidth = $contentWidth;
                $displayHeight = $displayWidth * $ratio;
                
                // Check if need new page
                if ($currentY + $displayHeight > $maxPageHeight && $currentY > $margin) {
                    $pdf->SetPageHeight($currentY + $margin);
                    $pdf->AddPage();
                    $currentY = $margin;
                }
                
                $pdf->Image($img_path, $margin, $currentY, $displayWidth, $displayHeight);
                $currentY += $displayHeight;
            }
            
            // Set final page height
            $pdf->SetPageHeight($currentY + $margin);
            
            $pdf->Output('F', $pdf_file);
            
            if (file_exists($pdf_file) && filesize($pdf_file) > 100) {
                return $pdf_file;
            }
        } catch (Exception $e) {
            // Continue to fallback
        }
        
        // Method 3: If ZipArchive available, create ZIP as fallback
        if (class_exists('ZipArchive')) {
            return $this->create_zip_from_images($images, $title);
        }
        
        // Method 4: No methods available - return false
        return false;
    }
    
    /**
     * Create ZIP from images
     */
    public function create_zip_from_images($images, $title) {
        if (!class_exists('ZipArchive')) {
            return false;
        }
        
        $zip_file = $this->temp_dir . '/' . sanitize_file_name($title) . '_' . uniqid() . '.zip';
        
        $zip = new ZipArchive();
        if ($zip->open($zip_file, ZipArchive::CREATE) !== true) {
            return false;
        }
        
        foreach ($images as $idx => $img_path) {
            if (file_exists($img_path)) {
                $ext = pathinfo($img_path, PATHINFO_EXTENSION);
                $zip->addFile($img_path, 'page_' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT) . '.' . $ext);
            }
        }
        
        $zip->close();
        
        return file_exists($zip_file) ? $zip_file : false;
    }
    
    /**
     * Generate multi-chapter ZIP with PDFs or images
     */
    public function generate_batch_zip($chapters, $manhwa_title) {
        if (!class_exists('ZipArchive')) {
            return false;
        }
        
        $batch_id = uniqid('batch_');
        $zip_file = $this->temp_dir . '/' . sanitize_file_name($manhwa_title) . '_' . $batch_id . '.zip';
        
        $zip = new ZipArchive();
        if ($zip->open($zip_file, ZipArchive::CREATE) !== true) {
            return false;
        }
        
        foreach ($chapters as $chapter) {
            $chapter_title = $chapter['title'] ?? 'Chapter';
            $images = $chapter['images'] ?? [];
            
            if (empty($images)) continue;
            
            // Create folder for chapter
            $folder_name = sanitize_file_name($chapter_title);
            
            // Download images
            foreach ($images as $idx => $img) {
                $url = is_array($img) ? ($img['url'] ?? '') : $img;
                if (empty($url)) continue;
                
                $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $filename = $batch_id . '_' . $folder_name . '_' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT) . '.' . $ext;
                
                $local_path = $this->download_image($url, $filename);
                if ($local_path) {
                    $zip->addFile($local_path, $folder_name . '/page_' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT) . '.' . $ext);
                }
            }
        }
        
        $zip->close();
        
        // Cleanup temp files
        $files = glob($this->temp_dir . '/' . $batch_id . '_*');
        foreach ($files as $file) {
            @unlink($file);
        }
        
        return file_exists($zip_file) && filesize($zip_file) > 100 ? $zip_file : false;
    }
    
    /**
     * Clean old temp files (older than 1 hour)
     */
    public function cleanup_old_files() {
        $files = glob($this->temp_dir . '/*');
        $now = time();
        
        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file)) > 3600) {
                @unlink($file);
            }
        }
    }
}

// AJAX Handler
add_action('wp_ajax_ks_generate_external_download', 'ks_generate_external_download');
add_action('wp_ajax_nopriv_ks_generate_external_download', 'ks_generate_external_download');

function ks_generate_external_download() {
    // Suppress any PHP notices/warnings that might break JSON
    error_reporting(0);
    @ini_set('display_errors', 0);
    
    // Increase limits
    @set_time_limit(600);
    @ini_set('memory_limit', '512M');
    
    try {
        $manhwa_title = sanitize_text_field($_POST['manhwa_title'] ?? 'Download');
        $chapters_json = stripslashes($_POST['chapters'] ?? '[]');
        $chapters = json_decode($chapters_json, true);
        
        if (empty($chapters)) {
            wp_send_json_error(['message' => 'No chapters provided']);
            return;
        }
        
        // Count total images
        $total_images = 0;
        foreach ($chapters as $ch) {
            $total_images += count($ch['images'] ?? []);
        }
        
        // Limit to prevent timeout (max 500 images per request)
        $max_images = 500;
        if ($total_images > $max_images) {
            wp_send_json_error([
                'message' => "Terlalu banyak gambar ($total_images). Maksimal $max_images gambar. Gunakan 'Copy Semua Link' untuk download manual."
            ]);
            return;
        }

        
        $generator = new KS_External_PDF_Generator();
        $generator->cleanup_old_files();
    
    // Single chapter = single file, multiple = ZIP
    if (count($chapters) === 1) {
        $chapter = $chapters[0];
        $images = [];
        
        foreach ($chapter['images'] ?? [] as $img) {
            $url = is_array($img) ? ($img['url'] ?? '') : $img;
            if ($url) $images[] = $url;
        }
        
        if (empty($images)) {
            wp_send_json_error(['message' => 'No images found']);
        }
        
        $title = $manhwa_title . ' - ' . ($chapter['title'] ?? 'Chapter');
        
        // Try PDF first, fallback to ZIP
        $file_path = $generator->generate_pdf_from_images($images, $title);
        
        if (!$file_path) {
            wp_send_json_error(['message' => 'Failed to generate file']);
        }
        
        $upload_dir = wp_upload_dir();
        $file_url = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $file_path);
        $file_ext = pathinfo($file_path, PATHINFO_EXTENSION);
        
        wp_send_json_success([
            'url' => $file_url,
            'filename' => basename($file_path),
            'type' => $file_ext,
            'size' => filesize($file_path),
        ]);
    } else {
        // Multiple chapters - create ZIP
        if (!class_exists('ZipArchive')) {
            wp_send_json_error([
                'message' => 'PHP ZipArchive tidak tersedia. Pilih hanya 1 chapter untuk generate PDF, atau aktifkan extension zip di php.ini.'
            ]);
            return;
        }
        
        $file_path = $generator->generate_batch_zip($chapters, $manhwa_title);
        
        if (!$file_path) {
            wp_send_json_error(['message' => 'Failed to generate ZIP. Coba pilih 1 chapter saja.']);
            return;
        }
        
        $upload_dir = wp_upload_dir();
        $file_url = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $file_path);
        
        wp_send_json_success([
            'url' => $file_url,
            'filename' => basename($file_path),
            'type' => 'zip',
            'size' => filesize($file_path),
            'chapters' => count($chapters),
        ]);
    }
    
    } catch (Exception $e) {
        wp_send_json_error(['message' => 'Error: ' . $e->getMessage()]);
    } catch (Error $e) {
        wp_send_json_error(['message' => 'Fatal Error: ' . $e->getMessage()]);
    }
}
