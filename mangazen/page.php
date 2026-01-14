<?php
/**
 * Page Template
 *
 * @package Komik_Starter
 */

get_header();
?>

<?php komik_starter_breadcrumb(); ?>

<div class="postbody full">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('bixbox page'); ?>>
            <!-- Page Header -->
            <div class="releases">
                <h1><?php the_title(); ?></h1>
            </div>

            <!-- Page Content -->
            <div class="info-desc">
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>

                <?php
                wp_link_pages(array(
                    'before' => '<div class="page-links">' . __('Pages:', 'komik-starter'),
                    'after'  => '</div>',
                ));
                ?>
            </div>

            <!-- Comments Section -->
            <?php if (comments_open() || get_comments_number()) : ?>
                <div class="cmt">
                    <div class="commentx">
                        <?php comments_template(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </article>
    <?php endwhile; ?>
</div><!-- .postbody -->

<?php get_footer(); ?>
