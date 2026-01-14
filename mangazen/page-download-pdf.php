<?php
/**
 * Template Name: PDF Downloader
 * Template untuk halaman download chapter sebagai PDF
 *
 * @package Komik_Starter
 */

defined("ABSPATH") || die("!");

get_header();

// Get all manhwa for search
$manhwa_list = get_posts(array(
    'post_type' => 'manhwa',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
    'post_status' => 'publish',
));
?>

<style>
/* Reset theme styles for this page */
.pdf-downloader-page,
.pdf-downloader-page * {
    box-sizing: border-box;
}

/* Override WordPress/Theme content wrappers */
#content,
.wrapper,
.postbody,
.bixbox,
.entry-content,
.page-content,
article,
.site-content {
    background: transparent !important;
    padding: 0 !important;
    margin: 0 !important;
    max-width: none !important;
    width: 100% !important;
}

#content {
    background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%) !important;
}

/* PDF Downloader Page Styles */
.pdf-downloader-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%);
    padding: 40px 20px;
}

.pdf-container {
    max-width: 800px;
    margin: 0 auto;
}

/* Header */
.pdf-header {
    text-align: center;
    margin-bottom: 40px;
}

.pdf-logo {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    font-size: 28px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 10px;
}

.pdf-logo i {
    color: #8b5cf6;
    font-size: 32px;
}

.pdf-logo span {
    background: linear-gradient(135deg, #8b5cf6, #a78bfa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.pdf-tagline {
    color: #888;
    font-size: 14px;
}

/* Main Card */
.pdf-main-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(139, 92, 246, 0.2);
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 30px;
}

.pdf-title {
    font-size: 28px;
    font-weight: 700;
    color: #fff;
    text-align: center;
    margin-bottom: 10px;
}

.pdf-subtitle {
    color: #888;
    text-align: center;
    margin-bottom: 30px;
    font-size: 15px;
}

/* Search Box */
.pdf-search-box {
    position: relative;
    margin-bottom: 30px;
}

.pdf-search-input {
    width: 100%;
    padding: 18px 20px 18px 50px;
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid rgba(139, 92, 246, 0.3);
    border-radius: 12px;
    color: #fff;
    font-size: 16px;
    transition: all 0.3s;
}

.pdf-search-input::placeholder {
    color: #666;
}

.pdf-search-input:focus {
    outline: none;
    border-color: #8b5cf6;
    background: rgba(139, 92, 246, 0.1);
}

.pdf-search-icon {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: #8b5cf6;
    font-size: 18px;
}

/* Search Results Dropdown */
.pdf-search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #1a1a2e;
    border: 1px solid rgba(139, 92, 246, 0.3);
    border-radius: 12px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 100;
    display: none;
    margin-top: 5px;
}

.pdf-search-results.active {
    display: block;
}

.pdf-search-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 15px;
    cursor: pointer;
    transition: background 0.2s;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.pdf-search-item:last-child {
    border-bottom: none;
}

.pdf-search-item:hover {
    background: rgba(139, 92, 246, 0.1);
}

.pdf-search-item img {
    width: 40px;
    height: 55px;
    object-fit: cover;
    border-radius: 6px;
}

.pdf-search-item-info {
    flex: 1;
}

.pdf-search-item-title {
    color: #fff;
    font-weight: 600;
    font-size: 14px;
}

.pdf-search-item-chapters {
    color: #888;
    font-size: 12px;
}

/* Selected Manhwa */
.pdf-selected-manhwa {
    background: rgba(139, 92, 246, 0.1);
    border: 1px solid rgba(139, 92, 246, 0.3);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    display: none;
}

.pdf-selected-manhwa.active {
    display: block;
}

.pdf-selected-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.pdf-selected-cover {
    width: 60px;
    height: 85px;
    object-fit: cover;
    border-radius: 8px;
}

.pdf-selected-info h3 {
    color: #fff;
    font-size: 18px;
    margin: 0 0 5px 0;
}

.pdf-selected-info p {
    color: #888;
    font-size: 13px;
    margin: 0;
}

.pdf-selected-close {
    margin-left: auto;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: #fff;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    transition: background 0.2s;
}

.pdf-selected-close:hover {
    background: rgba(239, 68, 68, 0.5);
}

/* Chapter Selection */
.pdf-chapter-section {
    margin-bottom: 25px;
}

.pdf-section-title {
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.pdf-section-title i {
    color: #8b5cf6;
}

.pdf-chapter-mode {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.pdf-mode-btn {
    flex: 1;
    padding: 12px;
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid rgba(139, 92, 246, 0.2);
    border-radius: 10px;
    color: #888;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
}

.pdf-mode-btn:hover {
    border-color: rgba(139, 92, 246, 0.5);
}

.pdf-mode-btn.active {
    background: rgba(139, 92, 246, 0.2);
    border-color: #8b5cf6;
    color: #fff;
}

.pdf-mode-btn i {
    display: block;
    font-size: 20px;
    margin-bottom: 5px;
}

/* Chapter Range */
.pdf-chapter-range {
    display: none;
}

.pdf-chapter-range.active {
    display: block;
}

.pdf-range-inputs {
    display: flex;
    gap: 15px;
    align-items: center;
}

.pdf-range-group {
    flex: 1;
}

.pdf-range-group label {
    display: block;
    color: #888;
    font-size: 13px;
    margin-bottom: 8px;
}

.pdf-range-group select,
.pdf-range-group input {
    width: 100%;
    padding: 12px;
    background: #1a1a2e;
    border: 1px solid rgba(139, 92, 246, 0.3);
    border-radius: 8px;
    color: #fff;
    font-size: 14px;
    cursor: pointer;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

.pdf-range-group select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23888' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 35px;
}

.pdf-range-group select:focus,
.pdf-range-group input:focus {
    outline: none;
    border-color: #8b5cf6;
}

.pdf-range-group select option {
    background: #1a1a2e;
    color: #fff;
    padding: 10px;
}

.pdf-range-separator {
    color: #666;
    padding-top: 25px;
}

/* Chapter Grid */
.pdf-chapter-grid {
    display: none;
    max-height: 300px;
    overflow-y: auto;
}

.pdf-chapter-grid.active {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 8px;
}

.pdf-chapter-item {
    padding: 10px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(139, 92, 246, 0.2);
    border-radius: 8px;
    text-align: center;
    color: #888;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.pdf-chapter-item:hover {
    border-color: rgba(139, 92, 246, 0.5);
}

.pdf-chapter-item.selected {
    background: rgba(139, 92, 246, 0.3);
    border-color: #8b5cf6;
    color: #fff;
}

/* Download Button */
.pdf-download-btn {
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    border: none;
    border-radius: 12px;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.pdf-download-btn:hover {
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3);
}

.pdf-download-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.pdf-download-btn i {
    font-size: 18px;
}

/* Info Card */
.pdf-info-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(139, 92, 246, 0.15);
    border-radius: 16px;
    padding: 25px;
}

.pdf-info-title {
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.pdf-info-title i {
    color: #8b5cf6;
}

.pdf-info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.pdf-info-list li {
    color: #888;
    font-size: 14px;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.pdf-info-list li:last-child {
    border-bottom: none;
}

.pdf-info-list li i {
    color: #10b981;
    margin-top: 3px;
}

/* Progress Modal */
.pdf-progress-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.pdf-progress-modal.active {
    display: flex;
}

.pdf-progress-box {
    background: #1a1a2e;
    border: 1px solid rgba(139, 92, 246, 0.3);
    border-radius: 20px;
    padding: 40px;
    max-width: 400px;
    width: 90%;
    text-align: center;
}

.pdf-progress-icon {
    font-size: 48px;
    color: #8b5cf6;
    margin-bottom: 20px;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.pdf-progress-title {
    color: #fff;
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 10px;
}

.pdf-progress-text {
    color: #888;
    margin-bottom: 25px;
}

.pdf-progress-bar {
    height: 8px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 15px;
}

.pdf-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #8b5cf6, #a78bfa);
    width: 0;
    transition: width 0.3s;
}

.pdf-progress-count {
    color: #888;
    font-size: 14px;
}

/* Responsive */
@media (max-width: 600px) {
    .pdf-main-card {
        padding: 25px 20px;
    }
    
    .pdf-title {
        font-size: 22px;
    }
    
    .pdf-chapter-mode {
        flex-direction: column;
    }
    
    .pdf-range-inputs {
        flex-direction: column;
    }
    
    .pdf-range-separator {
        padding: 0;
        text-align: center;
    }
}
</style>

<div class="pdf-downloader-page">
    <div class="pdf-container">
        <!-- Header -->
        <div class="pdf-header">
            <div class="pdf-logo">
                <i class="fas fa-file-pdf"></i>
                <span>Manga → PDF</span>
            </div>
            <p class="pdf-tagline"><?php echo get_bloginfo('name'); ?></p>
        </div>
        
        <!-- Main Card -->
        <div class="pdf-main-card">
            <h1 class="pdf-title">Cari Manga/Manhwa Kamu</h1>
            <p class="pdf-subtitle">Cari dan unduh chapter manga dengan konversi otomatis ke PDF</p>
            
            <!-- Search Box -->
            <div class="pdf-search-box">
                <i class="fas fa-search pdf-search-icon"></i>
                <input type="text" class="pdf-search-input" id="pdfSearchInput" placeholder="Ketik judul manga/manhwa..." autocomplete="off">
                
                <div class="pdf-search-results" id="pdfSearchResults">
                    <!-- Results will be inserted here -->
                </div>
            </div>
            
            <!-- Selected Manhwa -->
            <div class="pdf-selected-manhwa" id="pdfSelectedManhwa">
                <div class="pdf-selected-header">
                    <img src="" alt="" class="pdf-selected-cover" id="pdfSelectedCover">
                    <div class="pdf-selected-info">
                        <h3 id="pdfSelectedTitle">-</h3>
                        <p id="pdfSelectedChapters">- chapters available</p>
                    </div>
                    <button class="pdf-selected-close" id="pdfClearSelection">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Chapter Selection -->
                <div class="pdf-chapter-section">
                    <div class="pdf-section-title">
                        <i class="fas fa-list-ol"></i>
                        Pilih Chapter
                    </div>
                    
                    <div class="pdf-chapter-mode">
                        <button class="pdf-mode-btn active" data-mode="single">
                            <i class="fas fa-file"></i>
                            Single Chapter
                        </button>
                        <button class="pdf-mode-btn" data-mode="range">
                            <i class="fas fa-layer-group"></i>
                            Range/Batch
                        </button>
                    </div>
                    
                    <!-- Single Chapter Select -->
                    <div class="pdf-chapter-range active" id="pdfSingleMode">
                        <div class="pdf-range-group">
                            <label>Pilih Chapter</label>
                            <select id="pdfSingleChapter">
                                <option value="">-- Pilih Chapter --</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Range Mode -->
                    <div class="pdf-chapter-range" id="pdfRangeMode">
                        <div class="pdf-range-inputs">
                            <div class="pdf-range-group">
                                <label>Dari Chapter</label>
                                <select id="pdfFromChapter">
                                    <option value="">Awal</option>
                                </select>
                            </div>
                            <div class="pdf-range-separator">→</div>
                            <div class="pdf-range-group">
                                <label>Sampai Chapter</label>
                                <select id="pdfToChapter">
                                    <option value="">Akhir</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Generate Button -->
            <button class="pdf-download-btn" id="pdfGenerateBtn" disabled>
                <i class="fas fa-file-pdf"></i>
                Generate PDF
            </button>
            
            <!-- Generation Progress & Result (inline) -->
            <div id="pdfGenerateResult" style="display: none; margin-top: 20px; padding: 20px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px;">
                
                <!-- Loading State -->
                <div id="pdfGenLoading" style="text-align: center;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #10b981;"></i>
                    <p style="color: #fff; margin: 15px 0 5px; font-weight: 600;">Sedang Memproses...</p>
                    <p style="color: #888; margin: 0; font-size: 13px;" id="pdfGenStatus">Mengunduh gambar dari server...</p>
                    <div class="pdf-progress-bar" style="margin-top: 15px;">
                        <div class="pdf-progress-fill" id="pdfGenProgress" style="width: 0%;"></div>
                    </div>
                </div>
                
                <!-- Success State -->
                <div id="pdfGenSuccess" style="display: none; text-align: center;">
                    <i class="fas fa-check-circle" style="font-size: 48px; color: #10b981;"></i>
                    <p style="color: #fff; margin: 15px 0 5px; font-weight: 600; font-size: 18px;">File Siap Diunduh!</p>
                    <p style="color: #888; margin: 0 0 20px; font-size: 13px;" id="pdfGenFileInfo">-</p>
                    
                    <!-- Download Link - This is what IDM will capture -->
                    <a href="#" id="pdfGenDownloadLink" target="_blank" class="pdf-download-btn" style="display: inline-block; padding: 15px 40px; text-decoration: none; font-size: 16px; background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-download"></i>
                        Download File (Klik Kanan → Save As untuk IDM)
                    </a>
                    
                    <p style="color: #f59e0b; margin: 15px 0 0; font-size: 12px;">
                        💡 Tip: Klik kanan link di atas → "Download with IDM" atau "Save link as..."
                    </p>
                </div>
                
                <!-- Error State -->
                <div id="pdfGenError" style="display: none; text-align: center;">
                    <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #ef4444;"></i>
                    <p style="color: #fff; margin: 15px 0 5px; font-weight: 600;">Gagal Membuat File</p>
                    <p style="color: #ef4444; margin: 0; font-size: 13px;" id="pdfGenErrorMsg">-</p>
                    <button class="pdf-mode-btn" id="pdfGenRetry" style="margin-top: 15px; padding: 10px 20px;">
                        <i class="fas fa-redo"></i> Coba Lagi
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="pdf-info-card">
            <div class="pdf-info-title">
                <i class="fas fa-info-circle"></i>
                Cara Menggunakan
            </div>
            <ul class="pdf-info-list">
                <li><i class="fas fa-check"></i> Ketik judul manga/manhwa yang ingin diunduh</li>
                <li><i class="fas fa-check"></i> Pilih manga dari hasil pencarian</li>
                <li><i class="fas fa-check"></i> Pilih mode: Single Chapter atau Range/Batch</li>
                <li><i class="fas fa-check"></i> Tentukan chapter yang ingin diunduh</li>
                <li><i class="fas fa-check"></i> Klik "Generate PDF/ZIP" dan tunggu proses</li>
                <li><i class="fas fa-check"></i> Klik link download (IDM akan menangkap otomatis)</li>
            </ul>
        </div>
    </div>
</div>

<!-- Old modals removed - using inline result section now -->


<!-- jsPDF Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- JSZip Library for batch download -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
(function() {
    // Manhwa data from WordPress
    var manhwaData = <?php 
        $data = array();
        foreach ($manhwa_list as $manhwa) {
            $chapters = get_post_meta($manhwa->ID, '_manhwa_chapters', true);
            $thumbnail = get_the_post_thumbnail_url($manhwa->ID, 'medium');
            if (!$thumbnail) {
                $thumbnail = get_post_meta($manhwa->ID, '_thumbnail_url', true);
            }
            $data[] = array(
                'id' => $manhwa->ID,
                'title' => $manhwa->post_title,
                'slug' => $manhwa->post_name,
                'thumbnail' => $thumbnail ?: '',
                'chapters' => is_array($chapters) ? $chapters : array()
            );
        }
        echo json_encode($data);
    ?>;
    
    var selectedManhwa = null;
    var selectedChapters = [];
    var currentMode = 'single';
    
    // DOM Elements
    var searchInput = document.getElementById('pdfSearchInput');
    var searchResults = document.getElementById('pdfSearchResults');
    var selectedBox = document.getElementById('pdfSelectedManhwa');
    var generateBtn = document.getElementById('pdfGenerateBtn');
    var resultSection = document.getElementById('pdfGenerateResult');
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        var query = this.value.toLowerCase().trim();
        
        if (query.length < 2) {
            searchResults.classList.remove('active');
            return;
        }
        
        var results = manhwaData.filter(function(m) {
            return m.title.toLowerCase().includes(query);
        }).slice(0, 10);
        
        if (results.length === 0) {
            searchResults.innerHTML = '<div style="padding: 20px; text-align: center; color: #888;">Tidak ditemukan</div>';
        } else {
            searchResults.innerHTML = results.map(function(m) {
                return '<div class="pdf-search-item" data-id="' + m.id + '">' +
                    '<img src="' + (m.thumbnail || 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 140"><rect fill="%23333" width="100" height="140"/></svg>') + '" alt="">' +
                    '<div class="pdf-search-item-info">' +
                        '<div class="pdf-search-item-title">' + m.title + '</div>' +
                        '<div class="pdf-search-item-chapters">' + m.chapters.length + ' chapters</div>' +
                    '</div>' +
                '</div>';
            }).join('');
        }
        
        searchResults.classList.add('active');
    });
    
    // Click outside to close
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.pdf-search-box')) {
            searchResults.classList.remove('active');
        }
    });
    
    // Select manhwa
    searchResults.addEventListener('click', function(e) {
        var item = e.target.closest('.pdf-search-item');
        if (!item) return;
        
        var id = parseInt(item.dataset.id);
        selectedManhwa = manhwaData.find(function(m) { return m.id === id; });
        
        if (selectedManhwa) {
            document.getElementById('pdfSelectedCover').src = selectedManhwa.thumbnail || '';
            document.getElementById('pdfSelectedTitle').textContent = selectedManhwa.title;
            document.getElementById('pdfSelectedChapters').textContent = selectedManhwa.chapters.length + ' chapters available';
            
            // Populate chapter selects
            populateChapterSelects();
            
            selectedBox.classList.add('active');
            searchResults.classList.remove('active');
            searchInput.value = '';
            
            updateDownloadButton();
        }
    });
    
    // Clear selection
    document.getElementById('pdfClearSelection').addEventListener('click', function() {
        selectedManhwa = null;
        selectedBox.classList.remove('active');
        generateBtn.disabled = true;
        resultSection.style.display = 'none';
    });
    
    // Mode toggle
    document.querySelectorAll('.pdf-mode-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.pdf-mode-btn').forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            
            currentMode = this.dataset.mode;
            
            document.getElementById('pdfSingleMode').classList.toggle('active', currentMode === 'single');
            document.getElementById('pdfRangeMode').classList.toggle('active', currentMode === 'range');
            
            updateDownloadButton();
        });
    });
    
    // Populate chapter selects
    function populateChapterSelects() {
        var chapters = selectedManhwa.chapters || [];
        var options = '<option value="">-- Pilih --</option>';
        
        // Reverse chapters so Chapter 01 appears first
        var reversedChapters = chapters.slice().reverse();
        
        reversedChapters.forEach(function(ch, i) {
            var title = ch.title || ('Chapter ' + (i + 1));
            // Store original index (from end)
            var originalIndex = chapters.length - 1 - i;
            options += '<option value="' + originalIndex + '">' + title + '</option>';
        });
        
        document.getElementById('pdfSingleChapter').innerHTML = options;
        document.getElementById('pdfFromChapter').innerHTML = '<option value="">Awal</option>' + options.replace('-- Pilih --', 'Awal');
        document.getElementById('pdfToChapter').innerHTML = '<option value="">Akhir</option>' + options.replace('-- Pilih --', 'Akhir');
    }
    
    // Update download button
    function updateDownloadButton() {
        var enabled = false;
        
        if (selectedManhwa) {
            if (currentMode === 'single') {
                enabled = document.getElementById('pdfSingleChapter').value !== '';
            } else {
                enabled = true; // Range mode always enabled if manhwa selected
            }
        }
        
        generateBtn.disabled = !enabled;
    }
    
    // Chapter select change
    document.getElementById('pdfSingleChapter').addEventListener('change', updateDownloadButton);
    document.getElementById('pdfFromChapter').addEventListener('change', updateDownloadButton);
    document.getElementById('pdfToChapter').addEventListener('change', updateDownloadButton);
    
    // Helper function to get selected chapters
    function getSelectedChapters(chapters) {
        var chaptersToProcess = [];
        
        if (currentMode === 'single') {
            var idx = parseInt(document.getElementById('pdfSingleChapter').value);
            if (!isNaN(idx) && chapters[idx]) {
                chaptersToProcess = [chapters[idx]];
            }
        } else {
            var fromIdx = document.getElementById('pdfFromChapter').value;
            var toIdx = document.getElementById('pdfToChapter').value;
            
            fromIdx = fromIdx === '' ? 0 : parseInt(fromIdx);
            toIdx = toIdx === '' ? chapters.length - 1 : parseInt(toIdx);
            
            if (fromIdx > toIdx) {
                var temp = fromIdx;
                fromIdx = toIdx;
                toIdx = temp;
            }
            
            chaptersToProcess = chapters.slice(fromIdx, toIdx + 1);
        }
        
        return chaptersToProcess;
    }
    
    // ============================================
    // GENERATE PDF/ZIP BUTTON HANDLER
    // ============================================
    generateBtn.addEventListener('click', function() {
        if (!selectedManhwa) return;
        
        var chapters = selectedManhwa.chapters || [];
        var chaptersToProcess = getSelectedChapters(chapters);
        
        if (chaptersToProcess.length === 0) {
            alert('Pilih chapter terlebih dahulu!');
            return;
        }
        
        // Prepare chapters data
        var chaptersData = chaptersToProcess.map(function(ch) {
            console.log('Processing chapter:', ch.title);
            console.log('Chapter has images_local:', ch.images_local);
            
            var images = [];
            
            // Check if images are available
            if (ch.images && ch.images.length > 0) {
                ch.images.forEach(function(img, idx) {
                    var url = '';
                    
                    if (typeof img === 'string') {
                        url = img;
                    } else if (typeof img === 'object') {
                        // Prefer local URL if available
                        url = img.url || img.local_url || img.src || '';
                    }
                    
                    // Check for local_images array as fallback
                    if (ch.local_images && ch.local_images[idx]) {
                        url = ch.local_images[idx];
                    }
                    
                    console.log('Image ' + (idx + 1) + ' URL:', url);
                    if (url) images.push({ url: url });
                });
            }
            
            console.log('Extracted images count:', images.length);
            
            return {
                title: ch.title || 'Chapter',
                images: images
            };
        });
        
        // Count total images
        var totalImages = 0;
        chaptersData.forEach(function(ch) {
            totalImages += ch.images.length;
        });
        
        // DEBUG: Show what we got
        console.log('Total images found:', totalImages);
        console.log('Chapters data:', JSON.stringify(chaptersData, null, 2));
        
        if (totalImages === 0) {
            // Show more helpful message
            var chaptersList = chaptersToProcess.map(function(ch) {
                return ch.title + ' (has images key: ' + (ch.images ? 'yes, count: ' + (ch.images.length || 0) : 'no') + ')';
            }).join('\n');
            alert('Tidak ada gambar yang tersedia.\n\nChapter yang dipilih:\n' + chaptersList + '\n\nCoba pilih chapter lain yang sudah memiliki gambar.');
            return;
        }
        
        // Warn if too many images
        var hardMax = 200;
        if (totalImages > hardMax) {
            alert('❌ Terlalu banyak gambar (' + totalImages + ').\n\nMaksimal ' + hardMax + ' gambar untuk client-side generation.');
            return;
        }
        
        if (totalImages > 30) {
            if (!confirm('⚠️ ' + totalImages + ' gambar akan diproses di browser.\n\nProses mungkin memakan waktu beberapa menit.\n\nLanjutkan?')) {
                return;
            }
        }
        
        // Show result section with loading state
        resultSection.style.display = 'block';
        document.getElementById('pdfGenLoading').style.display = 'block';
        document.getElementById('pdfGenSuccess').style.display = 'none';
        document.getElementById('pdfGenError').style.display = 'none';
        document.getElementById('pdfGenStatus').textContent = 'Memuat gambar...';
        document.getElementById('pdfGenProgress').style.width = '5%';
        
        // Disable button
        generateBtn.disabled = true;
        generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        
        // Collect all image URLs
        var allImageUrls = [];
        chaptersData.forEach(function(ch) {
            ch.images.forEach(function(img) {
                allImageUrls.push(img.url);
            });
        });
        
        // Load images in browser
        var loadedImages = [];
        var loadedCount = 0;
        var failedCount = 0;
        
        function updateProgress() {
            var pct = Math.round((loadedCount + failedCount) / allImageUrls.length * 80);
            document.getElementById('pdfGenProgress').style.width = pct + '%';
            document.getElementById('pdfGenStatus').textContent = 'Memuat gambar ' + (loadedCount + failedCount) + '/' + allImageUrls.length + '...';
        }
        
        function loadImage(url, index) {
            return new Promise(function(resolve) {
                var img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = function() {
                    loadedCount++;
                    updateProgress();
                    resolve({ img: img, index: index });
                };
                img.onerror = function() {
                    failedCount++;
                    updateProgress();
                    console.warn('Failed to load:', url);
                    resolve({ img: null, index: index });
                };
                img.src = url;
            });
        }
        
        // Load all images
        Promise.all(allImageUrls.map(function(url, i) {
            return loadImage(url, i);
        })).then(function(results) {
            // Sort by index to maintain order
            results.sort(function(a, b) { return a.index - b.index; });
            
            // Filter valid images
            var validImages = results.filter(function(r) { return r.img !== null; }).map(function(r) { return r.img; });
            
            if (validImages.length === 0) {
                throw new Error('Tidak ada gambar yang berhasil dimuat');
            }
            
            document.getElementById('pdfGenStatus').textContent = 'Membuat PDF (' + validImages.length + ' gambar)...';
            document.getElementById('pdfGenProgress').style.width = '85%';
            
            // Create PDF using jsPDF
            var { jsPDF } = window.jspdf;
            var pdfWidth = 210; // A4 width in mm
            var margin = 5;
            var contentWidth = pdfWidth - (margin * 2);
            var maxPageHeight = 2000;
            
            var currentY = margin;
            var pageNum = 0;
            var pages = [[]];
            
            // Calculate layout
            validImages.forEach(function(img) {
                var ratio = img.height / img.width;
                var imgWidth = contentWidth;
                var imgHeight = imgWidth * ratio;
                
                if (currentY + imgHeight > maxPageHeight && pages[pageNum].length > 0) {
                    pageNum++;
                    pages[pageNum] = [];
                    currentY = margin;
                }
                
                pages[pageNum].push({
                    img: img,
                    y: currentY,
                    width: imgWidth,
                    height: imgHeight
                });
                currentY += imgHeight;
            });
            
            // Generate PDF
            var pdf = null;
            
            pages.forEach(function(pageItems, pIdx) {
                var pageHeight = margin;
                pageItems.forEach(function(item) {
                    pageHeight = Math.max(pageHeight, item.y + item.height + margin);
                });
                
                if (pIdx === 0) {
                    pdf = new jsPDF({
                        orientation: 'portrait',
                        unit: 'mm',
                        format: [pdfWidth, pageHeight]
                    });
                } else {
                    pdf.addPage([pdfWidth, pageHeight], 'portrait');
                }
                
                pageItems.forEach(function(item) {
                    try {
                        pdf.addImage(item.img, 'JPEG', margin, item.y, item.width, item.height);
                    } catch(e) {
                        console.warn('Failed to add image:', e);
                    }
                });
            });
            
            document.getElementById('pdfGenProgress').style.width = '95%';
            document.getElementById('pdfGenStatus').textContent = 'Finalizing...';
            
            // Generate filename
            var chapterTitle = chaptersData.length === 1 ? chaptersData[0].title : (chaptersData.length + ' Chapters');
            var filename = selectedManhwa.title + ' - ' + chapterTitle;
            filename = filename.replace(/[^a-z0-9\s-]/gi, '').substring(0, 80) + '.pdf';
            
            // Get PDF as blob URL (don't auto-download)
            var pdfBlob = pdf.output('blob');
            var pdfUrl = URL.createObjectURL(pdfBlob);
            var pdfSize = (pdfBlob.size / 1024 / 1024).toFixed(2);
            
            // Show success with download button
            document.getElementById('pdfGenProgress').style.width = '100%';
            document.getElementById('pdfGenLoading').style.display = 'none';
            document.getElementById('pdfGenSuccess').style.display = 'block';
            document.getElementById('pdfGenFileInfo').textContent = 'PDF • ' + pdfSize + ' MB • ' + validImages.length + ' halaman';
            
            // Setup download link
            var downloadLink = document.getElementById('pdfGenDownloadLink');
            downloadLink.style.display = 'inline-block';
            downloadLink.href = pdfUrl;
            downloadLink.download = filename;
            downloadLink.textContent = '📥 Download PDF: ' + filename;
            
            generateBtn.disabled = false;
            generateBtn.innerHTML = '<i class="fas fa-file-pdf"></i> Generate PDF';
            
        }).catch(function(error) {
            document.getElementById('pdfGenLoading').style.display = 'none';
            document.getElementById('pdfGenError').style.display = 'block';
            document.getElementById('pdfGenErrorMsg').textContent = error.message;
            
            generateBtn.disabled = false;
            generateBtn.innerHTML = '<i class="fas fa-cogs"></i> Generate PDF';
        });
    });
    
    // Retry button
    document.getElementById('pdfGenRetry').addEventListener('click', function() {
        resultSection.style.display = 'none';
        generateBtn.click();
    });
    
})();

</script>

<?php get_footer(); ?>

