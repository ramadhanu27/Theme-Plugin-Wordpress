<?php
/**
 * Template for Archive Manhwa (Manhwa List)
 * Displays all manhwa from the manhwa-manager plugin
 *
 * @package Komik_Starter
 */

defined("ABSPATH") || die("!");
get_header();
?>

<div class="postbody">
    <!-- Page Header -->
    <div class="bixbox has-dropdown">
        <div class="releases">
            <h1><?php _e('All Manhwa', 'komik-starter'); ?></h1>
            <span class="vl"><?php echo wp_count_posts('manhwa')->publish; ?> <?php _e('Series', 'komik-starter'); ?></span>
        </div>
        
        <!-- Filter/Sort Options -->
        <div class="filter-sort">
            <form method="get" action="" id="manhwa-filter-form" class="filter-form">
                
                <!-- Type Filter -->
                <div class="filter-select-wrapper">
                    <select name="type" class="filter-select">
                        <option value=""><?php _e('All Types', 'komik-starter'); ?></option>
                        <option value="Manhwa" <?php selected(isset($_GET['type']) ? $_GET['type'] : '', 'Manhwa'); ?>>Manhwa</option>
                        <option value="Manga" <?php selected(isset($_GET['type']) ? $_GET['type'] : '', 'Manga'); ?>>Manga</option>
                        <option value="Manhua" <?php selected(isset($_GET['type']) ? $_GET['type'] : '', 'Manhua'); ?>>Manhua</option>
                    </select>
                    <i class="fas fa-chevron-down"></i>
                </div>
                
                <!-- Genre Filter -->
                <div class="filter-select-wrapper">
                    <select name="genre" class="filter-select">
                        <option value=""><?php _e('All Genres', 'komik-starter'); ?></option>
                        <?php
                        $genres = get_terms(array(
                            'taxonomy' => 'manhwa_genre',
                            'hide_empty' => true,
                            'orderby' => 'name',
                            'order' => 'ASC',
                        ));
                        if (!empty($genres) && !is_wp_error($genres)) :
                            foreach ($genres as $genre) :
                        ?>
                        <option value="<?php echo esc_attr($genre->slug); ?>" <?php selected(isset($_GET['genre']) ? $_GET['genre'] : '', $genre->slug); ?>><?php echo esc_html($genre->name); ?></option>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </select>
                    <i class="fas fa-chevron-down"></i>
                </div>
                
                <!-- Status Filter -->
                <div class="filter-select-wrapper">
                    <select name="status" class="filter-select">
                        <option value=""><?php _e('All Status', 'komik-starter'); ?></option>
                        <option value="ongoing" <?php selected(isset($_GET['status']) ? $_GET['status'] : '', 'ongoing'); ?>>Ongoing</option>
                        <option value="completed" <?php selected(isset($_GET['status']) ? $_GET['status'] : '', 'completed'); ?>>Completed</option>
                        <option value="hiatus" <?php selected(isset($_GET['status']) ? $_GET['status'] : '', 'hiatus'); ?>>Hiatus</option>
                    </select>
                    <i class="fas fa-chevron-down"></i>
                </div>
                
                <!-- Order By -->
                <div class="filter-select-wrapper">
                    <select name="orderby" class="filter-select">
                        <option value="" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', ''); ?>><?php _e('Default', 'komik-starter'); ?></option>
                        <option value="az" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'az'); ?>>A-Z</option>
                        <option value="za" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'za'); ?>>Z-A</option>
                        <option value="update" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'update'); ?>><?php _e('Update', 'komik-starter'); ?></option>
                        <option value="added" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'added'); ?>><?php _e('Added', 'komik-starter'); ?></option>
                        <option value="popular" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'popular'); ?>><?php _e('Popular', 'komik-starter'); ?></option>
                    </select>
                    <i class="fas fa-chevron-down"></i>
                </div>
                
                <!-- Search Input -->
                <div class="filter-search-wrapper">
                    <input type="text" name="search" class="filter-search-input" placeholder="<?php _e('Search...', 'komik-starter'); ?>" value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>">
                    <button type="submit" class="filter-search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Manhwa List -->
    <div class="bixbox">
        <div class="listupd">
            <?php
            // Build query args
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
            $args = array(
                'post_type'      => 'manhwa',
                'posts_per_page' => 25,
                'paged'          => $paged,
            );
            
            // Search filter
            if (!empty($_GET['search'])) {
                $args['s'] = sanitize_text_field($_GET['search']);
            }
            
            // Type filter
            if (!empty($_GET['type'])) {
                $args['meta_query'][] = array(
                    'key'   => '_manhwa_type',
                    'value' => sanitize_text_field($_GET['type']),
                );
            }
            
            // Status filter
            if (!empty($_GET['status'])) {
                $args['meta_query'][] = array(
                    'key'   => '_manhwa_status',
                    'value' => sanitize_text_field($_GET['status']),
                );
            }
            
            // Genre filter
            if (!empty($_GET['genre'])) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'manhwa_genre',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['genre']),
                );
            }
            
            // Order by
            $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';
            switch ($orderby) {
                case 'az':
                    $args['orderby'] = 'title';
                    $args['order'] = 'ASC';
                    break;
                case 'za':
                    $args['orderby'] = 'title';
                    $args['order'] = 'DESC';
                    break;
                case 'update':
                    $args['orderby'] = 'modified';
                    $args['order'] = 'DESC';
                    break;
                case 'added':
                    $args['orderby'] = 'date';
                    $args['order'] = 'DESC';
                    break;
                case 'popular':
                    $args['meta_key'] = '_manhwa_views';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'DESC';
                    break;
                default:
                    // Default - by modified date (latest updates)
                    $args['orderby'] = 'modified';
                    $args['order'] = 'DESC';
            }
            
            $manhwa_query = new WP_Query($args);
            
            if ($manhwa_query->have_posts()) :
                while ($manhwa_query->have_posts()) : $manhwa_query->the_post();
                    $type = get_post_meta(get_the_ID(), '_manhwa_type', true);
                    $status = get_post_meta(get_the_ID(), '_manhwa_status', true);
                    $rating = get_post_meta(get_the_ID(), '_manhwa_rating', true);
                    $chapters = get_post_meta(get_the_ID(), '_manhwa_chapters', true);
                    $ch_count = is_array($chapters) ? count($chapters) : 0;
                    $latest_ch = !empty($chapters) ? $chapters[0] : null;
                    
                    // Calculate stars (out of 5) - rating is stored as 0-10 scale
                    $rating_val = floatval($rating);
                    $stars_filled = $rating ? floor($rating_val / 2) : 0;
                    $stars_half = ($rating && (($rating_val / 2) - floor($rating_val / 2)) >= 0.5) ? 1 : 0;
                    $rating_display = $rating ? min(10, round($rating_val)) : '';
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
                                    <?php 
                                    // Flag icon based on type
                                    $flag_icon = '';
                                    switch(strtolower($type)) {
                                        case 'manga':
                                            $flag_icon = get_template_directory_uri() . '/assets/images/manga.png';
                                            break;
                                        case 'manhua':
                                            $flag_icon = get_template_directory_uri() . '/assets/images/manhua.png';
                                            break;
                                        case 'manhwa':
                                            $flag_icon = get_template_directory_uri() . '/assets/images/manhwa.png';
                                            break;
                                    }
                                    if ($flag_icon) : ?>
                                        <span class="flag-badge">
                                            <img src="<?php echo esc_url($flag_icon); ?>" alt="<?php echo esc_attr($type); ?> Flag" />
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if ($status == 'completed') : ?>
                                    <span class="status-completed">COMPLETED</span>
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="tt"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
                        <?php if ($latest_ch) : ?>
                            <div class="chapter-text"><?php echo esc_html($latest_ch['title']); ?></div>
                        <?php endif; ?>
                        <div class="rating-row">
                            <div class="numscore">
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <?php if ($i <= $stars_filled) : ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif ($i == $stars_filled + 1 && $stars_half) : ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else : ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <?php if ($rating_display) : ?>
                                    <span class="score"><?php echo $rating_display; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                endwhile;
            else :
            ?>
                <div class="notf" style="padding: 40px; text-align: center; width: 100%;">
                    <i class="fas fa-book-open" style="font-size: 64px; color: #444; margin-bottom: 20px;"></i>
                    <h3 style="color: #fff; margin-bottom: 10px;"><?php _e('No Manhwa Found', 'komik-starter'); ?></h3>
                    <p style="color: #888;"><?php _e('There are no manhwa matching your filter criteria.', 'komik-starter'); ?></p>
                    <a href="<?php echo get_post_type_archive_link('manhwa'); ?>" style="display: inline-block; margin-top: 15px; background: var(--color-accent); color: #fff; padding: 10px 20px; border-radius: 5px;"><?php _e('View All', 'komik-starter'); ?></a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($manhwa_query->max_num_pages > 1) : ?>
        <div class="pagination" style="padding: 20px; text-align: center;">
            <?php
            echo paginate_links(array(
                'total'     => $manhwa_query->max_num_pages,
                'current'   => $paged,
                'prev_text' => '<i class="fas fa-chevron-left"></i>',
                'next_text' => '<i class="fas fa-chevron-right"></i>',
            ));
            ?>
        </div>
        <?php endif; ?>
        
        <?php wp_reset_postdata(); ?>
    </div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
