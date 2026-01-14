<?php
/**
 * Template Name: Login Page
 * User Login Page
 * 
 * @package Komik_Starter
 */

// Redirect if already logged in
if (is_user_logged_in()) {
    $profile_page = get_page_by_path('profile');
    if ($profile_page) {
        wp_redirect(home_url('/profile/'));
    } else {
        wp_redirect(home_url('/'));
    }
    exit;
}

get_header();
?>

<div class="account-page">
    <div class="account-container">
        <div class="account-card">
            <div class="account-header">
                <h1><?php _e('Welcome Back!', 'komik-starter'); ?></h1>
                <p><?php _e('Login to access your bookmarks and reading history', 'komik-starter'); ?></p>
            </div>
            
            <form id="login-form" class="account-form" method="post">
                <div class="form-message" id="login-message"></div>
                
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i>
                        <?php _e('Username or Email', 'komik-starter'); ?>
                    </label>
                    <input type="text" id="username" name="username" required placeholder="<?php esc_attr_e('Enter username or email', 'komik-starter'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        <?php _e('Password', 'komik-starter'); ?>
                    </label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" required placeholder="<?php esc_attr_e('Enter password', 'komik-starter'); ?>">
                        <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" value="true">
                        <span><?php _e('Remember me', 'komik-starter'); ?></span>
                    </label>
                    <a href="<?php echo wp_lostpassword_url(); ?>" class="forgot-link"><?php _e('Forgot Password?', 'komik-starter'); ?></a>
                </div>
                
                <button type="submit" class="btn-submit">
                    <span class="btn-text"><?php _e('Login', 'komik-starter'); ?></span>
                    <span class="btn-loading"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </form>
            
            <div class="account-footer">
                <p><?php _e("Don't have an account?", 'komik-starter'); ?> <a href="<?php echo home_url('/register/'); ?>"><?php _e('Register here', 'komik-starter'); ?></a></p>
            </div>
        </div>
        
    </div>
</div>

<?php get_footer(); ?>
