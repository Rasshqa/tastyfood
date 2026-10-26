@extends('layouts.admin')

@section('header', 'Edit Foto')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.galeri.update', $gallery) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                <input type="text" name="title" value="{{ old('title', $gallery->title) }}" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Gambar</label>
                <img src="{{ asset($gallery->image) }}" alt="Current" class="w-32 h-32 object-cover rounded mb-2">
                <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded-lg">{{ old('description', $gallery->description) }}</textarea>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border rounded-lg">
                    <option value="active" {{ $gallery->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $gallery->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800">Update</button>
                <a href="{{ route('admin.galeri.index') }}" class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
