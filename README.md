# MailMan - Secure PHP Email Sender

MailMan adalah aplikasi PHP untuk mengirim email menggunakan SMTP Gmail dengan dukungan template HTML dan attachment.

## Fitur

- 🔐 **Keamanan**: Enkripsi AES-256 untuk konfigurasi sensitif
- 📧 **Gmail SMTP**: Integrasi dengan Google SMTP
- 📝 **Template Management**: CRUD template email HTML dengan preview
- 🎨 **Dark Mode UI**: Tampilan dark mode yang modern
- 📎 **Attachment**: Dukungan lampiran file
- 🔄 **Variable Replacement**: Template dengan variabel dinamis ({{nama}}, {{email}}, dll)
- 📊 **Email Logging**: Riwayat pengiriman email dengan statistik
- ✅ **Email Validation**: Validasi format email

## Requirements

- PHP 7.4 atau lebih tinggi
- Composer
- Extension PHP: OpenSSL, JSON

## Instalasi

1. Clone atau download repository ini
2. Install dependencies:
   ```bash
   composer install
   ```

3. Set permission untuk direktori data:
   ```bash
   chmod 755 config data uploads
   ```

4. Jalankan development server:
   ```bash
   php -S localhost:8000
   ```

5. Akses aplikasi di browser: `http://localhost:8000`

## Default Login

- **Username**: user1234
- **Password**: mostech

⚠️ **Penting**: Segera ubah kredensial default setelah login pertama di halaman Konfigurasi!

## Konfigurasi SMTP Gmail

1. Login ke aplikasi
2. Buka halaman **Konfigurasi**
3. Isi data SMTP:
   - SMTP Host: `smtp.gmail.com`
   - SMTP Port: `587`
   - Email Gmail: Email Anda
   - App Password: Generate di [Google Account Settings](https://myaccount.google.com/apppasswords)

### Cara Membuat App Password Gmail:

1. Buka [https://myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
2. Pilih "Mail" dan "Other (Custom name)"
3. Beri nama, misal: "MailMan"
4. Copy password yang digenerate (16 karakter)
5. Paste di field "App Password Gmail"

## Penggunaan

### Mengirim Email

1. Buka halaman **Kirim Email**
2. Pilih sumber template:
   - **Template Tersimpan**: Pilih dari template yang sudah dibuat
   - **Template Custom**: Input HTML atau teks biasa secara langsung
3. Isi data penerima (email & opsional: nama)
4. Upload attachment jika diperlukan
5. Klik **Kirim Email**

### Mengelola Template

1. Buka halaman **Template**
2. Klik **+ Buat Template Baru**
3. Isi:
   - Nama template
   - Subject email
   - Konten HTML (gunakan variabel seperti `{{nama}}`, `{{email}}`)
4. Klik **Simpan**

#### Contoh Template dengan Variabel:

```html
<h1>Halo {{nama}}!</h1>
<p>Terima kasih telah mendaftar dengan email: {{email}}</p>
<p>Kode aktivasi Anda: {{kode_aktivasi}}</p>
```

Variabel yang didukung secara otomatis:
- `{{nama}}` - Nama penerima
- `{{email}}` - Email penerima
- Variabel custom akan diminta saat mengirim email

### Melihat Log

Halaman **Log** menampilkan:
- Riwayat pengiriman email (100 terakhir)
- Status pengiriman (berhasil/gagal)
- Statistik: Total email, success rate, dll
- Pesan error jika ada

## Struktur Folder

```
├── assets/          # CSS dan JavaScript
├── config/          # Konfigurasi terenkripsi (auto-generated)
├── data/
│   ├── templates/   # Template email (JSON)
│   └── logs/        # Log pengiriman (JSON)
├── includes/        # Class PHP
├── pages/           # Halaman aplikasi
├── uploads/         # Temporary upload (auto-cleanup)
├── vendor/          # Composer dependencies
└── index.php        # Login page
```

## Keamanan

- ✅ Semua konfigurasi sensitif dienkripsi dengan AES-256
- ✅ Session-based authentication
- ✅ Password hashing menggunakan `password_hash()`
- ✅ Validasi email
- ✅ CSRF protection (form based)
- ✅ File permission protection via .htaccess

## Troubleshooting

### Email tidak terkirim

1. Pastikan SMTP sudah dikonfigurasi dengan benar
2. Cek App Password Gmail valid
3. Pastikan 2-Step Verification aktif di akun Gmail
4. Cek log error di halaman **Log**

### "Authentication failed"

- Pastikan menggunakan App Password, bukan password Gmail biasa
- Regenerate App Password baru di Google Account Settings

### Template variabel tidak ter-replace

- Pastikan format variabel: `{{nama_variabel}}` (tanpa spasi)
- Pastikan isi nilai variabel saat mengirim email

## Development

### Struktur Class

- `Auth.php` - Authentication & session management
- `Config.php` - Configuration management
- `Encryption.php` - AES-256 encryption/decryption
- `Template.php` - Template CRUD & variable rendering
- `Mailer.php` - Email sending via PHPMailer
- `Logger.php` - Email logging system

### Menambah Fitur Baru

Semua class menggunakan namespace `MailMan` dan autoload via Composer PSR-4.

## Support

Untuk bug report atau feature request, silakan buat issue di repository ini.
