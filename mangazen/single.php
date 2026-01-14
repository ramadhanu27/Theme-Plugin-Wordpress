<?php
/**
 * Single Post Template - Chapter Reader
 * Halaman untuk membaca chapter dengan tampilan mirip MangaReader
 *
 * @package Komik_Starter
 */

defined("ABSPATH") || die("!");
get_header();

// Set post views
if (is_single()) {
    komik_starter_set_post_views(get_the_ID());
}

// Get prev/next posts globally
$prev_post = get_previous_post();
$next_post = get_next_post();

// Get series info (if using custom post type relationship)
$series_id = get_post_meta(get_the_ID(), 'manga_series', true);
$series_title = $series_id ? get_the_title($series_id) : '';
$series_link = $series_id ? get_permalink($series_id) : '';

// Get chapter number if stored
$chapter_number = get_post_meta(get_the_ID(), 'chapter_number', true);
?>

<div class="chapterbody">
    <div class="postarea">
        <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('hentry'); ?> itemscope="itemscope" itemtype="http://schema.org/CreativeWork">
            
            <!-- Chapter Header -->
            <div class="headpost">
                <h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1>
                <?php if ($series_link) : ?>
                    <div class="allc">
                        <a href="<?php echo esc_url($series_link); ?>">← <?php echo esc_html($series_title); ?></a>
                    </div>
                <?php else : ?>
                    <div class="allc">
                        <span class="author"><i class="fas fa-user"></i> <?php the_author(); ?></span>
                        <span class="date"><i class="far fa-calendar-alt"></i> <?php echo get_the_date(); ?></span>
                        <span class="views"><i class="fas fa-eye"></i> <?php echo komik_starter_get_post_views(get_the_ID()); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Breadcrumb -->
            <?php komik_starter_breadcrumb(); ?>

            <!-- Chapter Content -->
            <div class="entry-content entry-content-single maincontent" itemprop="description">
                
                <!-- Chapter Navigation Top -->
                <div class="chnav ctop">
                    <div class="nextprev">
                        <?php if ($prev_post) : ?>
                            <a href="<?php echo get_permalink($prev_post); ?>" class="ch-prev-btn" rel="prev">
                                <i class="fas fa-chevron-left"></i> <?php _e('Prev', 'komik-starter'); ?>
                            </a>
                        <?php else : ?>
                            <span class="ch-prev-btn disabled"><i class="fas fa-chevron-left"></i> <?php _e('Prev', 'komik-starter'); ?></span>
                        <?php endif; ?>
                        
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="ch-home-btn">
                            <i class="fas fa-home"></i>
                        </a>
                        
                        <button type="button" class="ch-download-btn" id="downloadPdfBtn" title="<?php _e('Download as PDF', 'komik-starter'); ?>">
                            <i class="fas fa-file-pdf"></i> <span class="dl-text"><?php _e('PDF', 'komik-starter'); ?></span>
                        </button>
                        
                        <?php if ($next_post) : ?>
                            <a href="<?php echo get_permalink($next_post); ?>" class="ch-next-btn" rel="next">
                                <?php _e('Next', 'komik-starter'); ?> <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php else : ?>
                            <span class="ch-next-btn disabled"><?php _e('Next', 'komik-starter'); ?> <i class="fas fa-chevron-right"></i></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Reader Area - Main Content -->
                
                <?php komik_starter_display_ad('reader_top'); ?>
                
                <div id="readerarea" class="rdminimal">
                    <?php the_content(); ?>
                </div>
                
                <?php komik_starter_display_ad('reader_bottom'); ?>

                <!-- Chapter Navigation Bottom -->
                <div class="chnav cbot">
                    <div class="nextprev">
                        <?php if ($prev_post) : ?>
                            <a href="<?php echo get_permalink($prev_post); ?>" class="ch-prev-btn" rel="prev">
                                <i class="fas fa-chevron-left"></i> <?php _e('Prev', 'komik-starter'); ?>
                            </a>
                        <?php else : ?>
                            <span class="ch-prev-btn disabled"><i class="fas fa-chevron-left"></i> <?php _e('Prev', 'komik-starter'); ?></span>
                        <?php endif; ?>
                        
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="ch-home-btn">
                            <i class="fas fa-home"></i>
                        </a>
                        
                        <button type="button" class="ch-download-btn" id="downloadPdfBtn2" title="<?php _e('Download as PDF', 'komik-starter'); ?>">
                            <i class="fas fa-file-pdf"></i> <span class="dl-text"><?php _e('PDF', 'komik-starter'); ?></span>
                        </button>
                        
                        <?php if ($next_post) : ?>
                            <a href="<?php echo get_permalink($next_post); ?>" class="ch-next-btn" rel="next">
                                <?php _e('Next', 'komik-starter'); ?> <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php else : ?>
                            <span class="ch-next-btn disabled"><?php _e('Next', 'komik-starter'); ?> <i class="fas fa-chevron-right"></i></span>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Chapter Tags -->
            <?php if (has_tag()) : ?>
            <div class="chaptertags">
                <p>
                    <?php _e('Tags:', 'komik-starter'); ?> <?php the_tags('', ', ', ''); ?>,
                    <time class="entry-date" datetime="<?php the_time('c'); ?>" itemprop="datePublished"><?php echo get_the_date(); ?></time>,
                    <span itemprop="author"><?php the_author(); ?></span>
                </p>
            </div>
            <?php endif; ?>

        </article>

        <!-- Related Posts -->
        <div class="bixbox">
            <div class="releases">
                <h2><span><?php _e('Related', 'komik-starter'); ?></span></h2>
            </div>
            <div class="listupd">
                <?php
                $categories = get_the_category();
                if (!empty($categories)) {
                    $cat_ids = array();
                    foreach ($categories as $cat) {
                        $cat_ids[] = $cat->term_id;
                    }

                    $related = new WP_Query(array(
                        'category__in'        => $cat_ids,
                        'post__not_in'        => array(get_the_ID()),
                        'posts_per_page'      => 7,
                        'orderby'             => 'rand',
                        'ignore_sticky_posts' => true,
                    ));

                    if ($related->have_posts()) :
                        while ($related->have_posts()) : $related->the_post();
                ?>
                    <div class="bs styletere">
                        <div class="bsx">
                            <a href="<?php the_permalink(); ?>">
                                <div class="limit">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php else : ?>
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholder.jpg" alt="<?php the_title_attribute(); ?>" />
                                    <?php endif; ?>
                                </div>
                                <div class="tt"><?php the_title(); ?></div>
                            </a>
                        </div>
                    </div>
                <?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                }
                ?>
            </div>
        </div>

        <!-- Comments Section -->
        <?php if (comments_open() || get_comments_number()) : ?>
        <div id="comments" class="bixbox comments-area">
            <div class="releases">
                <h2><span><?php _e('Comments', 'komik-starter'); ?></span></h2>
            </div>
            <div class="cmt commentx">
                <?php comments_template(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php endwhile; ?>
    </div>
</div>

<!-- Reading Progress Bar (Bottom Navigation) - Always visible -->
<div class="readingnav rnavbot">
    <div class="readingnavbot">
        <div class="readingbar">
            <div class="readingprogress" id="readingProgress"></div>
        </div>
        <div class="readingoption">
            <div class="btm-np nextprev">
                <?php if ($prev_post) : ?>
                    <a class="ch-prev-btn" href="<?php echo get_permalink($prev_post); ?>" rel="prev" title="<?php _e('Previous', 'komik-starter'); ?>">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                <?php else : ?>
                    <span class="ch-prev-btn disabled" title="<?php _e('No Previous', 'komik-starter'); ?>">
                        <i class="fas fa-arrow-left"></i>
                    </span>
                <?php endif; ?>
                
                <a class="ch-home-btn" href="<?php echo esc_url(home_url('/')); ?>" title="<?php _e('Home', 'komik-starter'); ?>">
                    <i class="fas fa-home"></i>
                </a>
                
                <?php if ($next_post) : ?>
                    <a class="ch-next-btn" href="<?php echo get_permalink($next_post); ?>" rel="next" title="<?php _e('Next', 'komik-starter'); ?>">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                <?php else : ?>
                    <span class="ch-next-btn disabled" title="<?php _e('No Next', 'komik-starter'); ?>">
                        <i class="fas fa-arrow-right"></i>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Reading progress bar
document.addEventListener('scroll', function() {
    var docElement = document.documentElement;
    var winScroll = docElement.scrollTop || document.body.scrollTop;
    var height = docElement.scrollHeight - docElement.clientHeight;
    var scrolled = (winScroll / height) * 100;
    var progressBar = document.getElementById('readingProgress');
    if (progressBar) {
        progressBar.style.width = scrolled + '%';
    }
});

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    <?php if ($prev_post) : ?>
    if (e.keyCode === 37 || e.keyCode === 65) { // Left arrow or A
        window.location.href = '<?php echo esc_url(get_permalink($prev_post)); ?>';
    }
    <?php endif; ?>
    <?php if ($next_post) : ?>
    if (e.keyCode === 39 || e.keyCode === 68) { // Right arrow or D
        window.location.href = '<?php echo esc_url(get_permalink($next_post)); ?>';
    }
    <?php endif; ?>
});
</script>

<!-- jsPDF Library for PDF Download -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
(function() {
    var chapterTitle = <?php echo json_encode(get_the_title()); ?>;
    var downloading = false;
    
    // PDF Download functionality
    function downloadAsPDF() {
        if (downloading) return;
        downloading = true;
        
        var readerArea = document.getElementById('readerarea');
        if (!readerArea) {
            alert('Reader area not found!');
            downloading = false;
            return;
        }
        
        var images = readerArea.querySelectorAll('img');
        if (images.length === 0) {
            alert('No images found in this chapter!');
            downloading = false;
            return;
        }
        
        // Create progress overlay
        var overlay = document.createElement('div');
        overlay.id = 'pdf-progress-overlay';
        overlay.innerHTML = `
            <div class="pdf-progress-box">
                <div class="pdf-progress-title"><i class="fas fa-file-pdf"></i> Creating PDF</div>
                <div class="pdf-progress-text">Preparing images...</div>
                <div class="pdf-progress-bar">
                    <div class="pdf-progress-fill" id="pdfProgressFill"></div>
                </div>
                <div class="pdf-progress-count" id="pdfProgressCount">0 / ${images.length}</div>
            </div>
        `;
        overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);display:flex;align-items:center;justify-content:center;z-index:999999;';
        document.body.appendChild(overlay);
        
        // Add progress styles
        var style = document.createElement('style');
        style.textContent = `
            .pdf-progress-box{background:#1a1a2e;padding:30px 40px;border-radius:16px;text-align:center;min-width:300px;box-shadow:0 20px 60px rgba(0,0,0,0.5);}
            .pdf-progress-title{font-size:24px;font-weight:700;color:#fff;margin-bottom:15px;}
            .pdf-progress-title i{color:#e74c3c;margin-right:10px;}
            .pdf-progress-text{color:#aaa;margin-bottom:20px;}
            .pdf-progress-bar{height:12px;background:#333;border-radius:6px;overflow:hidden;}
            .pdf-progress-fill{height:100%;background:linear-gradient(90deg,#3498db,#2ecc71);width:0;transition:width 0.3s;}
            .pdf-progress-count{margin-top:15px;color:#fff;font-size:14px;}
        `;
        document.head.appendChild(style);
        
        var { jsPDF } = window.jspdf;
        var pdf = new jsPDF('p', 'mm', 'a4');
        var pdfWidth = pdf.internal.pageSize.getWidth();
        var pdfHeight = pdf.internal.pageSize.getHeight();
        var margin = 5;
        var contentWidth = pdfWidth - (margin * 2);
        
        var imageArray = Array.from(images);
        var loadedImages = [];
        var loadedCount = 0;
        
        // Load all images first
        function loadImage(imgElement, index) {
            return new Promise(function(resolve) {
                var img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = function() {
                    loadedCount++;
                    document.getElementById('pdfProgressFill').style.width = (loadedCount / imageArray.length * 50) + '%';
                    document.getElementById('pdfProgressCount').textContent = 'Loading: ' + loadedCount + ' / ' + imageArray.length;
                    resolve({ img: img, index: index });
                };
                img.onerror = function() {
                    loadedCount++;
                    document.getElementById('pdfProgressFill').style.width = (loadedCount / imageArray.length * 50) + '%';
                    document.getElementById('pdfProgressCount').textContent = 'Loading: ' + loadedCount + ' / ' + imageArray.length;
                    resolve(null);
                };
                img.src = imgElement.src;
            });
        }
        
        // Load images in parallel
        Promise.all(imageArray.map(function(img, i) {
            return loadImage(img, i);
        })).then(function(results) {
            loadedImages = results.filter(function(r) { return r !== null; });
            loadedImages.sort(function(a, b) { return a.index - b.index; });
            
            if (loadedImages.length === 0) {
                alert('Failed to load images. They may be protected from downloading.');
                overlay.remove();
                downloading = false;
                return;
            }
            
            document.querySelector('.pdf-progress-text').textContent = 'Creating PDF...';
            
            // Add images to PDF
            var addedCount = 0;
            loadedImages.forEach(function(item, i) {
                var img = item.img;
                var imgWidth = img.width;
                var imgHeight = img.height;
                var ratio = imgHeight / imgWidth;
                var pageImgWidth = contentWidth;
                var pageImgHeight = pageImgWidth * ratio;
                
                // If image is taller than page, fit to page height
                if (pageImgHeight > pdfHeight - (margin * 2)) {
                    pageImgHeight = pdfHeight - (margin * 2);
                    pageImgWidth = pageImgHeight / ratio;
                }
                
                // Center image
                var xPos = (pdfWidth - pageImgWidth) / 2;
                var yPos = margin;
                
                if (i > 0) {
                    pdf.addPage();
                }
                
                try {
                    pdf.addImage(img, 'JPEG', xPos, yPos, pageImgWidth, pageImgHeight);
                    addedCount++;
                } catch(e) {
                    console.log('Failed to add image:', e);
                }
                
                document.getElementById('pdfProgressFill').style.width = (50 + (addedCount / loadedImages.length * 50)) + '%';
                document.getElementById('pdfProgressCount').textContent = 'Processing: ' + addedCount + ' / ' + loadedImages.length;
            });
            
            // Save PDF
            var filename = chapterTitle.replace(/[^a-z0-9]/gi, '_').substring(0, 50) + '.pdf';
            pdf.save(filename);
            
            // Success message
            document.querySelector('.pdf-progress-text').textContent = 'Download started!';
            document.getElementById('pdfProgressFill').style.width = '100%';
            document.getElementById('pdfProgressCount').textContent = 'Complete!';
            
            setTimeout(function() {
                overlay.remove();
                downloading = false;
            }, 1500);
            
        }).catch(function(err) {
            console.error('PDF generation error:', err);
            alert('Error generating PDF. Please try again.');
            overlay.remove();
            downloading = false;
        });
    }
    
    // Bind click events
    document.querySelectorAll('.ch-download-btn').forEach(function(btn) {
        btn.addEventListener('click', downloadAsPDF);
    });
})();
</script>

<?php get_footer(); ?>
