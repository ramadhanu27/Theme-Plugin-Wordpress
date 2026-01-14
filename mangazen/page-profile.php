<?php
/**
 * Template Name: Profile Page
 * User Profile/Dashboard Page
 * 
 * @package Komik_Starter
 */

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login/'));
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$reading_stats = komik_get_reading_stats($user_id);
$joined_date = get_user_meta($user_id, 'komik_joined', true);
if (!$joined_date) {
    $joined_date = $current_user->user_registered;
}

// Get bookmarks
$bookmarks = get_user_meta($user_id, 'komik_bookmarks', true);
if (!is_array($bookmarks)) {
    $bookmarks = [];
}

// Get reading history
$reading_history = get_user_meta($user_id, 'komik_reading_history', true);
if (!is_array($reading_history)) {
    $reading_history = [];
}

get_header();

// Get level stats
$level_stats = komik_get_level_stats($user_id);
?>

<div class="profile-page">
    <div class="profile-container">
        <!-- Profile Sidebar -->
        <aside class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-avatar">
                    <?php echo komik_get_user_avatar($user_id, 120); ?>
                    <button type="button" id="change-avatar-btn" class="change-avatar" title="<?php esc_attr_e('Change Avatar', 'komik-starter'); ?>">
                        <i class="fas fa-camera"></i>
                    </button>
                    <input type="file" id="avatar-input" accept="image/*" style="display: none;">
                </div>
                <h2 class="profile-name"><?php echo esc_html($current_user->display_name); ?></h2>
                <p class="profile-username">@<?php echo esc_html($current_user->user_login); ?></p>
                <p class="profile-joined">
                    <i class="fas fa-calendar-alt"></i>
                    <?php printf(__('Member since %s', 'komik-starter'), date_i18n('F Y', strtotime($joined_date))); ?>
                </p>
            </div>
            
            <!-- User Level Card -->
            <div class="profile-level-card<?php echo !empty($level_stats['is_banned']) ? ' banned' : ''; ?>">
                <div class="level-header">
                    <div class="level-badge-large">
                        <?php echo komik_get_level_badge($user_id, 'large'); ?>
                    </div>
                    <div class="level-info">
                        <?php if (!empty($level_stats['is_banned'])) : ?>
                        <span class="level-name" style="color:#ef4444;"><?php _e('BANNED', 'komik-starter'); ?></span>
                        <span class="level-xp" style="color:#ef4444;"><?php _e('XP Earning Disabled', 'komik-starter'); ?></span>
                        <?php else : ?>
                        <span class="level-name"><?php echo esc_html($level_stats['level_info']['name']); ?></span>
                        <span class="level-xp"><?php echo number_format($level_stats['xp']); ?> XP</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($level_stats['is_banned'])) : ?>
                <div class="level-banned-message" style="text-align:center;padding:15px;background:rgba(239,68,68,0.1);border-radius:8px;margin-top:10px;">
                    <i class="fas fa-ban" style="font-size:24px;color:#ef4444;margin-bottom:8px;display:block;"></i>
                    <p style="margin:0;color:#ef4444;font-size:13px;"><?php _e('Your account has been banned from earning XP due to spam activity.', 'komik-starter'); ?></p>
                </div>
                <?php elseif ($level_stats['next_level']) : ?>
                <div class="level-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo esc_attr($level_stats['progress']); ?>%;"></div>
                    </div>
                    <div class="progress-text">
                        <span><?php echo esc_html($level_stats['progress']); ?>%</span>
                        <span><?php printf(__('Next: %s', 'komik-starter'), $level_stats['next_level']['name']); ?></span>
                    </div>
                </div>
                <?php else : ?>
                <div class="level-max">
                    <i class="fas fa-crown"></i>
                    <?php _e('Maximum Level Reached!', 'komik-starter'); ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-value"><?php echo esc_html($reading_stats['bookmarks']); ?></span>
                    <span class="stat-label"><?php _e('Bookmarks', 'komik-starter'); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo esc_html($reading_stats['chapters_read']); ?></span>
                    <span class="stat-label"><?php _e('Chapters Read', 'komik-starter'); ?></span>
                </div>
            </div>
            
            <nav class="profile-nav">
                <a href="#overview" class="nav-item active" data-tab="overview">
                    <i class="fas fa-home"></i>
                    <?php _e('Overview', 'komik-starter'); ?>
                </a>
                <a href="#bookmarks" class="nav-item" data-tab="bookmarks">
                    <i class="fas fa-bookmark"></i>
                    <?php _e('Bookmarks', 'komik-starter'); ?>
                </a>
                <a href="#history" class="nav-item" data-tab="history">
                    <i class="fas fa-history"></i>
                    <?php _e('Reading History', 'komik-starter'); ?>
                </a>
                <a href="#settings" class="nav-item" data-tab="settings">
                    <i class="fas fa-cog"></i>
                    <?php _e('Settings', 'komik-starter'); ?>
                </a>
                <a href="<?php echo wp_logout_url(home_url('/')); ?>" class="nav-item logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <?php _e('Logout', 'komik-starter'); ?>
                </a>
            </nav>
        </aside>
        
        <!-- Profile Content -->
        <main class="profile-content">
            <!-- Overview Tab -->
            <section id="overview" class="profile-tab active">
                <h2 class="tab-title"><?php _e('Dashboard', 'komik-starter'); ?></h2>
                
                <div class="overview-grid">
                    <div class="overview-card">
                        <div class="card-icon"><i class="fas fa-bookmark"></i></div>
                        <div class="card-content">
                            <h3><?php echo esc_html($reading_stats['bookmarks']); ?></h3>
                            <p><?php _e('Bookmarked Series', 'komik-starter'); ?></p>
                        </div>
                    </div>
                    <div class="overview-card">
                        <div class="card-icon"><i class="fas fa-book-reader"></i></div>
                        <div class="card-content">
                            <h3><?php echo esc_html($reading_stats['chapters_read']); ?></h3>
                            <p><?php _e('Chapters Read', 'komik-starter'); ?></p>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($reading_history)) : 
                    $recent_history = array_slice($reading_history, 0, 5);
                ?>
                <div class="recent-section">
                    <h3><?php _e('Continue Reading', 'komik-starter'); ?></h3>
                    <div class="recent-list">
                        <?php foreach ($recent_history as $item) : 
                            $manhwa = get_post($item['manhwa_id']);
                            if (!$manhwa) continue;
                        ?>
                        <div class="recent-item">
                            <a href="<?php echo get_permalink($item['manhwa_id']); ?>" class="recent-thumb">
                                <?php echo get_the_post_thumbnail($item['manhwa_id'], 'thumbnail'); ?>
                            </a>
                            <div class="recent-info">
                                <h4><a href="<?php echo get_permalink($item['manhwa_id']); ?>"><?php echo esc_html($manhwa->post_title); ?></a></h4>
                                <p><?php printf(__('Chapter %s', 'komik-starter'), esc_html($item['chapter'])); ?></p>
                                <a href="<?php echo esc_url($item['chapter_url']); ?>" class="continue-btn">
                                    <?php _e('Continue', 'komik-starter'); ?> <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else : ?>
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <h3><?php _e('No reading history yet', 'komik-starter'); ?></h3>
                    <p><?php _e('Start reading some manhwa to track your progress!', 'komik-starter'); ?></p>
                    <a href="<?php echo get_post_type_archive_link('manhwa'); ?>" class="btn-primary"><?php _e('Browse Manhwa', 'komik-starter'); ?></a>
                </div>
                <?php endif; ?>
            </section>
            
            <!-- Bookmarks Tab -->
            <section id="bookmarks" class="profile-tab">
                <h2 class="tab-title"><?php _e('My Bookmarks', 'komik-starter'); ?></h2>
                
                <?php if (!empty($bookmarks)) : ?>
                <div class="bookmark-grid">
                    <?php foreach ($bookmarks as $bookmark_id) : 
                        $manhwa = get_post($bookmark_id);
                        if (!$manhwa || $manhwa->post_status !== 'publish') continue;
                        $chapters = get_post_meta($bookmark_id, '_manhwa_chapters', true);
                        $latest_ch = !empty($chapters) && is_array($chapters) ? $chapters[0] : null;
                    ?>
                    <div class="bookmark-item" data-id="<?php echo esc_attr($bookmark_id); ?>">
                        <a href="<?php echo get_permalink($bookmark_id); ?>" class="bookmark-thumb">
                            <?php echo get_the_post_thumbnail($bookmark_id, 'medium'); ?>
                        </a>
                        <div class="bookmark-info">
                            <h4><a href="<?php echo get_permalink($bookmark_id); ?>"><?php echo esc_html($manhwa->post_title); ?></a></h4>
                            <?php if ($latest_ch) : ?>
                            <p class="latest-ch"><?php echo esc_html($latest_ch['title']); ?></p>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="remove-bookmark" data-id="<?php echo esc_attr($bookmark_id); ?>" title="<?php esc_attr_e('Remove Bookmark', 'komik-starter'); ?>">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else : ?>
                <div class="empty-state">
                    <i class="fas fa-bookmark"></i>
                    <h3><?php _e('No bookmarks yet', 'komik-starter'); ?></h3>
                    <p><?php _e('Bookmark your favorite manhwa to find them easily!', 'komik-starter'); ?></p>
                    <a href="<?php echo get_post_type_archive_link('manhwa'); ?>" class="btn-primary"><?php _e('Browse Manhwa', 'komik-starter'); ?></a>
                </div>
                <?php endif; ?>
            </section>
            
            <!-- History Tab -->
            <section id="history" class="profile-tab">
                <h2 class="tab-title"><?php _e('Reading History', 'komik-starter'); ?></h2>
                
                <?php if (!empty($reading_history)) : ?>
                <div class="history-list">
                    <?php foreach ($reading_history as $item) : 
                        $manhwa = get_post($item['manhwa_id']);
                        if (!$manhwa) continue;
                    ?>
                    <div class="history-item">
                        <a href="<?php echo get_permalink($item['manhwa_id']); ?>" class="history-thumb">
                            <?php echo get_the_post_thumbnail($item['manhwa_id'], 'thumbnail'); ?>
                        </a>
                        <div class="history-info">
                            <h4><a href="<?php echo get_permalink($item['manhwa_id']); ?>"><?php echo esc_html($manhwa->post_title); ?></a></h4>
                            <p class="history-chapter"><?php printf(__('Chapter %s', 'komik-starter'), esc_html($item['chapter'])); ?></p>
                            <p class="history-time"><?php echo human_time_diff(strtotime($item['read_at']), current_time('timestamp')) . ' ' . __('ago', 'komik-starter'); ?></p>
                        </div>
                        <a href="<?php echo esc_url($item['chapter_url']); ?>" class="continue-btn">
                            <i class="fas fa-play"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else : ?>
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <h3><?php _e('No reading history', 'komik-starter'); ?></h3>
                    <p><?php _e('Your reading history will appear here.', 'komik-starter'); ?></p>
                </div>
                <?php endif; ?>
            </section>
            
            <!-- Settings Tab -->
            <section id="settings" class="profile-tab">
                <h2 class="tab-title"><?php _e('Account Settings', 'komik-starter'); ?></h2>
                
                <!-- Profile Settings -->
                <div class="settings-section">
                    <h3><i class="fas fa-user"></i> <?php _e('Profile Information', 'komik-starter'); ?></h3>
                    <form id="profile-form" class="settings-form">
                        <div class="form-message" id="profile-message"></div>
                        
                        <div class="form-group">
                            <label for="display_name"><?php _e('Display Name', 'komik-starter'); ?></label>
                            <input type="text" id="display_name" name="display_name" value="<?php echo esc_attr($current_user->display_name); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="user_email"><?php _e('Email Address', 'komik-starter'); ?></label>
                            <input type="email" id="user_email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="user_bio"><?php _e('Bio', 'komik-starter'); ?></label>
                            <textarea id="user_bio" name="bio" rows="3" placeholder="<?php esc_attr_e('Tell us about yourself...', 'komik-starter'); ?>"><?php echo esc_textarea($current_user->description); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn-submit">
                            <span class="btn-text"><?php _e('Save Changes', 'komik-starter'); ?></span>
                            <span class="btn-loading"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </form>
                </div>
                
                <!-- Password Settings -->
                <div class="settings-section">
                    <h3><i class="fas fa-lock"></i> <?php _e('Change Password', 'komik-starter'); ?></h3>
                    <form id="password-form" class="settings-form">
                        <div class="form-message" id="password-message"></div>
                        
                        <div class="form-group">
                            <label for="current_password"><?php _e('Current Password', 'komik-starter'); ?></label>
                            <div class="password-input">
                                <input type="password" id="current_password" name="current_password" required>
                                <button type="button" class="toggle-password"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password"><?php _e('New Password', 'komik-starter'); ?></label>
                                <div class="password-input">
                                    <input type="password" id="new_password" name="new_password" required minlength="6">
                                    <button type="button" class="toggle-password"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password"><?php _e('Confirm New Password', 'komik-starter'); ?></label>
                                <div class="password-input">
                                    <input type="password" id="confirm_password" name="confirm_password" required>
                                    <button type="button" class="toggle-password"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-submit">
                            <span class="btn-text"><?php _e('Update Password', 'komik-starter'); ?></span>
                            <span class="btn-loading"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </form>
                </div>
            </section>
        </main>
    </div>
</div>

<?php get_footer(); ?>
