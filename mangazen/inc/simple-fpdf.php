<?php
/**
 * Simple FPDF for creating PDF from images
 * Lightweight alternative when TCPDF is not available
 */

if (!class_exists('FPDF')) {
    // Minimal FPDF implementation for image-only PDFs
    class FPDF {
        protected $page;
        protected $buffer;
        protected $pages;
        protected $images;
        protected $pageWidth;
        protected $pageHeight;
        protected $currentPage;
        
        public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
            $this->pages = [];
            $this->images = [];
            $this->currentPage = 0;
            $this->buffer = '';
            $this->pageWidth = 210;
            $this->pageHeight = 297;
        }
        
        public function AddPage($orientation = '', $size = '', $rotation = 0) {
            $this->currentPage++;
            $this->pages[$this->currentPage] = [
                'content' => '',
                'images' => [],
                'height' => $this->pageHeight
            ];
        }
        
        public function SetPageHeight($height) {
            if (isset($this->pages[$this->currentPage])) {
                $this->pages[$this->currentPage]['height'] = $height;
            }
        }
        
        public function Image($file, $x = null, $y = null, $w = 0, $h = 0, $type = '', $link = '') {
            if (!file_exists($file)) return;
            
            $info = $this->_parseImage($file);
            if (!$info) return;
            
            $this->pages[$this->currentPage]['images'][] = [
                'file' => $file,
                'x' => $x,
                'y' => $y,
                'w' => $w,
                'h' => $h,
                'info' => $info
            ];
        }
        
        protected function _parseImage($file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            if ($ext == 'jpg' || $ext == 'jpeg') {
                return $this->_parseJpg($file);
            } elseif ($ext == 'png') {
                return $this->_parsePng($file);
            } elseif ($ext == 'gif') {
                return $this->_parseGif($file);
            } elseif ($ext == 'webp') {
                return $this->_parseWebp($file);
            }
            
            return null;
        }
        
        protected function _parseJpg($file) {
            $size = getimagesize($file);
            if (!$size) return null;
            
            return [
                'w' => $size[0],
                'h' => $size[1],
                'type' => 'jpg',
                'data' => file_get_contents($file)
            ];
        }
        
        protected function _parsePng($file) {
            $size = getimagesize($file);
            if (!$size) return null;
            
            // Convert PNG to JPG for simpler PDF embedding
            $img = @imagecreatefrompng($file);
            if (!$img) return null;
            
            ob_start();
            imagejpeg($img, null, 90);
            $data = ob_get_clean();
            imagedestroy($img);
            
            return [
                'w' => $size[0],
                'h' => $size[1],
                'type' => 'jpg',
                'data' => $data
            ];
        }
        
        protected function _parseGif($file) {
            $size = getimagesize($file);
            if (!$size) return null;
            
            $img = @imagecreatefromgif($file);
            if (!$img) return null;
            
            ob_start();
            imagejpeg($img, null, 90);
            $data = ob_get_clean();
            imagedestroy($img);
            
            return [
                'w' => $size[0],
                'h' => $size[1],
                'type' => 'jpg',
                'data' => $data
            ];
        }
        
        protected function _parseWebp($file) {
            $size = getimagesize($file);
            if (!$size) return null;
            
            $img = @imagecreatefromwebp($file);
            if (!$img) return null;
            
            ob_start();
            imagejpeg($img, null, 90);
            $data = ob_get_clean();
            imagedestroy($img);
            
            return [
                'w' => $size[0],
                'h' => $size[1],
                'type' => 'jpg',
                'data' => $data
            ];
        }
        
        public function Output($dest = '', $name = '', $isUTF8 = false) {
            $this->_buildPdf();
            
            if ($dest == 'F') {
                file_put_contents($name, $this->buffer);
                return $name;
            }
            
            return $this->buffer;
        }
        
        protected function _buildPdf() {
            $this->buffer = '';
            $offsets = [];
            $n = 0;
            
            // Header
            $this->_out('%PDF-1.4');
            
            // Collect all unique images
            $imageObjects = [];
            $imgNum = 0;
            
            foreach ($this->pages as $pageNum => $page) {
                foreach ($page['images'] as $img) {
                    $imgNum++;
                    $imageObjects[$imgNum] = $img['info'];
                }
            }
            
            // Objects
            $imgObjStart = 3; // Start image objects after catalog and pages
            
            // Catalog
            $n++;
            $offsets[$n] = strlen($this->buffer);
            $this->_out($n . ' 0 obj');
            $this->_out('<< /Type /Catalog /Pages 2 0 R >>');
            $this->_out('endobj');
            
            // Pages
            $n++;
            $offsets[$n] = strlen($this->buffer);
            $this->_out($n . ' 0 obj');
            $kids = '';
            $pageObjStart = $imgObjStart + count($imageObjects);
            for ($i = 0; $i < count($this->pages); $i++) {
                $kids .= ($pageObjStart + $i) . ' 0 R ';
            }
            $this->_out('<< /Type /Pages /Kids [' . trim($kids) . '] /Count ' . count($this->pages) . ' >>');
            $this->_out('endobj');
            
            // Image objects
            $imgN = $imgObjStart;
            foreach ($imageObjects as $idx => $info) {
                $n = $imgN;
                $offsets[$n] = strlen($this->buffer);
                $this->_out($n . ' 0 obj');
                $this->_out('<< /Type /XObject /Subtype /Image');
                $this->_out('/Width ' . $info['w'] . ' /Height ' . $info['h']);
                $this->_out('/ColorSpace /DeviceRGB');
                $this->_out('/BitsPerComponent 8');
                $this->_out('/Filter /DCTDecode');
                $this->_out('/Length ' . strlen($info['data']) . ' >>');
                $this->_out('stream');
                $this->buffer .= $info['data'];
                $this->_out('');
                $this->_out('endstream');
                $this->_out('endobj');
                $imgN++;
            }
            
            // Page objects
            $imgIdx = 0;
            foreach ($this->pages as $pageNum => $page) {
                $n = $pageObjStart + $pageNum - 1;
                $offsets[$n] = strlen($this->buffer);
                
                $pageH = $page['height'];
                $pageW = $this->pageWidth;
                
                // Build content stream
                $content = 'q ';
                foreach ($page['images'] as $img) {
                    $imgIdx++;
                    $imgObjNum = $imgObjStart + $imgIdx - 1;
                    
                    // Convert mm to points (1mm = 2.83465 points)
                    $scale = 2.83465;
                    $x = $img['x'] * $scale;
                    $y = ($pageH - $img['y'] - $img['h']) * $scale;
                    $w = $img['w'] * $scale;
                    $h = $img['h'] * $scale;
                    
                    $content .= sprintf('%.2f 0 0 %.2f %.2f %.2f cm /I%d Do ', $w, $h, $x, $y, $imgIdx);
                }
                $content .= 'Q';
                
                // Resources
                $resources = '/XObject << ';
                $localImgIdx = 0;
                foreach ($page['images'] as $img) {
                    $localImgIdx++;
                    $resources .= '/I' . ($imgIdx - count($page['images']) + $localImgIdx) . ' ' . ($imgObjStart + $imgIdx - count($page['images']) + $localImgIdx - 1) . ' 0 R ';
                }
                $resources .= '>>';
                
                $this->_out($n . ' 0 obj');
                $this->_out('<< /Type /Page /Parent 2 0 R');
                $this->_out('/MediaBox [0 0 ' . ($pageW * 2.83465) . ' ' . ($pageH * 2.83465) . ']');
                $this->_out('/Resources << ' . $resources . ' >>');
                $this->_out('/Contents ' . ($n + count($this->pages)) . ' 0 R >>');
                $this->_out('endobj');
            }
            
            // Content streams
            $contentN = $pageObjStart + count($this->pages);
            $imgIdx = 0;
            foreach ($this->pages as $pageNum => $page) {
                $n = $contentN + $pageNum - 1;
                $offsets[$n] = strlen($this->buffer);
                
                $pageH = $page['height'];
                
                $content = 'q ';
                foreach ($page['images'] as $img) {
                    $imgIdx++;
                    
                    $scale = 2.83465;
                    $x = $img['x'] * $scale;
                    $y = ($pageH - $img['y'] - $img['h']) * $scale;
                    $w = $img['w'] * $scale;
                    $h = $img['h'] * $scale;
                    
                    $content .= sprintf('%.2f 0 0 %.2f %.2f %.2f cm /I%d Do ', $w, $h, $x, $y, $imgIdx);
                }
                $content .= 'Q';
                
                $this->_out($n . ' 0 obj');
                $this->_out('<< /Length ' . strlen($content) . ' >>');
                $this->_out('stream');
                $this->buffer .= $content;
                $this->_out('');
                $this->_out('endstream');
                $this->_out('endobj');
            }
            
            // XRef
            $xrefPos = strlen($this->buffer);
            $this->_out('xref');
            $this->_out('0 ' . (count($offsets) + 1));
            $this->_out('0000000000 65535 f ');
            foreach ($offsets as $offset) {
                $this->_out(sprintf('%010d 00000 n ', $offset));
            }
            
            // Trailer
            $this->_out('trailer');
            $this->_out('<< /Size ' . (count($offsets) + 1) . ' /Root 1 0 R >>');
            $this->_out('startxref');
            $this->_out($xrefPos);
            $this->_out('%%EOF');
        }
        
        protected function _out($s) {
            $this->buffer .= $s . "\n";
        }
    }
}
