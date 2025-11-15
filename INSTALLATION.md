# Panduan Instalasi MailMan

Panduan instalasi MailMan di hosting production.

## Persyaratan Hosting

- PHP 7.4 atau lebih tinggi
- PHP Extensions: OpenSSL, JSON, cURL
- Akses SSH (opsional, untuk composer install)
- Support untuk .htaccess (Apache)

## Cara Instalasi

### Opsi 1: Upload ke Hosting (Tanpa SSH)

1. **Download** file `mailman-production.zip`

2. **Upload** file zip ke hosting melalui cPanel File Manager atau FTP

3. **Extract** file zip di direktori web root (public_html atau htdocs)

4. **Set Permissions** untuk folder berikut via cPanel atau FTP:
   ```
   chmod 755 config
   chmod 755 data
   chmod 755 data/templates
   chmod 755 data/logs
   chmod 755 uploads
   ```

5. **Akses** aplikasi via browser: `http://yourdomain.com`

6. **Login** dengan kredensial default:
   - Username: `user1234`
   - Password: `mostech`

7. **Konfigurasi SMTP** di halaman Konfigurasi

8. **Ubah Password** default untuk keamanan

### Opsi 2: Upload via SSH/Terminal

1. **Upload** file zip ke server:
   ```bash
   scp mailman-production.zip user@server:/path/to/webroot/
   ```

2. **SSH** ke server:
   ```bash
   ssh user@server
   ```

3. **Extract** zip:
   ```bash
   cd /path/to/webroot
   unzip mailman-production.zip
   mv email.nokito.pro/* .
   rm -rf email.nokito.pro
   ```

4. **Install Dependencies** (jika belum ada):
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

5. **Set Permissions**:
   ```bash
   chmod 755 config data data/templates data/logs uploads
   chmod 644 config/.gitkeep data/templates/.gitkeep data/logs/.gitkeep
   ```

6. **Akses** aplikasi dan konfigurasi

## Konfigurasi .htaccess

File `.htaccess` sudah disertakan untuk keamanan. Pastikan Apache mod_rewrite aktif.

Jika menggunakan Nginx, tambahkan konfigurasi berikut:

```nginx
location ~ ^/(config|data|includes|uploads)/ {
    deny all;
    return 403;
}

location ~ /\. {
    deny all;
    return 403;
}
```

## Konfigurasi Gmail SMTP

1. **Aktifkan 2-Step Verification** di akun Gmail
2. **Generate App Password**:
   - Buka https://myaccount.google.com/apppasswords
   - Pilih "Mail" dan "Other (Custom name)"
   - Beri nama "MailMan"
   - Copy password (16 karakter)
3. **Isi di halaman Konfigurasi**:
   - SMTP Host: `smtp.gmail.com`
   - SMTP Port: `587`
   - Email Gmail: email Anda
   - App Password: password yang digenerate

## Troubleshooting

### Permission Denied saat menyimpan

Pastikan folder `config`, `data`, dan `uploads` memiliki permission 755:
```bash
chmod 755 config data data/templates data/logs uploads
```

### Composer dependencies tidak ada

Jika folder `vendor/` kosong atau error autoload:
```bash
composer install --no-dev --optimize-autoloader
```

Atau download versi dengan vendor sudah included dari release.

### Email tidak terkirim

1. Pastikan App Password Gmail benar
2. Cek 2-Step Verification aktif
3. Cek log error di halaman Log
4. Pastikan hosting allow koneksi SMTP keluar (port 587)

### .htaccess tidak bekerja

Tambahkan di `.htaccess`:
```apache
Options -MultiViews
RewriteEngine On
RewriteBase /
```

Atau hubungi hosting support untuk aktifkan mod_rewrite.

## Update Aplikasi

1. Backup folder `config/`, `data/`, dan `uploads/`
2. Download versi terbaru
3. Extract dan replace semua file kecuali folder backup
4. Restore folder backup
5. Run `composer install` jika diperlukan

## Keamanan Production

- ✅ Ubah password default segera
- ✅ Backup folder `config/` dan `data/` secara berkala
- ✅ Gunakan HTTPS (SSL Certificate)
- ✅ Aktifkan firewall hosting
- ✅ Update PHP dan dependencies secara berkala

## Support

Untuk bantuan lebih lanjut, buka issue di GitHub repository.
