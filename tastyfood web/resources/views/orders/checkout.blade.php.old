@extends('layouts.app')

@section('title', 'Checkout – Padang Rice')

@section('content')
<div class="min-h-screen bg-[#f5f4f1] pt-20 pb-16">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Header --}}
        <div class="py-8">
            <nav class="text-xs text-gray-400 mb-2 flex items-center gap-1.5">
                <a href="{{ route('home') }}" class="hover:text-yellow-600 transition">Home</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('cart.index') }}" class="hover:text-yellow-600 transition">Keranjang</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-700 font-medium">Checkout</span>
            </nav>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-display">Checkout</h1>

            {{-- Steps --}}
            <div class="flex items-center gap-2 mt-4">
                <div class="flex items-center gap-1.5 text-xs font-semibold text-yellow-700">
                    <span class="w-5 h-5 bg-yellow-600 text-white rounded-full flex items-center justify-center text-[10px] font-bold">1</span>
                    Data Pemesan
                </div>
                <div class="h-px w-8 bg-gray-300"></div>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-yellow-700">
                    <span class="w-5 h-5 bg-yellow-600 text-white rounded-full flex items-center justify-center text-[10px] font-bold">2</span>
                    Pengiriman
                </div>
                <div class="h-px w-8 bg-gray-300"></div>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-gray-400">
                    <span class="w-5 h-5 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center text-[10px] font-bold">3</span>
                    Selesai
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-3 mb-6 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('orders.store') }}" method="POST" id="checkoutForm" class="flex flex-col lg:flex-row gap-6">
            @csrf

            {{-- ===== LEFT COLUMN ===== --}}
            <div class="flex-1 space-y-5">

                {{-- Data Pemesan --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 bg-yellow-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h2 class="font-bold text-gray-900 text-base">Data Pemesan</h2>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Nama Lengkap</label>
                            <input type="text" name="customer_name" required
                                   value="{{ old('customer_name') }}"
                                   class="w-full border {{ $errors->has('customer_name') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition outline-none"
                                   placeholder="Masukkan nama lengkap Anda">
                            @error('customer_name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Email</label>
                                <input type="email" name="customer_email" required
                                       value="{{ old('customer_email') }}"
                                       class="w-full border {{ $errors->has('customer_email') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition outline-none"
                                       placeholder="email@contoh.com">
                                @error('customer_email') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">No. Telepon</label>
                                <input type="text" name="customer_phone" required
                                       value="{{ old('customer_phone') }}"
                                       class="w-full border {{ $errors->has('customer_phone') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition outline-none"
                                       placeholder="08xxxxxxxxxx">
                                @error('customer_phone') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Metode Pengiriman --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 bg-yellow-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        </div>
                        <h2 class="font-bold text-gray-900 text-base">Metode Pengiriman</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                        {{-- Pickup --}}
                        <label id="label-pickup" onclick="setDelivery('pickup')"
                               class="delivery-option relative flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all {{ old('delivery_method', 'pickup') === 'pickup' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200 hover:border-yellow-300' }}">
                            <input type="radio" name="delivery_method" value="pickup" {{ old('delivery_method', 'pickup') === 'pickup' ? 'checked' : '' }}
                                   class="sr-only">
                            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-900 text-sm">Ambil Sendiri</p>
                                <p class="text-xs text-gray-500 mt-0.5">Ambil di restoran kami</p>
                                <span class="inline-block mt-1.5 text-xs font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Gratis</span>
                            </div>
                            <div id="check-pickup" class="w-5 h-5 rounded-full border-2 border-yellow-500 flex items-center justify-center {{ old('delivery_method', 'pickup') === 'pickup' ? '' : 'hidden' }}">
                                <div class="w-2.5 h-2.5 bg-yellow-500 rounded-full"></div>
                            </div>
                        </label>

                        {{-- Delivery --}}
                        <label id="label-delivery" onclick="setDelivery('delivery')"
                               class="delivery-option relative flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all {{ old('delivery_method') === 'delivery' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200 hover:border-yellow-300' }}">
                            <input type="radio" name="delivery_method" value="delivery" {{ old('delivery_method') === 'delivery' ? 'checked' : '' }}
                                   class="sr-only">
                            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-900 text-sm">Diantar</p>
                                <p class="text-xs text-gray-500 mt-0.5">Ke alamat Anda</p>
                                <span class="inline-block mt-1.5 text-xs font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full">+Rp 10.000</span>
                            </div>
                            <div id="check-delivery" class="w-5 h-5 rounded-full border-2 border-yellow-500 flex items-center justify-center {{ old('delivery_method') === 'delivery' ? '' : 'hidden' }}">
                                <div class="w-2.5 h-2.5 bg-yellow-500 rounded-full"></div>
                            </div>
                        </label>
                    </div>

                    {{-- Delivery Address Fields --}}
                    <div id="delivery-fields" class="space-y-4 {{ old('delivery_method') !== 'delivery' ? 'hidden' : '' }}">
                        <div class="relative">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Alamat Pengiriman</label>
                            <div class="relative">
                                <input type="text" id="address-input" name="delivery_address"
                                       value="{{ old('delivery_address') }}"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-3 pr-36 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition outline-none"
                                       placeholder="Cari atau ketik alamat lengkap"
                                       autocomplete="off">
                                <button type="button" onclick="getCurrentLocation()"
                                        id="loc-btn"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Lokasi Saya
                                </button>
                            </div>
                            <div id="suggestions" class="hidden absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl max-h-52 overflow-y-auto"></div>
                            @error('delivery_address') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Pilih Lokasi di Peta</label>
                            <div id="map" class="h-64 rounded-xl border border-gray-200 overflow-hidden"></div>
                            <p class="text-xs text-gray-400 mt-1.5">Klik peta atau geser marker untuk menandai lokasi pengiriman</p>
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                            @error('latitude') <p class="text-red-500 text-xs mt-1">Pilih lokasi di peta</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 bg-yellow-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <h2 class="font-bold text-gray-900 text-base">Catatan <span class="text-gray-400 font-normal text-sm">(opsional)</span></h2>
                    </div>
                    <textarea name="notes" rows="2"
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition outline-none resize-none"
                              placeholder="Contoh: Tidak pakai sambal, tingkat pedas sedang, dll.">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- ===== RIGHT COLUMN – Order Summary ===== --}}
            <div class="lg:w-80 shrink-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h2 class="font-bold text-gray-900 text-base mb-4">Ringkasan Pesanan</h2>

                    <div class="space-y-3 mb-4">
                        @foreach($cartItems as $item)
                            <div class="flex items-center gap-3">
                                <div class="relative shrink-0">
                                    <img src="{{ $item['menu']->image ?? 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=60' }}"
                                         alt="{{ $item['menu']->name }}"
                                         class="w-12 h-12 rounded-lg object-cover"
                                         onerror="this.src='https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=60'">
                                    <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-yellow-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $item['quantity'] }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $item['menu']->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $item['menu']->formattedPrice }}</p>
                                </div>
                                <span class="text-sm font-bold text-gray-800 shrink-0">{{ 'Rp ' . number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-dashed border-gray-200 my-4"></div>

                    <div class="space-y-2.5 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-semibold text-gray-800">{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Ongkir</span>
                            <span id="delivery-fee-display" class="font-semibold text-gray-800">{{ old('delivery_method') === 'delivery' ? 'Rp 10.000' : 'Rp 0' }}</span>
                        </div>
                    </div>

                    <div class="bg-yellow-50 rounded-xl p-3.5 mb-5">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-900">Total</span>
                            <span id="total-display" class="font-bold text-2xl text-yellow-700">{{ 'Rp ' . number_format(old('delivery_method') === 'delivery' ? $subtotal + 10000 : $subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn"
                            class="w-full bg-yellow-600 hover:bg-yellow-700 active:scale-[0.98] text-white font-bold py-4 rounded-xl transition-all shadow-sm shadow-yellow-600/30 text-base">
                        Buat Pesanan →
                    </button>

                    <p class="text-xs text-gray-400 text-center mt-3">
                        Dengan memesan, Anda menyetujui syarat & ketentuan Padang Rice
                    </p>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const SUBTOTAL = {{ $subtotal }};
const DELIVERY_FEE = 10000;
let map, marker, searchTimeout;
let currentDelivery = '{{ old('delivery_method', 'pickup') }}';

function setDelivery(method) {
    currentDelivery = method;

    // Update radio inputs
    document.querySelectorAll('input[name="delivery_method"]').forEach(r => {
        r.checked = r.value === method;
    });

    // Update card styles
    ['pickup', 'delivery'].forEach(m => {
        const label = document.getElementById('label-' + m);
        const check = document.getElementById('check-' + m);
        if (m === method) {
            label.classList.add('border-yellow-500', 'bg-yellow-50');
            label.classList.remove('border-gray-200');
            check.classList.remove('hidden');
        } else {
            label.classList.remove('border-yellow-500', 'bg-yellow-50');
            label.classList.add('border-gray-200');
            check.classList.add('hidden');
        }
    });

    // Toggle delivery address fields
    const deliveryFields = document.getElementById('delivery-fields');
    const isDelivery = method === 'delivery';
    deliveryFields.classList.toggle('hidden', !isDelivery);

    // Update pricing
    const fee = isDelivery ? DELIVERY_FEE : 0;
    document.getElementById('delivery-fee-display').textContent = isDelivery ? 'Rp 10.000' : 'Rp 0';
    document.getElementById('total-display').textContent = 'Rp ' + (SUBTOTAL + fee).toLocaleString('id-ID');

    if (isDelivery) {
        if (!map) {
            setTimeout(initMap, 100);
        } else {
            setTimeout(() => map.invalidateSize(), 100);
        }
    }
}

function initMap() {
    const defaultLat = -6.9175, defaultLng = 107.6191;
    map = L.map('map').setView([defaultLat, defaultLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const oldLat = document.getElementById('latitude').value;
    const oldLng = document.getElementById('longitude').value;
    if (oldLat && oldLng) {
        marker = L.marker([parseFloat(oldLat), parseFloat(oldLng)], { draggable: true }).addTo(map);
        map.setView([parseFloat(oldLat), parseFloat(oldLng)], 15);
        marker.on('dragend', onMarkerDrag);
    }

    map.on('click', e => {
        setMarker(e.latlng.lat, e.latlng.lng);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });
}

function setMarker(lat, lng) {
    if (marker) map.removeLayer(marker);
    marker = L.marker([lat, lng], { draggable: true }).addTo(map);
    marker.on('dragend', onMarkerDrag);
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
}

function onMarkerDrag(e) {
    const pos = e.target.getLatLng();
    document.getElementById('latitude').value = pos.lat;
    document.getElementById('longitude').value = pos.lng;
    reverseGeocode(pos.lat, pos.lng);
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
    btn.innerHTML = `<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Mencari...`;
    btn.disabled = true;

    navigator.geolocation.getCurrentPosition(
        pos => {
            setMarker(pos.coords.latitude, pos.coords.longitude);
            map.setView([pos.coords.latitude, pos.coords.longitude], 15);
            reverseGeocode(pos.coords.latitude, pos.coords.longitude);
            btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Lokasi Saya`;
            btn.disabled = false;
        },
        () => {
            alert('Tidak dapat mengakses lokasi');
            btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Lokasi Saya`;
            btn.disabled = false;
        }
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
                    `<div class="px-4 py-3 hover:bg-yellow-50 cursor-pointer border-b last:border-0 text-sm text-gray-700 hover:text-yellow-700 transition"
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

// Submit button loading state
document.getElementById('checkoutForm').addEventListener('submit', () => {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = `<svg class="w-5 h-5 animate-spin inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Memproses...`;
    btn.disabled = true;
});

@if(old('delivery_method') === 'delivery')
    document.addEventListener('DOMContentLoaded', initMap);
@endif
</script>
@endsection
