@extends('layouts.app')

@section('title', 'Menu Kami – Padang Rice')

@section('content')
{{-- Toast Notification --}}
<div id="toast" class="fixed top-6 right-6 z-[9999] flex items-center gap-3 bg-gray-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl translate-x-[120%] transition-all duration-500 ease-out max-w-xs">
    <div id="toast-icon" class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center shrink-0">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
    </div>
    <p id="toast-msg" class="text-sm font-medium leading-snug"></p>
</div>

{{-- Checkout Sidebar Overlay --}}
<div id="checkout-overlay" class="fixed inset-0 bg-black/50 z-[100] hidden transition-opacity" onclick="closeCheckout()"></div>

{{-- Checkout Sidebar --}}
<div id="checkout-sidebar" class="fixed top-0 right-0 h-full w-full md:w-[480px] bg-white z-[101] transform translate-x-full transition-transform duration-300 shadow-2xl overflow-y-auto">
    <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10">
        <h2 class="font-bold text-gray-900 text-lg">Checkout</h2>
        <button onclick="closeCheckout()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div id="checkout-content" class="p-6 pb-32">
        <div id="empty-cart-msg" class="text-center py-12 hidden">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <p class="text-gray-500 text-sm">Keranjang kosong</p>
            <p class="text-gray-400 text-xs mt-1">Pilih menu untuk memulai pesanan</p>
        </div>

        <form id="checkoutForm" action="{{ route('menu.checkout') }}" method="POST" class="space-y-5 hidden">
            @csrf
            
            {{-- Cart Items --}}
            <div class="bg-gray-50 rounded-xl p-4">
                <h3 class="font-bold text-gray-900 text-sm mb-3">Pesanan Anda</h3>
                <div id="checkout-cart-items" class="space-y-2 mb-3"></div>
                <div class="border-t border-gray-200 pt-3 space-y-1.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span id="checkout-subtotal" class="font-semibold">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ongkir</span>
                        <span id="checkout-delivery-fee" class="font-semibold">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t">
                        <span>Total</span>
                        <span id="checkout-total">Rp 0</span>
                    </div>
                </div>
            </div>

            {{-- Customer Data --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Nama Lengkap *</label>
                <input type="text" name="customer_name" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent transition outline-none"
                       placeholder="Masukkan nama lengkap">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Email *</label>
                    <input type="email" name="customer_email" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent transition outline-none"
                           placeholder="email@contoh.com">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">No. Telepon *</label>
                    <input type="text" name="customer_phone" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent transition outline-none"
                           placeholder="08xxxxxxxxxx">
                </div>
            </div>

            {{-- Delivery Method --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Metode Pengiriman *</label>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <label class="delivery-option relative flex items-center gap-2 p-3 border-2 border-gray-500 bg-gray-50 rounded-xl cursor-pointer" id="label-pickup">
                        <input type="radio" name="delivery_method" value="pickup" checked class="sr-only">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 text-xs">Ambil Sendiri</p>
                            <p class="text-[10px] text-green-600 font-medium">Gratis</p>
                        </div>
                    </label>
                    <label class="delivery-option relative flex items-center gap-2 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-gray-300" id="label-delivery">
                        <input type="radio" name="delivery_method" value="delivery" class="sr-only">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 text-xs">Diantar</p>
                            <p class="text-[10px] text-orange-600 font-medium">+Rp 10.000</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Delivery Fields --}}
            <div id="delivery-fields" class="space-y-3 hidden">
                <div class="relative">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Alamat Pengiriman *</label>
                    <div class="relative">
                        <input type="text" id="address-input" name="delivery_address"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-24 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent transition outline-none"
                               placeholder="Cari alamat...">
                        <button type="button" id="loc-btn" onclick="getCurrentLocation()"
                                class="absolute right-2 top-1/2 -translate-y-1/2 bg-gray-900 hover:bg-gray-800 text-white px-2.5 py-1.5 rounded-lg text-[10px] font-semibold flex items-center gap-1 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Lokasi
                        </button>
                    </div>
                    <div id="suggestions" class="absolute z-20 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden max-h-48 overflow-y-auto"></div>
                </div>
                <div id="map" class="w-full h-48 rounded-xl border border-gray-200"></div>
                <div id="distance-info" class="hidden bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-blue-700 font-medium">Jarak dari restoran:</span>
                        <span id="distance-value" class="font-bold text-blue-900">0 km</span>
                    </div>
                </div>
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Catatan (opsional)</label>
                <textarea name="notes" rows="2"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent transition outline-none resize-none"
                          placeholder="Contoh: Tidak pakai sambal, tingkat pedas sedang"></textarea>
            </div>

            <button type="submit" id="submitBtn"
                    class="w-full bg-gray-900 hover:bg-gray-800 active:scale-[0.98] text-white font-bold py-3 rounded-xl transition-all shadow-sm">
                Buat Pesanan →
            </button>
        </form>
    </div>
</div>

{{-- Floating Checkout Button --}}
<button id="floating-checkout-btn" onclick="openCheckout()"
        class="hidden fixed bottom-6 right-24 z-50 bg-gray-900 hover:bg-gray-800 text-white font-bold px-6 py-4 rounded-full shadow-2xl flex items-center gap-2 transition-all hover:scale-105 active:scale-95">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
    <span id="floating-cart-count">0</span>
</button>

<div class="min-h-screen bg-[#f5f4f1] pt-20">

    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100 py-8 px-4">
        <div class="max-w-[1280px] mx-auto flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                {{-- Breadcrumb --}}
                <nav class="text-xs text-gray-400 mb-2 flex items-center gap-1.5">
                    <a href="{{ route('home') }}" class="hover:text-gray-900 transition">Home</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-gray-700 font-medium">Menu Lengkap</span>
                </nav>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-display">Menu Lengkap</h1>
                <p class="text-gray-500 mt-1">
                    <span class="font-semibold text-gray-900">{{ $menus->total() }}</span> hidangan khas Minangkabau tersedia
                </p>
            </div>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('menu.index') }}" id="searchForm" class="flex w-full md:w-auto md:max-w-md">
                @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                @if(request('price_min')) <input type="hidden" name="price_min" value="{{ request('price_min') }}"> @endif
                @if(request('price_max')) <input type="hidden" name="price_max" value="{{ request('price_max') }}"> @endif
                <div class="flex w-full border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm focus-within:ring-2 focus-within:ring-gray-900 focus-within:border-transparent transition-all">
                    <span class="pl-4 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari menu, rendang, gulai..."
                           class="flex-1 px-3 py-3 text-sm outline-none bg-transparent text-gray-700 placeholder-gray-400">
                    <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-3 font-semibold text-sm transition">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="max-w-[1280px] mx-auto px-4 py-8 flex gap-8">

        {{-- ===== SIDEBAR FILTER ===== --}}
        <aside class="w-[220px] shrink-0 hidden lg:block">
            <form method="GET" action="{{ route('menu.index') }}" id="filterForm">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                <div class="bg-white rounded-xl border border-gray-200 p-5 sticky top-24">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="font-bold text-gray-900 text-base">Filter</h2>
                        @if(request('category') || request('price_min') || request('price_max'))
                            <a href="{{ route('menu.index', array_filter(['search' => request('search'), 'sort' => request('sort')])) }}" class="text-xs text-gray-900 hover:text-gray-900 font-medium transition">Hapus Semua</a>
                        @endif
                    </div>

                    {{-- Category --}}
                    <div class="mb-6">
                        <h3 class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-3">Kategori</h3>
                        <div class="space-y-2">
                            @php
                                $categories = [
                                    '' => 'Semua Menu',
                                    'lauk' => 'Lauk Pauk',
                                    'sayur' => 'Sayur & Pelengkap',
                                    'nasi' => 'Nasi',
                                    'minuman' => 'Minuman',
                                ];
                            @endphp
                            @foreach($categories as $val => $label)
                                @php
                                    $active = request('category', '') === $val;
                                @endphp
                                <label class="block cursor-pointer">
                                    <input type="radio" name="category" value="{{ $val }}"
                                           {{ $active ? 'checked' : '' }}
                                           onchange="document.getElementById('filterForm').submit()"
                                           class="sr-only peer">
                                    <span class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg border transition-all
                                          {{ $active
                                              ? 'bg-gray-900 border-gray-900 text-white font-semibold shadow-sm'
                                              : 'bg-white border-gray-200 text-gray-700 hover:border-gray-400 hover:bg-gray-50'
                                          }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $active ? 'bg-white' : 'bg-gray-300' }}"></span>
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Price Range --}}
                    <div class="mb-2">
                        <h3 class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-3">Kisaran Harga</h3>
                        <div class="space-y-3">
                            <div class="relative">
                                <input type="range" id="priceRange" min="{{ $minPrice }}" max="{{ $maxPrice }}" step="1000"
                                       value="{{ request('price_max', $maxPrice) }}"
                                       class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-gray-900"
                                       oninput="document.getElementById('priceDisplay').textContent = 'Maks: Rp ' + parseInt(this.value).toLocaleString('id-ID')">
                                <div class="flex justify-between text-[11px] text-gray-500 mt-2">
                                    <span>Rp {{ number_format($minPrice, 0, ',', '.') }}</span>
                                    <span>Rp {{ number_format($maxPrice, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div id="priceDisplay" class="text-sm font-semibold text-gray-900 bg-gray-50 rounded-md px-3 py-2 text-center">
                                Maks: Rp {{ number_format(request('price_max', $maxPrice), 0, ',', '.') }}
                            </div>
                            <input type="hidden" name="price_max" id="priceMaxInput" value="{{ request('price_max', $maxPrice) }}">
                            <button type="button" onclick="applyPrice()" class="w-full bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold py-2 rounded-lg transition">
                                Terapkan
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="category" id="hiddenCategory" value="{{ request('category', '') }}">
                </div>
            </form>
        </aside>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="flex-1 min-w-0">

            {{-- Mobile Category Tabs --}}
            <div class="lg:hidden flex gap-2 overflow-x-auto pb-2 mb-6 scrollbar-hide -mx-1 px-1">
                @foreach(['' => 'Semua', 'lauk' => 'Lauk', 'sayur' => 'Sayur', 'nasi' => 'Nasi', 'minuman' => 'Minuman'] as $val => $label)
                    @php
                        $mobileActive = request('category', '') === $val;
                    @endphp
                    <a href="{{ route('menu.index', array_filter(['category' => $val, 'search' => request('search'), 'sort' => request('sort')])) }}"
                       class="shrink-0 px-4 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap {{ $mobileActive ? 'bg-gray-900 text-white shadow-sm' : 'bg-white text-gray-700 border border-gray-200 hover:border-gray-400' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Toolbar: Count + Sort --}}
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-gray-500">
                    Menampilkan <span class="font-semibold text-gray-800">{{ $menus->firstItem() }}–{{ $menus->lastItem() }}</span> dari <span class="font-semibold text-gray-800">{{ $menus->total() }}</span> menu
                </p>
                <div class="relative">
                    <select onchange="sortMenu(this.value)"
                            class="appearance-none bg-white border border-gray-200 rounded-lg pl-4 pr-9 py-2 text-sm font-medium text-gray-700 cursor-pointer focus:ring-2 focus:ring-gray-900 focus:border-transparent shadow-sm">
                        <option value="" {{ !request('sort') ? 'selected' : '' }}>Terpopuler</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>A – Z</option>
                    </select>
                    <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            @if($menus->isEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-16 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-700 mb-2">Menu tidak ditemukan</h3>
                    <p class="text-gray-400 text-sm mb-6">Coba ubah filter atau kata pencarian Anda</p>
                    <a href="{{ route('menu.index') }}" class="inline-block bg-gray-900 hover:bg-gray-800 text-white font-semibold px-6 py-2.5 rounded-lg transition">
                        Lihat Semua Menu
                    </a>
                </div>
            @else
                {{-- Menu Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($menus as $menu)
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden group hover:border-gray-300 hover:shadow-lg transition-all duration-300 flex flex-col"
                             id="menu-card-{{ $menu->id }}">

                            {{-- IMAGE — 4:3 aspect ratio --}}
                            <div class="relative aspect-[4/3] w-full shrink-0 overflow-hidden bg-gray-100">
                                <a href="{{ route('menu.show', $menu) }}" class="block w-full h-full">
                                    <img src="{{ $menu->image ?? 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400' }}"
                                         alt="{{ $menu->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         onerror="this.src='https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400'">
                                </a>

                                @if(!$menu->available)
                                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                        <span class="bg-white/95 text-gray-900 text-xs font-bold px-4 py-1.5 rounded-md">Habis</span>
                                    </div>
                                @endif

                                <button class="absolute top-3 right-3 w-8 h-8 bg-white/95 rounded-full flex items-center justify-center shadow opacity-0 group-hover:opacity-100 transition-opacity duration-200 active:scale-90"
                                        onclick="event.preventDefault(); wishlistToggle(this)">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </button>
                            </div>

                            {{-- INFO — 16px padding, flexible height --}}
                            <div class="p-4 flex flex-col flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ $menu->categoryLabel }}</span>
                                    @if($menu->available)
                                        <span class="text-[10px] text-green-700 font-medium bg-green-50 px-2 py-0.5 rounded-full">Tersedia</span>
                                    @else
                                        <span class="text-[10px] text-gray-500 font-medium bg-gray-100 px-2 py-0.5 rounded-full">Habis</span>
                                    @endif
                                </div>

                                <a href="{{ route('menu.show', $menu) }}" class="block mb-2">
                                    <h3 class="font-bold text-gray-900 text-lg leading-tight hover:text-gray-900 transition-colors line-clamp-1">{{ $menu->name }}</h3>
                                </a>

                                <p class="text-gray-500 text-sm leading-relaxed line-clamp-2 mb-4">{{ $menu->description }}</p>

                                <div class="flex items-center justify-between mt-auto pt-3 border-t border-gray-100">
                                    <span class="text-gray-900 font-bold text-base">{{ $menu->formattedPrice }}</span>

                                    @if($menu->available)
                                        <button onclick="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}')"
                                                class="cart-btn-{{ $menu->id }} px-3 py-1.5 bg-gray-900 hover:bg-gray-900 active:bg-gray-800 text-white text-xs font-semibold rounded-md flex items-center gap-1 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Tambah
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 font-medium">Habis</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($menus->hasPages())
                    <div class="mt-12 flex justify-center">
                        <div class="bg-white rounded-xl border border-gray-200 px-4 py-2 inline-flex items-center gap-1">
                            {{ $menus->appends(request()->query())->links('vendor.pagination.padang') }}
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const CART_AJAX_URL = '{{ route('cart.add.ajax') }}';
const CSRF_TOKEN = '{{ csrf_token() }}';
const CALC_DELIVERY_URL = '{{ route('api.calculate-delivery-fee') }}';
const RESTAURANT_LAT = {{ (float) config('restaurant.latitude') }};
const RESTAURANT_LNG = {{ (float) config('restaurant.longitude') }};
const RESTAURANT_NAME = '{{ config('restaurant.name') }}';

let map, marker, restaurantMarker, searchTimeout;
let currentDelivery = 'pickup';
let cartMenuData = @json($cartWithMenus);
let currentDeliveryFee = 0;
let currentDistance = 0;
let isCalculatingFee = false;

document.addEventListener('DOMContentLoaded', () => {
    updateCheckoutUI();
    // Toggle delivery method
    document.querySelectorAll('input[name="delivery_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Update UI label highlight
            document.querySelectorAll('.delivery-option').forEach(label => {
                label.classList.remove('border-gray-500', 'bg-gray-50');
                label.classList.add('border-gray-200');
            });
            const parentLabel = this.closest('.delivery-option');
            parentLabel.classList.add('border-gray-500', 'bg-gray-50');
            parentLabel.classList.remove('border-gray-200');
            
            // Show/hide delivery fields
            const deliveryFields = document.getElementById('delivery-fields');
            if (this.value === 'delivery') {
                deliveryFields.classList.remove('hidden');
                if (!map) setTimeout(() => initMap(), 100);
            } else {
                deliveryFields.classList.add('hidden');
            }
            currentDelivery = this.value;
            renderCheckoutCart();
        });
    });
});

function addToCart(menuId, menuName) {
    const btn = document.querySelector('.cart-btn-' + menuId);
    if (!btn) return;

    btn.innerHTML = `<svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>`;
    btn.disabled = true;

    fetch(CART_AJAX_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ menu_id: menuId, quantity: 1 }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            // Update cart data without page reload
            if (!cartMenuData[data.menu_id]) {
                cartMenuData[data.menu_id] = { menu: data.menu, quantity: 1 };
            } else {
                cartMenuData[data.menu_id].quantity += 1;
            }
            updateCheckoutUI();
        } else {
            showToast(data.message, 'error');
            resetBtn(btn);
        }
    })
    .catch(() => {
        showToast('Gagal menambahkan ke keranjang', 'error');
        resetBtn(btn);
    });
}

function resetBtn(btn) {
    btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah`;
    btn.disabled = false;
}

function updateCheckoutUI() {
    const isEmpty = Object.keys(cartMenuData).length === 0;
    const floatingBtn = document.getElementById('floating-checkout-btn');
    const emptyMsg = document.getElementById('empty-cart-msg');
    const checkoutForm = document.getElementById('checkoutForm');
    const floatingCount = document.getElementById('floating-cart-count');

    if (isEmpty) {
        floatingBtn.classList.add('hidden');
        emptyMsg.classList.remove('hidden');
        checkoutForm.classList.add('hidden');
    } else {
        floatingBtn.classList.remove('hidden');
        emptyMsg.classList.add('hidden');
        checkoutForm.classList.remove('hidden');
        floatingCount.textContent = Object.keys(cartMenuData).length;
        renderCheckoutCart();
    }
}

function calculateSubtotal() {
    let subtotal = 0;
    Object.values(cartMenuData).forEach(item => {
        subtotal += item.menu.price * item.quantity;
    });
    return subtotal;
}

function renderCheckoutCart() {
    const container = document.getElementById('checkout-cart-items');
    let html = '';

    Object.entries(cartMenuData).forEach(([menuId, item]) => {
        const menu = item.menu;
        const quantity = item.quantity;
        const itemTotal = menu.price * quantity;
        html += `
            <div class="flex items-center gap-2 text-xs">
                <span class="flex-1 font-semibold text-gray-900">${quantity}x ${menu.name}</span>
                <span class="font-bold">Rp ${itemTotal.toLocaleString('id-ID')}</span>
            </div>
        `;
    });

    container.innerHTML = html;
    
    const subtotal = calculateSubtotal();
    const deliveryFee = currentDelivery === 'delivery' ? currentDeliveryFee : 0;
    const total = subtotal + deliveryFee;

    document.getElementById('checkout-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('checkout-delivery-fee').textContent = 'Rp ' + deliveryFee.toLocaleString('id-ID');
    document.getElementById('checkout-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
    
    // Update distance info if visible
    updateDistanceInfo();
}

function openCheckout() {
    if (Object.keys(cartMenuData).length === 0) return;
    document.getElementById('checkout-overlay').classList.remove('hidden');
    document.getElementById('checkout-sidebar').classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
    
    // Init map if delivery is selected
    if (currentDelivery === 'delivery' && !map) {
        setTimeout(() => initMap(), 100);
    }
}

function closeCheckout() {
    document.getElementById('checkout-overlay').classList.add('hidden');
    document.getElementById('checkout-sidebar').classList.add('translate-x-full');
    document.body.style.overflow = '';
}

function initMap() {
    map = L.map('map').setView([RESTAURANT_LAT, RESTAURANT_LNG], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);
    
    // Add restaurant marker
    const restaurantIcon = L.divIcon({
        html: `<div style="background:#dc2626;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);">
                  <svg style="width:20px;height:20px;color:white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
               </div>`,
        iconSize: [36, 36],
        iconAnchor: [18, 18],
        className: 'restaurant-marker'
    });
    
    restaurantMarker = L.marker([RESTAURANT_LAT, RESTAURANT_LNG], { icon: restaurantIcon })
        .addTo(map)
        .bindPopup(`<div style="text-align:center;font-size:12px;"><strong>${RESTAURANT_NAME}</strong><br><small>Restoran</small></div>`);
    
    // Delivery radius circle
    const radius = {{ (float) config('restaurant.delivery_radius_km') }} * 1000; // meters
    L.circle([RESTAURANT_LAT, RESTAURANT_LNG], {
        radius: radius,
        color: '#eab308',
        fillColor: '#fde047',
        fillOpacity: 0.15,
        weight: 2,
        dashArray: '5, 5'
    }).addTo(map);
}

function setMarker(lat, lng) {
    if (marker) map.removeLayer(marker);
    const deliveryIcon = L.divIcon({
        html: `<div style="background:#eab308;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);">
                  <svg style="width:18px;height:18px;color:white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
               </div>`,
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        className: 'delivery-marker'
    });
    
    marker = L.marker([lat, lng], { draggable: true, icon: deliveryIcon }).addTo(map);
    marker.on('dragend', onMarkerDrag);
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    requestDeliveryFee(lat, lng);
}

function onMarkerDrag(e) {
    const pos = e.target.getLatLng();
    document.getElementById('latitude').value = pos.lat;
    document.getElementById('longitude').value = pos.lng;
    reverseGeocode(pos.lat, pos.lng);
    requestDeliveryFee(pos.lat, pos.lng);
}

function requestDeliveryFee(lat, lng) {
    if (isCalculatingFee) return;
    isCalculatingFee = true;
    
    const subtotal = calculateSubtotal();
    
    fetch(CALC_DELIVERY_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ latitude: lat, longitude: lng, subtotal: subtotal }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            currentDeliveryFee = data.delivery_fee;
            currentDistance = data.distance;
            renderCheckoutCart();
        } else {
            currentDeliveryFee = 0;
            currentDistance = 0;
            showToast(data.message || 'Lokasi di luar jangkauan', 'error');
            renderCheckoutCart();
        }
    })
    .catch(() => {
        showToast('Gagal menghitung ongkir', 'error');
    })
    .finally(() => {
        isCalculatingFee = false;
    });
}

function updateDistanceInfo() {
    const info = document.getElementById('distance-info');
    if (!info) return;
    if (currentDelivery === 'delivery' && currentDistance > 0) {
        info.classList.remove('hidden');
        document.getElementById('distance-value').textContent = currentDistance.toFixed(1) + ' km';
    } else {
        info.classList.add('hidden');
    }
}

function reverseGeocode(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(r => r.json())
        .then(data => {
            if (data.display_name) document.getElementById('address-input').value = data.display_name;
        }).catch(() => {});
}

function getCurrentLocation() {
    if (!navigator.geolocation) return alert('Geolocation tidak didukung');
    const btn = document.getElementById('loc-btn');
    const originalHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Lokasi`;
    btn.innerHTML = `<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>`;
    btn.disabled = true;

    navigator.geolocation.getCurrentPosition(
        pos => {
            setMarker(pos.coords.latitude, pos.coords.longitude);
            map.setView([pos.coords.latitude, pos.coords.longitude], 15);
            reverseGeocode(pos.coords.latitude, pos.coords.longitude);
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        },
        () => {
            alert('Tidak dapat mengakses lokasi');
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

document.getElementById('address-input')?.addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const query = e.target.value;
    if (query.length < 3) {
        document.getElementById('suggestions').classList.add('hidden');
        return;
    }
    searchTimeout = setTimeout(() => {
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`)
            .then(r => r.json())
            .then(data => {
                const sug = document.getElementById('suggestions');
                if (!data.length) { sug.classList.add('hidden'); return; }
                sug.innerHTML = data.map(item =>
                    `<div class="px-4 py-2 hover:bg-gray-50 cursor-pointer border-b last:border-0 text-xs"
                          onclick="selectPlace(${item.lat}, ${item.lon}, '${item.display_name.replace(/'/g, "\\'")}')">
                        ${item.display_name}
                    </div>`
                ).join('');
                sug.classList.remove('hidden');
            }).catch(() => {});
    }, 500);
});

function selectPlace(lat, lng, address) {
    document.getElementById('address-input').value = address;
    document.getElementById('suggestions').classList.add('hidden');
    setMarker(lat, lng);
    map.setView([lat, lng], 15);
}

document.addEventListener('click', e => {
    if (!e.target.closest('#address-input') && !e.target.closest('#suggestions')) {
        document.getElementById('suggestions')?.classList.add('hidden');
    }
});

document.getElementById('checkoutForm')?.addEventListener('submit', () => {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = `<svg class="w-5 h-5 animate-spin inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Memproses...`;
    btn.disabled = true;
});

function showToast(msg, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toast-msg');
    const toastIcon = document.getElementById('toast-icon');

    toastMsg.textContent = msg;
    if (type === 'success') {
        toastIcon.className = 'w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center shrink-0';
        toastIcon.innerHTML = `<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`;
    } else {
        toastIcon.className = 'w-8 h-8 bg-red-500 rounded-full flex items-center justify-center shrink-0';
        toastIcon.innerHTML = `<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
    }

    toast.classList.remove('translate-x-[120%]');
    toast.classList.add('translate-x-0');

    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-[120%]');
    }, 3000);
}

function wishlistToggle(btn) {
    const svg = btn.querySelector('svg');
}
</script>

<style>
/* Mobile: Stack floating buttons vertically */
@media (max-width: 640px) {
    #floating-checkout-btn {
        right: 1rem !important;
        bottom: 5rem !important;
    }
}
</style>
@endsection
