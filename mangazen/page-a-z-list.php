<?php
/**
 * Template Name: A-Z List
 * Template for A-Z Manhwa List Page
 *
 * @package Komik_Starter
 */

defined("ABSPATH") || die("!");
get_header();

// Get current letter filter
$current_letter = isset($_GET['letter']) ? sanitize_text_field($_GET['letter']) : '';
$letters = array_merge(['0-9'], range('A', 'Z'));

// Build base URL
$base_url = get_permalink();

// Check if manhwa post type exists
$post_type_exists = post_type_exists('manhwa');
?>

<div class="postbody full">
    <div class="bixbox">
        <!-- Page Header -->
        <div class="releases">
            <h1><i class="fas fa-sort-alpha-down"></i> <?php _e('A-Z List', 'komik-starter'); ?></h1>
        </div>
        
        <!-- A-Z Navigation -->
        <div class="az-list-nav">
            <a href="<?php echo esc_url($base_url); ?>" class="az-btn <?php echo empty($current_letter) ? 'active' : ''; ?>">
                <?php _e('All', 'komik-starter'); ?>
            </a>
            <?php foreach ($letters as $letter) : 
                $is_active = (strtoupper($current_letter) === strtoupper($letter));
                $letter_url = add_query_arg('letter', $letter, $base_url);
            ?>
            <a href="<?php echo esc_url($letter_url); ?>" 
               class="az-btn <?php echo $is_active ? 'active' : ''; ?>">
                <?php echo esc_html($letter); ?>
            </a>
            <?php endforeach; ?>
        </div>
        
        <?php
        if (!$post_type_exists) {
            echo '<div class="az-empty"><p>Post type "manhwa" not found. Please make sure Manhwa Manager plugin is active.</p></div>';
        } else {
            // Build query args
            $args = array(
                'post_type'      => 'manhwa',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
                'post_status'    => 'publish',
            );
            
            // Letter filter using posts_where
            $letter_filter = $current_letter;
            if (!empty($letter_filter)) {
                add_filter('posts_where', function($where) use ($letter_filter) {
                    global $wpdb;
                    if ($letter_filter === '0-9') {
                        $where .= " AND {$wpdb->posts}.post_title REGEXP '^[0-9]'";
                    } else {
                        $letter = strtoupper($letter_filter);
                        $where .= $wpdb->prepare(" AND UPPER(SUBSTRING({$wpdb->posts}.post_title, 1, 1)) = %s", $letter);
                    }
                    return $where;
                });
            }
            
            $manhwa_query = new WP_Query($args);
            
            // Remove filter after query
            remove_all_filters('posts_where');
            
            // Group manhwa by first letter
            $grouped = [];
            if ($manhwa_query->have_posts()) {
                while ($manhwa_query->have_posts()) {
                    $manhwa_query->the_post();
                    $title = get_the_title();
                    $first_char = mb_strtoupper(mb_substr($title, 0, 1));
                    
                    // Check if starts with number
                    if (is_numeric($first_char)) {
                        $first_char = '0-9';
                    }
                    
                    if (!isset($grouped[$first_char])) {
                        $grouped[$first_char] = [];
                    }
                    
                    $chapters = get_post_meta(get_the_ID(), '_manhwa_chapters', true);
                    
                    $grouped[$first_char][] = [
                        'id' => get_the_ID(),
                        'title' => $title,
                        'url' => get_permalink(),
                        'type' => get_post_meta(get_the_ID(), '_manhwa_type', true),
                        'status' => get_post_meta(get_the_ID(), '_manhwa_status', true),
                        'ch_count' => is_array($chapters) ? count($chapters) : 0,
                    ];
                }
                wp_reset_postdata();
            }
            
            // Sort groups
            uksort($grouped, function($a, $b) {
                if ($a === '0-9') return -1;
                if ($b === '0-9') return 1;
                return strcmp($a, $b);
            });
            
            // Count total
            $total_count = array_sum(array_map('count', $grouped));
            ?>
            
            <!-- Stats -->
            <div class="az-stats">
                <?php if ($current_letter) : ?>
                    <span class="az-current">
                        <?php 
                        if ($current_letter === '0-9') {
                            echo __('Showing manhwa starting with numbers', 'komik-starter');
                        } else {
                            printf(__('Showing manhwa starting with "%s"', 'komik-starter'), strtoupper($current_letter));
                        }
                        ?>
                    </span>
                <?php else : ?>
                    <span class="az-current"><?php _e('Showing all manhwa', 'komik-starter'); ?></span>
                <?php endif; ?>
                <span class="az-total">
                    <?php printf(_n('%d Series', '%d Series', $total_count, 'komik-starter'), $total_count); ?>
                </span>
            </div>
            
            <!-- A-Z Content -->
            <div class="az-content">
                <?php if (empty($grouped)) : ?>
                    <div class="az-empty">
                        <i class="fas fa-search"></i>
                        <p><?php _e('No manhwa found.', 'komik-starter'); ?></p>
                    </div>
                <?php else : ?>
                    <?php foreach ($grouped as $letter => $items) : ?>
                    <div class="az-group" id="letter-<?php echo esc_attr(sanitize_title($letter)); ?>">
                        <div class="az-group-header">
                            <span class="az-letter"><?php echo esc_html($letter); ?></span>
                            <span class="az-count"><?php echo count($items); ?> series</span>
                        </div>
                        <div class="az-group-list">
                            <?php foreach ($items as $item) : ?>
                            <div class="az-item">
                                <a href="<?php echo esc_url($item['url']); ?>" class="az-item-link">
                                    <span class="az-item-title"><?php echo esc_html($item['title']); ?></span>
                                    <?php if ($item['type']) : ?>
                                    <span class="az-item-type type-<?php echo esc_attr(strtolower($item['type'])); ?>">
                                        <?php echo esc_html($item['type']); ?>
                                    </span>
                                    <?php endif; ?>
                                    <?php if ($item['status'] === 'completed') : ?>
                                    <span class="az-item-status completed">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                    <?php endif; ?>
                                </a>
                                <span class="az-item-chapters">
                                    <?php echo $item['ch_count']; ?> Ch
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Quick Jump (for All view) -->
            <?php if (empty($current_letter) && !empty($grouped)) : ?>
            <div class="az-quick-jump">
                <span class="az-quick-label"><?php _e('Jump to:', 'komik-starter'); ?></span>
                <?php foreach (array_keys($grouped) as $letter) : ?>
                <a href="#letter-<?php echo esc_attr(sanitize_title($letter)); ?>" class="az-quick-link"><?php echo esc_html($letter); ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
        <?php } // end post_type_exists check ?>
    </div>
</div>

<style>
/* A-Z List Page Styles */
.az-list-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 20px 0;
    border-bottom: 1px solid var(--color-border);
    margin-bottom: 20px;
}

.az-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 12px;
    background: var(--color-bg-secondary);
    color: var(--color-text-secondary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition-base);
}

.az-btn:hover {
    background: var(--color-accent);
    color: #fff;
    border-color: var(--color-accent);
    transform: translateY(-2px);
}

.az-btn.active {
    background: var(--color-accent);
    color: #fff;
    border-color: var(--color-accent);
    box-shadow: 0 4px 12px rgba(54, 106, 211, 0.4);
}

.az-stats {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 20px;
    background: var(--color-bg-secondary);
    border-radius: var(--radius-md);
    margin-bottom: 20px;
}

.az-current {
    color: var(--color-text-secondary);
    font-size: 14px;
}

.az-total {
    color: var(--color-accent);
    font-weight: 600;
    font-size: 14px;
}

.az-content {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.az-group {
    background: var(--color-bg-secondary);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.az-group-header {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 20px;
    background: var(--color-accent);
    color: #fff;
}

.az-letter {
    font-size: 28px;
    font-weight: 700;
    min-width: 40px;
}

.az-count {
    font-size: 13px;
    opacity: 0.8;
}

.az-group-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1px;
    background: var(--color-border);
}

.az-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 15px;
    background: var(--color-bg-card);
    transition: var(--transition-base);
}

.az-item:hover {
    background: var(--color-bg-hover);
}

.az-item-link {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    color: var(--color-text-primary);
    text-decoration: none;
    overflow: hidden;
}

.az-item-title {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 14px;
}

.az-item-type {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 3px;
    font-weight: 600;
    text-transform: uppercase;
    flex-shrink: 0;
}

.az-item-type.type-manhwa { background: #3b82f6; color: #fff; }
.az-item-type.type-manga { background: #ef4444; color: #fff; }
.az-item-type.type-manhua { background: #22c55e; color: #fff; }

.az-item-status.completed {
    color: var(--color-completed);
    font-size: 14px;
}

.az-item-chapters {
    font-size: 12px;
    color: var(--color-text-muted);
    flex-shrink: 0;
    margin-left: 10px;
}

.az-empty {
    text-align: center;
    padding: 60px 20px;
    color: var(--color-text-muted);
    background: var(--color-bg-secondary);
    border-radius: var(--radius-lg);
}

.az-empty i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
    display: block;
}

.az-quick-jump {
    position: sticky;
    bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: rgba(22, 21, 29, 0.95);
    backdrop-filter: blur(10px);
    border-radius: var(--radius-full);
    margin-top: 30px;
    justify-content: center;
    flex-wrap: wrap;
    box-shadow: 0 4px 20px rgba(0,0,0,0.5);
}

.az-quick-label {
    color: var(--color-text-muted);
    font-size: 13px;
}

.az-quick-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: var(--color-bg-hover);
    color: var(--color-text-secondary);
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition-base);
}

.az-quick-link:hover {
    background: var(--color-accent);
    color: #fff;
}

/* Responsive */
@media (max-width: 768px) {
    .az-btn {
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        font-size: 12px;
    }
    
    .az-group-list {
        grid-template-columns: 1fr;
    }
    
    .az-stats {
        flex-direction: column;
        gap: 5px;
        text-align: center;
    }
    
    .az-letter {
        font-size: 22px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Smooth scroll for quick jump links
    $('.az-quick-link').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        if ($(target).length) {
            $('html, body').animate({
                scrollTop: $(target).offset().top - 80
            }, 500);
        }
    });
});
</script>

<?php get_footer(); ?>
