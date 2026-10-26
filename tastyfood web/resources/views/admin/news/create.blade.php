@extends('layouts.admin')

@section('header', 'Tambah Berita')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full px-4 py-2 border rounded-lg @error('title') border-red-500 @enderror">
                @error('title')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Gambar</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full px-4 py-2 border rounded-lg @error('image') border-red-500 @enderror">
                @error('image')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Konten</label>
                <textarea name="content" rows="10" required
                          class="w-full px-4 py-2 border rounded-lg @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                @error('content')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Penulis</label>
                <input type="text" name="author" value="{{ old('author', 'Admin') }}" required
                       class="w-full px-4 py-2 border rounded-lg @error('author') border-red-500 @enderror">
                @error('author')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800">
                    Simpan
                </button>
                <a href="{{ route('admin.berita.index') }}" class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
