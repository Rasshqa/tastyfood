@extends('layouts.app')

@section('title', 'Upload Bukti Pembayaran - Padang Rice')

@section('content')
<div class="min-h-screen bg-[#f5f4f1] pt-20 pb-16">
    <div class="max-w-2xl mx-auto px-4">
        <div class="py-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-display">Upload Bukti Pembayaran</h1>
            <p class="text-gray-600 mt-2">Order #{{ $order->order_number }}</p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-3 mb-6 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="font-bold text-gray-900 text-lg mb-4">Detail Pembayaran</h2>
            
            <div class="space-y-3 mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Metode</span>
                    <span class="font-semibold text-gray-900">{{ $order->payment->paymentMethod->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total</span>
                    <span class="font-bold text-xl text-gray-900">{{ $order->formattedTotal }}</span>
                </div>
            </div>

            @if($order->payment->paymentMethod->account_name || $order->payment->paymentMethod->account_number)
                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    @if($order->payment->paymentMethod->account_name)
                        <p class="text-sm text-gray-600">Nama Pemilik</p>
                        <p class="font-semibold text-gray-900 mb-2">{{ $order->payment->paymentMethod->account_name }}</p>
                    @endif
                    @if($order->payment->paymentMethod->account_number)
                        <p class="text-sm text-gray-600">Nomor Rekening</p>
                        <p class="font-bold text-lg text-gray-900">{{ $order->payment->paymentMethod->account_number }}</p>
                    @endif
                </div>
            @endif

            @if($order->payment->paymentMethod->qr_code)
                <div class="text-center mb-4">
                    <p class="text-sm text-gray-600 mb-2">Scan QR Code</p>
                    <img src="{{ asset('storage/' . $order->payment->paymentMethod->qr_code) }}" 
                         alt="QR Code" class="w-48 h-48 object-contain mx-auto border rounded-xl">
                </div>
            @endif

            @if($order->payment->paymentMethod->instructions)
                <div class="bg-blue-50 rounded-xl p-4">
                    <p class="text-sm font-semibold text-blue-900 mb-1">Instruksi</p>
                    <p class="text-sm text-blue-800">{{ $order->payment->paymentMethod->instructions }}</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 text-lg mb-4">Upload Bukti Transfer</h2>
            
            <form action="{{ route('payments.uploadProof', $order) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Bukti Pembayaran</label>
                    <input type="file" name="proof_image" accept="image/jpeg,image/jpg,image/png,image/webp" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gray-900">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG, WEBP. Max: 5MB</p>
                    @error('proof_image') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Catatan (Opsional)</label>
                    <textarea name="user_notes" rows="2" 
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gray-900"
                              placeholder="Contoh: Sudah transfer dari rekening BCA atas nama ...">{{ old('user_notes') }}</textarea>
                    @error('user_notes') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-4 rounded-xl transition">
                    Kirim Bukti Pembayaran
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
