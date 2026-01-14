<?php
/**
 * Template Name: Series Detail
 * Template untuk halaman detail series/manga dengan layout mirip MangaReader
 *
 * @package Komik_Starter
 */

defined("ABSPATH") || die("!");
get_header();

// Set post views
komik_starter_set_post_views(get_the_ID());
?>

<?php while (have_posts()) : the_post(); ?>

<!-- Big Cover Banner -->
<div class="bigcover">
    <div class="bigbanner img-blur" style="background-image: url('<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>');"></div>
</div>

<div class="postbody full">
    <article id="post-<?php the_ID(); ?>" <?php post_class('hentry'); ?> itemscope="itemscope" itemtype="http://schema.org/CreativeWorkSeries">
        
        <div class="main-info">
            <!-- Left Side - Cover Image & Info -->
            <div class="info-left">
                <div class="info-left-margin">
                    <!-- Cover Thumbnail -->
                    <div class="thumb" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium', array('title' => get_the_title(), 'alt' => get_the_title(), 'itemprop' => 'image')); ?>
                        <?php else : ?>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholder.jpg" alt="<?php the_title_attribute(); ?>" />
                        <?php endif; ?>
                    </div>
                    
                    <!-- Mobile Title (shown on mobile) -->
                    <div id="mobiletitle"></div>

                    <!-- Bookmark Button -->
                    <div data-id="<?php echo get_the_ID(); ?>" class="bookmark">
                        <i class="far fa-bookmark" aria-hidden="true"></i> <?php _e('Bookmark', 'komik-starter'); ?>
                    </div>

                    <!-- Rating -->
                    <?php 
                    $score = get_post_meta(get_the_ID(), 'manga_score', true);
                    if (!$score) $score = '8.5'; // Default score
                    $scorepros = floatval($score) * 10;
                    ?>
                    <div class="rating bixbox">
                        <div class="rating-prc" itemscope="itemscope" itemprop="aggregateRating" itemtype="//schema.org/AggregateRating">
                            <meta itemprop="worstRating" content="1">
                            <meta itemprop="bestRating" content="10">
                            <meta itemprop="ratingCount" content="10">
                            <div class="rtp">
                                <div class="rtb"><span style="width:<?php echo $scorepros; ?>%"></span></div>
                            </div>
                            <div class="num" itemprop="ratingValue" content="<?php echo $score; ?>"><?php echo $score; ?></div>
                        </div>
                    </div>

                    <!-- Series Info Box -->
                    <div class="tsinfo bixbox">
                        <?php 
                        // Status
                        $status = get_post_meta(get_the_ID(), 'manga_status', true);
                        if ($status) : ?>
                        <div class="imptdt">
                            <?php _e('Status', 'komik-starter'); ?> <i><?php echo esc_html($status); ?></i>
                        </div>
                        <?php endif; ?>

                        <?php 
                        // Type
                        $type = get_post_meta(get_the_ID(), 'manga_type', true);
                        if ($type) : ?>
                        <div class="imptdt">
                            <?php _e('Type', 'komik-starter'); ?> <i><?php echo esc_html($type); ?></i>
                        </div>
                        <?php endif; ?>

                        <?php 
                        // Author
                        $author = get_post_meta(get_the_ID(), 'manga_author', true);
                        if ($author) : ?>
                        <div class="imptdt">
                            <?php _e('Author', 'komik-starter'); ?> <i><?php echo esc_html($author); ?></i>
                        </div>
                        <?php endif; ?>

                        <?php 
                        // Artist
                        $artist = get_post_meta(get_the_ID(), 'manga_artist', true);
                        if ($artist) : ?>
                        <div class="imptdt">
                            <?php _e('Artist', 'komik-starter'); ?> <i><?php echo esc_html($artist); ?></i>
                        </div>
                        <?php endif; ?>

                        <!-- Posted By -->
                        <div class="imptdt">
                            <?php _e('Posted By', 'komik-starter'); ?>
                            <span itemprop="author" itemscope itemtype="https://schema.org/Person" class="author vcard">
                                <i itemprop="name"><?php the_author(); ?></i>
                            </span>
                        </div>

                        <!-- Posted On -->
                        <div class="imptdt">
                            <?php _e('Posted On', 'komik-starter'); ?> 
                            <i><time itemprop="datePublished" datetime="<?php the_time('c'); ?>"><?php the_time('F j, Y'); ?></time></i>
                        </div>

                        <!-- Updated On -->
                        <div class="imptdt">
                            <?php _e('Updated On', 'komik-starter'); ?> 
                            <i><time itemprop="dateModified" datetime="<?php the_modified_date('c'); ?>"><?php the_modified_date('F j, Y'); ?></time></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Title, Genres, Synopsis -->
            <div class="info-right">
                <div class="info-desc bixbox">
                    <!-- Title -->
                    <div id="titledesktop">
                        <div id="titlemove">
                            <h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1>
                            <?php 
                            $alt_title = get_post_meta(get_the_ID(), 'manga_alternative', true);
                            if ($alt_title) : ?>
                                <span class="alternative"><?php echo esc_html($alt_title); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Genres/Categories -->
                    <?php 
                    $categories = get_the_category();
                    if (!empty($categories)) : ?>
                    <div class="wd-full">
                        <span class="mgen">
                            <?php foreach ($categories as $category) : ?>
                                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>"><?php echo esc_html($category->name); ?></a>
                            <?php endforeach; ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <!-- Tags -->
                    <?php if (has_tag()) : ?>
                    <div class="wd-full">
                        <span class="mgen">
                            <?php the_tags('', ' ', ''); ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <!-- Synopsis -->
                    <div class="wd-full">
                        <h2><?php printf(__('Synopsis %s', 'komik-starter'), get_the_title()); ?></h2>
                        <div class="entry-content entry-content-single" itemprop="description">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>

                <!-- Chapter List Section -->
                <div class="bixbox bxcl epcheck">
                    <div class="releases">
                        <h2><?php printf(__('%s Chapters', 'komik-starter'), get_the_title()); ?></h2>
                    </div>

                    <!-- First/Last Chapter Quick Links -->
                    <div class="lastend">
                        <div class="inepcx">
                            <a href="#chapter-list">
                                <span><?php _e('First Chapter', 'komik-starter'); ?></span>
                                <span class="epcur epcurfirst"><?php _e('Chapter', 'komik-starter'); ?> 1</span>
                            </a>
                        </div>
                        <div class="inepcx">
                            <a href="#chapter-list">
                                <span><?php _e('Latest Chapter', 'komik-starter'); ?></span>
                                <span class="epcur epcurlast"><?php _e('Chapter', 'komik-starter'); ?> ?</span>
                            </a>
                        </div>
                    </div>

                    <!-- Chapter List -->
                    <div class="eplister" id="chapterlist">
                        <ul class="clstyle">
                            <?php
                            // Get chapters as child posts or related posts
                            $chapters = new WP_Query(array(
                                'post_parent' => get_the_ID(),
                                'post_type'   => 'post',
                                'posts_per_page' => -1,
                                'orderby'     => 'date',
                                'order'       => 'DESC',
                            ));

                            if ($chapters->have_posts()) :
                                while ($chapters->have_posts()) : $chapters->the_post();
                            ?>
                            <li data-num="<?php echo get_post_meta(get_the_ID(), 'chapter_number', true); ?>">
                                <div class="chbox">
                                    <div class="eph-num">
                                        <a href="<?php the_permalink(); ?>">
                                            <span class="chapternum"><?php the_title(); ?></span>
                                            <span class="chapterdate"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></span>
                                        </a>
                                    </div>
                                    <div class="dt">
                                        <a href="<?php the_permalink(); ?>"><i class="fas fa-angle-right"></i></a>
                                    </div>
                                </div>
                            </li>
                            <?php
                                endwhile;
                                wp_reset_postdata();
                            else :
                            ?>
                            <li class="no-chapters">
                                <div class="chbox" style="text-align: center; padding: 20px;">
                                    <p><?php _e('No chapters yet. Check back later!', 'komik-starter'); ?></p>
                                </div>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Breadcrumb -->
                <?php komik_starter_breadcrumb(); ?>

            </div>
        </div>

        <!-- Related Series -->
        <?php
        $categories = get_the_category();
        if (!empty($categories)) :
            $cat_ids = wp_list_pluck($categories, 'term_id');
            $cat_ids = array_slice($cat_ids, 0, 2);
            
            $related = new WP_Query(array(
                'category__in'        => $cat_ids,
                'post__not_in'        => array(get_the_ID()),
                'posts_per_page'      => 7,
                'orderby'             => 'rand',
                'ignore_sticky_posts' => true,
            ));
            
            if ($related->have_posts()) :
        ?>
        <div class="bixbox">
            <div class="releases">
                <h2><span><?php _e('Recommended Series', 'komik-starter'); ?></span></h2>
            </div>
            <div class="listupd">
                <?php while ($related->have_posts()) : $related->the_post();
                    $rel_type = get_post_meta(get_the_ID(), 'manga_type', true);
                    $rel_status = get_post_meta(get_the_ID(), 'manga_status', true);
                    $rel_rating = get_post_meta(get_the_ID(), 'manga_score', true);
                    
                    // Calculate stars (out of 5)
                    $rel_stars_filled = $rel_rating ? floor(floatval($rel_rating) / 2) : 0;
                    $rel_stars_half = ($rel_rating && ((floatval($rel_rating) / 2) - floor(floatval($rel_rating) / 2)) >= 0.5) ? 1 : 0;
                    $rel_rating_display = $rel_rating ? round(floatval($rel_rating)) : '';
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
                                
                                <?php if ($rel_type) : ?>
                                    <span class="type-badge <?php echo esc_attr(strtolower($rel_type)); ?>"><?php echo esc_html(strtoupper($rel_type)); ?></span>
                                <?php endif; ?>
                                
                                <?php if ($rel_status == 'Completed' || $rel_status == 'completed') : ?>
                                    <span class="status-completed">COMPLETED</span>
                                <?php endif; ?>
                                
                                <span class="hot-icon"><i class="fas fa-fire"></i></span>
                            </div>
                        </a>
                        <div class="tt"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
                        <div class="rating-row">
                            <div class="numscore">
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <?php if ($i <= $rel_stars_filled) : ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif ($i == $rel_stars_filled + 1 && $rel_stars_half) : ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else : ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <?php if ($rel_rating_display) : ?>
                                    <span class="score"><?php echo $rel_rating_display; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php endif; endif; ?>

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

    </article>
</div>

<?php endwhile; ?>

<script>
// Move title to mobile on small screens
jQuery(document).ready(function($) {
    if ($(window).width() < 800) {
        $('#titlemove').appendTo('#mobiletitle');
    }
    $(window).resize(function() {
        if ($(window).width() < 800) {
            $('#titlemove').appendTo('#mobiletitle');
        } else {
            $('#titlemove').appendTo('#titledesktop');
        }
    });

    // Bookmark functionality
    $('.bookmark').click(function() {
        $(this).toggleClass('marked');
        var icon = $(this).find('i');
        if ($(this).hasClass('marked')) {
            icon.removeClass('far').addClass('fas');
            $(this).html('<i class="fas fa-bookmark"></i> <?php _e("Bookmarked", "komik-starter"); ?>');
        } else {
            icon.removeClass('fas').addClass('far');
            $(this).html('<i class="far fa-bookmark"></i> <?php _e("Bookmark", "komik-starter"); ?>');
        }
    });
});
</script>

<?php get_footer(); ?>
