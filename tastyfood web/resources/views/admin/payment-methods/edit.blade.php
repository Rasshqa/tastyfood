@extends('layouts.admin')

@section('title', 'Edit Metode Pembayaran')
@section('header', 'Edit Metode Pembayaran')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.payment-methods.update', $paymentMethod) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Metode Pembayaran</label>
                <input type="text" name="name" value="{{ old('name', $paymentMethod->name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Pembayaran</label>
                <select name="type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900">
                    <option value="bank_transfer" {{ old('type', $paymentMethod->type) === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="qr_payment" {{ old('type', $paymentMethod->type) === 'qr_payment' ? 'selected' : '' }}>QR Payment</option>
                    <option value="e_wallet" {{ old('type', $paymentMethod->type) === 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                    <option value="cash" {{ old('type', $paymentMethod->type) === 'cash' ? 'selected' : '' }}>Cash</option>
                </select>
                @error('type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pemilik / Merchant</label>
                <input type="text" name="account_name" value="{{ old('account_name', $paymentMethod->account_name) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900">
                @error('account_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Rekening / Nomor Pembayaran</label>
                <input type="text" name="account_number" value="{{ old('account_number', $paymentMethod->account_number) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900">
                @error('account_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">QR Code (opsional)</label>
                @if($paymentMethod->qr_code)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $paymentMethod->qr_code) }}" alt="QR Code" class="w-32 h-32 object-contain border rounded">
                        <p class="text-xs text-gray-500 mt-1">QR Code saat ini</p>
                    </div>
                @endif
                <input type="file" name="qr_code" accept="image/jpeg,image/jpg,image/png,image/webp"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <p class="text-xs text-gray-500 mt-1">Upload file baru untuk mengganti. Format: JPG, JPEG, PNG, WEBP. Max: 2MB</p>
                @error('qr_code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Instruksi Pembayaran (opsional)</label>
                <textarea name="instructions" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900">{{ old('instructions', $paymentMethod->instructions) }}</textarea>
                @error('instructions') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-6 py-2 rounded-lg">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.payment-methods.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
