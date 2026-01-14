<?php
/**
 * Archive Template
 *
 * @package Komik_Starter
 */

get_header();
?>

<?php komik_starter_breadcrumb(); ?>

<div class="postbody">
    <div class="bixbox">
        <div class="releases">
            <h1><?php the_archive_title(); ?></h1>
        </div>

        <?php if (have_posts()) : ?>
            <div class="listupd">
                <?php while (have_posts()) : the_post(); ?>
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
                <?php endwhile; ?>
            </div>

            <?php komik_starter_pagination(); ?>

        <?php else : ?>
            <div class="notf">
                <p><?php _e('No posts found.', 'komik-starter'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div><!-- .postbody -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
