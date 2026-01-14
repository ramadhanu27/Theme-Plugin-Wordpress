<?php
/**
 * Hero Slider Template Part
 * Displays featured manhwa in a slider format
 *
 * @package Komik_Starter
 */

defined("ABSPATH") || die("!");

// Get hero slider settings
$hero_enable = get_option('komik_hero_enable', '1');
$hero_count = get_option('komik_hero_count', 5);
$hero_mode = get_option('komik_hero_mode', 'latest');
$hero_autoplay = get_option('komik_hero_autoplay', '1');
$hero_interval = get_option('komik_hero_interval', 5000);
$hero_manhwa_ids = get_option('komik_hero_manhwa', array());

// Check if hero slider is disabled
if ($hero_enable !== '1') {
    return;
}

// Build query based on mode
if ($hero_mode === 'manual' && !empty($hero_manhwa_ids)) {
    // Manual mode - use selected IDs
    $featured_args = array(
        'post_type'      => 'manhwa',
        'posts_per_page' => count($hero_manhwa_ids),
        'post__in'       => $hero_manhwa_ids,
        'orderby'        => 'post__in',
    );
} elseif ($hero_mode === 'popular') {
    // Most Popular by views
    $featured_args = array(
        'post_type'      => 'manhwa',
        'posts_per_page' => $hero_count,
        'meta_key'       => '_manhwa_views',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    );
} elseif ($hero_mode === 'rating') {
    // Top Rated
    $featured_args = array(
        'post_type'      => 'manhwa',
        'posts_per_page' => $hero_count,
        'meta_key'       => '_manhwa_rating',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    );
} else {
    // Latest Update (default)
    $featured_args = array(
        'post_type'      => 'manhwa',
        'posts_per_page' => $hero_count,
        'orderby'        => 'modified',
        'order'          => 'DESC',
    );
}

$featured_query = new WP_Query($featured_args);

if ($featured_query->have_posts()) :
    $slide_count = 0;
?>

<div class="hero-slider-wrapper">
    <div class="hero-slider" id="heroSlider">
        <?php while ($featured_query->have_posts()) : $featured_query->the_post(); 
            $slide_count++;
            
            // Get manhwa data
            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'large');
            $synopsis = get_post_meta(get_the_ID(), '_manhwa_synopsis', true);
            $type = get_post_meta(get_the_ID(), '_manhwa_type', true);
            $status = get_post_meta(get_the_ID(), '_manhwa_status', true);
            $genres = get_the_terms(get_the_ID(), 'manhwa_genre');
            
            // Get latest chapter
            $chapters = get_post_meta(get_the_ID(), '_manhwa_chapters', true);
            $latest_chapter = '';
            $chapter_url = get_permalink();
            
            if (!empty($chapters) && is_array($chapters)) {
                $first_chapter = reset($chapters);
                if (isset($first_chapter['title'])) {
                    preg_match('/[\d.]+/', $first_chapter['title'], $matches);
                    $latest_chapter = isset($matches[0]) ? $matches[0] : '1';
                    // Use SEO-friendly URL format
                    $post = get_post();
                    $chapter_url = home_url('/' . $post->post_name . '-chapter-' . $latest_chapter . '/');
                }
            }
            
            // Truncate synopsis
            $synopsis_short = !empty($synopsis) ? wp_trim_words($synopsis, 30, '...') : get_the_excerpt();
        ?>
        
        <div class="hero-slide <?php echo $slide_count == 1 ? 'active' : ''; ?>" data-slide="<?php echo $slide_count; ?>">
            <!-- Background Image -->
            <div class="hero-bg" style="background-image: url('<?php echo esc_url($thumbnail); ?>');"></div>
            
            <!-- Content -->
            <div class="hero-content">
                <div class="hero-container">
                    <div class="hero-info">
                        <?php if ($latest_chapter) : ?>
                        <span class="hero-chapter">Chapter: <?php echo esc_html($latest_chapter); ?></span>
                        <?php endif; ?>
                        
                        <h2 class="hero-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        
                        <p class="hero-synopsis"><?php echo esc_html($synopsis_short); ?></p>
                        
                        <?php if (!empty($genres) && !is_wp_error($genres)) : ?>
                        <div class="hero-genres">
                            <?php 
                            $genre_count = 0;
                            foreach ($genres as $genre) : 
                                if ($genre_count >= 6) break;
                                $genre_count++;
                            ?>
                            <a href="<?php echo get_term_link($genre); ?>" class="hero-genre-tag"><?php echo esc_html($genre->name); ?></a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <a href="<?php echo esc_url($chapter_url); ?>" class="hero-btn">
                            <?php _e('Start Reading', 'komik-starter'); ?> <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Cover Image - Outside Container for Absolute Positioning -->
            <div class="hero-cover">
                <a href="<?php the_permalink(); ?>">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('large'); ?>
                    <?php endif; ?>
                </a>
            </div>
        </div>
        
        <?php endwhile; ?>
        
        <!-- Navigation Dots - Inside hero-slider for correct positioning -->
        <div class="hero-dots">
            <?php for ($i = 1; $i <= $slide_count; $i++) : ?>
            <span class="hero-dot <?php echo $i == 1 ? 'active' : ''; ?>" data-slide="<?php echo $i; ?>"></span>
            <?php endfor; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('heroSlider');
    if (!slider) return;
    
    const slides = slider.querySelectorAll('.hero-slide');
    const dots = slider.querySelectorAll('.hero-dot');
    let currentSlide = 1;
    let slideInterval;
    const totalSlides = slides.length;
    
    // Settings from PHP
    const autoplayEnabled = <?php echo $hero_autoplay === '1' ? 'true' : 'false'; ?>;
    const slideIntervalTime = <?php echo intval($hero_interval); ?>;
    
    function showSlide(n) {
        if (n > totalSlides) currentSlide = 1;
        if (n < 1) currentSlide = totalSlides;
        
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        const activeSlide = slider.querySelector('.hero-slide[data-slide="' + currentSlide + '"]');
        const activeDot = slider.querySelector('.hero-dot[data-slide="' + currentSlide + '"]');
        
        if (activeSlide) activeSlide.classList.add('active');
        if (activeDot) activeDot.classList.add('active');
    }
    
    function nextSlide() {
        currentSlide++;
        showSlide(currentSlide);
    }
    
    function startAutoPlay() {
        if (autoplayEnabled) {
            slideInterval = setInterval(nextSlide, slideIntervalTime);
        }
    }
    
    function stopAutoPlay() {
        clearInterval(slideInterval);
    }
    
    // Dot navigation
    dots.forEach(dot => {
        dot.addEventListener('click', function() {
            stopAutoPlay();
            currentSlide = parseInt(this.getAttribute('data-slide'));
            showSlide(currentSlide);
            startAutoPlay();
        });
    });
    
    // Start autoplay
    startAutoPlay();
    
    // Pause on hover
    slider.addEventListener('mouseenter', stopAutoPlay);
    slider.addEventListener('mouseleave', startAutoPlay);
});
</script>

<?php 
    wp_reset_postdata();
endif; 
?>

