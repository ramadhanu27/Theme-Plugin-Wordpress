<?php
/**
 * 404 Error Page Template
 *
 * @package Komik_Starter
 */

get_header();
?>

<div class="postbody full">
    <div class="bixbox">
        <div class="notf" style="padding: 60px 20px; text-align: center;">
            <div style="font-size: 120px; font-weight: 700; color: var(--color-accent); line-height: 1;">404</div>
            <h1 style="font-size: 28px; color: #fff; margin: 20px 0 10px;"><?php _e('Page Not Found', 'komik-starter'); ?></h1>
            <p style="font-size: 16px; color: #888; max-width: 500px; margin: 0 auto 30px;">
                <?php _e('Sorry, the page you are looking for does not exist. It might have been moved or deleted.', 'komik-starter'); ?>
            </p>
            
            <!-- Search Form -->
            <div style="max-width: 400px; margin: 0 auto 30px;">
                <form action="<?php echo esc_url(home_url('/')); ?>" method="get">
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="s" placeholder="<?php esc_attr_e('Search...', 'komik-starter'); ?>" style="flex: 1; padding: 12px 15px; background: #333; border: 1px solid #444; color: #fff; border-radius: 5px; font-size: 14px;" />
                        <button type="submit" style="padding: 12px 25px; background: var(--color-accent); color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <a href="<?php echo esc_url(home_url('/')); ?>" style="display: inline-block; padding: 12px 30px; background: var(--color-accent); color: #fff; border-radius: 25px; font-weight: 500; text-decoration: none;">
                <i class="fas fa-home"></i> <?php _e('Back to Homepage', 'komik-starter'); ?>
            </a>
        </div>
    </div>

    <!-- Suggested Content -->
    <div class="bixbox">
        <div class="releases">
            <h3><?php _e('You Might Like', 'komik-starter'); ?></h3>
        </div>
        <div class="listupd">
            <?php
            $random_posts = new WP_Query(array(
                'posts_per_page' => 6,
                'orderby'        => 'rand',
                'ignore_sticky_posts' => true,
            ));

            if ($random_posts->have_posts()) :
                while ($random_posts->have_posts()) : $random_posts->the_post();
            ?>
                <div class="bs">
                    <div class="bsx">
                        <a href="<?php the_permalink(); ?>">
                            <div class="limit">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium'); ?>
                                <?php else : ?>
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholder.jpg" alt="<?php the_title_attribute(); ?>" />
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="tt"><?php the_title(); ?></div>
                        <div class="adds">
                            <span class="epxs"><?php echo komik_starter_time_ago(get_the_date('Y-m-d H:i:s')); ?></span>
                        </div>
                    </div>
                </div>
            <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
