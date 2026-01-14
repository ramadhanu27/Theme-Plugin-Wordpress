<?php
/**
 * Search Results Template
 *
 * @package Komik_Starter
 */

get_header();
?>

<?php komik_starter_breadcrumb(); ?>

<div class="postbody">
    <div class="bixbox">
        <div class="releases">
            <h1>
                <?php
                printf(
                    __('Search Results for: %s', 'komik-starter'),
                    '<span>' . get_search_query() . '</span>'
                );
                ?>
            </h1>
        </div>

        <?php if (have_posts()) : ?>
            <div class="listupd">
                <?php while (have_posts()) : the_post(); 
                    // Check if it's a manhwa post type
                    $is_manhwa = (get_post_type() == 'manhwa');
                    
                    if ($is_manhwa) {
                        $type = get_post_meta(get_the_ID(), '_manhwa_type', true);
                        $status = get_post_meta(get_the_ID(), '_manhwa_status', true);
                        $rating = get_post_meta(get_the_ID(), '_manhwa_rating', true);
                        $chapters = get_post_meta(get_the_ID(), '_manhwa_chapters', true);
                        $latest_ch = !empty($chapters) && is_array($chapters) ? $chapters[0] : null;
                        
                        // Calculate stars (out of 5) - rating is stored as 0-10 scale
                        $rating_val = floatval($rating);
                        $stars_filled = $rating ? floor($rating_val / 2) : 0;
                        $stars_half = ($rating && (($rating_val / 2) - floor($rating_val / 2)) >= 0.5) ? 1 : 0;
                        $rating_display = $rating ? min(10, round($rating_val)) : '';
                    }
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
                                    
                                    <?php if ($is_manhwa && $type) : 
                                        // Get flag image based on type
                                        $flag_img = '';
                                        $type_lower = strtolower($type);
                                        if ($type_lower === 'manhwa' || $type_lower === 'korea') {
                                            $flag_img = 'manhwa.png';
                                        } elseif ($type_lower === 'manhua' || $type_lower === 'china') {
                                            $flag_img = 'manhua.png';
                                        } elseif ($type_lower === 'manga' || $type_lower === 'japan') {
                                            $flag_img = 'manga.png';
                                        }
                                        if ($flag_img) :
                                    ?>
                                        <span class="type-badge">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/<?php echo $flag_img; ?>" alt="<?php echo esc_attr($type); ?>">
                                        </span>
                                    <?php endif; endif; ?>
                                    
                                    <?php if ($is_manhwa && $status == 'completed') : ?>
                                        <span class="status-completed">COMPLETED</span>
                                    <?php endif; ?>
                                    
                                    <span class="hot-icon"><i class="fas fa-fire"></i></span>
                                </div>
                            </a>
                            <div class="tt"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
                            <?php if ($is_manhwa && $latest_ch) : ?>
                                <div class="chapter-text"><?php echo esc_html($latest_ch['title']); ?></div>
                            <?php else : ?>
                                <div class="chapter-text"><?php echo komik_starter_time_ago(get_the_date('Y-m-d H:i:s')); ?></div>
                            <?php endif; ?>
                            <div class="rating-row">
                                <div class="numscore">
                                    <?php if ($is_manhwa && $rating) : ?>
                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                            <?php if ($i <= $stars_filled) : ?>
                                                <i class="fas fa-star"></i>
                                            <?php elseif ($i == $stars_filled + 1 && $stars_half) : ?>
                                                <i class="fas fa-star-half-alt"></i>
                                            <?php else : ?>
                                                <i class="far fa-star"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        <span class="score"><?php echo $rating_display; ?></span>
                                    <?php else : ?>
                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                            <i class="far fa-star"></i>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php komik_starter_pagination(); ?>

        <?php else : ?>
            <div class="notf" style="padding: 30px; text-align: center;">
                <i class="fas fa-search" style="font-size: 48px; color: #555; margin-bottom: 15px;"></i>
                <h2 style="color: #888; margin-bottom: 10px;"><?php _e('No Results Found', 'komik-starter'); ?></h2>
                <p><?php _e('Sorry, but nothing matched your search terms. Please try again with different keywords.', 'komik-starter'); ?></p>
                
                <!-- Search Form -->
                <div style="max-width: 400px; margin: 20px auto;">
                    <form action="<?php echo esc_url(home_url('/')); ?>" method="get">
                        <div style="display: flex; gap: 10px;">
                            <input type="text" name="s" placeholder="<?php esc_attr_e('Search...', 'komik-starter'); ?>" style="flex: 1; padding: 10px; background: #333; border: 1px solid #444; color: #fff; border-radius: 5px;" />
                            <button type="submit" style="padding: 10px 20px; background: var(--color-accent); color: #fff; border: none; border-radius: 5px; cursor: pointer;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div><!-- .postbody -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
