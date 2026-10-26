@extends('layouts.admin')

@section('header', 'Kelola Berita')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <a href="{{ route('admin.berita.create') }}" class="inline-flex items-center px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Berita
    </a>

    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berita..." class="px-4 py-2 border rounded-lg">
        <select name="status" class="px-4 py-2 border rounded-lg">
            <option value="">Semua Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Filter</button>
    </form>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-6">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gambar</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($news as $item)
            <tr>
                <td class="px-6 py-4">
                    <img src="{{ asset($item->image ?? 'assets/ASET/nasipadang-hero.jpg') }}" 
                         alt="{{ $item->title }}" 
                         class="w-16 h-16 object-cover rounded">
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($item->title, 50) }}</div>
                    <div class="text-xs text-gray-500">{{ $item->author }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    {{ $item->created_at->format('d M Y') }}
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded {{ $item->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('admin.berita.edit', $item) }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                    <form action="{{ route('admin.berita.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $news->links() }}
</div>
@endsection
