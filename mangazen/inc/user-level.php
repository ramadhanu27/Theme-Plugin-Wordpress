<?php
/**
 * User Level System
 * Handles XP calculation and level badges
 * 
 * @package Komik_Starter
 */

if (!defined('ABSPATH')) {
    exit;
}

class Komik_User_Level {
    
    // Level configuration
    private static $levels = [
        'newbie'   => ['min_xp' => 0,    'name' => 'Newbie',   'color' => '#22c55e'],
        'reader'   => ['min_xp' => 50,   'name' => 'Reader',   'color' => '#3b82f6'],
        'bookworm' => ['min_xp' => 150,  'name' => 'Bookworm', 'color' => '#8b5cf6'],
        'expert'   => ['min_xp' => 350,  'name' => 'Expert',   'color' => '#f59e0b'],
        'master'   => ['min_xp' => 700,  'name' => 'Master',   'color' => '#ef4444'],
        'legend'   => ['min_xp' => 1500, 'name' => 'Legend',   'color' => '#ec4899'],
    ];
    
    // XP rewards for activities
    private static $xp_rewards = [
        'comment'        => 5,  // Post a comment (max 10 per day)
        'reply'          => 3,  // Reply to a comment (max 5 per day)
        'chapter_read'   => 2,  // Read a chapter
        'bookmark'       => 3,  // Bookmark a manhwa
        'daily_login'    => 1,  // Per day since registration
    ];
    
    // Anti-spam limits
    private static $daily_limits = [
        'comment' => 10,  // Max 10 comments per day for XP
        'reply'   => 5,   // Max 5 replies per day for XP
    ];
    
    /**
     * Check if user is admin
     */
    public static function is_admin($user_id) {
        $user = get_userdata($user_id);
        if ($user && in_array('administrator', $user->roles)) {
            return true;
        }
        return false;
    }
    
    /**
     * Check if user is banned from XP
     */
    public static function is_banned($user_id) {
        return (bool) get_user_meta($user_id, 'komik_xp_banned', true);
    }
    
    /**
     * Ban user from earning XP
     */
    public static function ban_user($user_id, $reason = '') {
        update_user_meta($user_id, 'komik_xp_banned', true);
        update_user_meta($user_id, 'komik_xp_banned_reason', $reason);
        update_user_meta($user_id, 'komik_xp_banned_date', current_time('mysql'));
    }
    
    /**
     * Unban user
     */
    public static function unban_user($user_id) {
        delete_user_meta($user_id, 'komik_xp_banned');
        delete_user_meta($user_id, 'komik_xp_banned_reason');
        delete_user_meta($user_id, 'komik_xp_banned_date');
    }
    
    /**
     * Calculate user's total XP with anti-spam protection
     */
    public static function calculate_xp($user_id) {
        if (!$user_id) return 0;
        
        // Admins get max XP
        if (self::is_admin($user_id)) {
            return 9999;
        }
        
        // Banned users get 0 XP
        if (self::is_banned($user_id)) {
            return 0;
        }
        
        $xp = 0;
        
        // XP from comments - count UNIQUE posts only (1 comment per post)
        // and limit daily comments
        $all_comments = get_comments([
            'user_id' => $user_id,
            'status' => 'approve',
            'fields' => 'ids',
        ]);
        
        // Get unique posts commented on
        $unique_posts = [];
        $daily_counts = [];
        
        foreach ($all_comments as $comment_id) {
            $comment = get_comment($comment_id);
            if (!$comment) continue;
            
            $post_id = $comment->comment_post_ID;
            $comment_date = date('Y-m-d', strtotime($comment->comment_date));
            
            // Track daily counts
            if (!isset($daily_counts[$comment_date])) {
                $daily_counts[$comment_date] = ['comment' => 0, 'reply' => 0];
            }
            
            // Check if already commented on this post
            if (!isset($unique_posts[$post_id])) {
                $unique_posts[$post_id] = true;
                
                // Check daily limit
                if ($comment->comment_parent == 0) {
                    // Regular comment
                    if ($daily_counts[$comment_date]['comment'] < self::$daily_limits['comment']) {
                        $xp += self::$xp_rewards['comment'];
                        $daily_counts[$comment_date]['comment']++;
                    }
                } else {
                    // Reply
                    if ($daily_counts[$comment_date]['reply'] < self::$daily_limits['reply']) {
                        $xp += self::$xp_rewards['reply'];
                        $daily_counts[$comment_date]['reply']++;
                    }
                }
            }
        }
        
        // XP from reading history
        $history = get_user_meta($user_id, 'komik_reading_history', true);
        if (is_array($history)) {
            $xp += count($history) * self::$xp_rewards['chapter_read'];
        }
        
        // XP from bookmarks
        $bookmarks = get_user_meta($user_id, 'komik_bookmarks', true);
        if (is_array($bookmarks)) {
            $xp += count($bookmarks) * self::$xp_rewards['bookmark'];
        }
        
        // XP from account age (days since registration)
        $user = get_userdata($user_id);
        if ($user) {
            $registered = strtotime($user->user_registered);
            $days = floor((time() - $registered) / 86400);
            $xp += $days * self::$xp_rewards['daily_login'];
        }
        
        return $xp;
    }
    
    /**
     * Get user's current level based on XP
     */
    public static function get_level($user_id) {
        // Admins are always Legend
        if (self::is_admin($user_id)) {
            return 'legend';
        }
        
        $xp = self::calculate_xp($user_id);
        $current_level = 'newbie';
        
        foreach (self::$levels as $key => $level) {
            if ($xp >= $level['min_xp']) {
                $current_level = $key;
            }
        }
        
        return $current_level;
    }
    
    /**
     * Get level info
     */
    public static function get_level_info($level_key) {
        return self::$levels[$level_key] ?? self::$levels['newbie'];
    }
    
    /**
     * Get all levels
     */
    public static function get_all_levels() {
        return self::$levels;
    }
    
    /**
     * Get XP needed for next level
     */
    public static function get_next_level_xp($user_id) {
        // Admins are already max level
        if (self::is_admin($user_id)) {
            return null;
        }
        
        $xp = self::calculate_xp($user_id);
        $current_level = self::get_level($user_id);
        
        $levels_array = array_keys(self::$levels);
        $current_index = array_search($current_level, $levels_array);
        
        // If at max level
        if ($current_index >= count($levels_array) - 1) {
            return null;
        }
        
        $next_level = $levels_array[$current_index + 1];
        return self::$levels[$next_level];
    }
    
    /**
     * Get progress to next level (percentage)
     */
    public static function get_level_progress($user_id) {
        $xp = self::calculate_xp($user_id);
        $current_level = self::get_level($user_id);
        $current_min = self::$levels[$current_level]['min_xp'];
        
        $next = self::get_next_level_xp($user_id);
        
        if (!$next) {
            return 100; // Max level
        }
        
        $xp_in_level = $xp - $current_min;
        $xp_needed = $next['min_xp'] - $current_min;
        
        return min(100, round(($xp_in_level / $xp_needed) * 100));
    }
    
    /**
     * Get level badge HTML
     */
    public static function get_badge_html($user_id, $size = 'small') {
        if (!$user_id) return '';
        
        $sizes = [
            'tiny'   => 16,
            'small'  => 20,
            'medium' => 28,
            'large'  => 40,
        ];
        
        $px = $sizes[$size] ?? 20;
        
        // Check if user is banned
        if (self::is_banned($user_id)) {
            return sprintf(
                '<span class="user-level-badge level-banned" title="%s">
                    <i class="fas fa-ban" style="font-size:%dpx;color:#ef4444;"></i>
                    <span class="level-label" style="color:#ef4444;">%s</span>
                </span>',
                esc_attr__('Banned', 'komik-starter'),
                $px,
                esc_html__('BANNED', 'komik-starter')
            );
        }
        
        $level = self::get_level($user_id);
        $info = self::get_level_info($level);
        $badge_url = get_template_directory_uri() . '/assets/images/badges/' . $level . '.png';
        
        return sprintf(
            '<span class="user-level-badge level-%s" title="%s">
                <img src="%s" alt="%s" width="%d" height="%d" />
                <span class="level-label">%s</span>
            </span>',
            esc_attr($level),
            esc_attr($info['name']),
            esc_url($badge_url),
            esc_attr($info['name']),
            $px,
            $px,
            esc_html($info['name'])
        );
    }
    
    /**
     * Get full stats for profile
     */
    public static function get_full_stats($user_id) {
        return [
            'xp' => self::calculate_xp($user_id),
            'level' => self::get_level($user_id),
            'level_info' => self::get_level_info(self::get_level($user_id)),
            'next_level' => self::get_next_level_xp($user_id),
            'progress' => self::get_level_progress($user_id),
            'all_levels' => self::$levels,
            'is_banned' => self::is_banned($user_id),
        ];
    }
}

/**
 * Helper function to get user level badge
 */
function komik_get_level_badge($user_id = null, $size = 'small') {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    return Komik_User_Level::get_badge_html($user_id, $size);
}

/**
 * Helper function to get user level stats
 */
function komik_get_level_stats($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    return Komik_User_Level::get_full_stats($user_id);
}

/**
 * Helper function to ban user from XP
 */
function komik_ban_user_xp($user_id, $reason = 'Spam') {
    Komik_User_Level::ban_user($user_id, $reason);
}

/**
 * Helper function to unban user from XP
 */
function komik_unban_user_xp($user_id) {
    Komik_User_Level::unban_user($user_id);
}

/**
 * Helper function to check if user is banned
 */
function komik_is_user_banned($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    return Komik_User_Level::is_banned($user_id);
}

/**
 * Add XP Ban action to WordPress user admin
 */
add_filter('user_row_actions', function($actions, $user) {
    if (current_user_can('manage_options') && !in_array('administrator', $user->roles)) {
        if (Komik_User_Level::is_banned($user->ID)) {
            $actions['unban_xp'] = '<a href="' . wp_nonce_url(admin_url('users.php?action=unban_xp&user=' . $user->ID), 'unban_xp_' . $user->ID) . '" style="color:#46b450;">Unban XP</a>';
        } else {
            $actions['ban_xp'] = '<a href="' . wp_nonce_url(admin_url('users.php?action=ban_xp&user=' . $user->ID), 'ban_xp_' . $user->ID) . '" style="color:#dc3232;">Ban XP</a>';
        }
    }
    return $actions;
}, 10, 2);

/**
 * Handle XP Ban/Unban actions
 */
add_action('admin_init', function() {
    if (!current_user_can('manage_options')) return;
    
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $user_id = isset($_GET['user']) ? intval($_GET['user']) : 0;
    
    if ($action === 'ban_xp' && $user_id && wp_verify_nonce($_GET['_wpnonce'], 'ban_xp_' . $user_id)) {
        Komik_User_Level::ban_user($user_id, 'Banned by admin');
        wp_redirect(admin_url('users.php?banned_xp=1'));
        exit;
    }
    
    if ($action === 'unban_xp' && $user_id && wp_verify_nonce($_GET['_wpnonce'], 'unban_xp_' . $user_id)) {
        Komik_User_Level::unban_user($user_id);
        wp_redirect(admin_url('users.php?unbanned_xp=1'));
        exit;
    }
});

/**
 * Show admin notice after ban/unban
 */
add_action('admin_notices', function() {
    if (isset($_GET['banned_xp'])) {
        echo '<div class="notice notice-warning is-dismissible"><p>User has been banned from earning XP.</p></div>';
    }
    if (isset($_GET['unbanned_xp'])) {
        echo '<div class="notice notice-success is-dismissible"><p>User XP ban has been removed.</p></div>';
    }
});
