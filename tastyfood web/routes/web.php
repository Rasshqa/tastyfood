<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Models\Menu;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/orders/{order}/payment', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/orders/{order}/payment/upload', [PaymentController::class, 'uploadForm'])->name('payments.upload');
    Route::post('/orders/{order}/payment/upload', [PaymentController::class, 'uploadProof'])->name('payments.uploadProof');
    Route::get('/orders/{order}/payment/status', [PaymentController::class, 'status'])->name('payments.status');

    Route::get('/pesanan', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/pesanan/sukses/{order}', [OrderController::class, 'success'])->name('orders.success');
    Route::patch('/pesanan/{order}/selesai', [OrderController::class, 'completeOrder'])->name('orders.complete');

    // ===== CHAT ROUTES =====
    Route::post('/api/chat', [ChatController::class, 'getOrCreateConversation'])->name('chat.init');
    Route::get('/api/chat/{conversation}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/api/chat/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/api/chat/{conversation}/read', [ChatController::class, 'markAsRead'])->name('chat.read');
    Route::post('/api/chat/{conversation}/typing', [ChatController::class, 'typing'])->name('chat.typing');
});

Route::get('/tentang', [AboutController::class, 'index'])->name('about');
Route::get('/berita', [NewsController::class, 'index'])->name('news.index');
Route::get('/berita/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/galeri', [GalleryController::class, 'index'])->name('gallery');
Route::get('/kontak', [ContactController::class, 'index'])->name('contact.index');
Route::post('/kontak', [ContactController::class, 'store'])->name('contact.store');

// ===== UNIFIED MENU + CHECKOUT =====
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{menu}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/checkout', function () {
    $cart = session('cart', []);
    if (empty($cart)) {
        return redirect()->route('cart.index')->with('error', 'Keranjang kosong');
    }
    $cartItems = [];
    $subtotal = 0;
    foreach ($cart as $id => $item) {
        $menu = Menu::find($id);
        if ($menu) {
            $cartItems[] = ['menu' => $menu, 'quantity' => $item['quantity'], 'subtotal' => $menu->price * $item['quantity']];
            $subtotal += $menu->price * $item['quantity'];
        }
    }

    return view('pages.orders.checkout', compact('cartItems', 'subtotal'));
})->name('orders.checkout')->middleware('auth');
Route::post('/menu/checkout', [OrderController::class, 'store'])->name('menu.checkout')->middleware('auth');
Route::post('/api/calculate-delivery-fee', [OrderController::class, 'calculateDeliveryFee'])->name('api.calculate-delivery-fee')->middleware('auth');

Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/tambah', [CartController::class, 'add'])->name('cart.add');
Route::post('/keranjang/tambah-ajax', [CartController::class, 'addAjax'])->name('cart.add.ajax');
Route::patch('/keranjang/{menu}', [CartController::class, 'update'])->name('cart.update');
Route::patch('/keranjang/{menu}/ajax', [CartController::class, 'updateAjax'])->name('cart.update.ajax');
Route::delete('/keranjang/{menu}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/keranjang', [CartController::class, 'clear'])->name('cart.clear');

Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:admin', 'admin'])->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::resource('admin/berita', App\Http\Controllers\Admin\NewsController::class)
        ->parameters(['berita' => 'news'])
        ->names('admin.berita');
    Route::resource('admin/galeri', App\Http\Controllers\Admin\GalleryController::class)
        ->parameters(['galeri' => 'gallery'])
        ->names('admin.galeri');
    Route::resource('admin/pesan', MessageController::class)
        ->parameters(['pesan' => 'contactMessage'])
        ->names('admin.pesan');
    Route::resource('admin/menus', App\Http\Controllers\Admin\MenuController::class)
        ->names('admin.menus');
    Route::resource('admin/payment-methods', PaymentMethodController::class)
        ->names('admin.payment-methods')
        ->except(['show']);

    // ===== UNIFIED ORDER MANAGEMENT =====
    Route::get('/admin/pesanan', [OrderManagementController::class, 'index'])->name('admin.order-management.index');
    Route::get('/admin/pesanan/{order}', [OrderManagementController::class, 'show'])->name('admin.order-management.show');
    Route::patch('/admin/pesanan/{order}/status', [OrderManagementController::class, 'updateOrderStatus'])->name('admin.order-management.update-status');
    Route::post('/admin/order-management/payment/{payment}/approve', [OrderManagementController::class, 'approvePayment'])->name('admin.order-management.approve-payment');
    Route::post('/admin/order-management/payment/{payment}/reject', [OrderManagementController::class, 'rejectPayment'])->name('admin.order-management.reject-payment');

    Route::get('/admin/pengaturan', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/pengaturan', [SettingController::class, 'update'])->name('admin.settings.update');

    // ===== ADMIN CHAT ROUTES =====
    Route::get('/admin/chat', [App\Http\Controllers\Admin\ChatController::class, 'index'])->name('admin.chat.index');
    Route::get('/admin/chat/{conversation}', [App\Http\Controllers\Admin\ChatController::class, 'show'])->name('admin.chat.show');
    Route::get('/admin/chat/{conversation}/messages', [App\Http\Controllers\Admin\ChatController::class, 'getMessages'])->name('admin.chat.messages');
    Route::post('/admin/chat/{conversation}/messages', [App\Http\Controllers\Admin\ChatController::class, 'sendMessage'])->name('admin.chat.send');
    Route::post('/admin/chat/{conversation}/read', [App\Http\Controllers\Admin\ChatController::class, 'markAsRead'])->name('admin.chat.read');
    Route::post('/admin/chat/{conversation}/typing', [App\Http\Controllers\Admin\ChatController::class, 'typing'])->name('admin.chat.typing');
    Route::post('/admin/chat/{conversation}/close', [App\Http\Controllers\Admin\ChatController::class, 'close'])->name('admin.chat.close');
    Route::get('/admin/chat-search', [App\Http\Controllers\Admin\ChatController::class, 'search'])->name('admin.chat.search');
    Route::post('/admin/chat-toggle', [App\Http\Controllers\Admin\ChatController::class, 'toggleAvailability'])->name('admin.chat.toggle');
    Route::get('/admin/chat-unread', [App\Http\Controllers\Admin\ChatController::class, 'getTotalUnread'])->name('admin.chat.unread');
});
