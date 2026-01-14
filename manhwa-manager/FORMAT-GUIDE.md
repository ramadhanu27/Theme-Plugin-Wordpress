# Manhwa Manager - JSON Format Guide

Plugin Manhwa Manager mendukung **3 format JSON** yang berbeda untuk memaksimalkan fleksibilitas import data dari berbagai sumber.

---

## 📋 Format 1: Standard Format (Recommended)

Format ini menggunakan wrapper object dengan key `"manhwa"` yang berisi array.

### Struktur:
```json
{
  "export_date": "2024-01-01 00:00:00",
  "version": "1.0",
  "manhwa": [
    {
      "title": "Solo Leveling",
      "description": "Full description...",
      "excerpt": "Short excerpt...",
      "slug": "solo-leveling",
      "author": "Chugong",
      "artist": "DUBU (Redice Studio)",
      "genres": ["Action", "Fantasy", "Adventure"],
      "status": "completed",
      "rating": 4.9,
      "views": 15000000,
      "release_year": 2018,
      "thumbnail_url": "https://example.com/image.jpg",
      "chapters": [
        {
          "title": "Chapter 1: The Weakest Hunter",
          "url": "https://example.com/chapter-1",
          "date": "2024-01-01"
        }
      ]
    }
  ]
}
```

### Field Descriptions:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `title` | string | ✅ Yes | Judul manhwa |
| `description` | string | ❌ No | Deskripsi lengkap |
| `excerpt` | string | ❌ No | Ringkasan singkat |
| `slug` | string | ❌ No | URL slug (auto-generated jika kosong) |
| `author` | string | ❌ No | Nama author/penulis |
| `artist` | string | ❌ No | Nama artist/illustrator |
| `genres` | array | ❌ No | Array genre |
| `status` | string | ❌ No | Status: "ongoing", "completed", "hiatus" |
| `rating` | number | ❌ No | Rating 0-5 |
| `views` | number | ❌ No | Jumlah views |
| `release_year` | number | ❌ No | Tahun rilis |
| `thumbnail_url` | string | ❌ No | URL gambar thumbnail |
| `chapters` | array | ❌ No | Array chapters |

---

## 📋 Format 2: Komiku/Scraper Format

Format ini adalah direct array tanpa wrapper, cocok untuk hasil scraping dari website seperti Komiku.

### Struktur:
```json
[
  {
    "slug": "solo-leveling",
    "title": "Solo Leveling",
    "image": "https://example.com/image.jpg",
    "synopsis": "Full synopsis...",
    "genres": ["Action", "Fantasy", "Adventure"],
    "status": "Ongoing",
    "type": "Manhwa",
    "rating": 4.9,
    "totalChapters": 179,
    "scrapedAt": "2025-10-30T11:13:39.892Z"
  },
  {
    "slug": "tower-of-god",
    "title": "Tower of God",
    "image": "https://example.com/image2.jpg",
    "synopsis": "Another synopsis...",
    "genres": ["Action", "Drama"],
    "status": "Ongoing",
    "type": "Manhwa",
    "rating": 4.7,
    "totalChapters": 590,
    "scrapedAt": "2025-10-30T11:14:20.123Z"
  }
]
```

### Field Descriptions:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `slug` | string | ✅ Yes* | URL slug |
| `title` | string | ✅ Yes* | Judul manhwa |
| `image` | string | ❌ No | URL gambar (alias untuk thumbnail_url) |
| `synopsis` | string | ❌ No | Sinopsis (alias untuk description) |
| `genres` | array | ❌ No | Array genre |
| `status` | string | ❌ No | Status (case-insensitive) |
| `type` | string | ❌ No | Tipe: "Manga", "Manhwa", "Manhua" |
| `rating` | number/null | ❌ No | Rating 0-5 atau null |
| `totalChapters` | number | ❌ No | Total jumlah chapter |
| `scrapedAt` | string | ❌ No | Timestamp scraping |

*Minimal salah satu dari `slug` atau `title` harus ada.

---

## 📋 Format 3: Single Object

Format untuk import single manhwa.

### Struktur:
```json
{
  "slug": "solo-leveling",
  "title": "Solo Leveling",
  "image": "https://example.com/image.jpg",
  "synopsis": "Description...",
  "genres": ["Action", "Fantasy"],
  "status": "Ongoing",
  "type": "Manhwa",
  "rating": 4.9,
  "totalChapters": 179
}
```

---

## 🔄 Field Mapping & Normalization

Plugin secara otomatis menormalisasi field dari berbagai format:

| Standard Format | Komiku Format | Normalized To |
|----------------|---------------|---------------|
| `description` | `synopsis` | `description` |
| `thumbnail_url` | `image` | `thumbnail_url` |
| `status` (lowercase) | `status` (any case) | `status` (lowercase) |
| `excerpt` | - | `excerpt` |
| `author` | - | `author` |
| `artist` | - | `artist` |
| `release_year` | - | `release_year` |
| - | `type` | `type` |
| - | `totalChapters` | `totalChapters` |
| - | `scrapedAt` | `scrapedAt` |

---

## 📝 Examples

### Example 1: Import dari Komiku Scraper
```json
[
  {
    "slug": "0-magic-a-high-spirit-and-a-demonic-sword",
    "title": "Komik 0 Magic, a High Spirit, and a Demonic Sword",
    "image": "https://thumbnail.komiku.org/uploads/manga/0-magic.jpg",
    "synopsis": "Cerita ini mengikuti petualangan...",
    "genres": ["Adventure", "Fantasy", "Magic", "Shounen"],
    "status": "Ongoing",
    "type": "Manga",
    "rating": null,
    "totalChapters": 3,
    "scrapedAt": "2025-10-30T11:13:39.892Z"
  }
]
```

### Example 2: Export dari Plugin (Standard Format)
```json
{
  "export_date": "2024-01-01 12:00:00",
  "version": "1.0",
  "manhwa": [
    {
      "title": "Solo Leveling",
      "description": "10 years ago, after the Gate...",
      "excerpt": "An E-rank Hunter becomes...",
      "slug": "solo-leveling",
      "author": "Chugong",
      "artist": "DUBU",
      "genres": ["Action", "Fantasy"],
      "status": "completed",
      "rating": 4.9,
      "views": 15000000,
      "release_year": 2018,
      "thumbnail_url": "https://example.com/solo-leveling.jpg",
      "chapters": [
        {
          "title": "Chapter 1: The Weakest Hunter",
          "url": "https://example.com/solo-leveling/chapter-1",
          "date": "2018-03-04"
        }
      ]
    }
  ]
}
```

---

## ⚙️ Import Options

Saat melakukan import, Anda memiliki opsi:

1. **Overwrite Existing** - Timpa manhwa yang sudah ada dengan data baru
2. **Skip Existing** - Lewati manhwa yang sudah ada, hanya import yang baru

---

## 🎯 Best Practices

1. **Gunakan slug yang konsisten** - Slug digunakan untuk identifikasi unik
2. **Sertakan thumbnail URL** - Plugin akan auto-download dan set sebagai featured image
3. **Gunakan genre yang konsisten** - Genre akan dibuat otomatis jika belum ada
4. **Status lowercase** - Plugin akan normalize, tapi lebih baik gunakan lowercase
5. **Rating 0-5** - Gunakan format desimal (contoh: 4.8, 4.5)
6. **Validate JSON** - Pastikan JSON valid sebelum upload

---

## 🚨 Common Errors & Solutions

### Error: "Invalid JSON format"
**Solusi:** Validate JSON menggunakan tool seperti jsonlint.com

### Error: "Missing title or slug"
**Solusi:** Pastikan setiap manhwa memiliki minimal field `title` atau `slug`

### Error: "Failed to download thumbnail"
**Solusi:** 
- Pastikan URL thumbnail valid dan accessible
- Check apakah server mengizinkan external requests
- Gunakan URL direct ke image (bukan halaman HTML)

---

## 📦 Sample Files

Plugin menyediakan 2 sample files:

1. **sample-manhwa.json** - Standard format dengan 6 manhwa populer
2. **sample-komiku-format.json** - Komiku format dengan 5 manhwa

Gunakan sample files ini sebagai template untuk membuat JSON Anda sendiri.

---

## 🔗 Resources

- [JSON Validator](https://jsonlint.com/)
- [WordPress Post Meta](https://developer.wordpress.org/reference/functions/update_post_meta/)
- [WordPress Taxonomies](https://developer.wordpress.org/reference/functions/wp_set_post_terms/)

---

**Version:** 1.0.0  
**Last Updated:** October 30, 2025
