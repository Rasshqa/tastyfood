# Dokumentasi Aplikasi Padang Rice

## Deskripsi Aplikasi

Padang Rice adalah website restoran Padang yang menyediakan informasi tentang menu, berita kuliner, galeri foto, dan kontak restoran. Aplikasi ini dilengkapi dengan panel admin untuk mengelola konten website.

## Teknologi yang Digunakan

- **Framework**: Laravel 11
- **Database**: SQLite
- **Frontend**: Tailwind CSS, Vite
- **PHP Version**: 8.2+

## Fitur Aplikasi

### 1. Frontend (Landing Page)

#### 1.1 Halaman Beranda
**URL**: `/`

Fitur:
- Hero section dengan gambar nasi padang
- Section "Tentang Kami" dengan 4 menu unggulan (Rendang, Ayam Pop, Gulai Tunjang, Daun Singkong)
- Section "Berita Kami" menampilkan 5 berita terbaru
- Section "Galeri Kami" menampilkan 6 foto terbaru
- Navbar sticky dengan transparansi dinamis saat scroll

**[SCREENSHOT 1: Halaman Beranda - Hero Section]**
```
Cara ambil screenshot:
1. Buka http://localhost:8000
2. Screenshot bagian hero dengan teks "PADANG RICE"
```

**[SCREENSHOT 2: Halaman Beranda - Menu Unggulan]**
```
Cara ambil screenshot:
1. Scroll ke section "TENTANG KAMI"
2. Screenshot 4 card menu (Rendang, Ayam Pop, Gulai Tunjang, Daun Singkong)
```

---

#### 1.2 Halaman Tentang Kami
**URL**: `/tentang`

Fitur:
- Informasi profil Padang Rice
- Visi perusahaan
- Misi perusahaan (4 poin)
- Foto-foto ilustrasi

**[SCREENSHOT 3: Halaman Tentang Kami]**
```
Cara ambil screenshot:
1. Buka http://localhost:8000/tentang
2. Screenshot section "PADANG RICE" dan "VISI"
```

---

#### 1.3 Halaman Berita
**URL**: `/berita`

Fitur:
- Featured news (berita utama) dengan gambar besar
- Grid berita lainnya (4 kolom)
- Pagination untuk berita
- Link ke detail berita

**[SCREENSHOT 4: Halaman Berita]**
```
Cara ambil screenshot:
1. Buka http://localhost:8000/berita
2. Screenshot featured news dan grid berita
```

---

#### 1.4 Halaman Detail Berita
**URL**: `/berita/{slug}`

Fitur:
- Judul berita
- Informasi penulis dan tanggal publish
- Gambar berita
- Konten berita lengkap
- Berita terkait (4 berita)

**[SCREENSHOT 5: Halaman Detail Berita]**
```
Cara ambil screenshot:
1. Buka http://localhost:8000/berita/apa-saja-makanan-khas-nusantara
2. Screenshot judul, gambar, dan konten berita
```

---

#### 1.5 Halaman Galeri
**URL**: `/galeri`

Fitur:
- Carousel slider (5 foto pertama)
- Grid galeri (4 kolom)
- Lightbox untuk melihat foto fullscreen
- Navigasi slider (prev/next/indicators)

**[SCREENSHOT 6: Halaman Galeri - Carousel]**
```
Cara ambil screenshot:
1. Buka http://localhost:8000/galeri
2. Screenshot carousel dengan kontrol navigasi
```

**[SCREENSHOT 7: Halaman Galeri - Grid]**
```
Cara ambil screenshot:
1. Scroll ke bawah pada halaman galeri
2. Screenshot grid foto
```

---

#### 1.6 Halaman Kontak
**URL**: `/kontak`

Fitur:
- Form kontak (Subject, Name, Email, Message)
- Validasi input
- Informasi kontak (Email, Phone, Location)
- Google Maps embed

**[SCREENSHOT 8: Halaman Kontak - Form]**
```
Cara ambil screenshot:
1. Buka http://localhost:8000/kontak
2. Screenshot form kontak dan info kontak
```

**[SCREENSHOT 9: Halaman Kontak - Maps]**
```
Cara ambil screenshot:
1. Scroll ke bawah pada halaman kontak
2. Screenshot Google Maps
```

---

### 2. Backend (Admin Panel)

#### 2.1 Login Admin
**URL**: `/admin/login`

Fitur:
- Form login (Email, Password, Remember Me)
- Validasi kredensial
- Session management
- CSRF protection

**Kredensial Default:**
- Email: `admin@padangrice.com`
- Password: `password`

**[SCREENSHOT 10: Halaman Login Admin]**
```
Cara ambil screenshot:
1. Buka http://localhost:8000/admin/login
2. Screenshot form login
```

---

#### 2.2 Dashboard Admin
**URL**: `/admin`

Fitur:
- Card statistik (Total Berita, Total Foto, Pesan Masuk)
- Berita terbaru (5 item)
- Pesan terbaru (5 item)
- Sidebar navigasi

**[SCREENSHOT 11: Dashboard Admin]**
```
Cara ambil screenshot:
1. Login sebagai admin
2. Screenshot dashboard dengan 3 card statistik
```

---

#### 2.3 Kelola Berita
**URL**: `/admin/berita`

Fitur:
- Daftar berita dengan tabel
- Filter berdasarkan search dan status
- Tambah berita baru
- Edit berita
- Hapus berita dengan konfirmasi
- Pagination

**[SCREENSHOT 12: Kelola Berita - Index]**
```
Cara ambil screenshot:
1. Klik menu "Kelola Berita"
2. Screenshot tabel berita
```

**[SCREENSHOT 13: Kelola Berita - Form Tambah]**
```
Cara ambil screenshot:
1. Klik tombol "Tambah Berita"
2. Screenshot form tambah berita
```

---

#### 2.4 Kelola Galeri
**URL**: `/admin/galeri`

Fitur:
- Grid foto dengan card
- Tambah foto baru
- Edit foto (judul, deskripsi, status)
- Hapus foto dengan konfirmasi
- Pagination

**[SCREENSHOT 14: Kelola Galeri]**
```
Cara ambil screenshot:
1. Klik menu "Kelola Galeri"
2. Screenshot grid foto galeri
```

**[SCREENSHOT 15: Kelola Galeri - Form Tambah]**
```
Cara ambil screenshot:
1. Klik tombol "Tambah Foto"
2. Screenshot form upload foto
```

---

#### 2.5 Kelola Pesan
**URL**: `/admin/pesan`

Fitur:
- Daftar pesan dari form kontak
- Status pesan (Baru/Dibaca)
- Detail pesan
- Hapus pesan
- Auto-mark sebagai dibaca saat dibuka

**[SCREENSHOT 16: Kelola Pesan]**
```
Cara ambil screenshot:
1. Klik menu "Kelola Pesan"
2. Screenshot tabel pesan
```

**[SCREENSHOT 17: Kelola Pesan - Detail]**
```
Cara ambil screenshot:
1. Klik "Lihat" pada salah satu pesan
2. Screenshot detail pesan
```

---

#### 2.6 Pengaturan
**URL**: `/admin/pengaturan`

Fitur:
- Edit informasi situs (Nama Brand, Deskripsi)
- Edit kontak (Email, Telepon, Lokasi)
- Auto-save ke database

**[SCREENSHOT 18: Pengaturan]**
```
Cara ambil screenshot:
1. Klik menu "Pengaturan"
2. Screenshot form pengaturan
```

---

## Struktur Database

### Tabel: `admins`
| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| name | string | Nama admin |
| email | string | Email (unique) |
| password | string | Password (hashed) |
| created_at | timestamp | - |
| updated_at | timestamp | - |

### Tabel: `news`
| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| title | string | Judul berita |
| slug | string | URL-friendly slug (unique) |
| image | string | Path gambar |
| content | text | Konten berita |
| author | string | Nama penulis |
| status | enum | draft/published |
| published_at | timestamp | Tanggal publish |
| created_at | timestamp | - |
| updated_at | timestamp | - |

### Tabel: `galleries`
| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| title | string | Judul foto |
| image | string | Path gambar |
| description | text | Deskripsi (nullable) |
| category | string | Kategori (nullable) |
| status | enum | active/inactive |
| sort_order | integer | Urutan tampil |
| created_at | timestamp | - |
| updated_at | timestamp | - |

### Tabel: `messages`
| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| subject | string | Subject pesan |
| name | string | Nama pengirim |
| email | string | Email pengirim |
| message | text | Isi pesan |
| is_read | boolean | Status baca (default: false) |
| created_at | timestamp | - |
| updated_at | timestamp | - |

### Tabel: `settings`
| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| setting_key | string | Key pengaturan (unique) |
| setting_value | text | Value pengaturan |
| created_at | timestamp | - |
| updated_at | timestamp | - |

**Keys yang tersedia:**
- `site_name`: Nama brand
- `site_description`: Deskripsi situs
- `email`: Email kontak
- `phone`: Nomor telepon
- `location`: Alamat lokasi

---

## Instalasi dan Setup

### 1. Clone Repository
```bash
cd /home/rasshqa/projects/TastyFood
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Copy Environment File
```bash
cp .env.example .env
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Setup Database
```bash
touch database/database.sqlite
php artisan migrate:fresh --seed
```

### 6. Build Assets
```bash
npm run build
```

### 7. Run Development Server
```bash
php artisan serve
```

Aplikasi akan berjalan di: `http://localhost:8000`

---

## Kredensial Default

### Admin Panel
- **Email**: admin@padangrice.com
- **Password**: password

---

## Security Features

### Frontend
- ✅ CSRF Protection pada semua form
- ✅ XSS Protection (Blade auto-escaping)
- ✅ SQL Injection Protection (Eloquent ORM)
- ✅ Input validation pada form kontak

### Admin Panel
- ✅ Authentication middleware (`auth:admin`)
- ✅ Authorization middleware (`AdminMiddleware`)
- ✅ Session regeneration on login/logout
- ✅ CSRF tokens pada semua form
- ✅ File upload validation (type, size)
- ✅ Password hashing (bcrypt)

---

## Struktur Folder

```
TastyFood/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Controller admin
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── NewsController.php
│   │   │   │   ├── GalleryController.php
│   │   │   │   ├── MessageController.php
│   │   │   │   └── SettingController.php
│   │   │   ├── HomeController.php
│   │   │   ├── AboutController.php
│   │   │   ├── NewsController.php
│   │   │   ├── GalleryController.php
│   │   │   └── ContactController.php
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   └── Models/
│       ├── Admin.php
│       ├── News.php
│       ├── Gallery.php
│       ├── Message.php
│       └── Setting.php
├── database/
│   ├── migrations/
│   │   └── 2025_01_01_000001_create_tastyfood_tables.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── public/
│   └── assets/ASET/           # Folder gambar
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   └── views/
│       ├── admin/             # Views admin
│       │   ├── auth/
│       │   ├── news/
│       │   ├── gallery/
│       │   ├── messages/
│       │   ├── settings/
│       │   └── dashboard.blade.php
│       ├── components/        # Komponen reusable
│       │   ├── navbar.blade.php
│       │   ├── footer.blade.php
│       │   └── hero.blade.php
│       ├── layouts/
│       │   ├── app.blade.php
│       │   └── admin.blade.php
│       └── pages/             # Halaman frontend
│           ├── home.blade.php
│           ├── about.blade.php
│           ├── contact.blade.php
│           ├── gallery.blade.php
│           └── news/
├── routes/
│   └── web.php
└── storage/
    └── app/public/            # Uploaded files
```

---

## Maintenance

### Update Berita
1. Login ke admin panel
2. Klik menu "Kelola Berita"
3. Klik "Tambah Berita" atau "Edit" pada berita yang ada
4. Isi form dan klik "Simpan"

### Upload Foto Galeri
1. Login ke admin panel
2. Klik menu "Kelola Galeri"
3. Klik "Tambah Foto"
4. Upload gambar (max 2MB)
5. Isi judul, deskripsi, dan status
6. Klik "Simpan"

### Baca Pesan Kontak
1. Login ke admin panel
2. Klik menu "Kelola Pesan"
3. Klik "Lihat" pada pesan yang ingin dibaca
4. Pesan otomatis ditandai sebagai "Dibaca"

### Update Informasi Kontak
1. Login ke admin panel
2. Klik menu "Pengaturan"
3. Edit field yang ingin diubah
4. Klik "Simpan Perubahan"

---

## Troubleshooting

### Problem: Gambar tidak muncul
**Solusi:**
```bash
php artisan storage:link
```

### Problem: CSS tidak load
**Solusi:**
```bash
npm run build
php artisan optimize:clear
```

### Problem: Error "APP_KEY not set"
**Solusi:**
```bash
php artisan key:generate
```

### Problem: Admin tidak bisa login
**Solusi:**
```bash
php artisan migrate:fresh --seed
```
Credentials akan reset ke default.

---

## Contact Developer

Untuk pertanyaan atau issue, silakan hubungi:
- Repository: [GitHub URL]
- Email: [developer@example.com]

---

## License

Copyright © 2023-2026 Padang Rice. All rights reserved.
