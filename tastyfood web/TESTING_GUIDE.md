# TastyFood Payment System - Testing Guide

## Test Credentials

### Customer Account
- Email: `customer@test.com`
- Password: `password`

### Admin Account
- Email: `admin@padangrice.com`
- Password: `password`
- Admin URL: http://127.0.0.1:8000/admin/login

## Pre-seeded Data
- 5 Payment Methods (BCA, Mandiri, QRIS, DANA, OVO)
- 14 Menu Items
- 1 Test Customer User
- 1 Admin User

## Test Scenarios (All 10 from Spec)

### 1. Customer Registration
1. Visit http://127.0.0.1:8000/register
2. Fill form: name, email, phone, password
3. Submit → should auto-login and redirect to home

### 2. Customer Login
1. Visit http://127.0.0.1:8000/login
2. Enter: `customer@test.com` / `password`
3. Submit → should redirect to home
4. Check navbar shows PROFIL + LOGOUT

### 3. Profile View/Edit
1. Login as customer
2. Click PROFIL in navbar
3. View profile → click "Edit Profil"
4. Update name/phone/password → submit
5. Should redirect back with success message

### 4. Browse Menu + Add to Cart
1. Visit http://127.0.0.1:8000/menu
2. Click any menu item
3. Adjust quantity → "Tambah ke Keranjang"
4. Cart badge should increment
5. Visit /keranjang to view cart

### 5. Checkout Flow (Auth Required)
1. Add items to cart
2. Visit http://127.0.0.1:8000/checkout
3. Fill customer details + delivery method
4. Submit order → redirect to success page

### 6. Payment Method Selection
1. After checkout success → click "Pilih Metode Pembayaran"
2. Should see 5 payment methods (BCA, Mandiri, QRIS, DANA, OVO)
3. Select one → click "Lanjutkan"
4. Should redirect to upload proof page

### 7. Upload Payment Proof
1. On upload page → see payment instructions (account number/QR code)
2. Select image file (JPG/PNG, max 5MB)
3. Optional: add notes
4. Submit → should redirect to order detail with status "Menunggu Verifikasi"

### 8. View Order History (Scoped to User)
1. Login as customer
2. Visit http://127.0.0.1:8000/pesanan
3. Should see only your own orders (not other users' orders)
4. Click order → see payment status

### 9. Admin Payment Verification
1. Login as admin at http://127.0.0.1:8000/admin/login
2. Click "Verifikasi Pembayaran" in sidebar
3. Filter by status "Menunggu Verifikasi"
4. Click "Detail" on a payment
5. View proof image
6. Click "Setujui Pembayaran" OR "Tolak Pembayaran" (with reason)
7. Order status should update to "Dikonfirmasi" if approved

### 10. Customer Payment Status Check
1. Login as customer
2. Visit order detail page
3. Should see payment status:
   - "Belum Dibayar" → button to upload proof
   - "Menunggu Verifikasi" → waiting message
   - "Lunas" → confirmed message
   - "Ditolak" → rejection reason + button to re-upload

## Admin Features

### Payment Method CRUD
1. Login as admin
2. Click "Metode Pembayaran" in sidebar
3. Create/Edit/Deactivate payment methods
4. Upload QR codes for QR payment methods
5. Set account name/number for bank transfers

### Payment Verification Flow
1. Admin receives notification of new payment (via payments list)
2. View proof image in full size
3. Verify authenticity
4. Approve → order moves to "confirmed" status
5. Reject → customer can re-upload

## Security Checks

### Authorization
- [x] Guest cannot access checkout
- [x] Guest cannot access profile
- [x] Guest cannot access order history
- [x] User can only see their own orders
- [x] Admin routes protected by `auth:admin` middleware

### File Upload Validation
- [x] Image MIME type validation (jpg, jpeg, png, webp)
- [x] Max file size: 5MB for proofs, 2MB for QR codes
- [x] Files stored in `storage/app/public/payment-proofs`
- [x] Old proof deleted when re-uploading

### CSRF Protection
- [x] All forms have @csrf tokens
- [x] Laravel default CSRF middleware active

### Password Security
- [x] Passwords hashed via bcrypt (User model)
- [x] No plaintext passwords in DB

## Common Issues & Solutions

### Issue: "storage/app/public" not accessible
**Solution:** Run `php artisan storage:link`

### Issue: Images not loading
**Solution:** Check `public/storage` symlink exists and points to `storage/app/public`

### Issue: "Unauthenticated" error on checkout
**Solution:** User must login first. Guest checkout disabled for payment system.

### Issue: Payment not showing in admin
**Solution:** Ensure payment status is set correctly. Check `payments` table in DB.

## Database Verification

```bash
# Check users
php artisan tinker
>>> \App\Models\User::count()
>>> \App\Models\User::first()

# Check payment methods
>>> \App\Models\PaymentMethod::active()->get()

# Check payments
>>> \App\Models\Payment::with('order')->latest()->first()
```

## File Structure

### Controllers
- `app/Http/Controllers/PaymentController.php` - Customer payment flow
- `app/Http/Controllers/Admin/PaymentController.php` - Admin verification
- `app/Http/Controllers/Admin/PaymentMethodController.php` - Payment method CRUD
- `app/Http/Controllers/Auth/*` - Login/Register
- `app/Http/Controllers/ProfileController.php` - Profile view/edit
- `app/Http/Controllers/OrderController.php` - Order history scoped to user

### Views
- `resources/views/payments/show.blade.php` - Payment method selection
- `resources/views/payments/upload.blade.php` - Proof upload form
- `resources/views/payments/status.blade.php` - Payment status page
- `resources/views/admin/payments/index.blade.php` - Admin payment list
- `resources/views/admin/payments/show.blade.php` - Admin proof view + approve/reject
- `resources/views/admin/payment-methods/` - Payment method CRUD views

### Models
- `app/Models/Payment.php` - Payment records with status tracking
- `app/Models/PaymentMethod.php` - Payment methods with soft deletes
- `app/Models/User.php` - Customer accounts with role field
- `app/Models/Order.php` - Orders with user_id and payment relationship

### Migrations
- `2026_10_07_100001_add_phone_and_role_to_users_table.php`
- `2026_10_07_100002_add_user_id_to_orders_table.php`
- `2026_10_07_100003_create_payment_methods_table.php`
- `2026_10_07_100004_create_payments_table.php`

## Success Criteria

✅ All 10 test scenarios pass
✅ Authorization works correctly
✅ File uploads validated and stored securely
✅ Admin can approve/reject payments
✅ Customer sees payment status updates
✅ Order history scoped to authenticated user
✅ Navbar shows auth state (Login/Register vs Profile/Logout)
✅ No SQL injection or XSS vulnerabilities
✅ CSRF protection active on all forms

---

**Status:** Implementation Complete (100%)
**Date:** 2026-10-07
