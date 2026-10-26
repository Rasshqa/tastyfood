@extends('layouts.admin')

@section('title', 'Detail Pesanan - ' . $order->order_number)

@section('content')
<div class="min-h-screen bg-gray-100 py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
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
                                @if($order->latitude && $order->longitude)
                                    <div class="mt-3">
                                        <div id="map" class="h-48 rounded-lg border border-gray-300"></div>
                                    </div>
                                @endif
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

                <div class="bg-gray-50 rounded-xl p-6 mb-8">
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
                    <div class="mb-8 p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm font-semibold text-gray-600 mb-1">Catatan:</p>
                        <p class="text-gray-800">{{ $order->notes }}</p>
                    </div>
                @endif

                @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                    <div class="border-t pt-6">
                        <h3 class="text-sm font-semibold text-gray-600 mb-4">UPDATE STATUS</h3>
                        <div class="flex flex-wrap gap-3">
                            @if($order->status === 'pending')
                                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                        Konfirmasi
                                    </button>
                                </form>
                                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                        Batalkan
                                    </button>
                                </form>
                            @elseif($order->status === 'confirmed')
                                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="preparing">
                                    <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded-lg transition">
                                        Proses
                                    </button>
                                </form>
                            @elseif($order->status === 'preparing')
                                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="ready">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                        Siap
                                    </button>
                                </form>
                            @elseif($order->status === 'ready')
                                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="delivered">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                        Selesai
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($order->latitude && $order->longitude)
@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('map').setView([{{ $order->latitude }}, {{ $order->longitude }}], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    L.marker([{{ $order->latitude }}, {{ $order->longitude }}]).addTo(map);
});
</script>
@endsection
@endif
@endsection
