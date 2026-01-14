<?php
/**
 * Template Name: Contact Page
 * Description: A contact page template with contact info
 */

get_header(); 

// Get contact settings from Theme Options
$contact_email = get_option('komik_contact_email', get_option('admin_email'));
$facebook_url = get_option('komik_contact_facebook', '');
$twitter_url = get_option('komik_contact_twitter', '');
$instagram_url = get_option('komik_contact_instagram', '');
$discord_url = get_option('komik_contact_discord', '');
$telegram_url = get_option('komik_contact_telegram', '');

$site_name = get_bloginfo('name');
?>

<div id="content">
    <div class="wrapper">
        
        <!-- Breadcrumb -->
        <div class="ts-breadcrumb bixbox">
            <ol class="brd" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a href="<?php echo home_url(); ?>" itemprop="item">
                        <span itemprop="name"><i class="fas fa-home"></i> Home</span>
                    </a>
                    <meta itemprop="position" content="1">
                </li>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="name"><?php _e('Contact', 'komik-starter'); ?></span>
                    <meta itemprop="position" content="2">
                </li>
            </ol>
        </div>

        <div class="postbody full">
            <div class="bixbox contact-page">
                
                <!-- Page Header -->
                <div class="contact-header">
                    <h1 class="contact-title">
                        <i class="fas fa-envelope"></i> <?php _e('Hubungi Kami', 'komik-starter'); ?>
                    </h1>
                    <p class="contact-subtitle">
                        <?php _e('Ada pertanyaan, saran, atau ingin beriklan? Silahkan hubungi kami melalui email atau media sosial.', 'komik-starter'); ?>
                    </p>
                </div>

                <div class="contact-content">
                    
                    <!-- Contact Info Section -->
                    <div class="contact-info-section">
                        <h2><i class="fas fa-info-circle"></i> <?php _e('Informasi Kontak', 'komik-starter'); ?></h2>
                        
                        <div class="contact-info-list">
                            <div class="contact-info-item">
                                <div class="info-icon"><i class="fas fa-envelope"></i></div>
                                <div class="info-content">
                                    <h4><?php _e('Email', 'komik-starter'); ?></h4>
                                    <p><a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a></p>
                                </div>
                            </div>
                            
                            <div class="contact-info-item">
                                <div class="info-icon"><i class="fas fa-clock"></i></div>
                                <div class="info-content">
                                    <h4><?php _e('Waktu Respons', 'komik-starter'); ?></h4>
                                    <p><?php _e('1-2 Hari Kerja', 'komik-starter'); ?></p>
                                </div>
                            </div>
                            
                            <div class="contact-info-item">
                                <div class="info-icon"><i class="fas fa-bullhorn"></i></div>
                                <div class="info-content">
                                    <h4><?php _e('Pemasangan Iklan', 'komik-starter'); ?></h4>
                                    <p><?php _e('Hubungi kami untuk informasi harga dan paket iklan', 'komik-starter'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Social Links -->
                        <?php 
                        $has_social = !empty($facebook_url) || !empty($twitter_url) || !empty($instagram_url) || !empty($discord_url) || !empty($telegram_url);
                        if ($has_social) : 
                        ?>
                        <div class="contact-social">
                            <h3><?php _e('Ikuti Kami', 'komik-starter'); ?></h3>
                            <div class="social-links">
                                <?php if (!empty($facebook_url)) : ?>
                                <a href="<?php echo esc_url($facebook_url); ?>" class="social-link facebook" title="Facebook" target="_blank" rel="noopener">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($twitter_url)) : ?>
                                <a href="<?php echo esc_url($twitter_url); ?>" class="social-link twitter" title="Twitter" target="_blank" rel="noopener">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($instagram_url)) : ?>
                                <a href="<?php echo esc_url($instagram_url); ?>" class="social-link instagram" title="Instagram" target="_blank" rel="noopener">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($discord_url)) : ?>
                                <a href="<?php echo esc_url($discord_url); ?>" class="social-link discord" title="Discord" target="_blank" rel="noopener">
                                    <i class="fab fa-discord"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($telegram_url)) : ?>
                                <a href="<?php echo esc_url($telegram_url); ?>" class="social-link telegram" title="Telegram" target="_blank" rel="noopener">
                                    <i class="fab fa-telegram-plane"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                    
                </div>
                
            </div>
        </div>
        
    </div>
</div>

<?php get_footer(); ?>
