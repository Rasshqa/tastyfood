@extends('layouts.app')

@section('title', 'Checkout - Padang Rice')

@section('content')
<x-hero title="CHECKOUT" />

<section class="py-16 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-6">{{ session('error') }}</div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('menu.checkout') }}" method="POST" id="checkout-form">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Left Column - Customer Info --}}
                <div>
                    <h2 class="text-xl font-bold mb-4">Informasi Pemesan</h2>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Nama</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" required class="w-full px-4 py-2 border rounded @error('customer_name') border-red-500 @enderror">
                        @error('customer_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" required class="w-full px-4 py-2 border rounded @error('customer_email') border-red-500 @enderror">
                        @error('customer_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">No. Telepon</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" required class="w-full px-4 py-2 border rounded @error('customer_phone') border-red-500 @enderror">
                        @error('customer_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Tipe Pesanan</label>
                        <select name="delivery_method" id="delivery_method" required class="w-full px-4 py-2 border rounded">
                            <option value="pickup" {{ old('delivery_method', 'pickup') === 'pickup' ? 'selected' : '' }}>Ambil Sendiri</option>
                            <option value="delivery" {{ old('delivery_method') === 'delivery' ? 'selected' : '' }}>Delivery</option>
                        </select>
                    </div>

                    {{-- Delivery Address Section --}}
                    <div id="delivery_section" style="display:none;">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Cari Alamat</label>
                            <div class="flex gap-2">
                                <input type="text" id="address_search" placeholder="Ketik nama jalan, gedung, atau tempat..." class="flex-1 px-4 py-2 border rounded">
                                <button type="button" id="search_btn" class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-lg text-sm font-semibold whitespace-nowrap">
                                    Cari
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Contoh: "Jl. Asia Afrika No.1 Bandung" atau "Trans Studio Mall"</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Pilih Lokasi di Peta</label>
                            <div id="delivery_map" class="w-full h-64 rounded-lg border border-gray-300" style="z-index: 1;"></div>
                            <p class="text-xs text-gray-500 mt-1">Klik pada peta atau geser marker untuk menentukan lokasi presisi</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Detail Alamat (Jalan, No. Rumah, RT/RW)</label>
                            <textarea name="delivery_address" id="delivery_address" rows="2" class="w-full px-4 py-2 border rounded" placeholder="Contoh: Jl. Cempaka No. 25, RT 03/RW 02">{{ old('delivery_address') }}</textarea>
                        </div>

                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                        <div id="delivery_info" class="mb-4 p-3 bg-gray-50 rounded text-xs text-gray-600" style="display:none;">
                            <strong>Alamat Terdeteksi:</strong> <span id="detected_address">-</span><br>
                            <strong>Koordinat:</strong> <span id="detected_coords">-</span><br>
                            <strong>Jarak:</strong> <span id="detected_distance">-</span> |
                            <strong>Ongkir:</strong> <span id="detected_fee">-</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Catatan (opsional)</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2 border rounded" placeholder="Contoh: Tidak pakai sambal, tingkat kepedasan">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Right Column - Order Summary --}}
                <div>
                    <h2 class="text-xl font-bold mb-4">Ringkasan Pesanan</h2>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 space-y-2">
                        @forelse($cartItems as $item)
                        <div class="flex justify-between text-sm">
                            <span>{{ $item['menu']->name }} x{{ $item['quantity'] }}</span>
                            <span class="font-medium">{{ 'Rp ' . number_format($item['subtotal'], 0, ',', '.') }}</span>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">Keranjang kosong</p>
                        @endforelse
                    </div>

                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between"><span>Subtotal</span><span>{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between" id="delivery_fee_row" style="display:none;"><span>Biaya Delivery</span><span id="delivery_fee_text">Rp 0</span></div>
                        <div class="flex justify-between font-bold text-lg border-t pt-2"><span>Total</span><span id="total_price">{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</span></div>
                    </div>

                    <button type="submit" class="w-full mt-6 px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800 font-semibold">
                        Pesan Sekarang
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-container { font-family: inherit; }
    .search-result { padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; }
    .search-result:hover { background-color: #fef3c7; }
    .search-result:last-child { border-bottom: none; }
    #search_results { max-height: 200px; overflow-y: auto; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const deliveryMethodSelect = document.getElementById('delivery_method');
    const deliverySection = document.getElementById('delivery_section');
    const deliveryFeeRow = document.getElementById('delivery_fee_row');
    const subtotal = {{ $subtotal }};
    const restaurantLat = {{ (float) config('restaurant.latitude') }};
    const restaurantLng = {{ (float) config('restaurant.longitude') }};

    let deliveryMap = null;
    let deliveryMarker = null;
    let currentDeliveryFee = 0;

    function haversineDistance(lat1, lng1, lat2, lng2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function calculateFee(distance) {
        const feePerKm = {{ (float) config('restaurant.delivery_fee_per_km') }};
        const minFee = {{ (float) config('restaurant.min_delivery_fee') }};
        const freeMin = {{ (float) config('restaurant.free_delivery_min_order') }};
        if (freeMin > 0 && subtotal >= freeMin) return 0;
        return Math.max(minFee, Math.ceil(distance) * feePerKm);
    }

    function updateTotal() {
        const isDelivery = deliveryMethodSelect.value === 'delivery';
        const total = isDelivery ? subtotal + currentDeliveryFee : subtotal;
        document.getElementById('total_price').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    function initDeliveryMap() {
        if (deliveryMap) return;
        deliveryMap = L.map('delivery_map').setView([restaurantLat, restaurantLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 19,
        }).addTo(deliveryMap);

        const restaurantIcon = L.icon({
            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41],
        });
        L.marker([restaurantLat, restaurantLng], { icon: restaurantIcon })
            .addTo(deliveryMap)
            .bindPopup('<strong>{{ config("restaurant.name") }}</strong><br>{{ config("restaurant.address") }}');

        const deliveryIcon = L.icon({
            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], className: 'delivery-marker'
        });

        const initialLat = parseFloat(document.getElementById('latitude').value) || restaurantLat;
        const initialLng = parseFloat(document.getElementById('longitude').value) || restaurantLng;
        deliveryMarker = L.marker([initialLat, initialLng], { icon: deliveryIcon, draggable: true }).addTo(deliveryMap);

        deliveryMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updateLocation(pos.lat, pos.lng);
        });

        deliveryMap.on('click', function(e) {
            deliveryMarker.setLatLng(e.latlng);
            updateLocation(e.latlng.lat, e.latlng.lng);
        });

        // Auto-set initial location if not already set
        if (!document.getElementById('latitude').value) {
            updateLocation(initialLat, initialLng);
        }

        // Trigger resize after showing
        setTimeout(() => { deliveryMap.invalidateSize(); }, 200);
    }

    function updateLocation(lat, lng) {
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        const distance = haversineDistance(restaurantLat, restaurantLng, lat, lng);
        currentDeliveryFee = calculateFee(distance);

        document.getElementById('detected_coords').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
        document.getElementById('detected_distance').textContent = distance.toFixed(2) + ' km';
        document.getElementById('delivery_fee_text').textContent = 'Rp ' + currentDeliveryFee.toLocaleString('id-ID');
        document.getElementById('detected_fee').textContent = currentDeliveryFee === 0 ? 'GRATIS' : 'Rp ' + currentDeliveryFee.toLocaleString('id-ID');

        // Reverse geocode
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
            .then(r => r.json())
            .then(data => {
                const addr = data.display_name || '-';
                document.getElementById('detected_address').textContent = addr;
                if (!document.getElementById('delivery_address').value) {
                    document.getElementById('delivery_address').value = addr;
                }
                document.getElementById('delivery_info').style.display = 'block';
            })
            .catch(() => {
                document.getElementById('delivery_info').style.display = 'block';
            });

        updateTotal();
    }

    // Search address using Nominatim
    async function searchAddress(query) {
        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=id&limit=5&addressdetails=1`;
        const response = await fetch(url);
        return await response.json();
    }

    document.getElementById('search_btn').addEventListener('click', async function() {
        const query = document.getElementById('address_search').value.trim();
        if (!query) return;

        const btn = this;
        btn.textContent = 'Mencari...';
        btn.disabled = true;

        try {
            const results = await searchAddress(query);
            if (results.length === 0) {
                alert('Alamat tidak ditemukan. Coba kata kunci lain.');
                return;
            }
            if (results.length === 1) {
                selectSearchResult(results[0]);
            } else {
                showSearchResults(results);
            }
        } catch (err) {
            alert('Gagal mencari alamat. Coba lagi.');
        } finally {
            btn.textContent = 'Cari';
            btn.disabled = false;
        }
    });

    document.getElementById('address_search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('search_btn').click();
        }
    });

    function showSearchResults(results) {
        let existing = document.getElementById('search_results');
        if (existing) existing.remove();

        const container = document.createElement('div');
        container.id = 'search_results';
        container.className = 'mt-2 bg-white border border-gray-300 rounded-lg shadow-lg';

        results.forEach(r => {
            const div = document.createElement('div');
            div.className = 'search-result';
            div.innerHTML = `<div class="text-sm font-medium text-gray-900">${r.display_name.split(',')[0]}</div><div class="text-xs text-gray-500">${r.display_name}</div>`;
            div.onclick = () => { selectSearchResult(r); container.remove(); };
            container.appendChild(div);
        });

        document.getElementById('address_search').parentElement.parentElement.appendChild(container);
    }

    function selectSearchResult(result) {
        const lat = parseFloat(result.lat);
        const lng = parseFloat(result.lon);
        deliveryMarker.setLatLng([lat, lng]);
        deliveryMap.setView([lat, lng], 17);
        updateLocation(lat, lng);
        document.getElementById('address_search').value = result.display_name;
    }

    deliveryMethodSelect.addEventListener('change', function() {
        const isDelivery = this.value === 'delivery';
        deliverySection.style.display = isDelivery ? 'block' : 'none';
        deliveryFeeRow.style.display = isDelivery ? 'flex' : 'none';
        if (isDelivery) {
            setTimeout(initDeliveryMap, 50);
        }
        updateTotal();
    });

    // Initialize if delivery already selected
    if (deliveryMethodSelect.value === 'delivery') {
        deliverySection.style.display = 'block';
        deliveryFeeRow.style.display = 'flex';
        setTimeout(initDeliveryMap, 50);
    }

    // Form validation before submit
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        const isDelivery = deliveryMethodSelect.value === 'delivery';
        if (isDelivery) {
            const lat = document.getElementById('latitude').value;
            const lng = document.getElementById('longitude').value;
            if (!lat || !lng) {
                e.preventDefault();
                alert('Pilih lokasi pengiriman di peta terlebih dahulu');
                return false;
            }
        }
    });
</script>
@endpush
