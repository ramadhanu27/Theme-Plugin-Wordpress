<?php
/**
 * Main template file
 *
 * @package Komik_Starter
 */

defined("ABSPATH") || die("!");
get_header();

// Check if Manhwa Manager plugin is active
$manhwa_active = komik_starter_is_manhwa_manager_active();
?>

<?php if ($manhwa_active) : ?>

<!-- Hero Slider -->
<?php get_template_part('template-parts/hero-slider'); ?>

<!-- Announcement Ticker Bar (Full Width - Right Below Hero) -->
<?php 
$ticker_enabled = get_option('komik_announcement_enable', '0');
$ticker_title = get_option('komik_announcement_title', '');
$ticker_type = get_option('komik_announcement_type', 'fire');

if ($ticker_enabled === '1' && !empty(trim($ticker_title))) :
?>
<div class="announcement-ticker announcement-ticker-<?php echo esc_attr($ticker_type); ?>">
    <span class="ticker-label">
        <i class="fas fa-bullhorn"></i> <?php _e('Pengumuman', 'komik-starter'); ?> |
    </span>
    <div class="ticker-marquee">
        <span class="ticker-text">
            <?php echo wp_kses_post(strip_tags($ticker_title)); ?>
        </span>
    </div>
</div>
<?php endif; ?>

<?php komik_starter_display_ad('homepage_top'); ?>

<!-- Popular Today Section - FULL WIDTH (Outside postbody) -->
<?php
// Get Popular Today settings
$today_mode = get_option('komik_popular_today_mode', 'views');
$today_limit = get_option('komik_popular_today_limit', 10);
$today_ids = get_option('komik_popular_today', array());

// Build query based on mode
if ($today_mode === 'manual' && !empty($today_ids)) {
    // Manual mode - use selected IDs
    $popular_manhwa = new WP_Query(array(
        'post_type'      => 'manhwa',
        'posts_per_page' => count($today_ids),
        'post__in'       => $today_ids,
        'orderby'        => 'post__in',
    ));
} elseif ($today_mode === 'rating') {
    // By Rating
    $popular_manhwa = new WP_Query(array(
        'post_type'      => 'manhwa',
        'posts_per_page' => $today_limit,
        'meta_key'       => '_manhwa_rating',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    ));
} else {
    // By Views (default)
    $popular_manhwa = new WP_Query(array(
        'post_type'      => 'manhwa',
        'posts_per_page' => $today_limit,
        'meta_key'       => '_manhwa_views',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    ));
}

if ($popular_manhwa->have_posts()) :
?>
<div class="popular-today-wrapper">
    <div class="popular-today-section">
        <div class="popular-header">
            <h2><?php _e('Popular Today', 'komik-starter'); ?></h2>
        </div>
        <div class="popular-slider" id="popularSlider">
            <?php while ($popular_manhwa->have_posts()) : $popular_manhwa->the_post(); 
                $type = get_post_meta(get_the_ID(), '_manhwa_type', true);
                $rating = get_post_meta(get_the_ID(), '_manhwa_rating', true);
                $chapters = get_post_meta(get_the_ID(), '_manhwa_chapters', true);
                $latest_ch = !empty($chapters) && is_array($chapters) ? $chapters[0] : null;
                
                // Get chapter number
                $ch_num = '';
                if ($latest_ch && isset($latest_ch['title'])) {
                    preg_match('/(\d+)/', $latest_ch['title'], $matches);
                    $ch_num = isset($matches[1]) ? $matches[1] : '';
                }
                
                // Rating calculation (0-10 to 0-5 stars)
                $rating_val = floatval($rating);
                $rating_display = $rating ? number_format($rating_val, 1) : '';
                $stars_filled = $rating ? floor($rating_val / 2) : 0;
                $stars_half = ($rating && (($rating_val / 2) - floor($rating_val / 2)) >= 0.5) ? 1 : 0;
            ?>
                <div class="popular-item">
                    <a href="<?php the_permalink(); ?>" class="popular-thumb">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium'); ?>
                        <?php else : ?>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholder.jpg" alt="<?php the_title_attribute(); ?>" />
                        <?php endif; ?>
                        
                        <?php if ($type) : 
                            $flag_image = '';
                            $type_lower = strtolower($type);
                            if ($type_lower === 'manhwa') {
                                $flag_image = get_template_directory_uri() . '/assets/images/manhwa.png';
                            } elseif ($type_lower === 'manga') {
                                $flag_image = get_template_directory_uri() . '/assets/images/manga.png';
                            } elseif ($type_lower === 'manhua') {
                                $flag_image = get_template_directory_uri() . '/assets/images/manhua.png';
                            }
                            
                            if ($flag_image) :
                        ?>
                            <span class="popular-flag">
                                <img src="<?php echo esc_url($flag_image); ?>" alt="<?php echo esc_attr($type); ?>">
                            </span>
                        <?php endif; endif; ?>
                    </a>
                    
                    <div class="popular-info">
                        <h3 class="popular-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <?php if ($ch_num) : ?>
                        <div class="popular-chapter">
                            Chapter <?php echo esc_html($ch_num); ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="popular-rating">
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <?php if ($i <= $stars_filled) : ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif ($i == $stars_filled + 1 && $stars_half) : ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else : ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <?php if ($rating_display) : ?>
                            <span class="rating-score"><?php echo $rating_display; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        
        <!-- Navigation Arrows -->
        <button class="popular-nav popular-prev" aria-label="Previous">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="popular-nav popular-next" aria-label="Next">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('popularSlider');
    const prevBtn = document.querySelector('.popular-prev');
    const nextBtn = document.querySelector('.popular-next');
    
    if (slider && prevBtn && nextBtn) {
        const scrollAmount = 300;
        
        prevBtn.addEventListener('click', function() {
            slider.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        });
        
        nextBtn.addEventListener('click', function() {
            slider.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });
        
        // Show/hide buttons based on scroll position
        function updateButtons() {
            const maxScroll = slider.scrollWidth - slider.clientWidth;
            prevBtn.style.opacity = slider.scrollLeft > 0 ? '1' : '0.3';
            nextBtn.style.opacity = slider.scrollLeft < maxScroll - 5 ? '1' : '0.3';
        }
        
        slider.addEventListener('scroll', updateButtons);
        updateButtons();
    }
});
</script>
<?php endif; ?>
<?php endif; ?>

<!-- Homepage Middle Ads Section (Full Width - 2 Rows x 2 Columns) -->
<?php 
// Get all 4 ad slots
$ad_1 = get_option('komik_ads_homepage_middle', '');
$ad_2 = get_option('komik_ads_homepage_middle_2', '');
$ad_3 = get_option('komik_ads_homepage_middle_3', '');
$ad_4 = get_option('komik_ads_homepage_middle_4', '');

// Check which ads have content
$has_1 = !empty(trim($ad_1));
$has_2 = !empty(trim($ad_2));
$has_3 = !empty(trim($ad_3));
$has_4 = !empty(trim($ad_4));

// Placeholder ad for rent
$placeholder_ad = '
<a href="' . esc_url(home_url('/contact')) . '" class="ad-placeholder" title="Pasang Iklan">
    <div class="ad-rent-banner">
        <div class="ad-rent-icon">📢</div>
        <div class="ad-rent-text">
            <span class="ad-rent-title">PASANG IKLAN DISINI</span>
            <span class="ad-rent-subtitle">Hubungi Kami untuk Beriklan</span>
        </div>
    </div>
</a>';

// Show actual ad or placeholder
$show_1 = $has_1 ? $ad_1 : $placeholder_ad;
$show_2 = $has_2 ? $ad_2 : $placeholder_ad;
$show_3 = $has_3 ? $ad_3 : $placeholder_ad;
$show_4 = $has_4 ? $ad_4 : $placeholder_ad;
?>
<div class="ad-section homepage-middle-ads full-width">
    <!-- Row 1 -->
    <div class="ad-grid two-column">
        <div class="ad-item"><?php echo $show_1; ?></div>
        <div class="ad-item"><?php echo $show_2; ?></div>
    </div>
    <!-- Row 2 -->
    <div class="ad-grid two-column" style="margin-top: 15px;">
        <div class="ad-item"><?php echo $show_3; ?></div>
        <div class="ad-item"><?php echo $show_4; ?></div>
    </div>
</div>

<!-- Announcement Box Section (Middle) -->
<?php 
$announce_enabled = get_option('komik_announcement_enable', '0');
$announce_title = get_option('komik_announcement_title', '');
$announce_content = get_option('komik_announcement_content', '');
$announce_type = get_option('komik_announcement_type', 'fire');
$announce_dismissable = get_option('komik_announcement_dismissable', '1');

if ($announce_enabled === '1' && (!empty(trim($announce_title)) || !empty(trim($announce_content)))) :
?>
<div class="announcement-section" id="announcement-section">
    <div class="announcement-box announcement-<?php echo esc_attr($announce_type); ?>">
        <?php if ($announce_dismissable === '1') : ?>
        <button type="button" class="announcement-close" id="announcement-close" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
        <?php endif; ?>
        
        <?php if (!empty($announce_title)) : ?>
        <div class="announcement-title">
            <?php echo wp_kses_post($announce_title); ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($announce_content)) : ?>
        <div class="announcement-content">
            <?php echo wp_kses_post(nl2br($announce_content)); ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<script>
(function() {
    var section = document.getElementById('announcement-section');
    var closeBtn = document.getElementById('announcement-close');
    var storageKey = 'announcement_dismissed_<?php echo md5($announce_title . $announce_content); ?>';
    
    var dismissed = localStorage.getItem(storageKey);
    if (dismissed && (Date.now() - parseInt(dismissed)) < 86400000) {
        if (section) section.style.display = 'none';
    }
    
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            if (section) section.style.display = 'none';
            localStorage.setItem(storageKey, Date.now().toString());
        });
    }
})();
</script>
<?php endif; ?>

<div class="postbody">
    <?php if ($manhwa_active) : ?>
    
    <!-- Latest Manhwa Updates Section -->
    <?php
    $latest_manhwa = new WP_Query(array(
        'post_type'      => 'manhwa',
        'posts_per_page' => 25,
        'orderby'        => 'modified',
        'order'          => 'DESC',
    ));
    
    if ($latest_manhwa->have_posts()) :
    ?>
    <div class="bixbox">
        <div class="releases">
            <h2><?php _e('Latest Update', 'komik-starter'); ?></h2>
            <a href="<?php echo get_post_type_archive_link('manhwa'); ?>" class="vl vl-btn"><?php _e('VIEW ALL', 'komik-starter'); ?></a>
        </div>
        <div class="listupd latest-updates-grid">
            <?php while ($latest_manhwa->have_posts()) : $latest_manhwa->the_post(); 
                $chapters = get_post_meta(get_the_ID(), '_manhwa_chapters', true);
                $type = get_post_meta(get_the_ID(), '_manhwa_type', true);
                
                // Get latest 3 chapters
                $recent_chapters = array();
                if (!empty($chapters) && is_array($chapters)) {
                    $recent_chapters = array_slice($chapters, 0, 3);
                }
                
                // Determine badge type
                // N = New manhwa (published within 7 days)
                // A = Auto-update tracked
                // H = Hot/Ongoing (default)
                $is_new = (time() - get_the_time('U')) < (7 * 24 * 60 * 60); // Published within 7 days
                
                // Check if manhwa is tracked for auto-update
                $tracked_manhwa = get_option('mws_tracked_manhwa', array());
                $current_id = get_the_ID();
                $is_tracked = false;
                if (!empty($tracked_manhwa) && is_array($tracked_manhwa)) {
                    foreach ($tracked_manhwa as $tracked) {
                        if (isset($tracked['post_id']) && $tracked['post_id'] == $current_id) {
                            $is_tracked = true;
                            break;
                        }
                    }
                }
                
                // Determine badge
                $badge_type = 'hot';
                $badge_text = 'H';
                if ($is_new) {
                    $badge_type = 'new';
                    $badge_text = 'N';
                } elseif ($is_tracked) {
                    $badge_type = 'auto';
                    $badge_text = 'A';
                }
            ?>
                <div class="utao stylesix">
                    <div class="uta">
                        <div class="imgu">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium'); ?>
                                <?php else : ?>
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholder.jpg" alt="<?php the_title_attribute(); ?>" />
                                <?php endif; ?>
                                
                                <?php if ($type) : ?>
                                    <span class="hot-badge <?php echo esc_attr($badge_type); ?>">
                                        <?php echo esc_html($badge_text); ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="luf">
                            <h4>
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h4>
                            <ul class="chapter-list">
                                <?php if (!empty($recent_chapters)) : ?>
                                    <?php foreach ($recent_chapters as $chapter) : 
                                        $ch_num = komik_starter_get_chapter_number($chapter['title']);
                                        $ch_url = komik_starter_get_chapter_url(get_the_ID(), $ch_num);
                                        $ch_date = !empty($chapter['date']) ? $chapter['date'] : '';
                                    ?>
                                    <li>
                                        <a href="<?php echo esc_url($ch_url); ?>">
                                            <span class="leftoff">
                                                <i class="fas fa-circle"></i> Ch. <?php echo esc_html($ch_num); ?>
                                            </span>
                                            <span class="rightoff">
                                                <?php 
                                                if ($ch_date) {
                                                    $timestamp = strtotime($ch_date);
                                                    if ($timestamp) {
                                                        echo human_time_diff($timestamp, current_time('timestamp')) . ' ago';
                                                    } else {
                                                        echo esc_html($ch_date);
                                                    }
                                                } else {
                                                    echo human_time_diff(get_the_modified_time('U'), current_time('timestamp')) . ' ago';
                                                }
                                                ?>
                                            </span>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <li class="no-chapter">
                                        <span class="rightoff"><?php echo human_time_diff(get_the_modified_time('U'), current_time('timestamp')) . ' ago'; ?></span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        
        <!-- Pagination Link -->
        <div class="hpage">
            <a href="<?php echo get_post_type_archive_link('manhwa'); ?>" class="r"><?php _e('View More', 'komik-starter'); ?> <i class="fa fa-chevron-right"></i></a>
        </div>
    </div>
    <?php endif; ?>


    <?php endif; // End manhwa_active check ?>
    
    <!-- Latest Blog Posts Section (Regular Posts) -->
    

</div><!-- .postbody -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
