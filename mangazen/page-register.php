<?php
/**
 * Template Name: Register Page
 * User Registration Page
 * 
 * @package Komik_Starter
 */

// Redirect if already logged in
if (is_user_logged_in()) {
    wp_redirect(home_url('/profile/'));
    exit;
}

// Check if registration is enabled
$registration_enabled = get_option('users_can_register');

get_header();
?>

<div class="account-page">
    <div class="account-container">
        <div class="account-card">
            <div class="account-header">
                <h1><?php _e('Create Account', 'komik-starter'); ?></h1>
                <p><?php _e('Join our community and start reading!', 'komik-starter'); ?></p>
            </div>
            
            <?php if (!$registration_enabled) : ?>
                <div class="form-message error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php _e('Registration is currently disabled. Please contact administrator.', 'komik-starter'); ?>
                </div>
            <?php else : ?>
            
            <form id="register-form" class="account-form" method="post">
                <div class="form-message" id="register-message"></div>
                
                <div class="form-group">
                    <label for="reg-username">
                        <i class="fas fa-user"></i>
                        <?php _e('Username', 'komik-starter'); ?>
                    </label>
                    <input type="text" id="reg-username" name="username" required 
                           placeholder="<?php esc_attr_e('Choose a username', 'komik-starter'); ?>"
                           pattern="[a-zA-Z0-9_]+" 
                           title="<?php esc_attr_e('Only letters, numbers, and underscores', 'komik-starter'); ?>">
                    <small class="field-hint"><?php _e('Letters, numbers, and underscores only', 'komik-starter'); ?></small>
                </div>
                
                <div class="form-group">
                    <label for="reg-email">
                        <i class="fas fa-envelope"></i>
                        <?php _e('Email Address', 'komik-starter'); ?>
                    </label>
                    <input type="email" id="reg-email" name="email" required 
                           placeholder="<?php esc_attr_e('Enter your email', 'komik-starter'); ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="reg-password">
                            <i class="fas fa-lock"></i>
                            <?php _e('Password', 'komik-starter'); ?>
                        </label>
                        <div class="password-input">
                            <input type="password" id="reg-password" name="password" required 
                                   placeholder="<?php esc_attr_e('Min. 6 characters', 'komik-starter'); ?>"
                                   minlength="6">
                            <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg-password-confirm">
                            <i class="fas fa-lock"></i>
                            <?php _e('Confirm Password', 'komik-starter'); ?>
                        </label>
                        <div class="password-input">
                            <input type="password" id="reg-password-confirm" name="password_confirm" required 
                                   placeholder="<?php esc_attr_e('Confirm password', 'komik-starter'); ?>">
                            <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms" required>
                        <span><?php printf(__('I agree to the %sTerms of Service%s', 'komik-starter'), '<a href="' . home_url('/terms/') . '" target="_blank">', '</a>'); ?></span>
                    </label>
                </div>
                
                <button type="submit" class="btn-submit">
                    <span class="btn-text"><?php _e('Create Account', 'komik-starter'); ?></span>
                    <span class="btn-loading"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </form>
            
            <?php endif; ?>
            
            <div class="account-footer">
                <p><?php _e('Already have an account?', 'komik-starter'); ?> <a href="<?php echo home_url('/login/'); ?>"><?php _e('Login here', 'komik-starter'); ?></a></p>
            </div>
        </div>
        
    </div>
</div>

<?php get_footer(); ?>
