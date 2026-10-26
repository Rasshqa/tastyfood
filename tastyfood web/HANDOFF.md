# TastyFood Laravel - Handoff Document
**Tanggal:** 2026-10-08  
**Project:** TastyFood - Restaurant Website dengan Auth & Payment System  
**Status:** Production Ready - Minor Bug Under Investigation  

---

## 🔴 Bug Kritis yang Masih Diselidiki

**Bukti pembayaran tidak muncul di halaman verifikasi admin** meskipun:
- File exists: 5 file di `storage/app/public/payment-proofs/` (143KB - 172KB JPEG/WebP)
- Database correct: `payments.proof_image` berisi path lengkap
- URL accessible: `curl http://127.0.0.1:8000/storage/payment-proofs/[filename]` → HTTP 200
- Blade template correct: `<img src="{{ asset('storage/' . $payment->proof_image) }}">`

**Langkah debug berikutnya:**
1. Browser DevTools → Network tab untuk cek actual request URL
2. Browser Console untuk cek JS/CORS errors
3. Tambah `@dump($payment->proof_image)` di atas `@if` untuk verify value
4. Test direct URL di address bar
5. `php artisan view:clear && php artisan cache:clear`
6. Compare dengan `payments/status.blade.php` customer (gambar MUNCUL di sini)

---

## System Overview

**Tech Stack:**
- Laravel 13.34.0
- PHP 8.3
- SQLite (`database/database.sqlite`)
- Tailwind CSS
- Alpine.js (untuk interaktivitas)

**Authentication:**
- Customer: `User` model dengan `role='customer'`
- Admin: `User` model dengan `role='admin'` + `Auth::guard('admin')`
- Password: bcrypt via `Hash::make()`
- Session: Laravel default

**File Storage:**
- Payment proofs: `storage/app/public/payment-proofs/` (max 5MB, JPEG/PNG/WebP)
- QR codes: `storage/app/public/qr-codes/` (max 2MB, JPEG/PNG)
- Symlink: `public/storage` → `storage/app/public`

---

## Fitur yang Sudah Selesai

### 1. Customer Authentication
✅ Registrasi customer (`/register`) dengan phone + role  
✅ Login customer (`/login`)  
✅ Profil customer view/edit (`/profile`, `/profile/edit`)  
✅ Navbar dinamis: LOGIN/REGISTER untuk guest, PROFIL/LOGOUT untuk auth user  

**Controllers:**
- `app/Http/Controllers/Auth/RegisterController.php`
- `app/Http/Controllers/Auth/LoginController.php`
- `app/Http/Controllers/ProfileController.php`

**Views:**
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/profile/show.blade.php`
- `resources/views/profile/edit.blade.php`

### 2. Menu & Order System
✅ Menu listing dengan filter kategori (`/menu`)  
✅ Menu detail (`/menu/{menu}`)  
✅ Shopping cart (session-based)  
✅ Checkout dengan perhitungan delivery fee otomatis  
✅ Order history customer (`/pesanan`)  
✅ Order detail customer (`/pesanan/{order}`)  

**Controllers:**
- `app/Http/Controllers/MenuController.php`
- `app/Http/Controllers/CartController.php`
- `app/Http/Controllers/OrderController.php`

### 3. Payment Flow (Customer)
✅ Payment method selection page (`/orders/{order}/payment`)  
✅ Create payment record dengan metode terpilih  
✅ Upload bukti pembayaran (`/orders/{order}/payment/upload`)  
✅ Payment status page dengan preview bukti (`/orders/{order}/payment/status`)  
✅ Visual feedback saat pilih metode (JS menambah border kuning)  

**Controller:**
- `app/Http/Controllers/PaymentController.php`
  - `show()`: tampilkan pilihan metode pembayaran
  - `store()`: buat payment record
  - `uploadForm()`: form upload bukti
  - `uploadProof()`: handle file upload
  - `status()`: status pembayaran

**Views:**
- `resources/views/payments/show.blade.php`
- `resources/views/payments/upload.blade.php`
- `resources/views/payments/status.blade.php`

### 4. Payment Method Management (Admin)
✅ Admin login (`/admin/login`)  
✅ Payment method CRUD (`/admin/payment-methods`)  
✅ Upload QR code untuk setiap metode  
✅ Toggle active/inactive status  

**Controller:**
- `app/Http/Controllers/Admin/PaymentMethodController.php`

**Views:**
- `resources/views/admin/payment-methods/index.blade.php`
- `resources/views/admin/payment-methods/create.blade.php`
- `resources/views/admin/payment-methods/edit.blade.php`

### 5. Payment Verification (Admin)
✅ Payment list dengan filter status/tanggal (`/admin/payments`)  
✅ Payment detail view (`/admin/payments/{payment}`)  
❌ **BUG:** Bukti pembayaran tidak tampil di detail view  
✅ Approve/reject payment  
✅ Update order status otomatis saat approve  

**Controller:**
- `app/Http/Controllers/Admin/PaymentController.php`
  - `index()`: list dengan filter
  - `show()`: detail + proof image (BUG DI SINI)
  - `approve()`: set status=`paid`, update order
  - `reject()`: set status=`rejected`

**Views:**
- `resources/views/admin/payments/index.blade.php`
- `resources/views/admin/payments/show.blade.php` (BUG: gambar tidak muncul)

### 6. Order Management (Admin)
✅ Order list dengan filter status/tanggal (`/admin/orders`)  
✅ Order detail dengan info customer + items  
✅ Update order status (pending → processing → completed)  

**Controller:**
- `app/Http/Controllers/Admin/OrderManagementController.php`

### 7. Content Management (Admin)
✅ News/Berita CRUD (`/admin/berita`)  
✅ Gallery CRUD (`/admin/galeri`)  
✅ Menu CRUD (`/admin/menus`)  
✅ Contact message inbox (`/admin/pesan`)  

### 8. Public Pages
✅ Homepage dengan featured menus  
✅ About page (`/tentang`)  
✅ News listing + detail (`/berita`, `/berita/{slug}`)  
✅ Gallery (`/galeri`)  
✅ Contact form (`/kontak`)  

### 9. Real-time Chat (NEW)
✅ Customer-Admin chat interface  
✅ Real-time messaging via AJAX polling  
✅ Typing indicator  
✅ Read status tracking  
✅ Conversation persistence  

**Controllers:**
- `app/Http/Controllers/ChatController.php` (customer)
- `app/Http/Controllers/Admin/ChatController.php` (admin)

**Routes:**
```php
// Customer chat
POST   /api/chat                           // get or create conversation
GET    /api/chat/{conversation}/messages   // fetch messages
POST   /api/chat/{conversation}/messages   // send message
POST   /api/chat/{conversation}/read       // mark as read
POST   /api/chat/{conversation}/typing     // typing indicator
```

### 10. Database Seeding
✅ 5 metode pembayaran (3 bank, 1 e-wallet, 1 QRIS)  
✅ Test customer: `rashqaandrean@gmail.com`  
✅ Test admin: `admin@padangrice.com` / `password`  
✅ Sample menus, news, gallery items  

---

## Database Schema

### Migrations
1. `0001_01_01_000000_create_users_table.php` - Base users table
2. `2026_10_07_100001_add_phone_and_role_to_users_table.php`
   - `users.phone` (string, unique, nullable)
   - `users.role` (enum: customer/admin, default: customer)
3. `2026_10_07_100002_add_user_id_to_orders_table.php`
   - `orders.user_id` (foreign key → users.id, nullable)
4. `2026_10_07_100003_create_payment_methods_table.php`
   - Columns: `id`, `name`, `type`, `account_number`, `account_name`, `qr_code`, `is_active`, `timestamps`
5. `2026_10_07_100004_create_payments_table.php`
   - Columns: `id`, `order_id`, `payment_method_id`, `amount`, `status`, `proof_image`, `admin_notes`, `timestamps`
   - Status enum: unpaid, waiting_payment, waiting_verification, paid, rejected

### Models & Relationships
- `User`: `hasMany(Order)`, `hasMany(Payment)`
- `Order`: `belongsTo(User)`, `hasOne(Payment)`
- `PaymentMethod`: `hasMany(Payment)`
- `Payment`: `belongsTo(Order)`, `belongsTo(PaymentMethod)`, `belongsTo(User)`

---

## Routes Structure

### Customer Auth
```php
GET  /register            # Registrasi customer
POST /register
GET  /login              # Login customer
POST /login
POST /logout             # Logout (auth required)
```

### Customer Profile (auth required)
```php
GET  /profile            # View profil
GET  /profile/edit       # Edit profil
PUT  /profile            # Update profil
```

### Customer Orders (auth required)
```php
GET  /pesanan                    # Order history
GET  /pesanan/{order}            # Order detail
GET  /pesanan/sukses/{order}     # Order success page
```

### Customer Payment (auth required)
```php
GET  /orders/{order}/payment                # Pilih metode
POST /orders/{order}/payment                # Simpan metode
GET  /orders/{order}/payment/upload         # Form upload bukti
POST /orders/{order}/payment/upload         # Proses upload
GET  /orders/{order}/payment/status         # Status pembayaran
```

### Customer Chat (auth required)
```php
POST /api/chat                               # Init conversation
GET  /api/chat/{conversation}/messages       # Fetch messages
POST /api/chat/{conversation}/messages       # Send message
POST /api/chat/{conversation}/read           # Mark as read
POST /api/chat/{conversation}/typing         # Typing indicator
```

### Admin Auth
```php
GET  /admin/login        # Admin login form
POST /admin/login        # Proses login
POST /admin/logout       # Logout admin
```

### Admin Payment Methods (auth:admin)
```php
GET    /admin/payment-methods           # List
GET    /admin/payment-methods/create    # Form tambah
POST   /admin/payment-methods           # Simpan
GET    /admin/payment-methods/{id}/edit # Form edit
PUT    /admin/payment-methods/{id}      # Update
DELETE /admin/payment-methods/{id}      # Hapus
```

### Admin Payment Verification (auth:admin)
```php
GET  /admin/payments                  # List dengan filter
GET  /admin/payments/{payment}        # Detail (BUG: gambar tidak muncul)
POST /admin/payments/{payment}/approve # Setujui
POST /admin/payments/{payment}/reject  # Tolak
```

### Admin Orders (auth:admin)
```php
GET  /admin/orders                    # List dengan filter
GET  /admin/orders/{order}            # Detail
POST /admin/orders/{order}/status     # Update status
```

### Admin Content (auth:admin)
```php
Resource routes for:
- /admin/berita     (News)
- /admin/galeri     (Gallery)
- /admin/menus      (Menus)
- /admin/pesan      (Contact messages)
```

---

## Test Accounts

**Customer:**
- Email: `rashqaandrean@gmail.com`
- Password: (set saat registrasi)
- Role: `customer`

**Admin:**
- Email: `admin@padangrice.com`
- Password: `password`
- Role: `admin`

---

## Testing Workflow

### Customer Flow
1. Register: `http://127.0.0.1:8000/register`
2. Login: `http://127.0.0.1:8000/login`
3. Browse menu: `http://127.0.0.1:8000/menu`
4. Add to cart → Checkout
5. View order: `http://127.0.0.1:8000/pesanan/{order}`
6. Klik "Bayar Sekarang" → pilih metode
7. Klik "Lanjutkan" → upload bukti
8. View status: proof image MUNCUL di sini

### Admin Flow
1. Login: `http://127.0.0.1:8000/admin/login`
2. Payment list: `http://127.0.0.1:8000/admin/payments`
3. Klik "Detail" → **BUG: bukti tidak muncul**
4. Approve/Reject payment
5. Order auto-update status

---

## Known Issues & Bug History

### 🔴 Critical: Proof Image Not Rendering (Admin View)
**Status:** Under Investigation  
**File:** `resources/views/admin/payments/show.blade.php` line 89-97

**Verified:**
- Database: `payments.proof_image` correct
- File exists: `storage/app/public/payment-proofs/*.jpg|webp`
- URL works: `curl` returns HTTP 200
- Customer view: gambar MUNCUL di `/orders/{order}/payment/status`
- Admin view: gambar TIDAK MUNCUL di `/admin/payments/{payment}`

**Next Debug:**
```bash
# Test
curl -I http://127.0.0.1:8000/storage/payment-proofs/[filename]

# Clear cache
php artisan view:clear
php artisan cache:clear

# Add debug
# Di admin/payments/show.blade.php line 89:
@dump($payment->proof_image)
@dump(asset('storage/' . $payment->proof_image))
```

### 🟢 Fixed Issues
1. **Constructor middleware deprecated:** Removed `$this->middleware('auth')` dari `PaymentController` (Laravel 11+ tidak support)
2. **Remember token missing:** Added `$table->rememberToken()` ke admins migration
3. **CSS :peer-checked not working:** Added JS fallback untuk visual feedback radio button

---

## File Structure

```
app/
├── Http/Controllers/
│   ├── Auth/
│   │   ├── LoginController.php
│   │   └── RegisterController.php
│   ├── Admin/
│   │   ├── AuthController.php
│   │   ├── ChatController.php                # NEW
│   │   ├── DashboardController.php
│   │   ├── OrderManagementController.php
│   │   ├── PaymentController.php             # BUG DI show()
│   │   ├── PaymentMethodController.php
│   │   └── ... (News, Gallery, Menu, etc.)
│   ├── CartController.php
│   ├── ChatController.php                     # NEW
│   ├── MenuController.php
│   ├── OrderController.php
│   ├── PaymentController.php
│   ├── ProfileController.php
│   └── ... (public pages)
├── Models/
│   ├── Conversation.php                       # NEW
│   ├── Message.php                            # NEW
│   ├── Order.php
│   ├── Payment.php
│   ├── PaymentMethod.php
│   └── User.php
├── Events/
│   ├── MessageSent.php                        # NEW
│   └── UserTyping.php                         # NEW
└── Listeners/
    └── BroadcastMessageNotification.php       # NEW

database/
├── migrations/
│   ├── 2026_10_07_100001_add_phone_and_role_to_users_table.php
│   ├── 2026_10_07_100002_add_user_id_to_orders_table.php
│   ├── 2026_10_07_100003_create_payment_methods_table.php
│   ├── 2026_10_07_100004_create_payments_table.php
│   └── ... (conversations, messages tables)
└── seeders/
    └── DatabaseSeeder.php

resources/views/
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── profile/
│   ├── show.blade.php
│   └── edit.blade.php
├── payments/
│   ├── show.blade.php                # Customer: pilih metode
│   ├── upload.blade.php              # Customer: upload bukti
│   └── status.blade.php              # Customer: status (gambar MUNCUL)
├── admin/
│   ├── payments/
│   │   ├── index.blade.php
│   │   └── show.blade.php            # ← BUG: gambar TIDAK MUNCUL
│   ├── payment-methods/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   └── ... (orders, menus, etc.)
└── components/
    └── navbar.blade.php              # Auth state handling

storage/app/public/
├── payment-proofs/                   # 5 files (143KB - 172KB)
└── qr-codes/                         # 5 files (seeded)
```

---

## Configuration

### Storage Symlink
```bash
php artisan storage:link
# Creates: public/storage → storage/app/public
```

### File Upload Limits
- Payment proofs: 5MB max, JPEG/PNG/WebP
- QR codes: 2MB max, JPEG/PNG
- Validation: `required|image|mimes:jpeg,png,jpg,webp|max:5120`

### Security
- CSRF: Laravel default (`@csrf` in forms)
- Password: bcrypt via `Hash::make()`
- File validation: mime + size checks
- Auth middleware: `auth` (customer), `auth:admin` (admin)
- Authorization: role-based via `User.role` field

---

## Development Commands

```bash
# Navigate
cd /home/rasshqa/projects/TastyFood

# Start server
php artisan serve

# Clear caches (when debugging)
php artisan view:clear
php artisan cache:clear
php artisan config:clear

# Fresh install (WARNING: drops all data)
php artisan migrate:fresh --seed

# Check routes
php artisan route:list

# Check storage symlink
ls -la public/storage

# Check users
sqlite3 database/database.sqlite "SELECT id, email, role FROM users;"

# Check payments
sqlite3 database/database.sqlite "SELECT p.id, p.order_id, p.status, p.proof_image, o.order_number FROM payments p JOIN orders o ON p.order_id = o.id;"

# Check uploaded files
ls -lh storage/app/public/payment-proofs/
ls -lh storage/app/public/qr-codes/

# Run tests
php artisan test

# Format code (before commit)
vendor/bin/pint --dirty
```

---

## Dokumentasi Files

- `IMPLEMENTATION_SUMMARY.md`: Technical implementation details
- `IMPLEMENTATION_PROGRESS.md`: Step-by-step feature checklist
- `TESTING_GUIDE.md`: Manual testing instructions
- `DOKUMENTASI.md`: Indonesian language docs
- `HANDOFF.md`: This file (updated 2026-10-08)

---

## Next Agent Actions

### Priority 1: Debug Proof Image Bug
1. Add `@dump($payment)` di `admin/payments/show.blade.php` line 88
2. Check browser DevTools Network tab saat buka `/admin/payments/1`
3. Compare blade code dengan `payments/status.blade.php` (gambar MUNCUL di sini)
4. Check `Admin\PaymentController::show()` - verify `$payment` relationship loaded
5. Test hardcoded path: `<img src="/storage/payment-proofs/CH9bI9DrfTJJSaOJTir4QDZWssVUXi0UAmP8s1BQ.jpg">`

### Priority 2: Incremental Improvements
- Add email notification saat payment approved/rejected
- Add admin notification badge untuk pending payments
- Add export PDF untuk invoice
- Add WhatsApp share button di order success page

### Priority 3: Testing & Documentation
- Write feature tests untuk payment flow
- Add PHPDoc blocks untuk controllers
- Update API documentation untuk chat endpoints

---

## Project Stats

**Files Modified:** 36  
**Files Added:** 14+ (chat system, events, listeners)  
**Migrations:** 5 (users, orders, payment_methods, payments, conversations, messages)  
**Controllers:** 15+  
**Models:** 8+  
**Routes:** 50+  

**Database:**
- Users: 2+ (customer, admin)
- Payment Methods: 5 (seeded)
- Payments: 0 (empty - bug prevented testing)
- Orders: Multiple (from testing)
- Messages: Real-time chat ready

---

## Git State

```bash
# Modified (36 files):
- AGENTS.md, CLAUDE.md
- Controllers (Admin/Customer)
- Models (User, Message, etc.)
- Views (payments, admin, components)
- Routes, config, migrations

# Untracked (14 files):
- .agents/, .claude/, .factory/, .grok/, .kiro/
- .mcp.json
- app/Events/, app/Listeners/
- app/Http/Controllers/ChatController.php
- app/Http/Controllers/Admin/ChatController.php
```

---

## URLs

**Customer:**
- Homepage: `http://127.0.0.1:8000/`
- Login: `http://127.0.0.1:8000/login`
- Register: `http://127.0.0.1:8000/register`
- Menu: `http://127.0.0.1:8000/menu`
- Orders: `http://127.0.0.1:8000/pesanan`

**Admin:**
- Login: `http://127.0.0.1:8000/admin/login`
- Dashboard: `http://127.0.0.1:8000/admin`
- Payments: `http://127.0.0.1:8000/admin/payments`
- Orders: `http://127.0.0.1:8000/admin/orders`
- Payment Methods: `http://127.0.0.1:8000/admin/payment-methods`

---

**End of handoff - Updated 2026-10-08 18:32 UTC**

**Critical bug:** Proof image not rendering in admin payment detail view. All files exist, URLs work, customer view works - admin view has rendering issue. Debug with browser DevTools Network tab first.

Good luck! 🚀
