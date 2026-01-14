# Manhwa Manager

A comprehensive WordPress plugin for managing manhwa/manga/manhua content. Create, organize, and display your comic library with ease.

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Recommended theme: **MangaZen**

## 🚀 Installation

### Method 1: Upload via WordPress Admin

1. Download the `manhwa-manager` folder as a ZIP file
2. Go to **Plugins > Add New > Upload Plugin**
3. Choose the ZIP file and click **Install Now**
4. After installation, click **Activate**

### Method 2: FTP Upload

1. Download the `manhwa-manager` folder
2. Connect to your server via FTP
3. Upload the folder to `/wp-content/plugins/`
4. Go to **Plugins** in WordPress admin
5. Find **Manhwa Manager** and click **Activate**

### Method 3: Manual Copy (Local Development)

```bash
# Copy plugin folder to WordPress plugins directory
cp -r manhwa-manager /path/to/wordpress/wp-content/plugins/
```

## ⚙️ Initial Setup

### After Activation

1. Go to **Manhwa Manager** in the admin menu
2. The plugin will automatically create:
   - Custom post type: `manhwa`
   - Taxonomies: `manhwa_genre`, `manhwa_author`, `manhwa_artist`
   - Database tables for additional data

### Configure Permalinks

1. Go to **Settings > Permalinks**
2. Click **Save Changes** (to flush rewrite rules)
3. Your manhwa URLs will be: `yoursite.com/manhwa/slug-name/`

## 📁 Plugin Structure

```
manhwa-manager/
├── includes/
│   ├── class-manhwa-manager.php      # Main plugin class
│   ├── class-manhwa-post-type.php    # CPT registration
│   ├── class-manhwa-meta-boxes.php   # Admin meta boxes
│   ├── class-manhwa-taxonomies.php   # Taxonomies
│   └── class-manhwa-ajax.php         # AJAX handlers
├── admin/
│   ├── css/
│   │   └── admin-style.css
│   ├── js/
│   │   └── admin-script.js
│   └── views/
│       ├── dashboard.php
│       └── settings.php
├── public/
│   ├── css/
│   └── js/
├── templates/
│   ├── single-manhwa.php
│   ├── archive-manhwa.php
│   └── chapter-reader.php
├── manhwa-manager.php                 # Main plugin file
└── uninstall.php                      # Cleanup on uninstall
```

## 📖 Usage

### Creating a Manhwa

1. Go to **Manhwa > Add New**
2. Fill in the details:

| Field             | Description                     |
| ----------------- | ------------------------------- |
| Title             | Manhwa title                    |
| Content           | Synopsis/description            |
| Featured Image    | Cover image                     |
| Type              | Manhwa, Manga, Manhua, or Comic |
| Status            | Ongoing, Completed, or Hiatus   |
| Author            | Original author name            |
| Artist            | Artist name                     |
| Release Year      | Year of first release           |
| Rating            | Score from 0-10                 |
| Alternative Title | Other names/aliases             |

3. Add **Genres** from the sidebar
4. Click **Publish**

### Adding Chapters

1. Edit a manhwa post
2. Scroll to **Chapters** meta box
3. Click **Add Chapter**
4. Fill in:
   - Chapter Title (e.g., "Chapter 1", "Chapter 1.5")
   - Images (upload or paste URLs)
5. Click **Save**

### Managing Chapters

- **Reorder**: Drag and drop chapters
- **Edit**: Click on chapter to expand
- **Delete**: Click the trash icon
- **Bulk Add**: Paste multiple image URLs

### Using Image URLs

For external images, paste URLs one per line:

```
https://example.com/chapter1/001.jpg
https://example.com/chapter1/002.jpg
https://example.com/chapter1/003.jpg
```

### Uploading Images

1. Click **Upload Images** button
2. Select multiple images from media library
3. Images are auto-ordered by filename

## 🔧 Settings

Go to **Manhwa Manager > Settings**:

### General Settings

- Posts per page on archive
- Default thumbnail size
- Enable/disable comments

### Display Settings

- Chapter list style (grid/list)
- Show rating on cards
- Show view count

### Advanced Settings

- Image CDN URL prefix
- Lazy loading
- Cache duration

## 📊 Dashboard

The dashboard shows:

- Total manhwa count
- Total chapters
- Recent updates
- Quick stats by type/status

## 🔌 Integration with Manhwa Scraper

This plugin works seamlessly with **Manhwa Scraper**:

- Scraped manhwa are saved as manhwa posts
- Chapters are automatically organized
- Images can be downloaded locally or kept as external URLs

## 🎣 Hooks & Filters

### Actions

```php
// After manhwa is saved
do_action('manhwa_manager_after_save', $post_id, $post);

// After chapter is added
do_action('manhwa_manager_chapter_added', $manhwa_id, $chapter_data);
```

### Filters

```php
// Modify manhwa data before save
apply_filters('manhwa_manager_pre_save', $data, $post_id);

// Modify chapter images
apply_filters('manhwa_manager_chapter_images', $images, $chapter_id);

// Modify archive query
apply_filters('manhwa_manager_archive_query', $args);
```

## 🗄️ Database

### Post Meta Keys

| Meta Key               | Description                       |
| ---------------------- | --------------------------------- |
| `_manhwa_type`         | Type (Manhwa/Manga/Manhua/Comic)  |
| `_manhwa_status`       | Status (Ongoing/Completed/Hiatus) |
| `_manhwa_rating`       | Rating score (0-10)               |
| `_manhwa_views`        | View count                        |
| `_manhwa_author`       | Author name                       |
| `_manhwa_artist`       | Artist name                       |
| `_manhwa_release_year` | Release year                      |
| `_manhwa_alternative`  | Alternative titles                |
| `_manhwa_chapters`     | Array of chapters                 |

### Chapter Data Structure

```php
[
    'title' => 'Chapter 1',
    'slug' => 'chapter-1',
    'images' => [
        'https://example.com/img1.jpg',
        'https://example.com/img2.jpg',
    ],
    'date' => '2024-01-01',
]
```

## ❓ Troubleshooting

### Manhwa not showing?

- Check if plugin is activated
- Flush permalinks: **Settings > Permalinks > Save**
- Check if theme supports the post type

### Chapters not saving?

- Check PHP max_input_vars (increase if needed)
- Check post meta size limits
- Check error logs

### Images not loading?

- Verify image URLs are accessible
- Check for CORS issues with external images
- Try enabling "Download to local" option in Scraper

## 📄 License

GNU General Public License v2 or later

---

**Manhwa Manager** - Your Complete Comic Library Solution 📚
