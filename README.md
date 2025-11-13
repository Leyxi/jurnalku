# PKL Hero Hub v2.0

Sistem Manajemen PKL (Praktik Kerja Lapangan) berbasis PHP Native + Tailwind CSS

## 🚀 Fitur Utama

- **Autentikasi Multi-Role**: Admin, Pembimbing, Siswa
- **CRUD Jurnal Harian**: Dengan upload foto bukti multiple
- **Sistem Review**: Pembimbing dapat approve/reject jurnal
- **Manajemen User**: Admin dapat CRUD user
- **Pengumuman**: Sistem broadcast pesan ke target audience
- **UI Modern**: Menggunakan Tailwind CSS + Flowbite

## 📋 Prerequisites

- PHP 7.4+
- MySQL 5.7+
- Node.js & npm (untuk build CSS)
- XAMPP / WAMP / Server lokal

## 🛠️ Instalasi

1. **Clone atau download project ini** ke folder `htdocs` XAMPP:
   ```
   C:\xampp\htdocs\jurnalku\
   ```

2. **Setup Database**:
   - Buka phpMyAdmin (http://localhost/phpmyadmin)
   - Import file `database_setup.sql`
   - Database `pkl_hero_db` akan terbuat otomatis dengan data testing

3. **Install Dependencies**:
   ```bash
   npm install
   npm run build-css
   ```

4. **Konfigurasi Database**:
   - Edit `app/_config/database.php` jika perlu
   - Default: host=localhost, db=pkl_hero_db, user=root, pass=(kosong)

5. **Akses Aplikasi**:
   - Buka browser: `http://localhost/jurnalku/public/login.php`

## 👤 Akun Testing

| Role       | Email              | Password |
|------------|--------------------|----------|
| Admin      | admin@pklhero.com  | password |
| Pembimbing | ahmad@pembimbing.com | password |
| Pembimbing | siti@pembimbing.com  | password |
| Siswa      | ahmad@siswa.com    | password |
| Siswa      | budi@siswa.com     | password |
| Siswa      | citra@siswa.com    | password |

## 📁 Struktur Folder

```
jurnalku/
├── app/                    # Backend logic
│   ├── _config/           # Database config
│   ├── _lib/              # Helper functions
│   └── _logic/            # Business logic
├── public/                # Public web files
│   ├── admin/             # Admin pages
│   ├── pembimbing/        # Pembimbing pages
│   ├── siswa/             # Siswa pages
│   ├── assets/            # CSS, JS, images
│   └── uploads/           # File uploads
├── templates/             # HTML templates
├── src/                   # Source CSS
├── package.json           # npm config
├── tailwind.config.js     # Tailwind config
└── database_setup.sql     # Database schema
```

## 🔧 Development

### Build CSS (Watch mode)
```bash
npm run build-css
```
CSS akan otomatis rebuild saat ada perubahan file PHP.

### Menambah Fitur Baru
1. Buat file PHP di folder yang sesuai (`public/`, `app/_logic/`)
2. Include template header/footer jika perlu
3. Pastikan validasi input dan error handling
4. Test di semua role yang relevan

## 📝 Catatan

- Upload folder (`public/uploads/jurnal/`) harus writable
- Pastikan PHP memiliki ekstensi PDO MySQL enabled
- Untuk production, ubah password database dan session security

## 🐛 Troubleshooting

**Error koneksi database**:
- Pastikan XAMPP MySQL running
- Cek kredensial di `database.php`

**CSS tidak load**:
- Jalankan `npm run build-css`
- Pastikan file `public/assets/css/style.css` ada

**Upload foto gagal**:
- Cek permission folder `uploads/`
- Pastikan ukuran file < 5MB dan format gambar valid

## 📄 Lisensi

Project ini dibuat untuk keperluan edukasi PKL.
