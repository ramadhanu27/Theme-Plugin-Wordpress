<?php
/**
 * Template for Manhwa Genre Taxonomy
 * Displays manhwa filtered by genre
 *
 * @package Komik_Starter
 */

defined("ABSPATH") || die("!");
get_header();

$term = get_queried_object();
?>

<div class="postbody">
    <!-- Genre Header -->
    <div class="bixbox">
        <div class="releases">
            <h1><?php echo esc_html($term->name); ?></h1>
            <span class="vl"><?php echo $term->count; ?> <?php _e('Series', 'komik-starter'); ?></span>
        </div>
        
        <?php if ($term->description) : ?>
        <div class="genre-desc" style="padding: 15px; color: #999;">
            <?php echo wp_kses_post($term->description); ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Manhwa List -->
    <div class="bixbox">
        <div class="listupd">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post();
                    $type = get_post_meta(get_the_ID(), '_manhwa_type', true);
                    $status = get_post_meta(get_the_ID(), '_manhwa_status', true);
                    $rating = get_post_meta(get_the_ID(), '_manhwa_rating', true);
                    $chapters = get_post_meta(get_the_ID(), '_manhwa_chapters', true);
                    $ch_count = is_array($chapters) ? count($chapters) : 0;
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
                                
                                <?php if ($type) : ?>
                                    <span class="typename <?php echo esc_attr($type); ?>"><?php echo esc_html($type); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="tt"><?php the_title(); ?></div>
                        </a>
                        <div class="adds">
                            <?php if ($rating) : ?>
                            <div class="rating">
                                <span class="rtg"><i class="fas fa-star" style="color: #f39c12;"></i> <?php echo esc_html($rating); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($ch_count > 0) : ?>
                            <div class="epxs"><?php echo $ch_count; ?> Ch</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="notf" style="padding: 40px; text-align: center; width: 100%;">
                    <p><?php _e('No manhwa found in this genre.', 'komik-starter'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <div class="pagination">
            <?php
            echo paginate_links(array(
                'prev_text' => '<i class="fas fa-chevron-left"></i>',
                'next_text' => '<i class="fas fa-chevron-right"></i>',
            ));
            ?>
        </div>
    </div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
