# MangaZen Theme

A modern dark theme for manga/manhwa reading websites. Clean design, excellent readability, and zen-like reading experience.

![MangaZen](screenshot.png)

---

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- **Manhwa Manager** plugin (required for full functionality)
- **Manhwa Scraper** plugin (optional, for auto-importing content)

---

## 🚀 Quick Installation Guide

### Step 1: Persiapan

Pastikan Anda sudah memiliki:

- ✅ WordPress sudah terinstall
- ✅ File theme `mangazen` (folder atau ZIP)
- ✅ Plugin `manhwa-manager` (wajib)
- ✅ Plugin `manhwa-scraper` (opsional)

---

### Step 2: Install Theme

#### Cara A: Upload via FTP/File Manager (Recommended)

1. **Extract** file ZIP theme jika masih dalam format ZIP
2. **Upload** folder `mangazen` ke:
   ```
   wp-content/themes/mangazen
   ```
3. Pastikan struktur folder seperti ini:
   ```
   wp-content/
   └── themes/
       └── mangazen/
           ├── style.css
           ├── functions.php
           ├── index.php
           ├── inc/
           └── ... (file lainnya)
   ```

#### Cara B: Upload via WordPress Admin

1. Buka **WordPress Admin → Appearance → Themes**
2. Klik **Add New → Upload Theme**
3. Pilih file `mangazen.zip`
4. Klik **Install Now**
5. Tunggu sampai selesai

#### Cara C: Local (XAMPP/Laragon)

1. Copy folder `mangazen` ke:
   ```
   C:\xampp\htdocs\wordpress\wp-content\themes\
   ```
   atau
   ```
   C:\laragon\www\wordpress\wp-content\themes\
   ```

---

### Step 3: Activate Theme

1. Buka **Appearance → Themes**
2. Cari theme **MangaZen**
3. Klik **Activate**

---

### Step 4: Install Required Plugin

⚠️ **PENTING**: Theme ini membutuhkan plugin **Manhwa Manager**!

1. Buka **Plugins → Add New → Upload Plugin**
2. Upload file `manhwa-manager.zip`
3. Klik **Install Now**
4. Klik **Activate**

**Opsional**: Install juga `manhwa-scraper` untuk auto import dari website lain.

---

### Step 5: Set Permalink

1. Buka **Settings → Permalinks**
2. Pilih **Post name**:
   ```
   /%postname%/
   ```
3. Klik **Save Changes**

---

### Step 6: Create Menu

1. Buka **Appearance → Menus**
2. Klik **Create a new menu**
3. Nama menu: `Main Menu`
4. Tambahkan halaman yang diinginkan:
   - Home
   - Daftar Komik
   - Bookmark
   - Contact
5. Di bagian **Menu Settings**, centang **Main Menu**
6. Klik **Save Menu**

---

### Step 7: Create Required Pages

Buat halaman-halaman berikut:

| Judul Halaman | Slug (URL)     | Keterangan           |
| ------------- | -------------- | -------------------- |
| Login         | `login`        | Halaman login user   |
| Register      | `register`     | Halaman daftar user  |
| Profile       | `profile`      | Halaman profil user  |
| Bookmark      | `bookmark`     | Daftar komik favorit |
| History       | `history`      | Riwayat baca         |
| Kontak        | `kontak`       | Halaman kontak       |
| Download Pdf  | `download-pdf` | Halaman download pdf |

**Cara membuat:**

1. Buka **Pages → Add New**
2. Isi **Title** dengan nama halaman
3. Biarkan konten kosong (template akan handle)
4. Di **Page Attributes**, pilih template yang sesuai jika ada
5. Klik **Publish**

---

### Step 8: Configure Theme Options

1. Buka **Appearance → Theme Options** di menu admin
2. Konfigurasi:

#### 🎨 General Settings

- Logo website
- Favicon
- Site title & tagline

#### 📢 Ads Management

- Below Main Menu (2x2 grid)
- Homepage Top (2x2 grid)
- Series Page Ads (2x2 grid)
- Chapter Reader Ads
- Floating Ads
- **Adsterra** (Popunder, Social Bar, Smartlink)
- **Direct Link** (Multi URL support)

#### 🎯 Hero Slider

- Enable/disable slider
- Mode: Manual / Latest / Popular / Rating
- Autoplay settings

#### 📊 Popular Today

- Mode: Manual / Views / Rating
- Jumlah item

#### 🎨 Color Settings

- Preset warna
- Custom colors

#### 📈 Analytics & Tracking

- Histats Counter
- Google Analytics
- Custom Head/Footer code

---

## ✅ Checklist Instalasi

Pastikan semua sudah dilakukan:

- [ ] Theme `mangazen` sudah di folder `wp-content/themes/`
- [ ] Theme sudah diaktifkan
- [ ] Plugin `manhwa-manager` sudah aktif
- [ ] Permalink sudah diset ke **Post name**
- [ ] Menu sudah dibuat dan di-assign
- [ ] Halaman Login, Register, Profile, Bookmark, History sudah dibuat
- [ ] Theme Options sudah dikonfigurasi

---

## 🎨 Features

### Homepage

- ✅ Hero Slider with featured manhwa
- ✅ Popular Today section
- ✅ Latest Updates grid
- ✅ Announcement bar

### Manhwa Detail Page

- ✅ Blurred background effect
- ✅ Rating display
- ✅ Genre tags
- ✅ Chapter list with search
- ✅ Bookmark functionality
- ✅ Related manhwa

### Chapter Reader

- ✅ Clean reading experience
- ✅ Keyboard navigation (← →)
- ✅ Reading progress bar
- ✅ Chapter selector dropdown
- ✅ PDF download option

### User Features

- ✅ User registration/login
- ✅ Profile page with avatar
- ✅ Bookmark system
- ✅ Reading history
- ✅ User leveling system
- ✅ Comment with emoji support

### SEO

- ✅ Schema.org markup
- ✅ OpenGraph tags
- ✅ Twitter Cards
- ✅ Custom meta descriptions
- ✅ Breadcrumb navigation

### Ads Features

- ✅ 2x2 Grid Ads Layout
- ✅ Adsterra Integration (Popunder, Social Bar, Smartlink)
- ✅ Direct Link Ads (Multi URL, Random Rotation)
- ✅ Responsive Ads

---

## 📁 Theme Structure

```
mangazen/
├── assets/
│   ├── css/
│   ├── js/
│   │   └── main.js
│   └── images/
├── inc/
│   ├── seo.php              # SEO functions
│   ├── theme-options.php    # Theme settings
│   ├── user-account.php     # User system
│   ├── user-level.php       # Leveling system
│   └── pdf-generator.php    # PDF download
├── template-parts/
│   ├── hero-slider.php      # Homepage slider
│   ├── popular-today.php    # Popular section
│   └── latest-updates.php   # Latest updates
├── functions.php            # Main functions
├── style.css                # Main stylesheet
├── index.php                # Homepage
├── single.php               # Single post
├── single-manhwa.php        # Manhwa detail
├── chapter-reader.php       # Chapter reader
├── archive-manhwa.php       # Manhwa archive
├── page-*.php               # Page templates
├── header.php               # Header
├── footer.php               # Footer
├── sidebar.php              # Sidebar
└── screenshot.png           # Theme preview
```

---

## 🔧 Customization

### Change Accent Color

Edit `style.css` or use Theme Options:

```css
:root {
  --color-accent: #366ad3; /* Main accent */
  --color-accent-hover: #2555b3; /* Hover state */
}
```

### Add Custom CSS

Go to **Appearance → Customize → Additional CSS**

---

## ❓ Troubleshooting

### Theme not showing manhwa?

- Make sure **Manhwa Manager** plugin is installed and activated
- Check if you have created manhwa posts

### Menu not appearing?

- Go to **Appearance → Menus**
- Create menu and assign to "Main Menu" location

### Styles not loading?

- Clear browser cache
- Check if `style.css` exists in theme folder

### Ads not showing?

- Check Theme Options → Ads Management
- Make sure ad codes are correctly pasted
- Check browser console for errors

### Direct Link not working?

- Enable di Theme Options → Ads Management → Floating Ads
- Isi URL yang valid
- Test di browser mode Incognito

---

## 📄 License

GNU General Public License v2 or later

---

## 👨‍💻 Credits

- Font Awesome - Icons
- Google Fonts - Fira Sans, Roboto
- WordPress - Platform

---

**MangaZen** - Zen-like Reading Experience 🧘‍♂️
