@extends('layouts.admin')

@section('header', 'Tambah Foto')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Gambar</label>
                <input type="file" name="image" accept="image/*" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded-lg">{{ old('description') }}</textarea>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border rounded-lg">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800">Simpan</button>
                <a href="{{ route('admin.galeri.index') }}" class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
