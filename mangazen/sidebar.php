<div id="sidebar">
    <!-- Bookmark Widget -->
    <div class="section bookmark-widget">
        <div class="releases">
            <h4><i class="fas fa-bookmark"></i> <?php _e('My Bookmarks', 'komik-starter'); ?></h4>
        </div>
        <div class="bookmark-widget-content" id="sidebar-bookmarks">
            <!-- Bookmarks loaded via JavaScript -->
            <div class="bookmark-widget-loading">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </div>
        <div class="bookmark-widget-empty" id="sidebar-bookmarks-empty" style="display: none;">
            <p><?php _e('No bookmarks yet', 'komik-starter'); ?></p>
            <a href="<?php echo get_post_type_archive_link('manhwa'); ?>" class="browse-link">
                <?php _e('Browse Manhwa', 'komik-starter'); ?>
            </a>
        </div>
        <div class="bookmark-widget-footer" id="sidebar-bookmarks-footer" style="display: none;">
            <a href="<?php echo home_url('/bookmark'); ?>" class="view-all-bookmarks">
                <?php _e('View All Bookmarks', 'komik-starter'); ?> <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
 <!-- Serial Populer Widget -->
    <?php
    // Get popular settings
    $popular_limit = get_option('komik_popular_limit', 5);
    
    // Get per-tab mode settings
    $weekly_mode = get_option('komik_popular_weekly_mode', 'manual');
    $monthly_mode = get_option('komik_popular_monthly_mode', 'manual');
    $all_mode = get_option('komik_popular_all_mode', 'manual');
    
    // Get manual IDs
    $weekly_ids = get_option('komik_popular_weekly', array());
    $monthly_ids = get_option('komik_popular_monthly', array());
    $all_ids = get_option('komik_popular_all', array());
    
    // Function to get manhwa by mode
    function get_popular_by_mode($mode, $manual_ids, $limit) {
        if ($mode === 'manual') {
            // Manual selection
            if (empty($manual_ids)) {
                return array();
            }
            $ids = array_slice($manual_ids, 0, $limit);
            $posts = get_posts(array(
                'post_type' => 'manhwa',
                'post__in' => $ids,
                'orderby' => 'post__in',
                'posts_per_page' => $limit,
            ));
        } elseif ($mode === 'views') {
            // By views
            $posts = get_posts(array(
                'post_type' => 'manhwa',
                'posts_per_page' => $limit,
                'meta_key' => '_manhwa_views',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
            ));
        } elseif ($mode === 'rating') {
            // By rating
            $posts = get_posts(array(
                'post_type' => 'manhwa',
                'posts_per_page' => $limit,
                'meta_key' => '_manhwa_rating',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
            ));
        } else {
            $posts = array();
        }
        
        $data = array();
        foreach ($posts as $post) {
            $data[] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'url' => get_permalink($post->ID),
                'thumbnail' => get_the_post_thumbnail_url($post->ID, 'thumbnail'),
                'rating' => get_post_meta($post->ID, '_manhwa_rating', true),
                'genres' => get_the_terms($post->ID, 'manhwa_genre'),
            );
        }
        return $data;
    }
    
    $weekly_data = get_popular_by_mode($weekly_mode, $weekly_ids, $popular_limit);
    $monthly_data = get_popular_by_mode($monthly_mode, $monthly_ids, $popular_limit);
    $all_data = get_popular_by_mode($all_mode, $all_ids, $popular_limit);
    
    // Check if there's any data
    $has_data = !empty($weekly_data) || !empty($monthly_data) || !empty($all_data);
    
    if ($has_data) :
    ?>
    <div class="section popular-widget">
        <div class="releases">
            <h4><i class="fas fa-fire"></i> <?php _e('Serial Populer', 'komik-starter'); ?></h4>
        </div>
        
        <!-- Tabs -->
        <div class="popular-tabs">
            <button class="pop-tab active" data-tab="weekly"><?php _e('Mingguan', 'komik-starter'); ?></button>
            <button class="pop-tab" data-tab="monthly"><?php _e('Bulanan', 'komik-starter'); ?></button>
            <button class="pop-tab" data-tab="all"><?php _e('Semua', 'komik-starter'); ?></button>
        </div>
        
        <!-- Weekly List -->
        <div class="popular-list pop-content active" data-content="weekly">
            <?php if (empty($weekly_data)) : ?>
            <p style="padding: 20px; text-align: center; color: var(--color-text-muted);"><?php _e('No data', 'komik-starter'); ?></p>
            <?php else : 
            $rank = 0;
            foreach ($weekly_data as $item) : $rank++;
                $rating_val = $item['rating'] ? round(floatval($item['rating']), 1) : '';
            ?>
            <div class="popular-item">
                <span class="pop-rank"><?php echo $rank; ?></span>
                <div class="pop-thumb">
                    <a href="<?php echo esc_url($item['url']); ?>">
                        <img src="<?php echo esc_url($item['thumbnail']); ?>" alt="<?php echo esc_attr($item['title']); ?>" loading="lazy">
                    </a>
                </div>
                <div class="pop-info">
                    <h5 class="pop-title">
                        <a href="<?php echo esc_url($item['url']); ?>" title="<?php echo esc_attr($item['title']); ?>"><?php echo esc_html($item['title']); ?></a>
                    </h5>
                    <?php if (!empty($item['genres']) && !is_wp_error($item['genres'])) : ?>
                    <div class="pop-genres">
                        <?php 
                        $genre_names = wp_list_pluck($item['genres'], 'name');
                        echo esc_html(implode(', ', array_slice($genre_names, 0, 3)));
                        ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($rating_val) : ?>
                    <div class="pop-rating"><i class="fas fa-star"></i> <span><?php echo esc_html($rating_val); ?></span></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
        
        <!-- Monthly List -->
        <div class="popular-list pop-content" data-content="monthly">
            <?php if (empty($monthly_data)) : ?>
            <p style="padding: 20px; text-align: center; color: var(--color-text-muted);"><?php _e('No data', 'komik-starter'); ?></p>
            <?php else : 
            $rank = 0;
            foreach ($monthly_data as $item) : $rank++;
                $rating_val = $item['rating'] ? round(floatval($item['rating']), 1) : '';
            ?>
            <div class="popular-item">
                <span class="pop-rank"><?php echo $rank; ?></span>
                <div class="pop-thumb">
                    <a href="<?php echo esc_url($item['url']); ?>">
                        <img src="<?php echo esc_url($item['thumbnail']); ?>" alt="<?php echo esc_attr($item['title']); ?>" loading="lazy">
                    </a>
                </div>
                <div class="pop-info">
                    <h5 class="pop-title">
                        <a href="<?php echo esc_url($item['url']); ?>" title="<?php echo esc_attr($item['title']); ?>"><?php echo esc_html($item['title']); ?></a>
                    </h5>
                    <?php if (!empty($item['genres']) && !is_wp_error($item['genres'])) : ?>
                    <div class="pop-genres">
                        <?php 
                        $genre_names = wp_list_pluck($item['genres'], 'name');
                        echo esc_html(implode(', ', array_slice($genre_names, 0, 3)));
                        ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($rating_val) : ?>
                    <div class="pop-rating"><i class="fas fa-star"></i> <span><?php echo esc_html($rating_val); ?></span></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
        
        <!-- All Time List -->
        <div class="popular-list pop-content" data-content="all">
            <?php if (empty($all_data)) : ?>
            <p style="padding: 20px; text-align: center; color: var(--color-text-muted);"><?php _e('No data', 'komik-starter'); ?></p>
            <?php else : 
            $rank = 0;
            foreach ($all_data as $item) : $rank++;
                $rating_val = $item['rating'] ? round(floatval($item['rating']), 1) : '';
            ?>
            <div class="popular-item">
                <span class="pop-rank"><?php echo $rank; ?></span>
                <div class="pop-thumb">
                    <a href="<?php echo esc_url($item['url']); ?>">
                        <img src="<?php echo esc_url($item['thumbnail']); ?>" alt="<?php echo esc_attr($item['title']); ?>" loading="lazy">
                    </a>
                </div>
                <div class="pop-info">
                    <h5 class="pop-title">
                        <a href="<?php echo esc_url($item['url']); ?>" title="<?php echo esc_attr($item['title']); ?>"><?php echo esc_html($item['title']); ?></a>
                    </h5>
                    <?php if (!empty($item['genres']) && !is_wp_error($item['genres'])) : ?>
                    <div class="pop-genres">
                        <?php 
                        $genre_names = wp_list_pluck($item['genres'], 'name');
                        echo esc_html(implode(', ', array_slice($genre_names, 0, 3)));
                        ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($rating_val) : ?>
                    <div class="pop-rating"><i class="fas fa-star"></i> <span><?php echo esc_html($rating_val); ?></span></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
    
    <script>
    (function() {
        // Popular tabs switching
        var popTabs = document.querySelectorAll('.pop-tab');
        var popContents = document.querySelectorAll('.pop-content');
        
        popTabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                var target = this.getAttribute('data-tab');
                
                popTabs.forEach(function(t) { t.classList.remove('active'); });
                popContents.forEach(function(c) { c.classList.remove('active'); });
                
                this.classList.add('active');
                document.querySelector('.pop-content[data-content="' + target + '"]').classList.add('active');
            });
        });
    })();
    </script>
    <?php endif; ?>

    <!-- Genres Widget -->
    <?php
    $genres = get_terms(array(
        'taxonomy' => 'manhwa_genre',
        'hide_empty' => true,
        'orderby' => 'name',
        'order' => 'ASC',
    ));
    
    if (!empty($genres) && !is_wp_error($genres)) :
    ?>
    <div class="section genre-widget">
        <div class="releases">
            <h4><i class="fas fa-tags"></i> <?php _e('Genres', 'komik-starter'); ?></h4>
        </div>
        <ul class="genre-list">
            <?php foreach ($genres as $genre) : ?>
            <li>
                <a href="<?php echo esc_url(get_term_link($genre)); ?>">
                    <span class="genre-name"><?php echo esc_html($genre->name); ?></span>
                    <span class="genre-count"><?php echo esc_html($genre->count); ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
   
    
    <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-1')) : ?>
        <!-- Default widgets if sidebar is empty -->
    <?php endif; ?>
</div>

<script>
(function() {
    // Load sidebar bookmarks
    function loadSidebarBookmarks() {
        var container = document.getElementById('sidebar-bookmarks');
        var emptyState = document.getElementById('sidebar-bookmarks-empty');
        var footer = document.getElementById('sidebar-bookmarks-footer');
        
        if (!container) return;
        
        var bookmarks = localStorage.getItem('manhwa_bookmarks');
        bookmarks = bookmarks ? JSON.parse(bookmarks) : [];
        
        // Clear loading
        container.innerHTML = '';
        
        if (bookmarks.length === 0) {
            emptyState.style.display = 'block';
            footer.style.display = 'none';
            return;
        }
        
        emptyState.style.display = 'none';
        footer.style.display = 'block';
        
        // Show max 5 bookmarks
        var items = bookmarks.slice(0, 5);
        
        items.forEach(function(bm) {
            var item = document.createElement('div');
            item.className = 'bookmark-widget-item';
            item.innerHTML = `
                <a href="${bm.url}">
                    <div class="bwi-thumb">
                        <img src="${bm.thumbnail}" alt="${bm.title}" loading="lazy">
                    </div>
                    <div class="bwi-info">
                        <div class="bwi-title">${bm.title}</div>
                        ${bm.latestChapter ? `<div class="bwi-chapter">${bm.latestChapter}</div>` : ''}
                    </div>
                </a>
            `;
            container.appendChild(item);
        });
        
        // Update count if more than 5
        if (bookmarks.length > 5) {
            var moreLink = document.querySelector('.view-all-bookmarks');
            if (moreLink) {
                moreLink.innerHTML = '<?php _e('View All', 'komik-starter'); ?> (' + bookmarks.length + ') <i class="fas fa-arrow-right"></i>';
            }
        }
    }
    
    // Load on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadSidebarBookmarks);
    } else {
        loadSidebarBookmarks();
    }
    
    // Listen for bookmark changes
    window.addEventListener('storage', function(e) {
        if (e.key === 'manhwa_bookmarks') {
            loadSidebarBookmarks();
        }
    });
})();
</script>
