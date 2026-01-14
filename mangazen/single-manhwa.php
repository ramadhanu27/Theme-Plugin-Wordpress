<?php
/**
 * Template for Single Manhwa - MangaReader Style
 * Displays the manhwa detail page with hero background and modern layout
 *
 * @package Komik_Starter
 */

defined("ABSPATH") || die("!");
get_header();

// Increment view count
$views = get_post_meta(get_the_ID(), '_manhwa_views', true);
update_post_meta(get_the_ID(), '_manhwa_views', intval($views) + 1);

// Get manhwa metadata
$author = get_post_meta(get_the_ID(), '_manhwa_author', true);
$artist = get_post_meta(get_the_ID(), '_manhwa_artist', true);
$rating = get_post_meta(get_the_ID(), '_manhwa_rating', true);
$status = get_post_meta(get_the_ID(), '_manhwa_status', true);
$type = get_post_meta(get_the_ID(), '_manhwa_type', true);
$release_year = get_post_meta(get_the_ID(), '_manhwa_release_year', true);
$alternative_title = get_post_meta(get_the_ID(), '_manhwa_alternative_title', true);
$synopsis = get_post_meta(get_the_ID(), '_manhwa_synopsis', true);
$chapters = get_post_meta(get_the_ID(), '_manhwa_chapters', true);
$thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'large');

// Get genres
$genres = get_the_terms(get_the_ID(), 'manhwa_genre');

// Ensure chapters is an array
if (!is_array($chapters)) {
    $chapters = array();
}

// Convert rating to star display (rating is 0-10, display as N.N)
$rating_value = $rating ? floatval($rating) : 0;
$rating_display = $rating ? number_format($rating_value, 1) : '';

// Sort chapters (newest first by default)
usort($chapters, function($a, $b) {
    $num_a = 0;
    $num_b = 0;
    if (preg_match('/(\d+)/', $a['title'], $m)) $num_a = floatval($m[1]);
    if (preg_match('/(\d+)/', $b['title'], $m)) $num_b = floatval($m[1]);
    return $num_b - $num_a;
});

// Get first and last chapter
$first_chapter = !empty($chapters) ? end($chapters) : null;
$latest_chapter = !empty($chapters) ? reset($chapters) : null;

// Function to get chapter number from title
function get_chapter_number($title) {
    if (preg_match('/([\d.]+)/', $title, $matches)) {
        return $matches[1];
    }
    return '1';
}

// Function to generate SEO-friendly chapter URL
function get_seo_chapter_url($post_id, $chapter_num) {
    $post = get_post($post_id);
    if (!$post) return '#';
    return home_url('/' . $post->post_name . '-chapter-' . $chapter_num . '/');
}
?>

<?php while (have_posts()) : the_post(); ?>

<!-- Big Cover Background -->
<div class="bigcover" style="background-image: url('<?php echo esc_url($thumbnail_url); ?>');"></div>

<div class="detail-wrapper">
    <div class="detail-container">
        <!-- Left Column - Cover -->
        <div class="detail-sidebar">
            <div class="detail-thumb">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('medium', array('title' => get_the_title(), 'alt' => get_the_title())); ?>
                <?php else : ?>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholder.jpg" alt="<?php the_title_attribute(); ?>" />
                <?php endif; ?>
            </div>
            
            <!-- Bookmark Button -->
            <button type="button" data-id="<?php echo get_the_ID(); ?>" class="detail-bookmark">
                <i class="far fa-bookmark"></i> <span><?php _e('Bookmark', 'komik-starter'); ?></span>
            </button>
            
            <!-- Rating Display - 5 Stars -->
            <?php if ($rating_display) : 
                // Convert 0-10 rating to 0-5 stars
                $stars = $rating_value / 2;
                $full_stars = floor($stars);
                $half_star = ($stars - $full_stars) >= 0.5 ? 1 : 0;
                $empty_stars = 5 - $full_stars - $half_star;
            ?>
            <div class="detail-rating">
                <div class="rating-stars">
                    <?php 
                    // Full stars
                    for ($i = 0; $i < $full_stars; $i++) : ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; 
                    
                    // Half star
                    if ($half_star) : ?>
                        <i class="fas fa-star-half-alt"></i>
                    <?php endif;
                    
                    // Empty stars
                    for ($i = 0; $i < $empty_stars; $i++) : ?>
                        <i class="far fa-star"></i>
                    <?php endfor; ?>
                </div>
                <span class="rating-value"><?php echo $rating_display; ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Right Column - Info -->
        <div class="detail-content">
            <!-- Title -->
            <h1 class="detail-title"><?php the_title(); ?></h1>
            
            <?php if ($alternative_title) : ?>
                <div class="detail-alt-title"><?php echo esc_html($alternative_title); ?></div>
            <?php endif; ?>
            
            <!-- Genres -->
            <?php if (!empty($genres) && !is_wp_error($genres)) : ?>
            <div class="detail-genres">
                <?php foreach ($genres as $genre) : ?>
                    <a href="<?php echo esc_url(get_term_link($genre)); ?>" class="detail-genre-tag"><?php echo esc_html($genre->name); ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Synopsis -->
            <div class="detail-synopsis">
                <?php if (!empty($synopsis)) : ?>
                    <?php echo wp_kses_post($synopsis); ?>
                <?php else : ?>
                    <?php the_content(); ?>
                <?php endif; ?>
            </div>
            
            <!-- Info Table -->
            <div class="detail-info-table">
                <?php if ($status) : ?>
                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value status-<?php echo esc_attr(strtolower($status)); ?>"><?php echo esc_html(ucfirst($status)); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($type) : ?>
                <div class="info-row">
                    <span class="info-label">Type</span>
                    <span class="info-value"><?php echo esc_html($type); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($release_year) : ?>
                <div class="info-row">
                    <span class="info-label">Released</span>
                    <span class="info-value"><?php echo esc_html($release_year); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($author) : ?>
                <div class="info-row">
                    <span class="info-label">Author</span>
                    <span class="info-value"><?php echo esc_html($author); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($artist && $artist !== $author) : ?>
                <div class="info-row">
                    <span class="info-label">Artist</span>
                    <span class="info-value"><?php echo esc_html($artist); ?></span>
                </div>
                <?php endif; ?>
                
                <div class="info-row">
                    <span class="info-label">Posted On</span>
                    <span class="info-value"><?php echo get_the_date('M j, Y'); ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Updated On</span>
                    <span class="info-value"><?php echo get_the_modified_date('M j, Y'); ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-eye" style="margin-right: 5px;"></i> Views</span>
                    <span class="info-value views-count"><?php echo number_format(intval($views) + 1); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <?php komik_starter_display_ad('series_before'); ?>
    
    <!-- Chapter Section -->
    <div class="detail-chapters">
        <div class="chapters-header">
            <h2><i class="fas fa-list"></i> <?php _e('Chapters', 'komik-starter'); ?></h2>
            <div class="chapters-search">
                <input type="text" id="chapter-search" placeholder="<?php _e('Search chapter...', 'komik-starter'); ?>" />
                <i class="fas fa-search"></i>
            </div>
        </div>
        
        <!-- First/Latest Chapter Buttons -->
        <?php if (!empty($chapters)) : ?>
        <div class="chapter-actions">
            <?php if ($first_chapter) : 
                $first_ch_num = get_chapter_number($first_chapter['title']);
                $first_url = get_seo_chapter_url(get_the_ID(), $first_ch_num);
            ?>
            <a href="<?php echo esc_url($first_url); ?>" class="chapter-btn first">
                <i class="fas fa-play"></i> <?php _e('First Chapter', 'komik-starter'); ?>
            </a>
            <?php endif; ?>
            
            <?php if ($latest_chapter) : 
                $latest_ch_num = get_chapter_number($latest_chapter['title']);
                $latest_url = get_seo_chapter_url(get_the_ID(), $latest_ch_num);
            ?>
            <a href="<?php echo esc_url($latest_url); ?>" class="chapter-btn latest">
                <i class="fas fa-bolt"></i> <?php _e('Latest Chapter', 'komik-starter'); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Chapter List -->
        <div class="chapter-list" id="chapter-list">
            <?php if (!empty($chapters)) : ?>
                <?php foreach ($chapters as $chapter) : 
                    $ch_num = get_chapter_number($chapter['title']);
                    $ch_url = get_seo_chapter_url(get_the_ID(), $ch_num);
                    $ch_date = !empty($chapter['date']) ? $chapter['date'] : get_the_modified_date('M j, Y');
                ?>
                <a href="<?php echo esc_url($ch_url); ?>" class="chapter-item" data-num="<?php echo esc_attr($ch_num); ?>">
                    <span class="chapter-name">Chapter <?php echo esc_html($ch_num); ?></span>
                    <span class="chapter-date"><?php echo esc_html($ch_date); ?></span>
                </a>
                <?php endforeach; ?>
            <?php else : ?>
            <div class="no-chapters">
                <i class="fas fa-book-open"></i>
                <p><?php _e('No chapters available yet.', 'komik-starter'); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php komik_starter_display_ad('series_after'); ?>
    
    <!-- Related Series -->
    <?php
    if (!empty($genres) && !is_wp_error($genres)) :
        $genre_ids = wp_list_pluck($genres, 'term_id');
        $genre_ids = array_slice($genre_ids, 0, 2);
        
        $related = new WP_Query(array(
            'post_type'           => 'manhwa',
            'tax_query'           => array(
                array(
                    'taxonomy' => 'manhwa_genre',
                    'field'    => 'id',
                    'terms'    => $genre_ids,
                    'operator' => 'IN',
                ),
            ),
            'post__not_in'        => array(get_the_ID()),
            'posts_per_page'      => 6,
            'orderby'             => 'rand',
            'ignore_sticky_posts' => true,
        ));
        
        if ($related->have_posts()) :
    ?>
    <div class="detail-related">
        <div class="related-header">
            <h2><i class="fas fa-th-large"></i> <?php _e('Recommended', 'komik-starter'); ?></h2>
        </div>
        <div class="related-grid">
            <?php while ($related->have_posts()) : $related->the_post(); 
                $rel_type = get_post_meta(get_the_ID(), '_manhwa_type', true);
                $rel_rating = get_post_meta(get_the_ID(), '_manhwa_rating', true);
                $rel_chapters = get_post_meta(get_the_ID(), '_manhwa_chapters', true);
                $rel_latest_ch = !empty($rel_chapters) && is_array($rel_chapters) ? $rel_chapters[0] : null;
            ?>
            <div class="related-item">
                <a href="<?php the_permalink(); ?>" class="related-thumb">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('medium'); ?>
                    <?php else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholder.jpg" alt="<?php the_title_attribute(); ?>" />
                    <?php endif; ?>
                    <!-- Country Flag Badge (Top Left) -->
                    <?php if ($rel_type) : 
                        // Get country flag image based on type
                        $flag_img = '';
                        $type_lower = strtolower($rel_type);
                        if ($type_lower === 'manhwa' || $type_lower === 'korea') {
                            $flag_img = 'manhwa.png';
                        } elseif ($type_lower === 'manhua' || $type_lower === 'china') {
                            $flag_img = 'manhua.png';
                        } elseif ($type_lower === 'manga' || $type_lower === 'japan') {
                            $flag_img = 'manga.png';
                        }
                        if ($flag_img) :
                    ?>
                        <span class="related-flag">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/<?php echo $flag_img; ?>" alt="<?php echo esc_attr($rel_type); ?>">
                        </span>
                    <?php endif; endif; ?>
                    <!-- Rating Badge (Bottom Left) -->
                    <?php if ($rel_rating) : ?>
                        <span class="related-rating"><i class="fas fa-star"></i> <?php echo number_format(floatval($rel_rating), 1); ?></span>
                    <?php endif; ?>
                </a>
                <div class="related-info">
                    <a href="<?php the_permalink(); ?>" class="related-title"><?php the_title(); ?></a>
                    <?php if ($rel_latest_ch) : ?>
                        <span class="related-chapter"><?php echo esc_html($rel_latest_ch['title']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
    <?php endif; endif; ?>

    <!-- Comments Section -->
    <?php if (comments_open() || get_comments_number()) : ?>
    <div class="detail-comments">
        <div class="comments-header">
            <h2><i class="fas fa-comments"></i> <?php _e('Comments', 'komik-starter'); ?></h2>
        </div>
        <div class="comments-content">
            <?php comments_template(); ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php endwhile; ?>

<script>
jQuery(document).ready(function($) {
    // Manhwa data for bookmark
    var manhwaId = <?php echo get_the_ID(); ?>;
    var isLoggedIn = typeof komikAccount !== 'undefined' && komikAccount.isLoggedIn;
    
    // ===== BOOKMARK FUNCTIONALITY =====
    
    // Get bookmarks from localStorage (for guests)
    function getLocalBookmarks() {
        var bookmarks = localStorage.getItem('manhwa_bookmarks');
        return bookmarks ? JSON.parse(bookmarks) : [];
    }
    
    // Save bookmarks to localStorage
    function saveLocalBookmarks(bookmarks) {
        localStorage.setItem('manhwa_bookmarks', JSON.stringify(bookmarks));
    }
    
    // Check if bookmarked locally
    function isLocalBookmarked(id) {
        var bookmarks = getLocalBookmarks();
        return bookmarks.some(function(b) { return b.id === id; });
    }
    
    // Update bookmark button UI
    function updateBookmarkUI(isMarked) {
        var $btn = $('.detail-bookmark');
        if (isMarked) {
            $btn.addClass('marked').html('<i class="fas fa-bookmark"></i> <span><?php _e("Bookmarked", "komik-starter"); ?></span>');
        } else {
            $btn.removeClass('marked').html('<i class="far fa-bookmark"></i> <span><?php _e("Bookmark", "komik-starter"); ?></span>');
        }
    }
    
    // Check bookmark status on page load
    if (isLoggedIn) {
        // Check from database
        $.ajax({
            url: komikAccount.ajaxUrl,
            type: 'POST',
            data: {
                action: 'komik_check_bookmark',
                nonce: komikAccount.nonce,
                manhwa_id: manhwaId
            },
            success: function(response) {
                if (response.success && response.data.bookmarked) {
                    updateBookmarkUI(true);
                }
            }
        });
    } else {
        // Check from localStorage
        if (isLocalBookmarked(manhwaId)) {
            updateBookmarkUI(true);
        }
    }
    
    // Bookmark button click handler
    $('.detail-bookmark').click(function() {
        var $btn = $(this);
        var isMarked = $btn.hasClass('marked');
        
        if (isLoggedIn) {
            // Save to database via AJAX
            var action = isMarked ? 'komik_remove_bookmark' : 'komik_add_bookmark';
            
            $btn.prop('disabled', true);
            
            $.ajax({
                url: komikAccount.ajaxUrl,
                type: 'POST',
                data: {
                    action: action,
                    nonce: komikAccount.nonce,
                    manhwa_id: manhwaId
                },
                success: function(response) {
                    if (response.success) {
                        updateBookmarkUI(!isMarked);
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        } else {
            // Save to localStorage for guests
            var manhwaData = {
                id: manhwaId,
                title: <?php echo json_encode(get_the_title()); ?>,
                url: <?php echo json_encode(get_permalink()); ?>,
                thumbnail: <?php echo json_encode($thumbnail_url ?: get_template_directory_uri() . '/assets/images/placeholder.jpg'); ?>,
                type: <?php echo json_encode($type ?: ''); ?>,
                rating: <?php echo json_encode($rating ?: ''); ?>,
                latestChapter: <?php echo json_encode($latest_chapter ? $latest_chapter['title'] : ''); ?>,
                date: Date.now()
            };
            
            if (isMarked) {
                var bookmarks = getLocalBookmarks().filter(function(b) { return b.id !== manhwaId; });
                saveLocalBookmarks(bookmarks);
                updateBookmarkUI(false);
            } else {
                var bookmarks = getLocalBookmarks();
                if (!isLocalBookmarked(manhwaId)) {
                    bookmarks.unshift(manhwaData);
                    saveLocalBookmarks(bookmarks);
                }
                updateBookmarkUI(true);
            }
        }
    });
    
    // ===== CHAPTER SEARCH =====
    $('#chapter-search').on('input', function() {
        var searchVal = $(this).val().toLowerCase();
        $('.chapter-item').each(function() {
            var chNum = $(this).data('num').toString();
            if (chNum.indexOf(searchVal) !== -1 || searchVal === '') {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // ===== TRACK READING HISTORY WHEN CLICKING CHAPTER =====
    $(document).on('click', '.chapter-item a', function() {
        var $item = $(this).closest('.chapter-item');
        var chapterTitle = $item.find('.chapter-title').text() || $item.find('a').text();
        var chapterUrl = $(this).attr('href');
        
        // Save to database for logged-in users
        if (isLoggedIn && typeof komikAccount !== 'undefined') {
            $.ajax({
                url: komikAccount.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'komik_add_reading_history',
                    nonce: komikAccount.nonce,
                    manhwa_id: manhwaId,
                    chapter: chapterTitle,
                    chapter_url: chapterUrl
                }
            });
        }
        
        // Also save to localStorage
        try {
            var history = JSON.parse(localStorage.getItem('komik_reading_history') || '[]');
            history = history.filter(function(item) {
                return item.manhwa_id !== manhwaId;
            });
            history.unshift({
                manhwa_id: manhwaId,
                manhwa_title: <?php echo json_encode(get_the_title()); ?>,
                manhwa_url: <?php echo json_encode(get_permalink()); ?>,
                chapter: chapterTitle,
                chapter_url: chapterUrl,
                read_at: new Date().toISOString()
            });
            history = history.slice(0, 50);
            localStorage.setItem('komik_reading_history', JSON.stringify(history));
        } catch(e) {}
    });
});
</script>

<?php get_footer(); ?>
