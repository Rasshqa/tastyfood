@extends('layouts.admin')

@section('title', 'Tambah Metode Pembayaran')
@section('header', 'Tambah Metode Pembayaran')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.payment-methods.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Metode Pembayaran</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Pembayaran</label>
                <select name="type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900">
                    <option value="bank_transfer" {{ old('type') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="qr_payment" {{ old('type') === 'qr_payment' ? 'selected' : '' }}>QR Payment</option>
                    <option value="e_wallet" {{ old('type') === 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                    <option value="cash" {{ old('type') === 'cash' ? 'selected' : '' }}>Cash</option>
                </select>
                @error('type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pemilik / Merchant</label>
                <input type="text" name="account_name" value="{{ old('account_name') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900">
                @error('account_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Rekening / Nomor Pembayaran</label>
                <input type="text" name="account_number" value="{{ old('account_number') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900">
                @error('account_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">QR Code (opsional)</label>
                <input type="file" name="qr_code" accept="image/jpeg,image/jpg,image/png,image/webp"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG, WEBP. Max: 2MB</p>
                @error('qr_code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Instruksi Pembayaran (opsional)</label>
                <textarea name="instructions" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900">{{ old('instructions') }}</textarea>
                @error('instructions') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-6 py-2 rounded-lg">
                    Simpan
                </button>
                <a href="{{ route('admin.payment-methods.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
