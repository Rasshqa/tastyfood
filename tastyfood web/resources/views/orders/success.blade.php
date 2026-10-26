@extends('layouts.app')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="min-h-screen bg-green-50 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Pesanan Berhasil!</h1>
            <p class="text-gray-600 mb-8">Terima kasih telah memesan di Nasi Padang Kita</p>
            
            <div class="bg-gray-50 rounded-xl p-6 mb-8">
                <p class="text-sm text-gray-600 mb-2">Nomor Pesanan</p>
                <p class="text-2xl font-bold text-gray-800">{{ $order->order_number }}</p>
            </div>

            <div class="space-y-4 text-left mb-8">
                <div class="flex justify-between">
                    <span class="text-gray-600">Status</span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $order->statusColor }}">
                        {{ $order->statusLabel }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Pembayaran</span>
                    <span class="font-bold text-gray-800">{{ $order->formattedTotal }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Metode Pengiriman</span>
                    <span class="text-gray-800">{{ $order->delivery_method === 'delivery' ? 'Diantar' : 'Ambil Sendiri' }}</span>
                </div>
            </div>

            <div class="space-y-3">
                <a href="{{ route('payments.show', $order) }}" 
                   class="block w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 rounded-xl transition">
                    Pilih Metode Pembayaran
                </a>
                <a href="{{ route('menu.index') }}" 
                   class="block w-full border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-semibold py-3 rounded-xl transition">
                    Pesan Lagi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
