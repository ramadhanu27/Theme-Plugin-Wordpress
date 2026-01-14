<?php
/**
 * Template Name: Bookmark Page
 * Template for displaying user's bookmarked manhwa
 * Supports both localStorage (guests) and database (logged-in users)
 *
 * @package Komik_Starter
 */

defined("ABSPATH") || die("!");

// Check if user is logged in and get their bookmarks from database
$is_logged_in = is_user_logged_in();
$db_bookmarks = [];

if ($is_logged_in) {
    $user_id = get_current_user_id();
    $db_bookmark_ids = get_user_meta($user_id, 'komik_bookmarks', true);
    if (is_array($db_bookmark_ids)) {
        foreach ($db_bookmark_ids as $manhwa_id) {
            $manhwa = get_post($manhwa_id);
            if (!$manhwa || $manhwa->post_status !== 'publish') continue;
            
            $chapters = get_post_meta($manhwa_id, '_manhwa_chapters', true);
            $latest_ch = !empty($chapters) && is_array($chapters) ? $chapters[0] : null;
            $type = get_post_meta($manhwa_id, '_manhwa_type', true);
            $rating = get_post_meta($manhwa_id, '_manhwa_rating', true);
            $thumbnail = get_the_post_thumbnail_url($manhwa_id, 'medium');
            
            $db_bookmarks[] = [
                'id' => $manhwa_id,
                'title' => $manhwa->post_title,
                'url' => get_permalink($manhwa_id),
                'thumbnail' => $thumbnail ?: get_template_directory_uri() . '/assets/images/placeholder.jpg',
                'type' => $type ?: 'Manhwa',
                'rating' => $rating ?: 0,
                'latestChapter' => $latest_ch ? $latest_ch['title'] : '',
                'date' => strtotime($manhwa->post_date) * 1000,
            ];
        }
    }
}

get_header();
?>

<div class="postbody full">
    <!-- Page Header -->
    <div class="bixbox">
        <div class="releases">
            <h1><i class="fas fa-bookmark"></i> <?php _e('My Bookmarks', 'komik-starter'); ?></h1>
            <span class="vl bookmark-count"><?php echo $is_logged_in ? count($db_bookmarks) : '0'; ?> <?php _e('Series', 'komik-starter'); ?></span>
        </div>
        
        <?php if ($is_logged_in) : ?>
        <div class="bookmark-login-status">
            <i class="fas fa-check-circle"></i>
            <?php _e('Your bookmarks are synced to your account', 'komik-starter'); ?>
        </div>
        <?php else : ?>
        <div class="bookmark-login-status guest">
            <i class="fas fa-info-circle"></i>
            <?php printf(__('Bookmarks are saved locally. <a href="%s">Login</a> to sync across devices.', 'komik-starter'), home_url('/login/')); ?>
        </div>
        <?php endif; ?>
        
        <!-- Bookmark Actions -->
        <div class="bookmark-actions">
            <button type="button" class="bookmark-action-btn" id="sort-bookmarks">
                <i class="fas fa-sort"></i> <?php _e('Sort', 'komik-starter'); ?>
            </button>
            <button type="button" class="bookmark-action-btn danger" id="clear-all-bookmarks">
                <i class="fas fa-trash"></i> <?php _e('Clear All', 'komik-starter'); ?>
            </button>
        </div>
    </div>

    <!-- Bookmarks List -->
    <div class="bixbox">
        <div id="bookmark-list" class="listupd">
            <!-- Bookmarks will be loaded here via JavaScript -->
        </div>
        
        <!-- Empty State -->
        <div id="bookmark-empty" class="bookmark-empty" style="display: none;">
            <div class="empty-icon">
                <i class="far fa-bookmark"></i>
            </div>
            <h3><?php _e('No Bookmarks Yet', 'komik-starter'); ?></h3>
            <p><?php _e('Start reading and bookmark your favorite manhwa to see them here!', 'komik-starter'); ?></p>
            <a href="<?php echo get_post_type_archive_link('manhwa'); ?>" class="browse-btn">
                <i class="fas fa-book-open"></i> <?php _e('Browse Manhwa', 'komik-starter'); ?>
            </a>
        </div>
        
        <!-- Loading State -->
        <div id="bookmark-loading" class="bookmark-loading">
            <div class="loading-spinner"></div>
            <p><?php _e('Loading bookmarks...', 'komik-starter'); ?></p>
        </div>
    </div>
</div>

<style>
.bookmark-login-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(34, 197, 94, 0.15);
    color: #22c55e;
    border-radius: 20px;
    font-size: 13px;
    margin-top: 15px;
}
.bookmark-login-status.guest {
    background: rgba(59, 130, 246, 0.15);
    color: #3b82f6;
}
.bookmark-login-status a {
    color: inherit;
    text-decoration: underline;
    font-weight: 600;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookmarkList = document.getElementById('bookmark-list');
    const bookmarkEmpty = document.getElementById('bookmark-empty');
    const bookmarkLoading = document.getElementById('bookmark-loading');
    const bookmarkCount = document.querySelector('.bookmark-count');
    
    // User login status and database bookmarks
    const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    const dbBookmarks = <?php echo json_encode($db_bookmarks); ?>;
    const komikAccount = window.komikAccount || null;
    
    // Get bookmarks from localStorage
    function getLocalBookmarks() {
        const bookmarks = localStorage.getItem('manhwa_bookmarks');
        return bookmarks ? JSON.parse(bookmarks) : [];
    }
    
    // Save bookmarks to localStorage
    function saveLocalBookmarks(bookmarks) {
        localStorage.setItem('manhwa_bookmarks', JSON.stringify(bookmarks));
    }
    
    // Get all bookmarks (database + localStorage merged, or just localStorage)
    function getAllBookmarks() {
        if (isLoggedIn) {
            // Use database bookmarks for logged-in users
            return dbBookmarks;
        } else {
            // Use localStorage for guests
            return getLocalBookmarks();
        }
    }
    
    // Remove bookmark
    function removeBookmark(id) {
        if (isLoggedIn && komikAccount) {
            // Remove from database via AJAX
            fetch(komikAccount.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=komik_remove_bookmark&nonce=' + encodeURIComponent(komikAccount.nonce) + '&manhwa_id=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove from local dbBookmarks array
                    const index = dbBookmarks.findIndex(b => b.id === id);
                    if (index > -1) {
                        dbBookmarks.splice(index, 1);
                    }
                    loadBookmarks();
                }
            });
        } else {
            // Remove from localStorage
            let bookmarks = getLocalBookmarks();
            bookmarks = bookmarks.filter(b => b.id !== id);
            saveLocalBookmarks(bookmarks);
            loadBookmarks();
        }
    }
    
    // Clear all bookmarks
    function clearAllBookmarks() {
        if (!confirm('<?php _e('Are you sure you want to remove all bookmarks?', 'komik-starter'); ?>')) {
            return;
        }
        
        if (isLoggedIn && komikAccount) {
            // Clear all from database
            const promises = dbBookmarks.map(b => {
                return fetch(komikAccount.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=komik_remove_bookmark&nonce=' + encodeURIComponent(komikAccount.nonce) + '&manhwa_id=' + b.id
                });
            });
            
            Promise.all(promises).then(() => {
                dbBookmarks.length = 0;
                loadBookmarks();
            });
        } else {
            localStorage.removeItem('manhwa_bookmarks');
            loadBookmarks();
        }
    }
    
    // Create bookmark card HTML
    function createBookmarkCard(bookmark) {
        const card = document.createElement('div');
        card.className = 'bs styletere bookmark-item';
        card.dataset.id = bookmark.id;
        card.dataset.date = bookmark.date || Date.now();
        card.dataset.title = bookmark.title || '';
        
        const typeClass = bookmark.type ? bookmark.type.toLowerCase() : 'manhwa';
        const rating = bookmark.rating ? parseFloat(bookmark.rating) : 0;
        const starsHtml = generateStars(rating);
        
        card.innerHTML = `
            <div class="bsx">
                <a href="${bookmark.url}">
                    <div class="limit">
                        <img src="${bookmark.thumbnail}" alt="${bookmark.title}" loading="lazy">
                        ${bookmark.type ? `<span class="type-badge ${typeClass}">${bookmark.type.toUpperCase()}</span>` : ''}
                    </div>
                </a>
                <div class="tt"><a href="${bookmark.url}">${bookmark.title}</a></div>
                ${bookmark.latestChapter ? `<div class="chapter-text">${bookmark.latestChapter}</div>` : ''}
                <div class="rating-row">
                    <div class="numscore">
                        ${starsHtml}
                        ${rating ? `<span class="score">${rating}</span>` : ''}
                    </div>
                </div>
                <button type="button" class="remove-bookmark-btn" data-id="${bookmark.id}" title="<?php _e('Remove Bookmark', 'komik-starter'); ?>">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        return card;
    }
    
    // Generate star rating HTML
    function generateStars(rating) {
        const normalizedRating = rating > 5 ? rating / 2 : rating;
        const fullStars = Math.floor(normalizedRating);
        const halfStar = (normalizedRating - fullStars) >= 0.5 ? 1 : 0;
        let html = '';
        
        for (let i = 1; i <= 5; i++) {
            if (i <= fullStars) {
                html += '<i class="fas fa-star"></i>';
            } else if (i === fullStars + 1 && halfStar) {
                html += '<i class="fas fa-star-half-alt"></i>';
            } else {
                html += '<i class="far fa-star"></i>';
            }
        }
        
        return html;
    }
    
    // Sort bookmarks
    let sortOrder = 'newest'; // newest, oldest, title-asc, title-desc
    function sortBookmarks() {
        const sortOptions = ['newest', 'oldest', 'title-asc', 'title-desc'];
        const sortLabels = {
            'newest': '<?php _e('Newest First', 'komik-starter'); ?>',
            'oldest': '<?php _e('Oldest First', 'komik-starter'); ?>',
            'title-asc': '<?php _e('Title A-Z', 'komik-starter'); ?>',
            'title-desc': '<?php _e('Title Z-A', 'komik-starter'); ?>'
        };
        
        const currentIndex = sortOptions.indexOf(sortOrder);
        sortOrder = sortOptions[(currentIndex + 1) % sortOptions.length];
        
        const sortBtn = document.getElementById('sort-bookmarks');
        if (sortBtn) {
            sortBtn.innerHTML = `<i class="fas fa-sort"></i> ${sortLabels[sortOrder]}`;
        }
        
        loadBookmarks();
    }
    
    // Load and display bookmarks
    function loadBookmarks() {
        let bookmarks = getAllBookmarks();
        
        // Sort bookmarks
        switch (sortOrder) {
            case 'newest':
                bookmarks.sort((a, b) => (b.date || 0) - (a.date || 0));
                break;
            case 'oldest':
                bookmarks.sort((a, b) => (a.date || 0) - (b.date || 0));
                break;
            case 'title-asc':
                bookmarks.sort((a, b) => (a.title || '').localeCompare(b.title || ''));
                break;
            case 'title-desc':
                bookmarks.sort((a, b) => (b.title || '').localeCompare(a.title || ''));
                break;
        }
        
        // Hide loading
        bookmarkLoading.style.display = 'none';
        
        // Update count
        bookmarkCount.textContent = `${bookmarks.length} <?php _e('Series', 'komik-starter'); ?>`;
        
        if (bookmarks.length === 0) {
            bookmarkList.style.display = 'none';
            bookmarkEmpty.style.display = 'flex';
            return;
        }
        
        bookmarkList.style.display = 'grid';
        bookmarkEmpty.style.display = 'none';
        bookmarkList.innerHTML = '';
        
        bookmarks.forEach(bookmark => {
            const card = createBookmarkCard(bookmark);
            bookmarkList.appendChild(card);
        });
        
        // Add remove button listeners
        document.querySelectorAll('.remove-bookmark-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const id = parseInt(this.dataset.id);
                removeBookmark(id);
            });
        });
    }
    
    // Event listeners
    document.getElementById('clear-all-bookmarks').addEventListener('click', clearAllBookmarks);
    document.getElementById('sort-bookmarks').addEventListener('click', sortBookmarks);
    
    // Initial load
    loadBookmarks();
});
</script>

<?php get_footer(); ?>
