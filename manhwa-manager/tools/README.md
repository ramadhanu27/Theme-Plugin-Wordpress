# Manhwa Manager - Tools

## 📦 JSON Splitter Tool

Tool ini membantu Anda split file JSON besar menjadi beberapa file kecil untuk memudahkan import ke WordPress.

### 🎯 Kenapa Perlu Split?

File JSON yang sangat besar (ribuan manhwa) dapat menyebabkan:
- ❌ Timeout saat upload
- ❌ Memory limit exceeded
- ❌ Server overload
- ❌ Import gagal

Dengan split file menjadi bagian-bagian kecil (100-200 items per file), proses import akan lebih stabil dan reliable.

---

## 🚀 Cara Menggunakan

### Method 1: Menggunakan Batch File (Windows - Recommended)

1. **Copy file `metadata.json` ke folder WordPress root**
   ```
   c:\xampp\htdocs\wordpress\metadata.json
   ```

2. **Jalankan batch file**
   - Double-click file `split-metadata.bat` di folder WordPress root
   - Script akan otomatis split file menjadi bagian-bagian kecil

3. **File hasil split akan tersimpan di:**
   ```
   c:\xampp\htdocs\wordpress\split\
   ```

4. **Upload satu per satu ke WordPress**
   - Buka WordPress Admin → Manhwa Manager → Upload List
   - Upload `metadata_part1.json`
   - Tunggu sampai selesai
   - Upload `metadata_part2.json`
   - Dan seterusnya...

---

### Method 2: Menggunakan Command Line

```bash
# Masuk ke folder WordPress
cd c:\xampp\htdocs\wordpress

# Jalankan splitter
php wp-content\plugins\manhwa-manager\tools\split-json.php metadata.json 100
```

**Parameters:**
- `metadata.json` - File input yang akan di-split
- `100` - Jumlah items per file (optional, default: 100)

---

## ⚙️ Konfigurasi

### Mengubah Jumlah Items Per File

Edit file `split-metadata.bat`, ubah baris:
```batch
set ITEMS_PER_FILE=100
```

Menjadi:
```batch
set ITEMS_PER_FILE=50    REM Untuk file lebih kecil
set ITEMS_PER_FILE=200   REM Untuk file lebih besar
```

**Rekomendasi:**
- **50 items** - Untuk server dengan resource terbatas
- **100 items** - Balanced (recommended)
- **200 items** - Untuk server dengan resource besar

---

## 📊 Contoh Output

```
Reading file: metadata.json
Format: Direct array
Total items: 2500
Items per file: 100
Will create 25 files

Created: split/metadata_part1.json (100 items, 0.85 MB)
Created: split/metadata_part2.json (100 items, 0.83 MB)
Created: split/metadata_part3.json (100 items, 0.87 MB)
...
Created: split/metadata_part25.json (100 items, 0.84 MB)

✓ Done! Files saved to: split
```

---

## 🔧 Troubleshooting

### Error: "PHP not found"
**Solusi:** Update path PHP di `split-metadata.bat`
```batch
set PHP_PATH=C:\xampp\php\php.exe
```

### Error: "Invalid JSON"
**Solusi:** 
1. Validate JSON di https://jsonlint.com/
2. Pastikan file tidak corrupt
3. Check encoding (harus UTF-8)

### File terlalu besar untuk di-split
**Solusi:**
1. Kurangi `ITEMS_PER_FILE` menjadi 50 atau 25
2. Atau split manual menggunakan text editor

---

## 💡 Tips

1. **Backup file original** sebelum split
2. **Test dengan 1 file kecil** dulu sebelum upload semua
3. **Monitor server resources** saat import
4. **Upload saat traffic rendah** untuk performa optimal
5. **Check hasil import** setelah setiap batch

---

## 📝 Format JSON yang Didukung

Tool ini support semua format yang didukung plugin:

### Format 1: Standard
```json
{
  "manhwa": [
    { "title": "...", "description": "..." }
  ]
}
```

### Format 2: Direct Array (Komiku)
```json
[
  { "slug": "...", "title": "...", "synopsis": "..." }
]
```

Output split akan selalu dalam format **Direct Array** untuk kompatibilitas maksimal.

---

## 🎯 Best Practices

### Untuk File Sangat Besar (>10MB)

1. **Split menjadi 50 items per file**
   ```bash
   php split-json.php metadata.json 50
   ```

2. **Upload bertahap**
   - Upload 5 file pertama
   - Check hasilnya di WordPress
   - Lanjutkan upload sisanya

3. **Monitor WordPress**
   - Check memory usage
   - Check database size
   - Check disk space

### Untuk File Sedang (1-10MB)

1. **Split menjadi 100 items per file**
   ```bash
   php split-json.php metadata.json 100
   ```

2. **Upload langsung semua file**

---

## 📞 Support

Jika mengalami masalah:
1. Check error log di `wp-content/debug.log`
2. Pastikan PHP memory limit cukup (min 256M)
3. Pastikan max execution time cukup (min 300s)

---

**Version:** 1.0.0  
**Last Updated:** October 31, 2025
