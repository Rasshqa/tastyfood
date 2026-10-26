@extends('layouts.app')

@section('title', 'Detail Pesanan - ' . $order->order_number)

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center">
            <a href="{{ route('orders.history') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Riwayat
            </a>
            @auth
            <button onclick="openChat({{ $order->id }})" class="ml-auto px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Chat dengan Admin
            </button>
            @endauth
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gray-900 p-6 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Nomor Pesanan</p>
                        <p class="text-2xl font-bold">{{ $order->order_number }}</p>
                    </div>
                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-white/20">
                        {{ $order->statusLabel }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">INFORMASI PEMESAN</h3>
                        <div class="space-y-2">
                            <p class="text-gray-800"><strong>{{ $order->customer_name }}</strong></p>
                            <p class="text-gray-600">{{ $order->customer_phone }}</p>
                            <p class="text-gray-600">{{ $order->customer_email }}</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">PENGIRIMAN</h3>
                        <div class="space-y-2">
                            <p class="text-gray-800 font-semibold">
                                {{ $order->delivery_method === 'delivery' ? 'Diantar' : 'Ambil Sendiri' }}
                            </p>
                            @if($order->delivery_method === 'delivery')
                                <p class="text-gray-600">{{ $order->delivery_address }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-sm font-semibold text-gray-600 mb-4">DETAIL PESANAN</h3>
                    <div class="border rounded-xl overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-4 py-3 text-sm font-semibold text-gray-600">Menu</th>
                                    <th class="text-center px-4 py-3 text-sm font-semibold text-gray-600">Qty</th>
                                    <th class="text-right px-4 py-3 text-sm font-semibold text-gray-600">Harga</th>
                                    <th class="text-right px-4 py-3 text-sm font-semibold text-gray-600">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-800">{{ $item->menu_name }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600">{{ $item->formattedPrice }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ $item->formattedSubtotal }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-6">
                    <div class="space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>{{ $order->formattedSubtotal }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Ongkir</span>
                            <span>{{ $order->formattedDeliveryFee }}</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold text-gray-800 pt-2 border-t">
                            <span>Total</span>
                            <span>{{ $order->formattedTotal }}</span>
                        </div>
                    </div>
                </div>

                @if($order->notes)
                    <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm font-semibold text-gray-600 mb-1">Catatan:</p>
                        <p class="text-gray-800">{{ $order->notes }}</p>
                    </div>
                @endif

                @if($order->delivery_method === 'delivery' && $order->latitude && $order->longitude)
                <div class="mt-8">
                    <h3 class="text-sm font-semibold text-gray-600 mb-4">LOKASI PENGIRIMAN</h3>
                    <div id="tracking-map" class="w-full h-72 rounded-xl border"></div>
                    <div class="mt-3 text-sm text-gray-600">
                        <p><strong>Alamat:</strong> {{ $order->delivery_address }}</p>
                    </div>
                </div>
                @endif

                @if($order->status === 'delivered')
                    <div class="mt-8 p-6 bg-orange-50 border border-orange-200 rounded-xl text-center">
                        <h3 class="font-bold text-orange-900 mb-2">Konfirmasi Pesanan Diterima</h3>
                        <p class="text-orange-800 text-sm mb-4">Pastikan pesanan Anda telah benar-benar sampai atau diterima dengan baik sebelum menekan tombol ini.</p>
                        <form action="{{ route('orders.complete', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin pesanan sudah diterima dengan baik?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-8 rounded-xl transition">
                                Pesanan Sudah Diterima
                            </button>
                        </form>
                    </div>
                @endif

                @if($order->payment)
                    <div class="mt-8 p-6 {{ $order->payment->status === 'paid' ? 'bg-green-50' : ($order->payment->status === 'rejected' ? 'bg-red-50' : 'bg-gray-50') }} rounded-xl">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-bold text-gray-900">Status Pembayaran</h3>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $order->payment->statusColor }}">
                                {{ $order->payment->statusLabel }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700 mb-3">Metode: {{ $order->payment->paymentMethod->name }}</p>
                        
                        @if($order->payment->status === 'unpaid')
                            <a href="{{ route('payments.upload', $order) }}" 
                               class="inline-block bg-gray-900 hover:bg-gray-800 text-white font-semibold px-6 py-2 rounded-lg transition">
                                Upload Bukti Pembayaran
                            </a>
                        @elseif($order->payment->status === 'waiting_verification')
                            <p class="text-sm text-gray-700">Bukti pembayaran sedang diverifikasi admin.</p>
                        @elseif($order->payment->status === 'rejected')
                            <p class="text-sm text-red-700 mb-3">{{ $order->payment->rejection_reason }}</p>
                            <a href="{{ route('payments.upload', $order) }}" 
                               class="inline-block bg-gray-900 hover:bg-gray-800 text-white font-semibold px-6 py-2 rounded-lg transition">
                                Upload Ulang Bukti
                            </a>
                        @elseif($order->payment->status === 'paid')
                            <p class="text-sm text-green-700">Pembayaran telah dikonfirmasi.</p>
                        @endif
                    </div>
                @else
                    <div class="mt-8 p-6 bg-blue-50 rounded-xl">
                        <h3 class="font-bold text-blue-900 mb-2">Belum Ada Pembayaran</h3>
                        <p class="text-blue-800 mb-4">Silakan pilih metode pembayaran untuk melanjutkan:</p>
                        <a href="{{ route('payments.show', $order) }}" 
                           class="inline-block bg-gray-900 hover:bg-gray-800 text-white font-semibold px-6 py-3 rounded-lg transition">
                            Pilih Metode Pembayaran
                        </a>
                    </div>
                @endif
            </div>
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

    const map = L.map('tracking-map').setView([RESTAURANT_LAT, RESTAURANT_LNG], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);

    // Restaurant marker
    const restaurantIcon = L.divIcon({
        html: `<div style="background:#dc2626;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);">
                  <svg style="width:20px;height:20px;color:white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
               </div>`,
        iconSize: [36, 36],
        iconAnchor: [18, 18],
    });
    
    L.marker([RESTAURANT_LAT, RESTAURANT_LNG], { icon: restaurantIcon })
        .addTo(map)
        .bindPopup('<div style="text-align:center;"><strong>{{ config('restaurant.name') }}</strong><br><small>Restoran</small></div>');

    // Delivery marker
    const deliveryIcon = L.divIcon({
        html: `<div style="background:#eab308;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);">
                  <svg style="width:20px;height:20px;color:white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
               </div>`,
        iconSize: [36, 36],
        iconAnchor: [18, 18],
    });
    
    L.marker([DELIVERY_LAT, DELIVERY_LNG], { icon: deliveryIcon })
        .addTo(map)
        .bindPopup('<div style="text-align:center;"><strong>Lokasi Anda</strong></div>');

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

    map.fitBounds([
        [RESTAURANT_LAT, RESTAURANT_LNG],
        [DELIVERY_LAT, DELIVERY_LNG]
    ], { padding: [50, 50] });
});
</script>
@endif
@endsection
