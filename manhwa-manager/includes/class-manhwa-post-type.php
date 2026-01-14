<?php
/**
 * Custom Post Type for Manhwa
 */

if (!defined('ABSPATH')) {
    exit;
}

class Manhwa_Post_Type {
    
    public function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_action('wp_ajax_manhwa_upload_cover_from_url', array($this, 'ajax_upload_cover_from_url'));
        
        // Yoast SEO Integration
        add_filter('wpseo_title', array($this, 'yoast_custom_title'), 10, 1);
        add_filter('wpseo_metadesc', array($this, 'yoast_custom_description'), 10, 1);
        add_filter('wpseo_opengraph_title', array($this, 'yoast_custom_title'), 10, 1);
        add_filter('wpseo_opengraph_desc', array($this, 'yoast_custom_description'), 10, 1);
        add_filter('wpseo_opengraph_type', array($this, 'yoast_og_type'), 10, 1);
        add_filter('wpseo_schema_article', array($this, 'yoast_schema_article'), 10, 1);
    }
    
    /**
     * Yoast SEO: Custom Title
     */
    public function yoast_custom_title($title) {
        if (is_singular('manhwa')) {
            global $post;
            $manhwa_title = get_the_title($post->ID);
            $type = get_post_meta($post->ID, '_manhwa_type', true);
            $status = get_post_meta($post->ID, '_manhwa_status', true);
            
            if ($type && $status) {
                return $manhwa_title . ' - ' . ucfirst($status) . ' ' . $type . ' | ' . get_bloginfo('name');
            }
        }
        
        if (is_tax('manhwa_genre')) {
            $term = get_queried_object();
            return $term->name . ' Manhwa - Read Online | ' . get_bloginfo('name');
        }
        
        return $title;
    }
    
    /**
     * Yoast SEO: Custom Description
     */
    public function yoast_custom_description($description) {
        if (is_singular('manhwa')) {
            global $post;
            
            // If Yoast description is set, use it
            if (!empty($description)) {
                return $description;
            }
            
            // Generate from content or meta
            $excerpt = get_the_excerpt($post->ID);
            if (!empty($excerpt)) {
                return wp_trim_words($excerpt, 30, '...');
            }
            
            $content = get_post_field('post_content', $post->ID);
            if (!empty($content)) {
                return wp_trim_words(strip_tags($content), 30, '...');
            }
            
            // Fallback description
            $type = get_post_meta($post->ID, '_manhwa_type', true) ?: 'Manhwa';
            $author = get_post_meta($post->ID, '_manhwa_author', true);
            $status = get_post_meta($post->ID, '_manhwa_status', true);
            
            return sprintf(
                'Read %s by %s online. Status: %s. Available on %s.',
                get_the_title($post->ID),
                $author ?: 'Unknown',
                ucfirst($status ?: 'Ongoing'),
                get_bloginfo('name')
            );
        }
        
        return $description;
    }
    
    /**
     * Yoast SEO: Open Graph Type
     */
    public function yoast_og_type($type) {
        if (is_singular('manhwa')) {
            return 'article';
        }
        return $type;
    }
    
    /**
     * Yoast SEO: Schema Article Data
     */
    public function yoast_schema_article($data) {
        if (is_singular('manhwa')) {
            global $post;
            
            $rating = get_post_meta($post->ID, '_manhwa_rating', true);
            $author = get_post_meta($post->ID, '_manhwa_author', true);
            $type = get_post_meta($post->ID, '_manhwa_type', true);
            
            // Add custom properties
            $data['@type'] = 'Article';
            $data['articleSection'] = $type ?: 'Manhwa';
            
            if ($author) {
                $data['author'] = array(
                    '@type' => 'Person',
                    'name' => $author,
                );
            }
            
            // Add aggregate rating if exists
            if ($rating && floatval($rating) > 0) {
                $data['aggregateRating'] = array(
                    '@type' => 'AggregateRating',
                    'ratingValue' => floatval($rating),
                    'bestRating' => 10,
                    'worstRating' => 0,
                    'ratingCount' => 1,
                );
            }
            
            // Add genres as keywords
            $genres = wp_get_post_terms($post->ID, 'manhwa_genre', array('fields' => 'names'));
            if (!empty($genres) && !is_wp_error($genres)) {
                $data['keywords'] = implode(', ', $genres);
            }
        }
        
        return $data;
    }
    
    /**
     * Register Manhwa Custom Post Type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => 'Manhwa',
            'singular_name'         => 'Manhwa',
            'menu_name'             => 'Manhwa',
            'name_admin_bar'        => 'Manhwa',
            'add_new'               => 'Add New',
            'add_new_item'          => 'Add New Manhwa',
            'edit_item'             => 'Edit Manhwa',
            'new_item'              => 'New Manhwa',
            'view_item'             => 'View Manhwa',
            'view_items'            => 'View Manhwa',
            'search_items'          => 'Search Manhwa',
            'not_found'             => 'No manhwa found',
            'not_found_in_trash'    => 'No manhwa found in trash',
            'all_items'             => 'All Manhwa',
            'archives'              => 'Manhwa Archives',
            'attributes'            => 'Manhwa Attributes',
            'insert_into_item'      => 'Insert into manhwa',
            'uploaded_to_this_item' => 'Uploaded to this manhwa',
            'filter_items_list'     => 'Filter manhwa list',
            'items_list_navigation' => 'Manhwa list navigation',
            'items_list'            => 'Manhwa list',
        );
        
        $args = array(
            'labels'              => $labels,
            'description'         => 'Manhwa/Manga/Manhua comic series',
            'public'              => true,
            'has_archive'         => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'show_in_rest'        => true,
            'rest_base'           => 'manhwa',
            'menu_icon'           => 'dashicons-book-alt',
            'menu_position'       => 5,
            'supports'            => array(
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'comments',
                'custom-fields',
                'author',
                'revisions',
            ),
            'rewrite'             => array(
                'slug'       => 'manhwa',
                'with_front' => false,
                'feeds'      => true,
                'pages'      => true,
            ),
            'capability_type'     => 'post',
            'map_meta_cap'        => true,
            'hierarchical'        => false,
            'query_var'           => true,
            'can_export'          => true,
            'delete_with_user'    => false,
            'taxonomies'          => array('manhwa_genre', 'manhwa_status'),
        );
        
        register_post_type('manhwa', $args);
    }
    
    /**
     * Register Taxonomies
     */
    public function register_taxonomies() {
        // Genre Taxonomy
        $genre_labels = array(
            'name'                       => 'Genres',
            'singular_name'              => 'Genre',
            'menu_name'                  => 'Genres',
            'all_items'                  => 'All Genres',
            'edit_item'                  => 'Edit Genre',
            'view_item'                  => 'View Genre',
            'update_item'                => 'Update Genre',
            'add_new_item'               => 'Add New Genre',
            'new_item_name'              => 'New Genre Name',
            'parent_item'                => 'Parent Genre',
            'parent_item_colon'          => 'Parent Genre:',
            'search_items'               => 'Search Genres',
            'popular_items'              => 'Popular Genres',
            'separate_items_with_commas' => 'Separate genres with commas',
            'add_or_remove_items'        => 'Add or remove genres',
            'choose_from_most_used'      => 'Choose from most used genres',
            'not_found'                  => 'No genres found',
            'back_to_items'              => '← Back to Genres',
        );
        
        register_taxonomy('manhwa_genre', 'manhwa', array(
            'labels'            => $genre_labels,
            'description'       => 'Manhwa genres like Action, Romance, Fantasy, etc.',
            'public'            => true,
            'publicly_queryable' => true,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_in_menu'      => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true,
            'rest_base'         => 'manhwa-genres',
            'show_tagcloud'     => true,
            'show_in_quick_edit' => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array(
                'slug'         => 'genre',
                'with_front'   => false,
                'hierarchical' => true,
            ),
        ));
        
        // Status Taxonomy
        $status_labels = array(
            'name'                       => 'Status',
            'singular_name'              => 'Status',
            'menu_name'                  => 'Status',
            'all_items'                  => 'All Status',
            'edit_item'                  => 'Edit Status',
            'view_item'                  => 'View Status',
            'update_item'                => 'Update Status',
            'add_new_item'               => 'Add New Status',
            'new_item_name'              => 'New Status Name',
            'search_items'               => 'Search Status',
            'not_found'                  => 'No status found',
            'back_to_items'              => '← Back to Status',
        );
        
        register_taxonomy('manhwa_status', 'manhwa', array(
            'labels'            => $status_labels,
            'description'       => 'Publication status: Ongoing, Completed, Hiatus',
            'public'            => true,
            'publicly_queryable' => true,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_in_menu'      => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true,
            'rest_base'         => 'manhwa-status',
            'show_tagcloud'     => false,
            'show_in_quick_edit' => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array(
                'slug'       => 'status',
                'with_front' => false,
            ),
        ));
    }
    
    /**
     * Add Meta Boxes
     */
    public function add_meta_boxes() {
        // Cover/Thumbnail Meta Box
        add_meta_box(
            'manhwa_cover',
            '📸 Manhwa Cover / Thumbnail',
            array($this, 'render_cover_meta_box'),
            'manhwa',
            'side',
            'high'
        );
        
        add_meta_box(
            'manhwa_details',
            'Manhwa Details',
            array($this, 'render_details_meta_box'),
            'manhwa',
            'normal',
            'high'
        );
        
        add_meta_box(
            'manhwa_chapters',
            'Chapters',
            array($this, 'render_chapters_meta_box'),
            'manhwa',
            'normal',
            'high'
        );
    }
    
    /**
     * Render Cover Meta Box
     */
    public function render_cover_meta_box($post) {
        wp_nonce_field('manhwa_cover_nonce', 'manhwa_cover_nonce_field');
        
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'medium');
        $cover_url_meta = get_post_meta($post->ID, '_manhwa_cover_url', true);
        ?>
        <div style="text-align: center;">
            <!-- Current Cover Preview -->
            <div id="manhwa-cover-preview" style="margin-bottom: 15px;">
                <?php if ($thumbnail_url) : ?>
                    <img src="<?php echo esc_url($thumbnail_url); ?>" style="max-width: 100%; height: auto; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <?php else : ?>
                    <div style="background: #f0f0f0; padding: 60px 20px; border-radius: 10px; color: #999;">
                        <span style="font-size: 48px;">📖</span>
                        <p style="margin: 10px 0 0 0;">No cover image</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Upload Button -->
            <button type="button" id="manhwa-upload-cover-btn" class="button button-primary button-large" style="width: 100%; margin-bottom: 10px;">
                📤 Upload Cover Image
            </button>
            
            <?php if ($thumbnail_id) : ?>
                <button type="button" id="manhwa-remove-cover-btn" class="button button-secondary" style="width: 100%; background: #dc3545; color: white; border: none;">
                    🗑️ Remove Cover
                </button>
            <?php endif; ?>
            
            <input type="hidden" id="manhwa-cover-id" name="manhwa_cover_id" value="<?php echo esc_attr($thumbnail_id); ?>">
            
            <!-- Or Upload from URL -->
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
                <p style="margin: 0 0 10px 0; font-weight: bold; text-align: left;">Or upload from URL:</p>
                <input type="text" id="manhwa-cover-url" name="manhwa_cover_url" value="<?php echo esc_attr($cover_url_meta); ?>" placeholder="https://example.com/image.jpg" style="width: 100%; padding: 8px; margin-bottom: 10px;">
                <button type="button" id="manhwa-upload-from-url-btn" class="button button-secondary" style="width: 100%;">
                    🌐 Upload from URL
                </button>
                <p style="margin: 10px 0 0 0; font-size: 12px; color: #666; text-align: left;">
                    Paste image URL and click button to download and set as cover
                </p>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // WordPress Media Uploader
            let mediaUploader;
            
            $('#manhwa-upload-cover-btn').on('click', function(e) {
                e.preventDefault();
                
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                mediaUploader = wp.media({
                    title: 'Choose Manhwa Cover',
                    button: {
                        text: 'Set as Cover'
                    },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#manhwa-cover-id').val(attachment.id);
                    $('#manhwa-cover-preview').html('<img src="' + attachment.url + '" style="max-width: 100%; height: auto; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">');
                    
                    // Show remove button
                    if ($('#manhwa-remove-cover-btn').length === 0) {
                        $('#manhwa-upload-cover-btn').after('<button type="button" id="manhwa-remove-cover-btn" class="button button-secondary" style="width: 100%; background: #dc3545; color: white; border: none; margin-top: 10px;">🗑️ Remove Cover</button>');
                    }
                });
                
                mediaUploader.open();
            });
            
            // Remove Cover
            $(document).on('click', '#manhwa-remove-cover-btn', function(e) {
                e.preventDefault();
                $('#manhwa-cover-id').val('');
                $('#manhwa-cover-preview').html('<div style="background: #f0f0f0; padding: 60px 20px; border-radius: 10px; color: #999;"><span style="font-size: 48px;">📖</span><p style="margin: 10px 0 0 0;">No cover image</p></div>');
                $(this).remove();
            });
            
            // Upload from URL
            $('#manhwa-upload-from-url-btn').on('click', function(e) {
                e.preventDefault();
                const url = $('#manhwa-cover-url').val();
                
                if (!url) {
                    alert('Please enter image URL');
                    return;
                }
                
                $(this).text('⏳ Uploading...').prop('disabled', true);
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'manhwa_upload_cover_from_url',
                        url: url,
                        post_id: <?php echo $post->ID; ?>,
                        nonce: '<?php echo wp_create_nonce('manhwa_cover_upload'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#manhwa-cover-id').val(response.data.attachment_id);
                            $('#manhwa-cover-preview').html('<img src="' + response.data.url + '" style="max-width: 100%; height: auto; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">');
                            alert('✅ Cover uploaded successfully!');
                            
                            // Show remove button
                            if ($('#manhwa-remove-cover-btn').length === 0) {
                                $('#manhwa-upload-cover-btn').after('<button type="button" id="manhwa-remove-cover-btn" class="button button-secondary" style="width: 100%; background: #dc3545; color: white; border: none; margin-top: 10px;">🗑️ Remove Cover</button>');
                            }
                        } else {
                            alert('❌ Error: ' + response.data.message);
                        }
                    },
                    error: function() {
                        alert('❌ Upload failed. Please try again.');
                    },
                    complete: function() {
                        $('#manhwa-upload-from-url-btn').text('🌐 Upload from URL').prop('disabled', false);
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * Render Details Meta Box
     */
    public function render_details_meta_box($post) {
        wp_nonce_field('manhwa_details_nonce', 'manhwa_details_nonce_field');
        
        $author = get_post_meta($post->ID, '_manhwa_author', true);
        $artist = get_post_meta($post->ID, '_manhwa_artist', true);
        $rating = get_post_meta($post->ID, '_manhwa_rating', true);
        $views = get_post_meta($post->ID, '_manhwa_views', true);
        $status = get_post_meta($post->ID, '_manhwa_status', true);
        $release_year = get_post_meta($post->ID, '_manhwa_release_year', true);
        $alternative_title = get_post_meta($post->ID, '_manhwa_alternative_title', true);
        $type = get_post_meta($post->ID, '_manhwa_type', true);
        ?>
        <style>
            .manhwa-details-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                padding: 20px;
                background: #f9f9f9;
                border-radius: 10px;
            }
            
            .manhwa-field {
                display: flex;
                flex-direction: column;
            }
            
            .manhwa-field label {
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                font-size: 14px;
            }
            
            .manhwa-field label .icon {
                margin-right: 8px;
                font-size: 16px;
            }
            
            .manhwa-field input,
            .manhwa-field select {
                padding: 12px 15px;
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                font-size: 14px;
                transition: all 0.3s ease;
                background: white;
            }
            
            .manhwa-field input:focus,
            .manhwa-field select:focus {
                outline: none;
                border-color: #3498db;
                box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            }
            
            .manhwa-field input[type="number"] {
                font-weight: 600;
            }
            
            .manhwa-field select {
                cursor: pointer;
            }
            
            .manhwa-field-full {
                grid-column: 1 / -1;
            }
            
            .field-hint {
                font-size: 12px;
                color: #7f8c8d;
                margin-top: 5px;
            }
            
            .status-badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                margin-left: 10px;
            }
            
            .status-ongoing {
                background: #3498db;
                color: white;
            }
            
            .status-completed {
                background: #27ae60;
                color: white;
            }
            
            .status-hiatus {
                background: #f39c12;
                color: white;
            }
        </style>
        
        <div class="manhwa-details-grid">
            <div class="manhwa-field">
                <label>
                    <span class="icon">✍️</span>
                    <strong>Author</strong>
                </label>
                <input type="text" name="manhwa_author" value="<?php echo esc_attr($author); ?>" placeholder="Enter author name">
                <span class="field-hint">The creator/writer of the manhwa</span>
            </div>
            
            <div class="manhwa-field">
                <label>
                    <span class="icon">🎨</span>
                    <strong>Artist</strong>
                </label>
                <input type="text" name="manhwa_artist" value="<?php echo esc_attr($artist); ?>" placeholder="Enter artist name">
                <span class="field-hint">The illustrator/artist</span>
            </div>
            
            <div class="manhwa-field manhwa-field-full">
                <label>
                    <span class="icon">📝</span>
                    <strong>Alternative Title</strong>
                </label>
                <input type="text" name="manhwa_alternative_title" value="<?php echo esc_attr($alternative_title); ?>" placeholder="Alternative title (optional)">
                <span class="field-hint">Other names for this manhwa</span>
            </div>
            
            <div class="manhwa-field">
                <label>
                    <span class="icon">⭐</span>
                    <strong>Rating</strong>
                </label>
                <input type="number" name="manhwa_rating" value="<?php echo esc_attr($rating ?: '7.0'); ?>" step="0.1" min="0" max="10" placeholder="7.0">
                <span class="field-hint">Rating from 0 to 10</span>
            </div>
            
            <div class="manhwa-field">
                <label>
                    <span class="icon">👁️</span>
                    <strong>Views</strong>
                </label>
                <input type="number" name="manhwa_views" value="<?php echo esc_attr($views ?: '0'); ?>" placeholder="0">
                <span class="field-hint">Total view count</span>
            </div>
            
            <div class="manhwa-field">
                <label>
                    <span class="icon">📊</span>
                    <strong>Status</strong>
                    <?php if ($status): ?>
                        <span class="status-badge status-<?php echo esc_attr($status); ?>">
                            <?php echo ucfirst($status); ?>
                        </span>
                    <?php endif; ?>
                </label>
                <select name="manhwa_status">
                    <option value="">Select status...</option>
                    <option value="ongoing" <?php selected($status, 'ongoing'); ?>>Ongoing</option>
                    <option value="completed" <?php selected($status, 'completed'); ?>>Completed</option>
                    <option value="hiatus" <?php selected($status, 'hiatus'); ?>>Hiatus</option>
                </select>
                <span class="field-hint">Current publication status</span>
            </div>
            
            <div class="manhwa-field">
                <label>
                    <span class="icon">📅</span>
                    <strong>Release Year</strong>
                </label>
                <input type="number" name="manhwa_release_year" value="<?php echo esc_attr($release_year); ?>" placeholder="2025">
                <span class="field-hint">Year of first publication</span>
            </div>
            
            <div class="manhwa-field">
                <label>
                    <span class="icon">📖</span>
                    <strong>Type</strong>
                </label>
                <select name="manhwa_type">
                    <option value="">Select type...</option>
                    <option value="Manhwa" <?php selected($type, 'Manhwa'); ?>>🇰🇷 Manhwa (Korean)</option>
                    <option value="Manga" <?php selected($type, 'Manga'); ?>>🇯🇵 Manga (Japanese)</option>
                    <option value="Manhua" <?php selected($type, 'Manhua'); ?>>🇨🇳 Manhua (Chinese)</option>
                    <option value="Comic" <?php selected($type, 'Comic'); ?>>🌍 Comic (Western)</option>
                </select>
                <span class="field-hint">Origin type of the comic</span>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render Chapters Meta Box
     */
    public function render_chapters_meta_box($post) {
        wp_nonce_field('manhwa_chapters_nonce', 'manhwa_chapters_nonce_field');
        
        $chapters = get_post_meta($post->ID, '_manhwa_chapters', true);
        if (!is_array($chapters)) {
            $chapters = array();
        }
        ?>
        <style>
            .chapter-item {
                background: #f5f5f5;
                padding: 15px;
                margin-bottom: 10px;
                border-radius: 8px;
                border: 1px solid #ddd;
            }
            .chapter-item:hover {
                border-color: #2271b1;
            }
            .chapter-header {
                display: grid;
                grid-template-columns: 1fr 1fr 100px;
                gap: 10px;
                align-items: center;
            }
            .chapter-images-section {
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px dashed #ccc;
            }
            .chapter-images-toggle {
                background: #2271b1;
                color: white;
                border: none;
                padding: 5px 10px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 12px;
            }
            .chapter-images-toggle:hover {
                background: #135e96;
            }
            .chapter-images-container {
                margin-top: 10px;
                display: none;
            }
            .chapter-images-container.active {
                display: block;
            }
            .chapter-images-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
                gap: 8px;
                margin-top: 10px;
                max-height: 200px;
                overflow-y: auto;
                padding: 10px;
                background: #fff;
                border-radius: 4px;
            }
            .chapter-image-thumb {
                width: 100%;
                height: 80px;
                object-fit: cover;
                border-radius: 4px;
                border: 1px solid #ddd;
            }
            .chapter-images-count {
                display: inline-block;
                background: #27ae60;
                color: white;
                padding: 2px 8px;
                border-radius: 10px;
                font-size: 11px;
                margin-left: 5px;
            }
            .chapter-images-textarea {
                width: 100%;
                height: 100px;
                font-family: monospace;
                font-size: 11px;
            }
        </style>
        
        <div id="manhwa-chapters-container">
            <div id="chapters-list">
                <?php foreach ($chapters as $index => $chapter) : 
                    $images = isset($chapter['images']) ? $chapter['images'] : array();
                    $images_count = is_array($images) ? count($images) : 0;
                ?>
                    <div class="chapter-item">
                        <div class="chapter-header">
                            <div>
                                <label><strong>Chapter Title:</strong></label>
                                <input type="text" name="manhwa_chapters[<?php echo $index; ?>][title]" value="<?php echo esc_attr($chapter['title']); ?>" style="width: 100%; padding: 8px;">
                            </div>
                            <div>
                                <label><strong>Chapter URL:</strong></label>
                                <input type="text" name="manhwa_chapters[<?php echo $index; ?>][url]" value="<?php echo esc_attr($chapter['url']); ?>" style="width: 100%; padding: 8px;">
                            </div>
                            <div>
                                <button type="button" class="button remove-chapter" style="background: #dc3545; color: white; border: none;">Remove</button>
                            </div>
                        </div>
                        <div style="margin-top: 10px; display: flex; gap: 15px; align-items: center;">
                            <div>
                                <label><strong>Release Date:</strong></label>
                                <input type="date" name="manhwa_chapters[<?php echo $index; ?>][date]" value="<?php echo esc_attr($chapter['date']); ?>" style="padding: 8px;">
                            </div>
                        </div>
                        
                        <!-- Images Section -->
                        <div class="chapter-images-section">
                            <button type="button" class="chapter-images-toggle" onclick="toggleChapterImages(this)">
                                📷 Images
                                <?php if ($images_count > 0): ?>
                                    <span class="chapter-images-count"><?php echo $images_count; ?></span>
                                <?php endif; ?>
                            </button>
                            
                            <div class="chapter-images-container">
                                <?php if ($images_count > 0): ?>
                                    <div class="chapter-images-grid">
                                        <?php foreach ($images as $img_index => $img): 
                                            $img_url = is_array($img) ? ($img['url'] ?? '') : $img;
                                        ?>
                                            <img src="<?php echo esc_url($img_url); ?>" class="chapter-image-thumb" loading="lazy" onerror="this.style.display='none'">
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div style="margin-top: 10px;">
                                    <label><strong>Image URLs (one per line):</strong></label>
                                    <textarea name="manhwa_chapters[<?php echo $index; ?>][images_text]" class="chapter-images-textarea" placeholder="https://example.com/page1.jpg&#10;https://example.com/page2.jpg"><?php 
                                        if ($images_count > 0) {
                                            $urls = array_map(function($img) {
                                                return is_array($img) ? ($img['url'] ?? '') : $img;
                                            }, $images);
                                            echo esc_textarea(implode("\n", $urls));
                                        }
                                    ?></textarea>
                                </div>
                                
                                <!-- Hidden field to store images as JSON -->
                                <input type="hidden" name="manhwa_chapters[<?php echo $index; ?>][images_json]" value="<?php echo esc_attr(json_encode($images)); ?>" class="images-json-field">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button type="button" id="add-chapter" class="button button-primary" style="margin-top: 10px;">
                + Add Chapter
            </button>
        </div>
        
        <script>
        function toggleChapterImages(btn) {
            var container = btn.parentNode.querySelector('.chapter-images-container');
            container.classList.toggle('active');
            btn.textContent = container.classList.contains('active') ? '📷 Hide Images' : '📷 Images';
            
            // Restore count badge
            var countBadge = btn.getAttribute('data-count');
            if (countBadge) {
                btn.innerHTML += ' <span class="chapter-images-count">' + countBadge + '</span>';
            }
        }
        
        jQuery(document).ready(function($) {
            let chapterIndex = <?php echo count($chapters); ?>;
            
            $('#add-chapter').on('click', function() {
                const chapterHtml = `
                    <div class="chapter-item">
                        <div class="chapter-header">
                            <div>
                                <label><strong>Chapter Title:</strong></label>
                                <input type="text" name="manhwa_chapters[${chapterIndex}][title]" style="width: 100%; padding: 8px;">
                            </div>
                            <div>
                                <label><strong>Chapter URL:</strong></label>
                                <input type="text" name="manhwa_chapters[${chapterIndex}][url]" style="width: 100%; padding: 8px;">
                            </div>
                            <div>
                                <button type="button" class="button remove-chapter" style="background: #dc3545; color: white; border: none;">Remove</button>
                            </div>
                        </div>
                        <div style="margin-top: 10px; display: flex; gap: 15px; align-items: center;">
                            <div>
                                <label><strong>Release Date:</strong></label>
                                <input type="date" name="manhwa_chapters[${chapterIndex}][date]" style="padding: 8px;">
                            </div>
                        </div>
                        
                        <div class="chapter-images-section">
                            <button type="button" class="chapter-images-toggle" onclick="toggleChapterImages(this)">
                                📷 Images
                            </button>
                            
                            <div class="chapter-images-container">
                                <div style="margin-top: 10px;">
                                    <label><strong>Image URLs (one per line):</strong></label>
                                    <textarea name="manhwa_chapters[${chapterIndex}][images_text]" class="chapter-images-textarea" placeholder="https://example.com/page1.jpg&#10;https://example.com/page2.jpg"></textarea>
                                </div>
                                <input type="hidden" name="manhwa_chapters[${chapterIndex}][images_json]" value="[]" class="images-json-field">
                            </div>
                        </div>
                    </div>
                `;
                $('#chapters-list').append(chapterHtml);
                chapterIndex++;
            });
            
            $(document).on('click', '.remove-chapter', function() {
                $(this).closest('.chapter-item').remove();
            });
            
            // Convert textarea to JSON on form submit
            $('form#post').on('submit', function() {
                $('.chapter-images-textarea').each(function() {
                    var urls = $(this).val().split('\n').filter(function(url) {
                        return url.trim() !== '';
                    }).map(function(url) {
                        return { url: url.trim() };
                    });
                    $(this).siblings('.images-json-field').val(JSON.stringify(urls));
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * AJAX: Upload Cover from URL
     */
    public function ajax_upload_cover_from_url() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'manhwa_cover_upload')) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
        }
        
        // Check permissions
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Permission denied'));
        }
        
        $url = esc_url_raw($_POST['url']);
        $post_id = intval($_POST['post_id']);
        
        if (empty($url)) {
            wp_send_json_error(array('message' => 'Invalid URL'));
        }
        
        // Download image
        $response = wp_remote_get($url, array(
            'timeout' => 30,
            'sslverify' => false
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }
        
        $image_data = wp_remote_retrieve_body($response);
        
        if (empty($image_data)) {
            wp_send_json_error(array('message' => 'Failed to download image'));
        }
        
        // Get filename
        $filename = basename(parse_url($url, PHP_URL_PATH));
        $filename = preg_replace('/\?.*/', '', $filename);
        $filename = sanitize_file_name($filename);
        
        if (!preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $filename)) {
            $filename .= '.jpg';
        }
        
        // Upload to WordPress
        $upload = wp_upload_bits($filename, null, $image_data);
        
        if ($upload['error']) {
            wp_send_json_error(array('message' => $upload['error']));
        }
        
        // Create attachment
        $attachment = array(
            'post_mime_type' => $upload['type'],
            'post_title' => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        $attachment_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
        
        if (is_wp_error($attachment_id)) {
            wp_send_json_error(array('message' => $attachment_id->get_error_message()));
        }
        
        // Generate metadata
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
        wp_update_attachment_metadata($attachment_id, $attachment_data);
        
        // Set as featured image
        set_post_thumbnail($post_id, $attachment_id);
        
        // Save URL to meta
        update_post_meta($post_id, '_manhwa_cover_url', $url);
        
        wp_send_json_success(array(
            'attachment_id' => $attachment_id,
            'url' => wp_get_attachment_url($attachment_id)
        ));
    }
    
    /**
     * Save Meta Boxes
     */
    public function save_meta_boxes($post_id) {
        // Check nonce
        if (!isset($_POST['manhwa_details_nonce_field']) || 
            !wp_verify_nonce($_POST['manhwa_details_nonce_field'], 'manhwa_details_nonce')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save cover
        if (isset($_POST['manhwa_cover_id'])) {
            $cover_id = intval($_POST['manhwa_cover_id']);
            if ($cover_id > 0) {
                set_post_thumbnail($post_id, $cover_id);
            } else {
                delete_post_thumbnail($post_id);
            }
        }
        
        if (isset($_POST['manhwa_cover_url'])) {
            update_post_meta($post_id, '_manhwa_cover_url', esc_url_raw($_POST['manhwa_cover_url']));
        }
        
        // Save details
        if (isset($_POST['manhwa_author'])) {
            update_post_meta($post_id, '_manhwa_author', sanitize_text_field($_POST['manhwa_author']));
        }
        
        if (isset($_POST['manhwa_artist'])) {
            update_post_meta($post_id, '_manhwa_artist', sanitize_text_field($_POST['manhwa_artist']));
        }
        
        if (isset($_POST['manhwa_alternative_title'])) {
            update_post_meta($post_id, '_manhwa_alternative_title', sanitize_text_field($_POST['manhwa_alternative_title']));
        }
        
        if (isset($_POST['manhwa_type'])) {
            update_post_meta($post_id, '_manhwa_type', sanitize_text_field($_POST['manhwa_type']));
        }
        
        if (isset($_POST['manhwa_rating'])) {
            update_post_meta($post_id, '_manhwa_rating', floatval($_POST['manhwa_rating']));
        }
        
        if (isset($_POST['manhwa_views'])) {
            update_post_meta($post_id, '_manhwa_views', intval($_POST['manhwa_views']));
        }
        
        if (isset($_POST['manhwa_status'])) {
            update_post_meta($post_id, '_manhwa_status', sanitize_text_field($_POST['manhwa_status']));
        }
        
        if (isset($_POST['manhwa_release_year'])) {
            update_post_meta($post_id, '_manhwa_release_year', intval($_POST['manhwa_release_year']));
        }
        
        // Save chapters
        if (isset($_POST['manhwa_chapters']) && is_array($_POST['manhwa_chapters'])) {
            $chapters = array();
            foreach ($_POST['manhwa_chapters'] as $chapter) {
                // Get images from JSON field or parse from text
                $images = array();
                if (!empty($chapter['images_json'])) {
                    $images = json_decode(stripslashes($chapter['images_json']), true);
                    if (!is_array($images)) {
                        $images = array();
                    }
                } elseif (!empty($chapter['images_text'])) {
                    // Parse from textarea
                    $lines = explode("\n", $chapter['images_text']);
                    foreach ($lines as $line) {
                        $url = trim($line);
                        if (!empty($url)) {
                            $images[] = array('url' => esc_url_raw($url));
                        }
                    }
                }
                
                $chapters[] = array(
                    'title' => sanitize_text_field($chapter['title']),
                    'url' => esc_url_raw($chapter['url']),
                    'date' => sanitize_text_field($chapter['date']),
                    'images' => $images,
                );
            }
            update_post_meta($post_id, '_manhwa_chapters', $chapters);
        }
    }
}
