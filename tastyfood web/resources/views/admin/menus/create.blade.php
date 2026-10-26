@extends('layouts.admin')

@section('title', 'Tambah Menu')

@section('content')
<div class="min-h-screen bg-gray-100 py-8 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.menus.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Menu</h1>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Menu</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                    <select name="category" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" {{ old('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900 focus:border-transparent">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="0" step="500"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG. Maks 2MB.</p>
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="available" value="1" {{ old('available', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                        <span class="text-sm font-semibold text-gray-700">Tersedia</span>
                    </label>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                        Simpan
                    </button>
                    <a href="{{ route('admin.menus.index') }}" class="border border-gray-300 hover:border-gray-400 text-gray-700 font-semibold px-6 py-2 rounded-lg transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
