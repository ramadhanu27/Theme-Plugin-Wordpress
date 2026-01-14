=== Manhwa Manager ===
Contributors: manhwa-reader
Tags: manhwa, webtoon, comic, manga, json
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin untuk mengelola list komik dan detail manhwa dengan export/import JSON.

== Description ==

Manhwa Manager adalah plugin WordPress yang powerful untuk mengelola koleksi manhwa/webtoon Anda. Plugin ini menyediakan fitur lengkap untuk upload, manage, dan export/import data manhwa dalam format JSON.

**Fitur Utama:**

* 📚 Custom Post Type untuk Manhwa
* 📤 Upload list manhwa dari file JSON
* 💾 Export semua data manhwa ke JSON
* 📥 Import data manhwa dari JSON backup
* 📖 Management chapter untuk setiap manhwa
* 🎨 Genre taxonomy
* ⭐ Rating dan views tracking
* 👤 Author dan Artist metadata
* 📊 Dashboard dengan statistik
* 🖼️ Auto download thumbnail dari URL

**Format JSON yang Didukung:**

Plugin mendukung multiple format JSON untuk fleksibilitas maksimal:

**Format 1: Standard (dengan wrapper "manhwa")**
```json
{
  "manhwa": [
    {
      "title": "Solo Leveling",
      "description": "Description here...",
      "author": "Chugong",
      "artist": "DUBU",
      "genres": ["Action", "Fantasy"],
      "status": "completed",
      "rating": 4.8,
      "thumbnail_url": "https://example.com/image.jpg"
    }
  ]
}
```

**Format 2: Komiku/Scraper (direct array)**
```json
[
  {
    "slug": "solo-leveling",
    "title": "Solo Leveling",
    "image": "https://example.com/image.jpg",
    "synopsis": "Description here...",
    "genres": ["Action", "Fantasy"],
    "status": "Ongoing",
    "type": "Manhwa",
    "rating": 4.8,
    "totalChapters": 179
  }
]
```

**Format 3: Single Object**
```json
{
  "title": "Solo Leveling",
  "synopsis": "Description...",
  "genres": ["Action"]
}
```

== Installation ==

1. Upload folder `manhwa-manager` ke direktori `/wp-content/plugins/`
2. Aktifkan plugin melalui menu 'Plugins' di WordPress
3. Buka menu 'Manhwa Manager' di admin dashboard
4. Mulai upload dan manage manhwa Anda!

== Frequently Asked Questions ==

= Apakah plugin ini gratis? =

Ya, plugin ini sepenuhnya gratis dan open source.

= Format JSON apa yang didukung? =

Plugin mendukung format JSON dengan struktur yang dijelaskan di dokumentasi.

= Apakah bisa import thumbnail dari URL? =

Ya, plugin akan otomatis download dan set thumbnail dari URL yang disediakan.

= Berapa banyak manhwa yang bisa di-import? =

Tidak ada batasan, tapi proses import untuk file besar mungkin membutuhkan waktu.

== Screenshots ==

1. Dashboard dengan statistik
2. Upload JSON interface
3. Chapter management
4. Export/Import page

== Changelog ==

= 1.0.0 =
* Initial release
* Custom post type untuk manhwa
* JSON upload/export/import
* Chapter management
* Dashboard statistics

== Upgrade Notice ==

= 1.0.0 =
Initial release of Manhwa Manager plugin.
