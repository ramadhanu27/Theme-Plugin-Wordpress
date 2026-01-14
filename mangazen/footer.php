        </div><!-- .wrapper -->
    </div><!-- #content -->

    <!-- Footer -->
    <footer id="footer" class="site-footer" itemscope="itemscope" itemtype="http://schema.org/WPFooter" role="contentinfo">
        
        <?php 
        $show_az = get_option('komik_footer_show_az', 1);
        $disclaimer = get_option('komik_footer_disclaimer', __('All the comics on this website are only previews of the original comics, there may be many language errors, character names, and story lines. For the original version, please buy the comic if it\'s available in your city.', 'komik-starter'));
        $custom_copyright = get_option('komik_footer_copyright', '');
        
        if ($show_az) : 
        ?>
        <!-- A-Z List Section -->
        <div class="footer-az-section">
            <div class="footer-container">
                <div class="az-header">
                    <h3 class="az-title">A-Z LIST</h3>
                    <span class="az-desc"><?php _e('Searching series order by alphabet name A to Z.', 'komik-starter'); ?></span>
                </div>
                <?php 
                // Get A-Z List page URL
                $az_page = get_page_by_path('a-z-list');
                $az_base_url = $az_page ? get_permalink($az_page) : home_url('/a-z-list/');
                ?>
                <div class="az-list">
                    <a href="<?php echo esc_url(add_query_arg('letter', '0-9', $az_base_url)); ?>" class="az-item">#</a>
                    <a href="<?php echo esc_url(add_query_arg('letter', '0-9', $az_base_url)); ?>" class="az-item">0-9</a>
                    <?php foreach (range('A', 'Z') as $letter) : ?>
                    <a href="<?php echo esc_url(add_query_arg('letter', $letter, $az_base_url)); ?>" class="az-item"><?php echo $letter; ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Social & Disclaimer Section -->
        <div class="footer-bottom-section">
            <div class="footer-container">
                <!-- Social Icons -->
                <div class="footer-social-center">
                    <?php 
                    $fb = get_option('komik_contact_facebook');
                    $ig = get_option('komik_contact_instagram');
                    $tw = get_option('komik_contact_twitter');
                    $dc = get_option('komik_contact_discord');
                    $tg = get_option('komik_contact_telegram');
                    ?>
                    <?php if ($fb) : ?><a href="<?php echo esc_url($fb); ?>" class="social-btn" target="_blank"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
                    <?php if ($ig) : ?><a href="<?php echo esc_url($ig); ?>" class="social-btn" target="_blank"><i class="fab fa-instagram"></i></a><?php endif; ?>
                    <?php if ($tw) : ?><a href="<?php echo esc_url($tw); ?>" class="social-btn" target="_blank"><i class="fab fa-twitter"></i></a><?php endif; ?>
                    <?php if ($dc) : ?><a href="<?php echo esc_url($dc); ?>" class="social-btn" target="_blank"><i class="fab fa-discord"></i></a><?php endif; ?>
                    <?php if ($tg) : ?><a href="<?php echo esc_url($tg); ?>" class="social-btn" target="_blank"><i class="fab fa-telegram"></i></a><?php endif; ?>
                    <?php if (!$fb && !$ig && !$tw && !$dc && !$tg) : ?>
                    <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                </div>
                
                <!-- Disclaimer -->
                <?php if ($disclaimer) : ?>
                <div class="footer-disclaimer">
                    <p><?php echo wp_kses_post($disclaimer); ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Copyright -->
                <div class="footer-copyright">
                    <?php if ($custom_copyright) : ?>
                    <p><?php echo esc_html($custom_copyright); ?></p>
                    <?php else : ?>
                    <p>&copy; <?php echo date('Y'); ?> <strong><?php bloginfo('name'); ?></strong>. <?php _e('All rights reserved.', 'komik-starter'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
    </footer>

</div><!-- .mainholder -->

<!-- Scroll to Top Button (Fixed) -->
<a href="#" class="scrollToTop" aria-label="Scroll to top">
    <i class="fas fa-chevron-up"></i>
</a>

<script>
// Mobile menu toggle - with touch support
document.addEventListener('DOMContentLoaded', function() {
    var menuToggle = document.querySelector('.shme');
    var mainMenu = document.getElementById('main-menu');
    
    if (menuToggle && mainMenu) {
        // Handle both click and touchstart
        function handleMenuToggle(e) {
            e.preventDefault();
            e.stopPropagation();
            
            mainMenu.classList.toggle('shwx');
            var icon = menuToggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }
        }
        
        menuToggle.addEventListener('click', handleMenuToggle);
        menuToggle.addEventListener('touchend', function(e) {
            e.preventDefault();
            handleMenuToggle(e);
        }, { passive: false });
    }

    // Mobile search toggle
    var searchToggle = document.querySelector('.srcmob');
    var searchBox = document.querySelector('.searchx');
    
    if (searchToggle && searchBox) {
        function handleSearchToggle(e) {
            e.preventDefault();
            e.stopPropagation();
            searchBox.classList.toggle('minmbx');
        }
        
        searchToggle.addEventListener('click', handleSearchToggle);
        searchToggle.addEventListener('touchend', function(e) {
            e.preventDefault();
            handleSearchToggle(e);
        }, { passive: false });
    }

    // Scroll to top
    var scrollBtn = document.querySelector('.scrollToTop');
    
    if (scrollBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollBtn.style.display = 'block';
            } else {
                scrollBtn.style.display = 'none';
            }
        });

        scrollBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
</script>

<?php
// Content Protection
$protection_enable = get_option('komik_protection_enable', 0);

if ($protection_enable && !is_admin() && !current_user_can('manage_options')) :
    $right_click = get_option('komik_protection_right_click', 1);
    $text_select = get_option('komik_protection_text_select', 1);
    $keyboard = get_option('komik_protection_keyboard', 1);
    $drag = get_option('komik_protection_drag', 1);
    $message = get_option('komik_protection_message', __('Content is protected!', 'komik-starter'));
?>
<script>
(function() {
    var protectionMessage = <?php echo json_encode($message); ?>;
    
    <?php if ($right_click) : ?>
    // Disable right click
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        if (protectionMessage) {
            showProtectionMessage(protectionMessage);
        }
        return false;
    });
    <?php endif; ?>
    
    <?php if ($text_select) : ?>
    // Disable text selection
    document.addEventListener('selectstart', function(e) {
        e.preventDefault();
        return false;
    });
    
    // CSS to disable selection
    var style = document.createElement('style');
    style.textContent = '* { -webkit-user-select: none !important; -moz-user-select: none !important; -ms-user-select: none !important; user-select: none !important; }';
    document.head.appendChild(style);
    <?php endif; ?>
    
    <?php if ($keyboard) : ?>
    // Disable keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + C (copy)
        if (e.ctrlKey && e.key === 'c') {
            e.preventDefault();
            if (protectionMessage) showProtectionMessage(protectionMessage);
            return false;
        }
        // Ctrl + U (view source)
        if (e.ctrlKey && e.key === 'u') {
            e.preventDefault();
            if (protectionMessage) showProtectionMessage(protectionMessage);
            return false;
        }
        // Ctrl + S (save)
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            if (protectionMessage) showProtectionMessage(protectionMessage);
            return false;
        }
        // Ctrl + A (select all)
        if (e.ctrlKey && e.key === 'a') {
            e.preventDefault();
            if (protectionMessage) showProtectionMessage(protectionMessage);
            return false;
        }
        // Ctrl + Shift + I (dev tools)
        if (e.ctrlKey && e.shiftKey && e.key === 'I') {
            e.preventDefault();
            return false;
        }
        // F12 (dev tools)
        if (e.key === 'F12') {
            e.preventDefault();
            return false;
        }
        // Ctrl + Shift + C (inspect element)
        if (e.ctrlKey && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            return false;
        }
    });
    <?php endif; ?>
    
    <?php if ($drag) : ?>
    // Disable image drag
    document.addEventListener('dragstart', function(e) {
        if (e.target.tagName === 'IMG') {
            e.preventDefault();
            return false;
        }
    });
    
    // Also prevent copy on images
    document.querySelectorAll('img').forEach(function(img) {
        img.setAttribute('draggable', 'false');
        img.style.webkitUserDrag = 'none';
    });
    <?php endif; ?>
    
    // Show protection message
    function showProtectionMessage(msg) {
        // Remove existing message
        var existing = document.querySelector('.protection-alert');
        if (existing) existing.remove();
        
        // Create message element
        var alertBox = document.createElement('div');
        alertBox.className = 'protection-alert';
        alertBox.innerHTML = '<span class="protection-icon">🛡️</span> ' + msg;
        alertBox.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:linear-gradient(135deg,#1e3a5f,#2271b1);color:#fff;padding:20px 40px;border-radius:12px;font-size:16px;font-weight:600;z-index:999999;box-shadow:0 10px 40px rgba(0,0,0,0.3);display:flex;align-items:center;gap:12px;animation:fadeInOut 2s forwards;';
        
        // Add animation style
        if (!document.querySelector('#protection-alert-style')) {
            var animStyle = document.createElement('style');
            animStyle.id = 'protection-alert-style';
            animStyle.textContent = '@keyframes fadeInOut{0%{opacity:0;transform:translate(-50%,-50%) scale(0.8);}10%{opacity:1;transform:translate(-50%,-50%) scale(1);}90%{opacity:1;transform:translate(-50%,-50%) scale(1);}100%{opacity:0;transform:translate(-50%,-50%) scale(0.8);}}';
            document.head.appendChild(animStyle);
        }
        
        document.body.appendChild(alertBox);
        
        // Remove after animation
        setTimeout(function() {
            alertBox.remove();
        }, 2000);
    }
})();
</script>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
