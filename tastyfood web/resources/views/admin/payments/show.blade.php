@extends('layouts.admin')

@section('header', 'Detail Pembayaran')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.payments.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali
    </a>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-6">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6">
        {{ session('error') }}
    </div>
@endif

<div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Informasi Pembayaran</h2>
        
        <div class="space-y-3">
            <div>
                <p class="text-xs font-semibold text-gray-500">Status</p>
                <span class="inline-block mt-1 px-3 py-1 text-sm font-medium rounded-full {{ $payment->statusColor }}">
                    {{ $payment->statusLabel }}
                </span>
            </div>
            
            <div>
                <p class="text-xs font-semibold text-gray-500">Nomor Pesanan</p>
                <p class="text-gray-900 font-medium">{{ $payment->order->order_number }}</p>
            </div>
            
            <div>
                <p class="text-xs font-semibold text-gray-500">Metode Pembayaran</p>
                <p class="text-gray-900">{{ $payment->paymentMethod->name }}</p>
            </div>
            
            <div>
                <p class="text-xs font-semibold text-gray-500">Jumlah</p>
                <p class="text-xl font-bold text-gray-900">{{ $payment->formattedAmount }}</p>
            </div>
            
            <div>
                <p class="text-xs font-semibold text-gray-500">Pelanggan</p>
                <p class="text-gray-900">{{ $payment->order->customer_name }}</p>
                <p class="text-sm text-gray-600">{{ $payment->order->customer_phone }}</p>
            </div>
            
            @if($payment->user_notes)
                <div>
                    <p class="text-xs font-semibold text-gray-500">Catatan Pelanggan</p>
                    <p class="text-gray-900">{{ $payment->user_notes }}</p>
                </div>
            @endif
            
            @if($payment->rejection_reason)
                <div class="bg-red-50 rounded-lg p-3">
                    <p class="text-xs font-semibold text-red-700 mb-1">Alasan Penolakan</p>
                    <p class="text-sm text-red-800">{{ $payment->rejection_reason }}</p>
                </div>
            @endif
            
            @if($payment->verified_at)
                <div>
                    <p class="text-xs font-semibold text-gray-500">Diverifikasi</p>
                    <p class="text-sm text-gray-900">{{ $payment->verified_at->format('d/m/Y H:i') }}</p>
                    @if($payment->verifier)
                        <p class="text-xs text-gray-600">oleh {{ $payment->verifier->name }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Bukti Pembayaran</h2>
        
        @if($payment->proof_image)
            <div class="bg-gray-50 rounded-lg p-2 mb-2">
                <img src="{{ asset('storage/' . $payment->proof_image) }}" 
                     alt="Bukti Pembayaran" 
                     class="w-full h-auto rounded-lg border border-gray-300 shadow-sm">
            </div>
            <p class="text-xs text-gray-500 mb-4">
                <span class="font-semibold">File:</span> {{ basename($payment->proof_image) }}
            </p>
            <a href="{{ asset('storage/' . $payment->proof_image) }}" 
               target="_blank"
               class="inline-block text-sm text-blue-600 hover:text-blue-800 underline mb-4">
                Buka gambar di tab baru
            </a>
            
            @if($payment->status === 'waiting_verification')
                <div class="mt-6 space-y-3">
                    <form action="{{ route('admin.payments.approve', $payment) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg">
                            Setujui Pembayaran
                        </button>
                    </form>
                    
                    <button type="button" 
                            onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                            class="w-full border-2 border-red-600 text-red-600 hover:bg-red-50 font-semibold py-3 rounded-lg">
                        Tolak Pembayaran
                    </button>
                </div>
            @endif
        @else
            <div class="bg-gray-50 rounded-lg p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-600">Belum ada bukti pembayaran</p>
            </div>
        @endif
    </div>
</div>

<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Tolak Pembayaran</h3>
        
        <form action="{{ route('admin.payments.reject', $payment) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Penolakan</label>
                <textarea name="rejection_reason" rows="3" required
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500"
                          placeholder="Contoh: Bukti transfer tidak jelas / Nama pengirim tidak sesuai"></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" 
                        onclick="document.getElementById('rejectModal').classList.add('hidden')"
                        class="flex-1 border border-gray-300 text-gray-700 font-semibold py-2 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg">
                    Tolak
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
