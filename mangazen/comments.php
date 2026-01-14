<?php
/**
 * Comments Template with Reply Support and Sorting
 *
 * @package Komik_Starter
 */

// Don't show if password is required
if (post_password_required()) {
    return;
}

// Get current sort order
$current_sort = isset($_GET['comment_sort']) ? sanitize_text_field($_GET['comment_sort']) : 'newest';
$valid_sorts = ['newest', 'oldest', 'popular'];
if (!in_array($current_sort, $valid_sorts)) {
    $current_sort = 'newest';
}
?>

<div id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <div class="comments-header-bar">
            <h3 class="comments-title">
                <i class="fas fa-comments"></i>
                <?php
                $comment_count = get_comments_number();
                if ($comment_count === '1') {
                    printf(__('1 Comment', 'komik-starter'));
                } else {
                    printf(
                        _n(
                            '%1$s Comment',
                            '%1$s Comments',
                            $comment_count,
                            'komik-starter'
                        ),
                        number_format_i18n($comment_count)
                    );
                }
                ?>
            </h3>
            
            <!-- Comment Sort Tabs -->
            <div class="comment-sort-tabs">
                <button type="button" class="sort-tab <?php echo $current_sort === 'newest' ? 'active' : ''; ?>" data-sort="newest">
                    <i class="fas fa-clock"></i>
                    <span><?php _e('Newest', 'komik-starter'); ?></span>
                </button>
                <button type="button" class="sort-tab <?php echo $current_sort === 'oldest' ? 'active' : ''; ?>" data-sort="oldest">
                    <i class="fas fa-history"></i>
                    <span><?php _e('Oldest', 'komik-starter'); ?></span>
                </button>
                <button type="button" class="sort-tab <?php echo $current_sort === 'popular' ? 'active' : ''; ?>" data-sort="popular">
                    <i class="fas fa-fire"></i>
                    <span><?php _e('Popular', 'komik-starter'); ?></span>
                </button>
            </div>
        </div>

        <ol class="comment-list" id="comment-list">
            <?php
            // Set order based on sort
            $order = ($current_sort === 'oldest') ? 'ASC' : 'DESC';
            
            // For popular sort, we'll use a custom query
            if ($current_sort === 'popular') {
                // Get comments ordered by reply count
                $args = array(
                    'post_id' => get_the_ID(),
                    'status' => 'approve',
                    'parent' => 0,
                    'orderby' => 'comment_date',
                    'order' => 'DESC',
                );
                
                $parent_comments = get_comments($args);
                
                // Sort by number of replies
                usort($parent_comments, function($a, $b) {
                    $a_replies = get_comments(array(
                        'parent' => $a->comment_ID,
                        'count' => true,
                    ));
                    $b_replies = get_comments(array(
                        'parent' => $b->comment_ID,
                        'count' => true,
                    ));
                    return $b_replies - $a_replies;
                });
                
                // Display sorted comments
                foreach ($parent_comments as $comment) {
                    $GLOBALS['comment'] = $comment;
                    komik_threaded_comment($comment, array(
                        'style' => 'ol',
                        'short_ping' => true,
                        'avatar_size' => 50,
                        'max_depth' => 5,
                        'reply_text' => '<i class="fas fa-reply"></i> ' . __('Reply', 'komik-starter'),
                    ), 1);
                    
                    // Get and display replies
                    $replies = get_comments(array(
                        'parent' => $comment->comment_ID,
                        'status' => 'approve',
                        'orderby' => 'comment_date',
                        'order' => 'ASC',
                    ));
                    
                    if (!empty($replies)) {
                        echo '<ol class="children">';
                        foreach ($replies as $reply) {
                            $GLOBALS['comment'] = $reply;
                            komik_threaded_comment($reply, array(
                                'style' => 'ol',
                                'short_ping' => true,
                                'avatar_size' => 50,
                                'max_depth' => 5,
                                'reply_text' => '<i class="fas fa-reply"></i> ' . __('Reply', 'komik-starter'),
                            ), 2);
                        }
                        echo '</ol>';
                    }
                    
                    echo '</li>';
                }
            } else {
                // Standard WordPress comment display
                wp_list_comments(array(
                    'style'       => 'ol',
                    'short_ping'  => true,
                    'avatar_size' => 50,
                    'max_depth'   => 5,
                    'callback'    => 'komik_threaded_comment',
                    'reply_text'  => '<i class="fas fa-reply"></i> ' . __('Reply', 'komik-starter'),
                    'reverse_top_level' => ($current_sort === 'newest'),
                ));
            }
            ?>
        </ol>

        <?php
        the_comments_navigation(array(
            'prev_text' => '<i class="fas fa-chevron-left"></i> ' . __('Older Comments', 'komik-starter'),
            'next_text' => __('Newer Comments', 'komik-starter') . ' <i class="fas fa-chevron-right"></i>',
        ));
        ?>

    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments"><i class="fas fa-lock"></i> <?php _e('Comments are closed.', 'komik-starter'); ?></p>
    <?php endif; ?>

    <?php
    // Custom comment form
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');
    
    $fields = array(
        'author' => '<div class="comment-form-fields"><div class="comment-form-author">
            <label for="author"><i class="fas fa-user"></i> ' . __('Name', 'komik-starter') . ($req ? ' <span class="required">*</span>' : '') . '</label>
            <input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" placeholder="' . esc_attr__('Your name', 'komik-starter') . '"' . $aria_req . ' />
        </div>',
        'email' => '<div class="comment-form-email">
            <label for="email"><i class="fas fa-envelope"></i> ' . __('Email', 'komik-starter') . ($req ? ' <span class="required">*</span>' : '') . '</label>
            <input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" placeholder="' . esc_attr__('Your email', 'komik-starter') . '"' . $aria_req . ' />
        </div></div>',
        'url' => '',
        'cookies' => '<p class="comment-form-cookies-consent">
            <input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . (empty($commenter['comment_author_email']) ? '' : ' checked="checked"') . ' />
            <label for="wp-comment-cookies-consent">' . __('Save my name and email for the next comment.', 'komik-starter') . '</label>
        </p>',
    );
    
    comment_form(array(
        'title_reply'          => '<i class="fas fa-edit"></i> ' . __('Leave a Comment', 'komik-starter'),
        'title_reply_to'       => '<i class="fas fa-reply"></i> ' . __('Reply to %s', 'komik-starter'),
        'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
        'title_reply_after'    => '</h3>',
        'cancel_reply_before'  => ' <small>',
        'cancel_reply_after'   => '</small>',
        'cancel_reply_link'    => '<i class="fas fa-times"></i> ' . __('Cancel', 'komik-starter'),
        'label_submit'         => __('Post Comment', 'komik-starter'),
        'class_form'           => 'comment-form',
        'class_submit'         => 'submit-btn',
        'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s"><i class="fas fa-paper-plane"></i> %4$s</button>',
        'submit_field'         => '<p class="form-submit">%1$s %2$s</p>',
        'comment_field'        => '<div class="comment-form-comment">
            <label for="comment"><i class="fas fa-comment"></i> ' . __('Comment', 'komik-starter') . ' <span class="required">*</span></label>
            <textarea id="comment" name="comment" cols="45" rows="5" required placeholder="' . esc_attr__('Write your comment here...', 'komik-starter') . '"></textarea>
            <div class="comment-media-tools">
                <button type="button" class="media-tool-btn" id="emoji-btn" title="' . esc_attr__('Add Emoji', 'komik-starter') . '">
                    <i class="fas fa-smile"></i>
                    <span>' . __('Emoji', 'komik-starter') . '</span>
                </button>
            </div>
            <!-- Inline Emoji Picker -->
            <div id="media-picker-inline" class="media-picker-inline" style="display:none;">
                <div class="media-picker-tabs">
                    <span class="picker-title"><i class="fas fa-smile"></i> ' . __('Select Emoji', 'komik-starter') . '</span>
                    <button type="button" class="picker-close-btn" id="close-picker">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="media-picker-body">
                    <div class="sticker-categories">
                        <button type="button" class="sticker-cat active" data-category="emoji">😀</button>
                        <button type="button" class="sticker-cat" data-category="reactions">👍</button>
                        <button type="button" class="sticker-cat" data-category="objects">🎮</button>
                        <button type="button" class="sticker-cat" data-category="love">❤️</button>
                        <button type="button" class="sticker-cat" data-category="funny">😂</button>
                    </div>
                    <div class="sticker-grid" id="sticker-grid"></div>
                </div>
            </div>
        </div>',
        'fields'               => $fields,
        'logged_in_as'         => '<p class="logged-in-as">' . 
            sprintf(
                __('Logged in as %s.', 'komik-starter'),
                '<a href="' . admin_url('profile.php') . '">' . wp_get_current_user()->display_name . '</a>'
            ) . 
            ' <a href="' . wp_logout_url(get_permalink()) . '">' . __('Log out?', 'komik-starter') . '</a></p>',
        'must_log_in'          => '<p class="must-log-in">' .
            sprintf(
                __('You must be <a href="%s">logged in</a> to post a comment.', 'komik-starter'),
                wp_login_url(get_permalink())
            ) . '</p>',
    ));
    ?>
</div>


<script>
jQuery(document).ready(function($) {
    // Comment Sort Tabs
    $('.sort-tab').on('click', function() {
        var sort = $(this).data('sort');
        var currentUrl = window.location.href.split('?')[0].split('#')[0];
        window.location.href = currentUrl + '?comment_sort=' + sort + '#comments';
    });
    
    // AJAX Comment Submit
    var $commentForm = $('#commentform');
    var $submitBtn = $commentForm.find('.submit-btn');
    var originalBtnText = $submitBtn.html();
    
    $commentForm.on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var formData = $form.serialize();
        var postId = $form.find('input[name="comment_post_ID"]').val();
        var parentId = $form.find('input[name="comment_parent"]').val() || 0;
        
        // Disable button and show loading
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <?php _e("Posting...", "komik-starter"); ?>');
        
        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: formData + '&action=komik_ajax_comment',
            success: function(response) {
                if (response.success) {
                    // Clear textarea
                    $form.find('textarea[name="comment"]').val('');
                    
                    // Show success message
                    showCommentNotice('success', '<?php _e("Comment posted successfully!", "komik-starter"); ?>');
                    
                    // Add new comment to list
                    if (response.data.comment_html) {
                        if (parentId && parentId != '0') {
                            // Reply - add to parent's children
                            var $parent = $('#comment-' + parentId);
                            var $childList = $parent.find('> .children');
                            if ($childList.length === 0) {
                                $parent.append('<ol class="children"></ol>');
                                $childList = $parent.find('> .children');
                            }
                            $childList.append(response.data.comment_html);
                            
                            // Cancel reply (move form back)
                            $('#cancel-comment-reply-link').trigger('click');
                        } else {
                            // Top-level comment - add to top of list
                            var $commentList = $('#comment-list');
                            if ($commentList.length) {
                                $commentList.prepend(response.data.comment_html);
                            } else {
                                // If no comments yet, reload to show new section
                                location.reload();
                            }
                        }
                        
                        // Update comment count
                        updateCommentCount(1);
                        
                        // Scroll to new comment
                        if (response.data.comment_id) {
                            var $newComment = $('#comment-' + response.data.comment_id);
                            if ($newComment.length) {
                                $newComment.addClass('new-comment');
                                $('html, body').animate({
                                    scrollTop: $newComment.offset().top - 100
                                }, 500);
                            }
                        }
                    }
                } else {
                    showCommentNotice('error', response.data.message || '<?php _e("Error posting comment", "komik-starter"); ?>');
                }
            },
            error: function(xhr, status, error) {
                showCommentNotice('error', '<?php _e("Error posting comment. Please try again.", "komik-starter"); ?>');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
    
    // Show notice function
    function showCommentNotice(type, message) {
        $('.comment-notice').remove();
        var iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        var bgColor = type === 'success' ? '#d1fae5' : '#fee2e2';
        var textColor = type === 'success' ? '#065f46' : '#991b1b';
        
        var $notice = $('<div class="comment-notice" style="padding:12px 16px;margin-bottom:15px;border-radius:8px;display:flex;align-items:center;gap:10px;background:' + bgColor + ';color:' + textColor + ';">' +
            '<i class="fas ' + iconClass + '"></i>' +
            '<span>' + message + '</span>' +
        '</div>');
        
        $commentForm.before($notice);
        
        setTimeout(function() {
            $notice.fadeOut(300, function() { $(this).remove(); });
        }, 5000);
    }
    
    // Update comment count
    function updateCommentCount(add) {
        var $title = $('.comments-title');
        if ($title.length) {
            var text = $title.text();
            var match = text.match(/(\d+)/);
            if (match) {
                var count = parseInt(match[1]) + add;
                $title.html('<i class="fas fa-comments"></i> ' + count + ' <?php echo _n("Comment", "Comments", 2, "komik-starter"); ?>');
            }
        }
    }
    
    // ========== EMOJI PICKER ==========
    var $picker = $('#media-picker-inline');
    var $textarea = $('#comment');
    
    // ASCII-based emoticons (compatible with utf8mb3 database)
    var stickerCategories = {
        emoji: [':)', ':D', ';)', ':P', 'XD', ':O', ':(', ":'(", ':/', ':|', '>:(', 'O:)', ':3', '^_^', '-_-', 'T_T', '@_@', '*_*', 'o_O', 'B)', ':*', '<3'],
        reactions: ['(y)', '(n)', '(ok)', '(clap)', '(wave)', '(pray)', '(muscle)', '(fist)', '(v)', '(point)', '(eyes)', '(fire)', '(100)', '(star)', '(check)', '(x)', '(!)'],
        objects: ['(book)', '(game)', '(music)', '(movie)', '(coffee)', '(pizza)', '(cake)', '(gift)', '(crown)', '(trophy)', '(medal)', '(gem)', '(bulb)', '(phone)', '(pc)'],
        love: ['<3', '</3', '(love)', '(kiss)', '(hug)', '(rose)', '(heart)', '(cupid)', ':*', '(blush)', 'xoxo', '(angel)', '(couple)', '(ring)'],
        funny: ['LOL', 'LMAO', 'ROFL', 'XD', ':D', 'haha', 'hehe', 'hihi', '(troll)', '(rip)', '(facepalm)', '(shrug)', '(dance)', '(party)', '(cool)', '(nerd)']
    };
    
    var currentCategory = 'emoji';
    
    // Toggle picker
    $('#emoji-btn').on('click', function() {
        togglePicker();
    });
    
    function togglePicker() {
        if ($picker.is(':visible')) {
            $picker.slideUp(200);
        } else {
            $picker.slideDown(200);
            loadStickers(currentCategory);
        }
    }
    
    // Close picker
    $('#close-picker').on('click', function() {
        $picker.slideUp(200);
    });
    
    // Sticker category switching
    $(document).on('click', '.sticker-cat', function() {
        var category = $(this).data('category');
        currentCategory = category;
        $('.sticker-cat').removeClass('active');
        $(this).addClass('active');
        loadStickers(category);
    });
    
    // Load stickers
    function loadStickers(category) {
        var stickers = stickerCategories[category] || stickerCategories.emoji;
        var $grid = $('#sticker-grid');
        $grid.empty();
        
        stickers.forEach(function(sticker) {
            var $item = $('<button type="button" class="sticker-item">' + sticker + '</button>');
            $item.on('click', function() {
                insertEmoji(sticker);
            });
            $grid.append($item);
        });
    }
    
    // Insert emoji into textarea
    function insertEmoji(content) {
        var currentVal = $textarea.val();
        $textarea.val(currentVal + (currentVal ? ' ' : '') + content);
        $picker.slideUp(200);
        $textarea.focus();
    }
    
    // Initialize
    loadStickers('emoji');
});
</script>



