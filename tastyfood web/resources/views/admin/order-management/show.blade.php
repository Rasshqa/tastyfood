@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.order-management.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mb-2 inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Daftar Pesanan
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Pesanan #{{ $order->order_number }}</h1>
            <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Order Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Customer Info --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold mb-4">Informasi Pelanggan</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama</span>
                        <span class="font-semibold">{{ $order->customer_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email</span>
                        <span class="font-semibold">{{ $order->customer_email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Telepon</span>
                        <span class="font-semibold">{{ $order->customer_phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Metode</span>
                        <span class="font-semibold">
                            @if($order->delivery_method === 'pickup')
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded">Ambil Sendiri</span>
                            @else
                                <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded">Delivery</span>
                            @endif
                        </span>
                    </div>
                    @if($order->delivery_method === 'delivery' && $order->delivery_address)
                        <div class="pt-2 border-t">
                            <span class="text-gray-600 block mb-1">Alamat Pengiriman</span>
                            <span class="font-semibold">{{ $order->delivery_address }}</span>
                        </div>
                    @endif
                    @if($order->notes)
                        <div class="pt-2 border-t">
                            <span class="text-gray-600 block mb-1">Catatan</span>
                            <span class="italic">{{ $order->notes }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Order Items --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold mb-4">Daftar Menu</h2>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex justify-between items-start border-b pb-3 last:border-0">
                            <div class="flex-1">
                                <p class="font-semibold">{{ $item->menu_name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->menu_price, 0, ',', '.') }}</p>
                            </div>
                            <p class="font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ongkir</span>
                        <span class="font-semibold">Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t">
                        <span>Total</span>
                        <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Map (if delivery) --}}
            @if($order->delivery_method === 'delivery' && $order->latitude && $order->longitude)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold mb-4">Lokasi Pengiriman</h2>
                    <div id="order-map" class="w-full h-80 rounded-lg border"></div>
                    <div class="mt-3 text-sm text-gray-600">
                        <p><strong>Koordinat:</strong> {{ number_format($order->latitude, 6) }}, {{ number_format($order->longitude, 6) }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column: Status --}}
        <div class="space-y-6">
            {{-- Order Status --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold mb-4">Status Pesanan</h2>
                @if(in_array($order->status, ['delivered', 'completed', 'cancelled']))
                    <div class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-700 font-semibold mb-3">
                        {{ $order->statusLabel }}
                    </div>
                @else
                    <form action="{{ route('admin.order-management.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm mb-3" onchange="this.form.submit()">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                            <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Diproses</option>
                            <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Siap</option>
                            <option value="in_transit" {{ $order->status === 'in_transit' ? 'selected' : '' }}>Dalam Perjalanan</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </form>
                @endif
            </div>

            {{-- Payment Status --}}
            @if($order->payment)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold mb-4">Status Pembayaran</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Metode</span>
                            <span class="font-semibold">{{ $order->payment->paymentMethod->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status</span>
                            <span class="font-semibold">
                                @if($order->payment->status === 'paid')
                                    <span class="text-green-600">Lunas</span>
                                @elseif($order->payment->status === 'waiting_verification')
                                    <span class="text-gray-900">Menunggu Verifikasi</span>
                                @elseif($order->payment->status === 'rejected')
                                    <span class="text-red-600">Ditolak</span>
                                @else
                                    <span class="text-gray-600">Belum Bayar</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($order->delivery_method === 'delivery' && $order->latitude && $order->longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const RESTAURANT_LAT = {{ (float) config('restaurant.latitude') }};
    const RESTAURANT_LNG = {{ (float) config('restaurant.longitude') }};
    const DELIVERY_LAT = {{ $order->latitude }};
    const DELIVERY_LNG = {{ $order->longitude }};

    const map = L.map('order-map').setView([RESTAURANT_LAT, RESTAURANT_LNG], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);

    // Restaurant marker
    const restaurantIcon = L.divIcon({
        html: `<div style="background:#dc2626;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid white;box-shadow:0 4px 6px rgba(0,0,0,0.3);">
                  <svg style="width:22px;height:22px;color:white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
               </div>`,
        iconSize: [40, 40],
        iconAnchor: [20, 20],
    });
    
    L.marker([RESTAURANT_LAT, RESTAURANT_LNG], { icon: restaurantIcon })
        .addTo(map)
        .bindPopup('<div style="text-align:center;"><strong>{{ config('restaurant.name') }}</strong><br><small>Restoran</small></div>');

    // Delivery marker
    const deliveryIcon = L.divIcon({
        html: `<div style="background:#eab308;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid white;box-shadow:0 4px 6px rgba(0,0,0,0.3);">
                  <svg style="width:22px;height:22px;color:white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
               </div>`,
        iconSize: [40, 40],
        iconAnchor: [20, 20],
    });
    
    L.marker([DELIVERY_LAT, DELIVERY_LNG], { icon: deliveryIcon })
        .addTo(map)
        .bindPopup('<div style="text-align:center;"><strong>Lokasi Pengiriman</strong><br><small>{{ $order->customer_name }}</small></div>');

    // Draw line
    L.polyline([
        [RESTAURANT_LAT, RESTAURANT_LNG],
        [DELIVERY_LAT, DELIVERY_LNG]
    ], {
        color: '#eab308',
        weight: 3,
        opacity: 0.7,
        dashArray: '10, 10'
    }).addTo(map);

    // Fit bounds to show both markers
    map.fitBounds([
        [RESTAURANT_LAT, RESTAURANT_LNG],
        [DELIVERY_LAT, DELIVERY_LNG]
    ], { padding: [50, 50] });
});
</script>
@endif
@endsection
