@extends('layouts.app')

@section('title', 'Keranjang – Padang Rice')

@section('content')

{{-- Toast --}}
<div id="toast" class="fixed top-6 right-6 z-[9999] flex items-center gap-3 bg-gray-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl translate-x-[120%] transition-all duration-500 ease-out max-w-xs">
    <div id="toast-icon" class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center shrink-0">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
    </div>
    <p id="toast-msg" class="text-sm font-medium leading-snug"></p>
</div>

<div class="min-h-screen bg-[#f5f4f1] pt-20 pb-16">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Header --}}
        <div class="py-8">
            <nav class="text-xs text-gray-400 mb-2 flex items-center gap-1.5">
                <a href="{{ route('home') }}" class="hover:text-gray-900 transition">Home</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-700 font-medium">Keranjang</span>
            </nav>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-display">Keranjang Saya</h1>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 mb-6 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-3 mb-6 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @if(empty($cartItems))
            {{-- Empty State --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-16 text-center">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Keranjang Anda kosong</h2>
                <p class="text-gray-500 text-sm mb-8">Belum ada menu yang dipilih. Yuk mulai pesan!</p>
                <a href="{{ route('menu.index') }}" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white font-semibold px-8 py-3 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    Lihat Menu
                </a>
            </div>
        @else
            <div class="flex flex-col lg:flex-row gap-6">

                {{-- ===== Cart Items ===== --}}
                <div class="flex-1 min-w-0 space-y-3" id="cart-items-container">
                    {{-- Header row --}}
                    <div class="hidden md:grid grid-cols-[2fr_1fr_1fr_auto] items-center gap-4 px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">
                        <span>Menu</span>
                        <span class="text-center">Jumlah</span>
                        <span class="text-right">Subtotal</span>
                        <span></span>
                    </div>

                    @foreach($cartItems as $item)
                        <div class="cart-row bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 flex flex-col sm:grid sm:grid-cols-[2fr_1fr_1fr_auto] items-center gap-4 transition-all duration-300"
                             id="row-{{ $item['menu']->id }}" data-price="{{ $item['menu']->price }}" data-id="{{ $item['menu']->id }}">

                            {{-- Menu info --}}
                            <div class="flex items-center gap-4 w-full sm:w-auto">
                                <a href="{{ route('menu.show', $item['menu']) }}" class="shrink-0">
                                    <img src="{{ $item['menu']->image ?? 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=80' }}"
                                         alt="{{ $item['menu']->name }}"
                                         class="w-16 h-16 rounded-xl object-cover"
                                         onerror="this.src='https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=80'">
                                </a>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-900 mb-0.5">{{ $item['menu']->categoryLabel }}</p>
                                    <h3 class="font-bold text-gray-900 text-sm leading-snug">{{ $item['menu']->name }}</h3>
                                    <p class="text-gray-400 text-xs">{{ $item['menu']->formattedPrice }}</p>
                                </div>
                            </div>

                            {{-- Qty Stepper --}}
                            <div class="flex items-center justify-center">
                                <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                                    <button type="button"
                                            onclick="changeQty({{ $item['menu']->id }}, -1)"
                                            class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-red-500 transition font-bold text-lg leading-none"
                                            aria-label="Kurangi">
                                        −
                                    </button>
                                    <span id="qty-{{ $item['menu']->id }}" class="w-10 text-center font-bold text-gray-900 text-sm select-none">{{ $item['quantity'] }}</span>
                                    <button type="button"
                                            onclick="changeQty({{ $item['menu']->id }}, 1)"
                                            class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-green-600 transition font-bold text-lg leading-none"
                                            aria-label="Tambah">
                                        +
                                    </button>
                                </div>
                            </div>

                            {{-- Subtotal --}}
                            <div class="text-right">
                                <span id="sub-{{ $item['menu']->id }}" class="font-bold text-gray-900">{{ 'Rp ' . number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>

                            {{-- Delete --}}
                            <form action="{{ route('cart.remove', $item['menu']->id) }}" method="POST" class="shrink-0">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-300 hover:text-red-500 hover:bg-red-50 transition"
                                        title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                {{-- ===== Order Summary Sidebar ===== --}}
                <div class="lg:w-80 shrink-0">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                        <h2 class="font-bold text-gray-900 text-lg mb-5">Ringkasan Pesanan</h2>

                        <div class="space-y-3 mb-4">
                            @foreach($cartItems as $item)
                                <div class="flex justify-between items-center text-sm" id="summary-{{ $item['menu']->id }}">
                                    <span class="text-gray-600 truncate pr-2">{{ $item['menu']->name }} <span class="text-gray-400">×<span id="sqty-{{ $item['menu']->id }}">{{ $item['quantity'] }}</span></span></span>
                                    <span class="font-medium text-gray-800 shrink-0" id="ssub-{{ $item['menu']->id }}">{{ 'Rp ' . number_format($item['subtotal'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-dashed border-gray-200 my-4"></div>

                        <div class="space-y-2 mb-5">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="font-semibold text-gray-800" id="cart-subtotal">{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Ongkir</span>
                                <span class="text-gray-400 text-xs italic">Ditentukan saat checkout</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4 mb-5">
                            <div class="flex justify-between">
                                <span class="font-bold text-gray-900">Estimasi Total</span>
                                <span class="font-bold text-xl text-gray-900" id="cart-total">{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>


                        <a href="{{ route('orders.checkout') }}" class="block w-full bg-gray-900 hover:bg-gray-800 text-white font-bold text-center py-4 rounded-xl transition shadow-md hover:shadow-lg mb-3">
                            Checkout Sekarang
                        </a>

                        <a href="{{ route('menu.index') }}" class="block w-full text-center text-sm text-gray-500 hover:text-gray-900 py-2 transition">
                            + Tambah Menu Lainnya
                        </a>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Yakin kosongkan keranjang?')"
                                        class="w-full text-xs text-red-400 hover:text-red-600 transition py-1">
                                    Kosongkan Keranjang
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Promo banner --}}
                    <div class="mt-4 bg-gray-500 rounded-2xl p-4 text-white">
                        <p class="text-xs font-bold uppercase tracking-wider opacity-80 mb-1">Info Pengiriman</p>
                        <p class="font-bold text-sm">Gratis ongkir untuk pickup!</p>
                        <p class="text-xs opacity-80 mt-0.5">Delivery +Rp 10.000 ke wilayah Bandung</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
const UPDATE_URL_BASE = '{{ url('/keranjang') }}';
const CSRF = '{{ csrf_token() }}';

function changeQty(menuId, delta) {
    const qtyEl = document.getElementById('qty-' + menuId);
    let current = parseInt(qtyEl.textContent);
    const newQty = Math.max(0, current + delta);

    // Optimistic UI update
    qtyEl.textContent = newQty;
    updateRowSubtotal(menuId, newQty);

    fetch(UPDATE_URL_BASE + '/' + menuId + '/ajax', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ quantity: newQty }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update totals
            document.getElementById('cart-subtotal').textContent = data.subtotal_formatted;
            document.getElementById('cart-total').textContent = data.subtotal_formatted;

            // If qty 0, remove the row
            if (newQty <= 0) {
                const row = document.getElementById('row-' + menuId);
                const summary = document.getElementById('summary-' + menuId);
                if (row) {
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(100%)';
                    setTimeout(() => row.remove(), 300);
                }
                if (summary) summary.remove();

                // Update cart badge
                updateCartBadge(data.cart_count);

                // If cart is now empty, reload
                if (data.cart_count === 0) {
                    setTimeout(() => window.location.reload(), 400);
                }
            } else {
                updateCartBadge(data.cart_count);
            }
        }
    })
    .catch(() => {
        // Revert on error
        qtyEl.textContent = current;
        updateRowSubtotal(menuId, current);
    });
}

function updateRowSubtotal(menuId, qty) {
    const row = document.getElementById('row-' + menuId);
    if (!row) return;
    const price = parseInt(row.dataset.price);
    const sub = price * qty;
    const formatted = 'Rp ' + sub.toLocaleString('id-ID');

    const subEl = document.getElementById('sub-' + menuId);
    if (subEl) subEl.textContent = formatted;

    const sqtyEl = document.getElementById('sqty-' + menuId);
    if (sqtyEl) sqtyEl.textContent = qty;

    const ssubEl = document.getElementById('ssub-' + menuId);
    if (ssubEl) ssubEl.textContent = formatted;

    // Recalculate total from DOM
    let total = 0;
    document.querySelectorAll('.cart-row').forEach(r => {
        const p = parseInt(r.dataset.price) || 0;
        const q = parseInt(document.getElementById('qty-' + r.dataset.id)?.textContent) || 0;
        total += p * q;
    });
    const totalFormatted = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('cart-subtotal').textContent = totalFormatted;
    document.getElementById('cart-total').textContent = totalFormatted;
}

function updateCartBadge(count) {
    const navBadge = document.querySelector('nav a[href*="keranjang"] span');
    if (navBadge) {
        navBadge.textContent = count;
        navBadge.classList.toggle('hidden', count <= 0);
    }
}
</script>
@endsection
