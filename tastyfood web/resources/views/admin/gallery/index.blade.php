@extends('layouts.admin')

@section('header', 'Kelola Galeri')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.galeri.create') }}" class="inline-flex items-center px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Foto
    </a>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-6">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse($galleries as $gallery)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <img src="{{ asset($gallery->image) }}" alt="{{ $gallery->title }}" class="w-full aspect-square object-cover">
        <div class="p-4">
            <h3 class="font-medium text-sm mb-2">{{ $gallery->title }}</h3>
            <div class="flex gap-2">
                <a href="{{ route('admin.galeri.edit', $gallery) }}" class="text-xs text-blue-600 hover:text-blue-800">Edit</a>
                <form action="{{ route('admin.galeri.destroy', $gallery) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-600 hover:text-red-800">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <p class="col-span-full text-center text-gray-500 py-8">Tidak ada foto.</p>
    @endforelse
</div>

<div class="mt-6">
    {{ $galleries->links() }}
</div>
@endsection
