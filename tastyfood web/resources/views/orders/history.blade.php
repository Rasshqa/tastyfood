@extends('layouts.app')

@section('title', 'Riwayat Pesanan')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Riwayat Pesanan</h1>

        @if($orders->isEmpty())
            <div class="bg-white rounded-xl shadow p-12 text-center">
                <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 mb-4">Belum ada pesanan</p>
                <a href="{{ route('menu.index') }}" class="inline-block bg-gray-900 hover:bg-gray-800 text-white font-semibold px-6 py-2 rounded-lg transition">
                    Mulai Pesan
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white rounded-xl shadow hover:shadow-md transition p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="font-bold text-gray-800">{{ $order->order_number }}</p>
                                <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $order->statusColor }}">
                                {{ $order->statusLabel }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-4">
                            @foreach($order->items->take(3) as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ $item->quantity }}x {{ $item->menu_name }}</span>
                                    <span class="text-gray-800">{{ $item->formattedSubtotal }}</span>
                                </div>
                            @endforeach
                            @if($order->items->count() > 3)
                                <p class="text-sm text-gray-500">+{{ $order->items->count() - 3 }} menu lainnya</p>
                            @endif
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t">
                            <div>
                                <p class="text-sm text-gray-600">Total</p>
                                <p class="font-bold text-gray-800">{{ $order->formattedTotal }}</p>
                            </div>
                            <a href="{{ route('orders.show', $order) }}" class="bg-gray-900 hover:bg-gray-800 text-white font-semibold px-6 py-2 rounded-lg transition">
                                Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
