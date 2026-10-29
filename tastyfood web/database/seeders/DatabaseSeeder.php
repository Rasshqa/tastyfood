<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\News;
use App\Models\Gallery;
use App\Models\Setting;
use App\Models\Menu;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ContactMessage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        Admin::create([
            'name' => 'Administrator',
            'email' => 'admin@padangrice.com',
            'password' => Hash::make('password'),
        ]);

        // Test Customer User
        \App\Models\User::create([
            'name' => 'Customer Test',
            'email' => 'customer@test.com',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        // Payment Methods
        \App\Models\PaymentMethod::create([
            'name' => 'BCA',
            'type' => 'bank_transfer',
            'account_name' => 'Padang Rice',
            'account_number' => '1234567890',
            'instructions' => 'Transfer ke rekening BCA dan upload bukti pembayaran',
            'is_active' => true,
        ]);

        \App\Models\PaymentMethod::create([
            'name' => 'Mandiri',
            'type' => 'bank_transfer',
            'account_name' => 'Padang Rice',
            'account_number' => '9876543210',
            'instructions' => 'Transfer ke rekening Mandiri dan upload bukti pembayaran',
            'is_active' => true,
        ]);

        \App\Models\PaymentMethod::create([
            'name' => 'QRIS',
            'type' => 'qr_payment',
            'instructions' => 'Scan QR Code dan upload bukti pembayaran',
            'is_active' => true,
        ]);

        \App\Models\PaymentMethod::create([
            'name' => 'DANA',
            'type' => 'e_wallet',
            'account_number' => '081234567890',
            'instructions' => 'Transfer ke DANA 081234567890 dan upload bukti',
            'is_active' => true,
        ]);

        \App\Models\PaymentMethod::create([
            'name' => 'OVO',
            'type' => 'e_wallet',
            'account_number' => '081234567890',
            'instructions' => 'Transfer ke OVO 081234567890 dan upload bukti',
            'is_active' => true,
        ]);

        // News
        News::create([
            'title' => 'APA SAJA MAKANAN KHAS NUSANTARA?',
            'slug' => 'apa-saja-makanan-khas-nusantara',
            'image' => 'assets/ASET/nasipadang-hero.jpg',
            'content' => 'Makanan khas Nusantara sangat beragam, dari Sabang sampai Merauke. Setiap daerah memiliki cita rasa unik yang diwariskan turun temurun. Nasi Padang, rendang, soto, gado-gado, dan sate adalah beberapa contoh kuliner Indonesia yang terkenal hingga mancanegara. Kekayaan rempah Nusantara membuat setiap hidangan memiliki aroma dan rasa yang khas.',
            'author' => 'Admin',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $newsImages = [
            'assets/ASET/nasipadang-1.jpg',
            'assets/ASET/nasipadang-2.jpg',
            'assets/ASET/nasipadang-3.jpg',
            'assets/ASET/nasipadang-4.jpg',
            'assets/ASET/nasipadang-5.jpg',
            'assets/ASET/nasipadang-6.jpg',
        ];

        $newsArticles = [
            ['title' => 'RESEP RENDANG AUTENTIK', 'content' => 'Rendang adalah masakan daging bercita rasa pedas yang menggunakan campuran berbagai bumbu dan rempah-rempah. Masakan ini dihasilkan dari proses memasak yang dipanaskan berulang-ulang dengan santan kelapa.'],
            ['title' => 'SEJARAH NASI PADANG', 'content' => 'Nasi Padang berasal dari Sumatera Barat dan kini menjadi salah satu kuliner Indonesia yang paling terkenal. Cita rasanya yang kaya rempah dan cara penyajiannya yang unik menjadi daya tarik tersendiri.'],
            ['title' => 'GULAI IKAN KHAS MINANG', 'content' => 'Gulai ikan adalah masakan berkuah santan dengan rempah-rempah pilihan. Ikan yang digunakan biasanya ikan laut segar yang dimasak dengan bumbu khas Minangkabau hingga meresap sempurna.'],
            ['title' => 'DENDENG BALADO PEDAS', 'content' => 'Dendeng balado adalah daging sapi yang diiris tipis, dikeringkan, kemudian digoreng dan dicampur dengan sambal balado yang pedas. Cocok sebagai lauk atau cemilan.'],
            ['title' => 'SAMBAL HIJAU KHAS PADANG', 'content' => 'Sambal hijau terbuat dari cabai hijau besar yang diulek dengan bawang dan tomat. Rasanya pedas segar dengan aroma cabai hijau yang khas, sempurna menemani nasi hangat.'],
            ['title' => 'SATE PADANG KUAH KUNING', 'content' => 'Sate Padang berbeda dari sate lainnya karena menggunakan kuah kuning kental dari tepung beras dan bumbu rempah. Dagingnya empuk dengan cita rasa gurih pedas yang menggugah selera.'],
        ];

        foreach ($newsImages as $index => $image) {
            News::create([
                'title' => $newsArticles[$index]['title'],
                'slug' => Str::slug($newsArticles[$index]['title']) . '-' . ($index + 2),
                'image' => $image,
                'content' => $newsArticles[$index]['content'],
                'author' => 'Admin',
                'status' => 'published',
                'published_at' => now()->subDays($index),
            ]);
        }

        // Gallery
        $galleryImages = [
            'assets/ASET/nasipadang-1.jpg',
            'assets/ASET/nasipadang-2.jpg',
            'assets/ASET/nasipadang-3.jpg',
            'assets/ASET/nasipadang-4.jpg',
            'assets/ASET/nasipadang-5.jpg',
            'assets/ASET/nasipadang-6.jpg',
            'assets/ASET/nasipadang-7.jpg',
            'assets/ASET/nasipadang-8.jpg',
            'assets/ASET/nasipadang-9.jpg',
            'assets/ASET/nasipadang-10.jpg',
            'assets/ASET/nasipadang-11.jpg',
            'assets/ASET/nasipadang-12.jpg',
        ];

        foreach ($galleryImages as $index => $image) {
            Gallery::create([
                'title' => 'Gallery ' . ($index + 1),
                'image' => $image,
                'description' => 'Beautiful food photography',
                'category' => 'food',
                'status' => 'active',
                'sort_order' => $index,
            ]);
        }

        // Settings
        Setting::create(['setting_key' => 'site_name', 'setting_value' => 'PADANG RICE']);
        Setting::create(['setting_key' => 'site_description', 'setting_value' => 'Restoran Padang Rice menyajikan hidangan khas Minangkabau dengan cita rasa autentik. Nikmati kelezatan rendang, gulai, sambal hijau, dan berbagai menu Padang lainnya yang menggugah selera.']);
        Setting::create(['setting_key' => 'email', 'setting_value' => 'padangrice@gmail.com']);
        Setting::create(['setting_key' => 'phone', 'setting_value' => '+62 812 3456 7890']);
        Setting::create(['setting_key' => 'location', 'setting_value' => 'Kota Bandung, Jawa Barat']);
        Setting::create(['setting_key' => 'contact_latitude', 'setting_value' => '-6.9175']);
        Setting::create(['setting_key' => 'contact_longitude', 'setting_value' => '107.6191']);

        // Menus - gambar dari Unsplash yang stabil
        $menus = [
            ['name' => 'Rendang Daging', 'category' => 'lauk', 'price' => 25000, 'description' => 'Daging sapi empuk dengan bumbu rempah khas Minang', 'available' => true, 'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400'],
            ['name' => 'Gulai Ayam', 'category' => 'lauk', 'price' => 18000, 'description' => 'Ayam dengan kuah gulai santan yang gurih', 'available' => true, 'image' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=400'],
            ['name' => 'Dendeng Balado', 'category' => 'lauk', 'price' => 22000, 'description' => 'Dendeng sapi kering dengan sambal balado pedas', 'available' => true, 'image' => 'https://images.unsplash.com/photo-1529042410759-befb1204b468?w=400'],
            ['name' => 'Gulai Ikan Kakap', 'category' => 'lauk', 'price' => 20000, 'description' => 'Ikan kakap segar dalam kuah gulai kuning', 'available' => true, 'image' => 'https://images.unsplash.com/photo-1580959375944-6f57f0c2e398?w=400'],
            ['name' => 'Ayam Pop', 'category' => 'lauk', 'price' => 19000, 'description' => 'Ayam goreng pucat khas Padang yang gurih', 'available' => true, 'image' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=400'],
            ['name' => 'Sambal Hijau', 'category' => 'sayur', 'price' => 8000, 'description' => 'Sambal cabai hijau dengan ikan teri', 'available' => true, 'image' => 'https://images.unsplash.com/photo-1596797038530-2c107229654b?w=400'],
            ['name' => 'Gulai Cubadak', 'category' => 'sayur', 'price' => 10000, 'description' => 'Nangka muda masak gulai santan', 'available' => true, 'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400'],
            ['name' => 'Gulai Daun Singkong', 'category' => 'sayur', 'price' => 9000, 'description' => 'Daun singkong berkuah santan pedas', 'available' => true, 'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400'],
            ['name' => 'Terong Balado', 'category' => 'sayur', 'price' => 10000, 'description' => 'Terong goreng dengan sambal balado', 'available' => true, 'image' => 'https://images.unsplash.com/photo-1607532941433-304659e8198a?w=400'],
            ['name' => 'Nasi Putih', 'category' => 'nasi', 'price' => 5000, 'description' => 'Nasi putih pulen', 'available' => true, 'image' => 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=400'],
            ['name' => 'Es Teh Manis', 'category' => 'minuman', 'price' => 5000, 'description' => 'Teh manis dingin segar', 'available' => true, 'image' => 'https://images.unsplash.com/photo-1558160074-4d7d8bdf4256?w=400'],
            ['name' => 'Es Jeruk', 'category' => 'minuman', 'price' => 7000, 'description' => 'Jus jeruk segar dengan es', 'available' => true, 'image' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=400'],
            ['name' => 'Teh Talua', 'category' => 'minuman', 'price' => 12000, 'description' => 'Teh khas Padang dengan kuning telur', 'available' => true, 'image' => 'https://images.unsplash.com/photo-1564890369478-c89ca6d9cde9?w=400'],
            ['name' => 'Air Mineral', 'category' => 'minuman', 'price' => 4000, 'description' => 'Air mineral botol 600ml', 'available' => true, 'image' => 'https://images.unsplash.com/photo-1523362628745-0c100150b504?w=400'],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }

        // Contact Messages
        ContactMessage::create([
            'subject' => 'Pertanyaan Katering Acara',
            'name' => 'Budi Santoso',
            'email' => 'budi@gmail.com',
            'message' => 'Halo, apakah TastyFood melayani pesanan katering untuk acara pernikahan 200 porsi?',
            'is_read' => false,
        ]);
        ContactMessage::create([
            'subject' => 'Konfirmasi Pembayaran',
            'name' => 'Siti Rahma',
            'email' => 'siti@gmail.com',
            'message' => 'Saya sudah transfer untuk pesanan nasi kotak, mohon dicek ya min.',
            'is_read' => false,
        ]);
        ContactMessage::create([
            'subject' => 'Pujian Masakan',
            'name' => 'Ahmad Dani',
            'email' => 'ahmad@gmail.com',
            'message' => 'Rendang dan ayam popnya luar biasa enak! Pelayanan ramah sekali.',
            'is_read' => true,
        ]);

        // Customer user 2
        $customer2 = User::firstOrCreate(
            ['email' => 'rashqaandrean@gmail.com'],
            [
                'name' => 'Rashqa Andrean',
                'phone' => '085723894370',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        // Orders & Payments
        $bca = PaymentMethod::where('name', 'BCA')->first();
        $qris = PaymentMethod::where('name', 'QRIS')->first();

        $order1 = Order::create([
            'user_id' => $customer2->id,
            'order_number' => 'PR-' . date('Ymd') . '-0001',
            'customer_name' => $customer2->name,
            'customer_email' => $customer2->email,
            'customer_phone' => $customer2->phone,
            'delivery_method' => 'delivery',
            'delivery_address' => 'Gegerkalong, Sukasari, Kota Bandung',
            'status' => 'pending',
            'subtotal' => 68000,
            'delivery_fee' => 10000,
            'total' => 78000,
            'notes' => 'Tolong sambal hijaunya dipisah',
            'latitude' => -6.8648960,
            'longitude' => 107.5904512,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'menu_id' => 1,
            'menu_name' => 'Rendang Daging',
            'menu_price' => 25000,
            'quantity' => 2,
            'subtotal' => 50000,
        ]);
        OrderItem::create([
            'order_id' => $order1->id,
            'menu_id' => 10,
            'menu_name' => 'Nasi Putih',
            'menu_price' => 5000,
            'quantity' => 2,
            'subtotal' => 10000,
        ]);
        OrderItem::create([
            'order_id' => $order1->id,
            'menu_id' => 12,
            'menu_name' => 'Es Jeruk',
            'menu_price' => 8000,
            'quantity' => 1,
            'subtotal' => 8000,
        ]);

        Payment::create([
            'order_id' => $order1->id,
            'payment_method_id' => $bca?->id ?? 1,
            'user_id' => $customer2->id,
            'amount' => 78000,
            'status' => 'waiting_verification',
            'proof_image' => 'assets/ASET/nasipadang-1.jpg',
            'user_notes' => 'Sudah transfer dari rekening BCA a.n Rashqa',
        ]);

        $order2 = Order::create([
            'user_id' => $customer2->id,
            'order_number' => 'PR-' . date('Ymd') . '-0002',
            'customer_name' => $customer2->name,
            'customer_email' => $customer2->email,
            'customer_phone' => $customer2->phone,
            'delivery_method' => 'pickup',
            'status' => 'confirmed',
            'subtotal' => 45000,
            'delivery_fee' => 0,
            'total' => 45000,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'menu_id' => 5,
            'menu_name' => 'Ayam Pop',
            'menu_price' => 19000,
            'quantity' => 2,
            'subtotal' => 38000,
        ]);
        OrderItem::create([
            'order_id' => $order2->id,
            'menu_id' => 11,
            'menu_name' => 'Es Teh Manis',
            'menu_price' => 7000,
            'quantity' => 1,
            'subtotal' => 7000,
        ]);

        Payment::create([
            'order_id' => $order2->id,
            'payment_method_id' => $qris?->id ?? 3,
            'user_id' => $customer2->id,
            'amount' => 45000,
            'status' => 'paid',
            'verified_by' => 1,
            'verified_at' => now(),
        ]);

        // Conversation & Messages
        $convo = Conversation::create([
            'user_id' => $customer2->id,
            'order_id' => $order1->id,
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        Message::create([
            'uuid' => (string) Str::uuid(),
            'conversation_id' => $convo->id,
            'sender_id' => 0,
            'sender_type' => 'system',
            'message' => 'Pesanan #' . $order1->order_number . ' telah dibuat.',
            'message_type' => 'system',
            'is_read' => true,
            'read_at' => now(),
        ]);

        Message::create([
            'uuid' => (string) Str::uuid(),
            'conversation_id' => $convo->id,
            'sender_id' => $customer2->id,
            'sender_type' => 'user',
            'message' => 'Halo admin, apakah pesanan saya sudah mulai diproses?',
            'message_type' => 'text',
            'is_read' => false,
        ]);
    }
}
