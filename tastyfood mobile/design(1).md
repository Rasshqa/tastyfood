# DESIGN.md — Padang Rice Admin Mobile

> Panduan desain UI/UX untuk aplikasi admin mobile **Padang Rice Admin**, dibangun menggunakan **Flutter** dan terhubung ke REST API Laravel yang sudah tersedia.
>
> **Arah visual:** glassmorphism yang elegan, bersih, ringan, dan berorientasi operasional. Referensi visual memperlihatkan kartu translucent, permukaan putih/abu-abu lembut, sudut membulat, bayangan halus, serta navigasi mobile yang sederhana.

---

## 1. Tujuan Desain

Aplikasi digunakan oleh manajer restoran, staf dapur, customer support, dan koordinator pengiriman untuk menangani operasional dari ponsel.

Prioritas desain:

1. Informasi penting—pesanan baru, pembayaran menunggu verifikasi, dan chat belum dibaca—harus terlihat dalam beberapa detik.
2. Aksi penting harus mudah dijangkau dengan satu tangan.
3. Status dan tindakan harus jelas; glassmorphism tidak boleh mengurangi keterbacaan.
4. Antarmuka harus tetap cepat dan nyaman pada perangkat Android kelas menengah.
5. Semua layar memakai pola visual konsisten dan mendukung loading, error, empty state, serta kondisi offline.

## 2. Prinsip Visual

- **Soft glass, bukan efek berlebihan.** Gunakan transparansi secukupnya, blur terukur, border putih tipis, dan shadow lembut.
- **Konten lebih penting daripada dekorasi.** Jangan meletakkan teks penting di atas background yang ramai.
- **Latar belakang netral.** Gunakan off-white dan abu-abu hangat dengan aksen kuning keemasan yang mengingatkan pada karakter brand restoran.
- **Warna status tetap konvensional.** Hijau untuk sukses, merah untuk masalah, kuning/oranye untuk menunggu, biru untuk informasi.
- **Hindari neon, glow kuat, gradient mencolok, dan dekorasi yang tidak membantu tugas admin.**
- **Konsisten.** Radius, spacing, typography, ikon, dan perilaku tombol harus seragam di semua halaman.

## 3. Design Tokens

Gunakan token terpusat, bukan warna dan ukuran hard-coded di setiap widget.

### 3.1 Warna

| Token | Hex | Penggunaan |
|---|---|---|
| `primary` | `#1F2937` | Tombol utama, navigasi aktif, teks kuat |
| `primaryDark` | `#111827` | Heading kuat, state pressed |
| `accent` | `#EAB308` | Aksen brand, indikator pilihan |
| `accentLight` | `#FEF3C7` | Background aksen lembut |
| `success` | `#10B981` | Berhasil, pembayaran disetujui |
| `warning` | `#F59E0B` | Menunggu, perlu perhatian |
| `error` | `#EF4444` | Gagal, ditolak, pembatalan |
| `info` | `#3B82F6` | Informasi, status terkonfirmasi |
| `background` | `#F3F4F6` | Background dasar aplikasi |
| `surface` | `#FFFFFF` | Konten utama dan form |
| `textPrimary` | `#111827` | Teks utama |
| `textSecondary` | `#6B7280` | Teks pendukung |
| `textDisabled` | `#9CA3AF` | Teks nonaktif |
| `border` | `#E5E7EB` | Garis pembatas |
| `glassTint` | `#FFFFFF` | Tint kartu glass |
| `overlay` | `#111827` | Overlay modal |

Gunakan opacity melalui `withOpacity`/API opacity yang sesuai versi Flutter. Pastikan teks tetap memenuhi kontras WCAG AA; jika kontras tidak cukup, gunakan permukaan solid.

### 3.2 Status Pesanan

| Status API | Label UI | Background | Teks |
|---|---|---|---|
| `pending` | Menunggu | `#FEF3C7` | `#92400E` |
| `confirmed` | Dikonfirmasi | `#DBEAFE` | `#1E40AF` |
| `preparing` | Sedang dimasak | `#FED7AA` | `#9A3412` |
| `ready` | Siap | `#D1FAE5` | `#065F46` |
| `in_transit` | Dalam perjalanan | `#E9D5FF` | `#6B21A8` |
| `delivered` | Terkirim | `#D1FAE5` | `#065F46` |
| `completed` | Selesai | `#E5E7EB` | `#374151` |
| `cancelled` | Dibatalkan | `#FEE2E2` | `#991B1B` |

Status pembayaran:
- `unpaid`: abu-abu.
- `waiting_verification`: kuning/oranye.
- `paid`: hijau.
- `rejected`: merah.
- `expired`: abu-abu gelap.

Jangan hanya mengandalkan warna. Selalu sertakan label teks dan, bila sesuai, ikon.

### 3.3 Typography

Gunakan **Inter** jika tersedia sebagai font asset; jika tidak, gunakan font sistem sans-serif.

| Style | Ukuran | Weight | Line height |
|---|---:|---:|---:|
| `display` | 30 | 700 | 38 |
| `h1` | 26 | 700 | 34 |
| `h2` | 22 | 600 | 30 |
| `h3` | 18 | 600 | 26 |
| `body` | 15 | 400 | 23 |
| `bodyMedium` | 15 | 500 | 23 |
| `label` | 13 | 600 | 18 |
| `caption` | 12 | 400 | 17 |
| `button` | 14 | 600 | 20 |

Gunakan format mata uang Indonesia: `Rp 125.000`. Tanggal dan waktu mengikuti zona waktu yang dikonfigurasi backend/perangkat, serta menggunakan format yang konsisten.

### 3.4 Spacing, Radius, dan Elevation

Spacing berbasis kelipatan 4:
- `xs`: 4
- `sm`: 8
- `md`: 16
- `lg`: 24
- `xl`: 32
- `xxl`: 48

Radius:
- `small`: 10
- `medium`: 16
- `large`: 22
- `pill`: 999

Shadow harus lembut dan tipis. Jangan gunakan shadow gelap dengan blur besar untuk setiap komponen. Kartu glass menggunakan border putih semi-transparan dan shadow abu-abu tipis.

---

## 4. Sistem Glassmorphism

### 4.1 Background

- Gunakan background dasar `#F3F4F6` atau off-white.
- Tambahkan 1–2 bentuk dekoratif abstrak yang sangat samar di belakang konten, misalnya blur abu-abu hangat atau kuning pucat.
- Dekorasi harus statis dan tidak mengganggu scrolling.
- Hindari foto atau pola kompleks sebagai background halaman operasional.

### 4.2 Glass Card

Karakteristik kartu:
- Tint putih sekitar 70–85% opacity.
- Blur sekitar 10–18 px jika digunakan.
- Border putih dengan opacity tinggi, sekitar 50–75%.
- Radius 18–24 px.
- Shadow tipis dengan opacity rendah.
- Padding internal umumnya 16 px.

Gunakan glass untuk:
- Kartu ringkasan dashboard.
- Quick actions.
- Panel filter atau bottom sheet.
- Header tertentu dan elemen navigasi.

Gunakan permukaan putih solid untuk:
- Form panjang.
- Daftar padat.
- Detail pesanan dengan banyak teks.
- Bukti pembayaran yang harus diperiksa.
- Konten yang membutuhkan kontras maksimal.

### 4.3 Implementasi Flutter

Buat reusable widget seperti `GlassCard`. Implementasi dapat memakai `ClipRRect`, `BackdropFilter`, `ImageFilter.blur`, `DecoratedBox`, dan `BoxDecoration`.

Contoh konsep:

```dart
ClipRRect(
  borderRadius: BorderRadius.circular(22),
  child: BackdropFilter(
    filter: ImageFilter.blur(sigmaX: 14, sigmaY: 14),
    child: Container(
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.78),
        borderRadius: BorderRadius.circular(22),
        border: Border.all(
          color: Colors.white.withOpacity(0.72),
          width: 1,
        ),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF111827).withOpacity(0.05),
            blurRadius: 22,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      padding: const EdgeInsets.all(16),
      child: child,
    ),
  ),
)
```

Tambahkan import `dart:ui` untuk `ImageFilter`. Sesuaikan pemakaian API opacity dengan versi Flutter. Jangan menumpuk banyak `BackdropFilter`; blur mahal untuk GPU. Pada daftar panjang, gunakan kartu solid atau blur hanya pada elemen terpilih.

---

## 5. Struktur Navigasi

Gunakan **bottom navigation bar** dengan empat tab utama:

1. **Beranda** — ringkasan operasional dan aksi cepat.
2. **Pesanan** — daftar, filter, detail, dan status pesanan.
3. **Chat** — inbox percakapan pelanggan.
4. **Lainnya** — menu, verifikasi pembayaran, berita, galeri, pesan masuk, pengaturan, dan logout.

Ketentuan:
- Tinggi navigasi nyaman untuk disentuh dan memperhitungkan safe area.
- Tab aktif memakai ikon solid atau indikator kecil dengan warna `primary`.
- Tampilkan badge untuk jumlah pesanan tertunda dan pesan belum dibaca.
- Jangan menjejalkan semua modul sebagai tab utama.
- Halaman detail dibuka melalui stack navigation dengan tombol kembali yang jelas.
- Notifikasi push harus dapat membuka halaman yang sesuai, bukan selalu dashboard.

Rute layar yang disarankan:
- `Splash / SessionCheck`
- `Login`
- `Dashboard`
- `Orders`
- `OrderDetail`
- `PaymentVerification`
- `PaymentProofViewer`
- `ChatInbox`
- `ChatConversation`
- `MenuList`
- `MenuForm`
- `NewsList` / `NewsForm`
- `GalleryList` / `GalleryForm`
- `ContactMessages`
- `Settings`
- `RestaurantLocationPicker`
- `PaymentMethods`

---

## 6. Spesifikasi Layar

## 6.1 Login

**Tujuan:** admin dapat masuk dengan cepat dan aman.

Layout:
- Background off-white dengan satu area glass lembut.
- Nama/logo **Padang Rice Admin** di bagian atas.
- Judul: “Selamat datang kembali”.
- Subjudul: “Masuk untuk mengelola operasional restoran.”
- Input email.
- Input password dengan toggle tampil/sembunyikan.
- Tombol utama “Masuk”.
- Error API tampil dekat form, bukan hanya snackbar.
- Loading state pada tombol saat login berlangsung.

Perilaku:
- Validasi input dasar sebelum request.
- Jangan pernah menyimpan password.
- Token disimpan di secure storage, bukan plain text/local preferences biasa.
- Setelah login berhasil, validasi sesi dan masuk ke dashboard.
- Jika API mengembalikan 401, tampilkan pesan yang jelas.

## 6.2 Dashboard / Beranda

**Tujuan:** memberikan gambaran keadaan restoran tanpa perlu membuka banyak halaman.

Urutan konten:
1. Header sapaan, nama admin, avatar/inisial, dan tombol notifikasi.
2. Ringkasan utama: pendapatan hari ini.
3. Kartu metrik: pesanan menunggu, pembayaran perlu verifikasi, chat belum dibaca, total pesanan hari ini.
4. Quick actions: “Verifikasi pembayaran”, “Lihat pesanan”, “Buka chat”, “Kelola menu”.
5. Daftar pesanan terbaru.
6. Pembayaran yang menunggu verifikasi.
7. Pesan pelanggan terbaru.

Layout:
- Header ringkas, bukan hero besar.
- Pendapatan dapat ditampilkan pada satu kartu utama dengan angka besar.
- Metrik lain dalam grid dua kolom di layar yang cukup lebar; pada layar kecil, gunakan grid adaptif atau daftar horizontal yang tidak memotong informasi penting.
- Gunakan glass card untuk ringkasan, sedangkan daftar pesanan memakai kartu putih sederhana.
- Setiap metrik bisa ditekan untuk membuka halaman yang telah difilter.

Data:
- Ikuti field API dashboard seperti `pending_orders`, `pending_payments`, `today_orders`, `today_revenue`, `active_chats`, dan `unread_messages` jika tersedia.
- Jangan membuat angka dummy saat API gagal. Gunakan placeholder skeleton atau label “Data belum tersedia”.
- Refresh dashboard melalui pull-to-refresh. Data yang penting dapat diperbarui dengan polling atau push sesuai spesifikasi backend.

## 6.3 Daftar Pesanan

Elemen:
- App bar “Pesanan”.
- Search field untuk nomor pesanan, nama pelanggan, atau nomor telepon.
- Filter status pesanan dan status pembayaran.
- Filter chip horizontal.
- Daftar kartu pesanan.
- Pagination/infinite scroll sesuai API.

Setiap kartu menampilkan:
- Nomor pesanan.
- Nama pelanggan.
- Waktu pesanan.
- Total.
- Metode pickup/delivery.
- Status pesanan dan pembayaran.
- Indikator pembayaran menunggu jika relevan.

Interaksi:
- Tap kartu membuka detail.
- Pull-to-refresh memperbarui data.
- Tampilkan jumlah pending sebagai badge tab.
- Pertahankan filter saat kembali dari detail.
- Sediakan empty state untuk hasil pencarian kosong.

## 6.4 Detail Pesanan

Susunan:
1. Nomor pesanan dan status utama.
2. Informasi pelanggan serta tombol telepon.
3. Metode pickup/delivery.
4. Daftar item dengan gambar kecil, harga, kuantitas, dan subtotal.
5. Catatan pelanggan.
6. Rincian subtotal, biaya pengiriman, dan total.
7. Informasi pembayaran dan bukti jika tersedia.
8. Peta dan rute untuk pesanan delivery.
9. Aksi status berikutnya di bagian bawah yang mudah dijangkau.

Status action mengikuti transisi yang diizinkan backend:
- `pending` → tunggu pembayaran/aksi sesuai kebijakan.
- `confirmed` → “Mulai memasak”.
- `preparing` → “Tandai siap”.
- `ready` → “Dalam perjalanan” untuk delivery atau “Selesai pickup”.
- `in_transit` → aksi penyelesaian pengiriman sesuai status API.
- `delivered` → “Selesaikan”.
- Pembatalan membutuhkan konfirmasi dan alasan bila diwajibkan.

Jangan menampilkan tombol transisi yang tidak valid. Minta konfirmasi untuk tindakan penting dan cegah double tap saat request berlangsung.

## 6.5 Verifikasi Pembayaran

Halaman ini harus dioptimalkan untuk memeriksa bukti transfer secara akurat.

Daftar:
- Urutkan pembayaran menunggu dari yang paling lama terlebih dahulu.
- Tampilkan nomor pesanan, pelanggan, nominal, metode, waktu unggah, dan thumbnail bukti.
- Gunakan indikator yang menonjol tetapi tidak berlebihan.

Detail:
- Ringkasan pesanan dan total.
- Metode pembayaran.
- Bukti pembayaran yang dapat dibuka fullscreen dan diperbesar.
- Catatan pelanggan jika ada.
- Tombol “Setujui pembayaran” dan “Tolak pembayaran”.

Alur menolak:
- Buka dialog/bottom sheet.
- Sediakan template alasan penolakan dan input alasan khusus.
- Validasi alasan minimal 10 karakter.
- Minta konfirmasi sebelum mengirim.
- Setelah sukses, keluarkan item dari antrean dan tampilkan feedback.
- Jangan mengubah UI menjadi sukses sebelum API mengonfirmasi hasilnya.

## 6.6 Chat Inbox

Elemen:
- App bar “Chat pelanggan”.
- Tab “Terbuka” dan “Ditutup”.
- Search nama pelanggan.
- Item percakapan dengan avatar/inisial, nama, preview pesan, waktu, badge unread, dan nomor pesanan bila tersedia.
- Titik status percakapan terbuka/ditutup.

Perilaku:
- Percakapan belum dibaca harus mudah dikenali tanpa membuat daftar terlalu ramai.
- Preview pesan dipotong secara wajar.
- Ketika percakapan dibuka, tandai pesan sebagai dibaca.
- Sediakan loading, empty, error, dan reconnect state.

## 6.7 Chat Conversation

Layout:
- Header dengan nama pelanggan, status, dan shortcut ke detail pesanan.
- Area pesan yang dapat discroll.
- Pesan pelanggan rata kiri dengan permukaan abu-abu terang.
- Pesan admin rata kanan dengan `primary` dan teks putih.
- Pesan sistem rata tengah dengan warna netral/kuning lembut.
- Timestamp kecil dan indikator pengiriman.
- Input pesan dan tombol kirim menempel di bagian bawah di atas safe area.

Perilaku:
- Pesan baru dapat muncul real-time melalui WebSocket jika backend mendukung.
- Gunakan optimistic UI dengan status “mengirim”; jika gagal, tampilkan aksi coba lagi.
- Gunakan UUID dari API untuk mencocokkan pesan sementara dengan respons server.
- Tampilkan indikator “sedang mengetik” bila event tersedia.
- Percakapan tertutup menonaktifkan input dan memperlihatkan status serta aksi buka kembali jika endpoint mendukung.
- Jika WebSocket tidak tersedia, gunakan polling sesuai spesifikasi backend.

## 6.8 Manajemen Menu

Daftar:
- Search menu.
- Filter kategori: nasi, lauk, sayur, minuman.
- Filter ketersediaan.
- Kartu dengan thumbnail, nama, harga, kategori, dan toggle tersedia/tidak tersedia.
- Floating action button “Tambah menu”.

Form tambah/edit:
- Pilih gambar dari galeri/kamera.
- Nama, deskripsi, harga, kategori, dan ketersediaan.
- Validasi field wajib dan harga numerik positif.
- Tampilkan preview gambar.
- Kompres gambar maksimal 2 MB sebelum upload jika memungkinkan.
- Progress indicator saat upload.
- Tombol simpan yang menunjukkan loading.

Hapus menu harus menggunakan dialog konfirmasi. Toggle ketersediaan boleh optimistic, tetapi wajib dikembalikan jika request gagal.

## 6.9 Berita dan Galeri

Berita:
- List dengan thumbnail, judul, status draft/published, dan tanggal.
- Form judul, konten, penulis, status, dan gambar.
- Simpan draft atau publish sesuai API.

Galeri:
- Grid thumbnail.
- Form judul, deskripsi, kategori, status, dan gambar.
- Preview gambar sebelum upload.
- Konfirmasi sebelum menghapus.

Gunakan layout sederhana; fungsi CMS yang kompleks tetap lebih nyaman dikelola di web jika tidak termasuk cakupan mobile.

## 6.10 Pesan Kontak

- Daftar nama, subjek, cuplikan pesan, waktu, dan indikator sudah/belum dibaca.
- Detail pesan menampilkan email dan isi lengkap.
- Aksi tandai dibaca.
- Hapus dengan konfirmasi.

## 6.11 Pengaturan

Kelompokkan pengaturan:
1. **Informasi restoran:** nama, alamat, telepon, email, jam buka.
2. **Pengiriman:** biaya dasar, biaya per kilometer, radius maksimal.
3. **Lokasi restoran:** peta, marker yang dapat digeser, alamat hasil reverse geocoding.
4. **Metode pembayaran:** daftar metode aktif, tambah/edit/hapus bila endpoint tersedia.
5. **Akun:** informasi admin dan logout.

Gunakan form yang jelas, label selalu terlihat, validasi inline, dan tombol simpan. Perubahan lokasi harus dikonfirmasi sebelum dikirim.

## 6.12 Lainnya

Tampilkan daftar modul dalam kelompok dengan ikon sederhana:
- Manajemen menu
- Verifikasi pembayaran
- Berita
- Galeri
- Pesan kontak
- Metode pembayaran
- Pengaturan
- Keluar

Jangan menampilkan semua fungsi dalam grid ikon yang padat. Gunakan list tile dengan judul dan deskripsi singkat.

---

## 7. Komponen Reusable

Buat komponen bersama agar seluruh aplikasi konsisten:

- `GlassCard`
- `AppScaffold`
- `AppBottomNavigation`
- `AppHeader`
- `PrimaryButton`
- `SecondaryButton`
- `DestructiveButton`
- `AppTextField`
- `SearchField`
- `FilterChipGroup`
- `StatusBadge`
- `MetricCard`
- `OrderCard`
- `PaymentQueueCard`
- `MenuCard`
- `ConversationTile`
- `ChatBubble`
- `Avatar`
- `EmptyState`
- `ErrorState`
- `LoadingSkeleton`
- `OfflineBanner`
- `ConfirmActionDialog`
- `ImagePreview`
- `RupiahText`

Setiap komponen interaktif harus memiliki state normal, pressed, disabled, loading, dan error jika relevan.

## 8. Tombol dan Form

- Tinggi tombol utama idealnya 48–52 px.
- Touch target minimal 44 × 44 px.
- Tombol utama menggunakan warna `primary`, teks putih, radius 14–16 px.
- Tombol destruktif menggunakan merah dan selalu disertai label eksplisit.
- Hindari hanya menggunakan ikon untuk aksi berisiko tinggi.
- Label form diletakkan di atas field, bukan hanya placeholder.
- Error validasi tampil di dekat field yang bermasalah.
- Saat submit, cegah pengiriman ganda.
- Dialog konfirmasi dipakai untuk approve/reject pembayaran, membatalkan pesanan, menghapus menu, dan logout bila diperlukan.

## 9. Ikon dan Gambar

- Gunakan satu keluarga ikon yang konsisten, misalnya Material Symbols atau Lucide.
- Gunakan ikon sederhana dengan ukuran 20–24 px.
- Jangan memakai emoji sebagai pengganti ikon UI.
- Gambar menu menggunakan `cached_network_image` dengan placeholder dan error fallback.
- Thumbnail memakai rasio konsisten dan `BoxFit.cover`.
- Bukti pembayaran tidak boleh terpotong pada layar detail; sediakan viewer fullscreen dan zoom.
- Avatar pelanggan boleh memakai foto jika tersedia; jika tidak, gunakan inisial.

## 10. Responsive Layout

Utamakan ponsel portrait. Tetap dukung layar kecil, layar besar, dan tablet.

- Gunakan `SafeArea`, `LayoutBuilder`, dan constraint yang sesuai.
- Hindari lebar tetap yang menyebabkan overflow.
- Di ponsel kecil, kartu metrik boleh menjadi satu kolom atau dua kolom dengan konten ringkas.
- Di tablet, gunakan batas lebar konten dan grid yang lebih lapang.
- Form panjang tetap satu kolom pada ponsel.
- Gunakan `SingleChildScrollView` hanya untuk form sederhana; untuk daftar panjang gunakan builder list/grid yang lazy.
- Dukung dynamic text scaling tanpa memotong label penting.
- Uji keyboard, safe area, notch, dan orientasi layar.

## 11. Motion dan Feedback

Animasi harus singkat dan fungsional:
- Perubahan halaman: transisi standar Flutter, sekitar 180–250 ms.
- Tekan kartu/tombol: feedback halus.
- Dialog/bottom sheet: transisi natural.
- Jangan memakai animasi looping yang mengganggu dashboard.

Feedback:
- Sukses: snackbar ringkas atau inline confirmation.
- Gagal: pesan yang menjelaskan masalah dan tindakan berikutnya.
- Loading: skeleton untuk konten awal, spinner pada aksi individual.
- Upload: progress indicator.
- Offline: banner yang konsisten di bagian atas konten.
- Aksi kritis hanya dinyatakan berhasil setelah server mengonfirmasi.

## 12. Loading, Empty, Error, dan Offline

### Loading
- Skeleton mengikuti bentuk konten sebenarnya.
- Pertahankan layout agar tidak terjadi layout shift besar.
- Tombol yang sedang mengirim request menampilkan progress dan tidak bisa ditekan berulang.

### Empty
Setiap layar kosong menampilkan:
- Ikon/ilustrasi sederhana.
- Judul yang jelas.
- Penjelasan singkat.
- Tombol tindakan bila masuk akal.

Contoh: “Belum ada pesanan” — “Pesanan baru akan muncul di sini.”

### Error
- Tampilkan pesan ramah dan tombol “Coba lagi”.
- Jangan memperlihatkan stack trace atau detail internal server.
- Tangani `401`, `403`, `404`, `422`, `500`, dan kegagalan jaringan secara berbeda.
- Untuk `401`, hapus sesi aman dan arahkan ke login.

### Offline
- Tampilkan banner “Tidak ada koneksi internet”.
- Izinkan data cache dibaca jika tersedia.
- Jangan mengizinkan approve/reject pembayaran atau mengklaim perubahan status berhasil saat offline.
- Aksi kritis seperti pembayaran tidak boleh dimasukkan ke antrean offline tanpa desain konsistensi dan idempotensi yang jelas.
- Saat koneksi pulih, refresh data dan beri feedback.

## 13. Arsitektur Flutter yang Disarankan

Gunakan struktur feature-first dan pisahkan UI dari API/domain logic.

```text
lib/
  app/
    app.dart
    router.dart
    theme/
      app_colors.dart
      app_theme.dart
      app_typography.dart
      app_spacing.dart
  core/
    api/
      api_client.dart
      api_endpoints.dart
      api_exceptions.dart
    storage/
      secure_storage_service.dart
    network/
      connectivity_service.dart
    widgets/
      glass_card.dart
      status_badge.dart
      app_button.dart
      app_text_field.dart
      empty_state.dart
      error_state.dart
      loading_skeleton.dart
      offline_banner.dart
    utils/
      currency_formatter.dart
      date_formatter.dart
  features/
    auth/
    dashboard/
    orders/
    payments/
    chat/
    menus/
    news/
    gallery/
    contact_messages/
    settings/
    notifications/
  main.dart
```

Pilihan teknis yang sesuai dengan spesifikasi:
- State management: `flutter_bloc`.
- HTTP: `dio`.
- Secure token storage: `flutter_secure_storage`.
- Maps: `google_maps_flutter`.
- Push notification: `firebase_messaging`.
- Pemilihan gambar: `image_picker`.
- Cache gambar: `cached_network_image`.
- Realtime: Laravel Echo/WebSocket jika kompatibel dengan konfigurasi backend; jika tidak, gunakan polling fallback.

Gunakan repository/service layer untuk request API. Widget tidak boleh berisi detail HTTP atau parsing response yang kompleks.

## 14. Integrasi API dan Keamanan UI

Base URL yang tercantum dalam spesifikasi backend:
- Production: `https://tastyfood.com`
- Staging: `https://staging.tastyfood.com`
- Local development: `http://localhost:8000` — pada perangkat fisik, `localhost` mengarah ke perangkat itu sendiri; gunakan alamat host development yang dapat dijangkau perangkat.

Ketentuan:
- Gunakan header `Accept: application/json`.
- Kirim token melalui `Authorization: Bearer <token>`.
- Simpan token di secure storage.
- Jangan memasukkan token atau password ke log.
- Tangani timeout dan koneksi terputus.
- Validasi response dan tampilkan error yang ramah.
- Pastikan endpoint benar-benar tersedia di backend sebelum menghubungkan fitur.
- Jangan menganggap contoh payload di dokumen sebagai data produksi.

### Catatan verifikasi kontrak API

Dokumen teknis backend menyebut beberapa endpoint sebagai referensi, tetapi implementasi perlu memverifikasi kontrak sebenarnya sebelum rilis. Khususnya:
- Alur pembaruan status pesanan dan label tombol harus cocok dengan status yang benar-benar diterima backend.
- Endpoint pembayaran, metode pembayaran, chat, serta lokasi harus diuji dengan request nyata.
- Contoh status “dikirim” dan “selesai” pada UI perlu dipetakan secara tepat ke status API, bukan disimpulkan dari label.
- Untuk peta/rute, koordinat restoran dan pelanggan harus valid; garis lurus di peta bukan rute jalan yang akurat. Gunakan layanan directions jika estimasi rute dan waktu dibutuhkan.

---

## 15. Aksesibilitas

- Touch target minimum 44 × 44 px.
- Kontras teks minimal WCAG AA.
- Setiap ikon interaktif memiliki semantic label.
- Status disampaikan melalui teks, bukan warna saja.
- Dukung ukuran teks sistem.
- Pastikan urutan fokus logis dan form dapat digunakan dengan screen reader.
- Uji dengan TalkBack pada Android dan VoiceOver pada iOS.
- Sediakan feedback yang tidak hanya mengandalkan suara atau getaran.

## 16. Performa

- Hindari blur besar dan banyak `BackdropFilter` pada satu layar.
- Gunakan `ListView.builder`/`GridView.builder` untuk daftar panjang.
- Cache gambar dan gunakan ukuran thumbnail yang wajar.
- Hindari rebuild seluruh halaman saat hanya satu badge berubah.
- Batasi polling dan hentikan timer saat halaman tidak aktif.
- Tutup stream, controller, dan listener saat widget/service tidak lagi digunakan.
- Optimistic update hanya untuk aksi yang aman dan harus rollback bila API gagal.
- Jangan meng-cache data sensitif tanpa kebijakan penyimpanan yang jelas.

## 17. QA Checklist

### Visual
- [ ] Glass card tetap terbaca di berbagai kondisi layar.
- [ ] Tidak ada teks penting yang tenggelam karena transparansi.
- [ ] Radius, spacing, warna, dan typography konsisten.
- [ ] Tampilan tidak overflow pada ponsel kecil.
- [ ] Bottom navigation tidak menutupi konten atau keyboard.

### Fungsional
- [ ] Login, validasi sesi, dan logout berjalan.
- [ ] Dashboard memakai data API, bukan angka statis.
- [ ] Filter dan pencarian pesanan bekerja.
- [ ] Detail pesanan menampilkan item dan total dengan benar.
- [ ] Status pesanan hanya dapat berpindah ke transisi yang valid.
- [ ] Bukti pembayaran dapat dilihat penuh dan di-zoom.
- [ ] Reject pembayaran membutuhkan alasan.
- [ ] Chat menampilkan pesan dan unread count secara konsisten.
- [ ] Toggle ketersediaan menu rollback saat API gagal.
- [ ] Upload gambar menunjukkan progress dan error.
- [ ] Push notification membuka layar yang benar.
- [ ] Offline state jelas dan tidak mengklaim aksi kritis berhasil.
- [ ] Error 401 mengakhiri sesi dengan aman.

### Performa dan aksesibilitas
- [ ] Scroll daftar panjang tetap lancar.
- [ ] Gambar besar tidak menyebabkan aplikasi crash.
- [ ] Tidak ada request ganda akibat tap cepat.
- [ ] TalkBack/VoiceOver membaca kontrol dengan benar.
- [ ] Dynamic text scaling tidak merusak layout.

---

## 18. Acceptance Criteria

Desain dianggap siap diimplementasikan jika:

1. Semua layar inti memiliki hierarki visual yang konsisten.
2. Admin dapat menemukan pesanan tertunda, pembayaran, dan chat belum dibaca tanpa navigasi berlapis.
3. Setiap aksi kritis memiliki state loading, success, dan failure.
4. Glassmorphism dipakai secara selektif dan tidak mengorbankan aksesibilitas.
5. Semua layar mendukung kondisi data kosong, error, loading, dan offline.
6. Desain sesuai dengan kontrak API Laravel yang telah diverifikasi.
7. UI diuji pada perangkat Android kecil dan besar serta iOS bila menjadi target rilis.

---

## 19. Referensi Visual

Gunakan gambar referensi yang diberikan sebagai arah visual, bukan sebagai layout yang harus disalin persis:
- Panel translucent dengan radius besar.
- Permukaan abu-abu muda dan putih.
- Bayangan lembut dan pemisahan konten yang jelas.
- Komponen ringkas dengan tipografi modern.
- Navigasi bawah yang sederhana.
- Kartu utama menonjol, sementara konten pendukung lebih tenang.

Untuk Padang Rice Admin, pertahankan estetika glassmorphism tetapi gunakan warna brand dan pola status dari spesifikasi operasional. Hindari elemen e-card, rumah sakit, atau konten visual yang tidak relevan dengan restoran pada gambar referensi.

---

**Dokumen:** `DESIGN.md`  
**Produk:** Padang Rice Admin Mobile  
**Platform:** Flutter — Android dan iOS  
**Backend:** Laravel REST API  
**Gaya:** Minimal, soft glassmorphism, operational-first
