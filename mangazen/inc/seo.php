<?php
/**
 * SEO Functions for Komik Starter Theme
 * 
 * Generates meta tags, Open Graph, Twitter Cards, and JSON-LD Schema
 *
 * @package Komik_Starter
 * @version 1.0.0
 */

defined("ABSPATH") || die("!");

/**
 * Add SEO meta tags to wp_head
 */
add_action('wp_head', 'komik_starter_seo_meta_tags', 1);
function komik_starter_seo_meta_tags() {
    $site_name = get_bloginfo('name');
    $site_description = get_bloginfo('description');
    $site_url = home_url('/');
    
    // Default values
    $title = $site_name;
    $description = $site_description;
    $image = '';
    $type = 'website';
    $url = $site_url;
    $author = '';
    
    // Get logo for default image
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
        if ($logo) {
            $image = $logo[0];
        }
    }
    
    // Customize based on page type
    if (is_singular()) {
        global $post;
        $title = get_the_title() . ' - ' . $site_name;
        $url = get_permalink();
        $type = 'article';
        $author = get_the_author();
        
        // Get post description
        if (has_excerpt()) {
            $description = get_the_excerpt();
        } else {
            $description = wp_trim_words(strip_tags($post->post_content), 30, '...');
        }
        
        // Get featured image
        if (has_post_thumbnail()) {
            $image = get_the_post_thumbnail_url(null, 'large');
        }
        
        // Manhwa specific
        if (is_singular('manhwa')) {
            $type = 'book';
            $manhwa_desc = get_post_meta(get_the_ID(), '_manhwa_description', true);
            if ($manhwa_desc) {
                $description = wp_trim_words(strip_tags($manhwa_desc), 30, '...');
            }
        }
        
        // Chapter page
        if (is_singular('post')) {
            $seri_id = get_post_meta(get_the_ID(), 'ero_seri', true);
            if (!$seri_id) {
                $seri_id = get_post_meta(get_the_ID(), '_manhwa_id', true);
            }
            if ($seri_id) {
                $series_title = get_the_title($seri_id);
                $description = 'Read ' . get_the_title() . ' from ' . $series_title . '. Read ' . $series_title . ' online for free at ' . $site_name;
                
                // Get series thumbnail if no chapter thumbnail
                if (!has_post_thumbnail() && has_post_thumbnail($seri_id)) {
                    $image = get_the_post_thumbnail_url($seri_id, 'large');
                }
            }
        }
        
    } elseif (is_home() || is_front_page()) {
        $title = $site_name . ' - ' . $site_description;
        $description = $site_description;
        
    } elseif (is_category() || is_tag() || is_tax()) {
        $term = get_queried_object();
        $title = $term->name . ' - ' . $site_name;
        if (!empty($term->description)) {
            $description = wp_trim_words(strip_tags($term->description), 30, '...');
        } else {
            $description = 'Browse ' . $term->name . ' manga/manhwa at ' . $site_name;
        }
        $url = get_term_link($term);
        
    } elseif (is_search()) {
        $title = 'Search: ' . get_search_query() . ' - ' . $site_name;
        $description = 'Search results for "' . get_search_query() . '" at ' . $site_name;
        
    } elseif (is_archive()) {
        $title = get_the_archive_title() . ' - ' . $site_name;
        $description = get_the_archive_description() ?: 'Browse all content at ' . $site_name;
        
    } elseif (is_404()) {
        $title = 'Page Not Found - ' . $site_name;
        $description = 'The page you are looking for could not be found.';
    }
    
    // Clean up description
    $description = esc_attr(wp_strip_all_tags($description));
    $title = esc_attr($title);
    
    // Output meta tags
    ?>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo $description; ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="<?php echo esc_url($url); ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:locale" content="<?php echo get_locale(); ?>">
    <meta property="og:type" content="<?php echo $type; ?>">
    <meta property="og:title" content="<?php echo $title; ?>">
    <meta property="og:description" content="<?php echo $description; ?>">
    <meta property="og:url" content="<?php echo esc_url($url); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr($site_name); ?>">
    <?php if ($image) : ?>
    <meta property="og:image" content="<?php echo esc_url($image); ?>">
    <meta property="og:image:secure_url" content="<?php echo esc_url($image); ?>">
    <meta property="og:image:alt" content="<?php echo $title; ?>">
    <?php endif; ?>
    <?php if (is_singular()) : ?>
    <meta property="article:published_time" content="<?php echo get_the_date('c'); ?>">
    <meta property="article:modified_time" content="<?php echo get_the_modified_date('c'); ?>">
    <?php if ($author) : ?>
    <meta property="article:author" content="<?php echo esc_attr($author); ?>">
    <?php endif; ?>
    <?php endif; ?>
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $title; ?>">
    <meta name="twitter:description" content="<?php echo $description; ?>">
    <?php if ($image) : ?>
    <meta name="twitter:image" content="<?php echo esc_url($image); ?>">
    <?php endif; ?>
    
    <?php
}

/**
 * Add JSON-LD Schema
 */
add_action('wp_head', 'komik_starter_json_ld_schema', 2);
function komik_starter_json_ld_schema() {
    $site_name = get_bloginfo('name');
    $site_url = home_url('/');
    $logo_url = '';
    
    // Get logo
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
        if ($logo) {
            $logo_url = $logo[0];
        }
    }
    
    // Website Schema (always present)
    $website_schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => $site_url . '#website',
        'url' => $site_url,
        'name' => $site_name,
        'description' => get_bloginfo('description'),
        'potentialAction' => array(
            '@type' => 'SearchAction',
            'target' => array(
                '@type' => 'EntryPoint',
                'urlTemplate' => $site_url . '?s={search_term_string}'
            ),
            'query-input' => 'required name=search_term_string'
        ),
        'inLanguage' => get_locale()
    );
    
    echo '<script type="application/ld+json">' . wp_json_encode($website_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>' . "\n";
    
    // Organization Schema (homepage)
    if (is_home() || is_front_page()) {
        $org_schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => $site_url . '#organization',
            'name' => $site_name,
            'url' => $site_url,
        );
        
        if ($logo_url) {
            $org_schema['logo'] = array(
                '@type' => 'ImageObject',
                '@id' => $site_url . '#logo',
                'url' => $logo_url,
                'caption' => $site_name
            );
            $org_schema['image'] = array('@id' => $site_url . '#logo');
        }
        
        echo '<script type="application/ld+json">' . wp_json_encode($org_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>' . "\n";
    }
    
    // Article/Book Schema for singular pages
    if (is_singular()) {
        $post_id = get_the_ID();
        $post_type = get_post_type();
        
        if ($post_type == 'manhwa') {
            // Comic/Book Schema for Manhwa
            $manhwa_schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'ComicSeries',
                '@id' => get_permalink() . '#manga',
                'name' => get_the_title(),
                'url' => get_permalink(),
                'description' => get_the_excerpt() ?: wp_trim_words(get_the_content(), 50),
                'inLanguage' => get_locale(),
            );
            
            // Add image
            if (has_post_thumbnail()) {
                $manhwa_schema['image'] = get_the_post_thumbnail_url(null, 'large');
            }
            
            // Add author
            $author = get_post_meta($post_id, '_manhwa_author', true);
            if ($author) {
                $manhwa_schema['author'] = array(
                    '@type' => 'Person',
                    'name' => $author
                );
            }
            
            // Add genres
            $genres = get_the_terms($post_id, 'manhwa_genre');
            if ($genres && !is_wp_error($genres)) {
                $manhwa_schema['genre'] = wp_list_pluck($genres, 'name');
            }
            
            // Add rating
            $rating = get_post_meta($post_id, '_manhwa_rating', true);
            if ($rating && is_numeric($rating)) {
                $manhwa_schema['aggregateRating'] = array(
                    '@type' => 'AggregateRating',
                    'ratingValue' => $rating,
                    'bestRating' => '10',
                    'worstRating' => '1',
                    'ratingCount' => get_post_meta($post_id, '_manhwa_views', true) ?: 1
                );
            }
            
            echo '<script type="application/ld+json">' . wp_json_encode($manhwa_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>' . "\n";
            
        } else {
            // Article Schema for regular posts/chapters
            $article_schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                '@id' => get_permalink() . '#article',
                'headline' => get_the_title(),
                'url' => get_permalink(),
                'datePublished' => get_the_date('c'),
                'dateModified' => get_the_modified_date('c'),
                'author' => array(
                    '@type' => 'Person',
                    'name' => get_the_author()
                ),
                'publisher' => array(
                    '@type' => 'Organization',
                    'name' => $site_name,
                    'logo' => $logo_url ? array(
                        '@type' => 'ImageObject',
                        'url' => $logo_url
                    ) : null
                ),
                'inLanguage' => get_locale(),
            );
            
            if (has_post_thumbnail()) {
                $article_schema['image'] = get_the_post_thumbnail_url(null, 'large');
            }
            
            if (has_excerpt()) {
                $article_schema['description'] = get_the_excerpt();
            }
            
            echo '<script type="application/ld+json">' . wp_json_encode($article_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>' . "\n";
        }
    }
    
    // BreadcrumbList Schema
    if (!is_home() && !is_front_page()) {
        $breadcrumb_schema = komik_starter_get_breadcrumb_schema();
        if ($breadcrumb_schema) {
            echo '<script type="application/ld+json">' . wp_json_encode($breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>' . "\n";
        }
    }
}

/**
 * Generate breadcrumb schema
 */
function komik_starter_get_breadcrumb_schema() {
    $items = array();
    $position = 1;
    
    // Home
    $items[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => get_bloginfo('name'),
        'item' => home_url('/')
    );
    
    if (is_singular('manhwa')) {
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Manhwa',
            'item' => get_post_type_archive_link('manhwa')
        );
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title()
        );
    } elseif (is_singular('post')) {
        // Check for series relation
        $seri_id = get_post_meta(get_the_ID(), 'ero_seri', true);
        if (!$seri_id) {
            $seri_id = get_post_meta(get_the_ID(), '_manhwa_id', true);
        }
        if ($seri_id) {
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => get_the_title($seri_id),
                'item' => get_permalink($seri_id)
            );
        }
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title()
        );
    } elseif (is_singular('page')) {
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title()
        );
    } elseif (is_category() || is_tag() || is_tax()) {
        $term = get_queried_object();
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $term->name
        );
    } elseif (is_search()) {
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Search: ' . get_search_query()
        );
    } elseif (is_archive()) {
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_archive_title()
        );
    }
    
    if (count($items) < 2) {
        return null;
    }
    
    return array(
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items
    );
}

/**
 * Custom title filter for better SEO
 */
add_filter('pre_get_document_title', 'komik_starter_seo_title', 10);
function komik_starter_seo_title($title) {
    $site_name = get_bloginfo('name');
    $sep = ' - ';
    
    if (is_singular('manhwa')) {
        $type = get_post_meta(get_the_ID(), '_manhwa_type', true) ?: 'Manhwa';
        return get_the_title() . $sep . 'Read ' . $type . ' Online' . $sep . $site_name;
    }
    
    if (is_singular('post')) {
        $seri_id = get_post_meta(get_the_ID(), 'ero_seri', true);
        if (!$seri_id) {
            $seri_id = get_post_meta(get_the_ID(), '_manhwa_id', true);
        }
        if ($seri_id) {
            return get_the_title() . $sep . get_the_title($seri_id) . $sep . $site_name;
        }
    }
    
    if (is_tax('manhwa_genre')) {
        $term = get_queried_object();
        return $term->name . ' Manhwa' . $sep . $site_name;
    }
    
    if (is_post_type_archive('manhwa')) {
        return 'Manhwa List' . $sep . $site_name;
    }
    
    return $title;
}

/**
 * Remove WordPress version for security
 */
remove_action('wp_head', 'wp_generator');

/**
 * Remove extra RSS feed links (if not needed)
 */
// remove_action('wp_head', 'feed_links_extra', 3);

/**
 * Add preconnect for external resources
 */
add_action('wp_head', 'komik_starter_preconnect', 1);
function komik_starter_preconnect() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
}
