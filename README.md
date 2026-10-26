# TastyFood (Padang Rice) — Monorepo

Sistem manajemen restoran dan pemesanan makanan komprehensif, mencakup aplikasi web (Laravel 13) dan aplikasi mobile admin (Flutter).

## Struktur Monorepo

```
tastyfood/
├── tastyfood web/       # Web App & Backend API (Laravel 13, Blade, Tailwind, MySQL/SQLite)
└── tastyfood mobile/    # Mobile Admin App (Flutter, Bloc, Dio, GoRouter)
```

## Komponen

### 1. TastyFood Web (`tastyfood web/`)
- **Framework**: Laravel 13
- **Frontend**: Blade + Vite + Tailwind CSS
- **Fitur**:
  - Landing page, menu interaktif, galeri, berita, dan kontak
  - Cart, unified checkout, ongkir calculator, dan payment upload
  - Admin dashboard untuk mengelola menu, pesanan, berita, galeri, pesan, dan pembayaran
  - WebSocket chat customer-admin via Laravel Reverb
  - API endpoint terintegrasi untuk aplikasi mobile

### 2. TastyFood Mobile (`tastyfood mobile/`)
- **Framework**: Flutter (Dart 3.x)
- **State Management**: `flutter_bloc`
- **Fitur**:
  - Admin login & auth session handling
  - Order monitoring & status updates
  - Payment queue verification
  - Real-time customer chat management
  - Menu catalog management

## Cara Menjalankan

### Web Backend:
```bash
cd "tastyfood web"
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Mobile App:
```bash
cd "tastyfood mobile"
flutter pub get
flutter run
```
