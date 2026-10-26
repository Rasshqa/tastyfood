@extends('layouts.app')

@section('title', 'Status Pembayaran')

@section('content')
<div class="min-h-screen bg-[#f5f4f1] pt-20 pb-16">
    <div class="max-w-2xl mx-auto px-4">
        <div class="py-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-display">Status Pembayaran</h1>
            <p class="text-gray-600 mt-2">Order #{{ $order->order_number }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="text-center mb-6">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center
                    {{ $order->payment->status === 'paid' ? 'bg-green-100' : ($order->payment->status === 'rejected' ? 'bg-red-100' : 'bg-gray-100') }}">
                    @if($order->payment->status === 'paid')
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @elseif($order->payment->status === 'rejected')
                        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    @else
                        <svg class="w-10 h-10 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @endif
                </div>
                
                <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold {{ $order->payment->statusColor }}">
                    {{ $order->payment->statusLabel }}
                </span>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between border-b pb-3">
                    <span class="text-gray-600">Metode Pembayaran</span>
                    <span class="font-semibold text-gray-900">{{ $order->payment->paymentMethod->name }}</span>
                </div>
                <div class="flex justify-between border-b pb-3">
                    <span class="text-gray-600">Jumlah</span>
                    <span class="font-bold text-xl text-gray-900">{{ $order->payment->formattedAmount }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tanggal Upload</span>
                    <span class="text-gray-900">{{ $order->payment->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            @if($order->payment->rejection_reason)
                <div class="mt-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <p class="text-sm font-semibold text-red-700 mb-1">Alasan Penolakan:</p>
                    <p class="text-sm text-red-800">{{ $order->payment->rejection_reason }}</p>
                </div>
            @endif

            @if($order->payment->status === 'waiting_verification')
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <p class="text-sm text-blue-800">
                        Bukti pembayaran Anda sedang diverifikasi oleh admin. Mohon tunggu konfirmasi.
                    </p>
                </div>
            @endif

            @if($order->payment->status === 'paid')
                <div class="mt-6 bg-green-50 border border-green-200 rounded-xl p-4">
                    <p class="text-sm text-green-800">
                        Pembayaran Anda telah dikonfirmasi. Pesanan sedang diproses.
                    </p>
                </div>
            @endif
        </div>

        <div class="space-y-3">
            <a href="{{ route('orders.show', $order) }}" 
               class="block w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 rounded-xl text-center transition">
                Lihat Detail Pesanan
            </a>
            
            @if($order->payment->status === 'rejected')
                <a href="{{ route('payments.upload', $order) }}" 
                   class="block w-full border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-semibold py-3 rounded-xl text-center transition">
                    Upload Ulang Bukti
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
