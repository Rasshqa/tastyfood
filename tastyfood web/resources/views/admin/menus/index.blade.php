@extends('layouts.admin')

@section('title', 'Kelola Menu')

@section('content')
<div class="min-h-screen bg-gray-100 py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Kelola Menu</h1>
            <a href="{{ route('admin.menus.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Menu
            </a>
        </div>

        @if($menus->isEmpty())
            <div class="bg-white rounded-xl shadow p-12 text-center">
                <p class="text-gray-500 mb-4">Belum ada menu</p>
                <a href="{{ route('admin.menus.create') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                    Tambah Menu Pertama
                </a>
            </div>
        @else
            <div class="bg-white rounded-xl shadow overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Menu</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Kategori</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Harga</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Status</th>
                            <th class="text-right px-6 py-4 text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($menus as $menu)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $menu->image ? asset('storage/' . $menu->image) : 'https://placehold.co/100x100/e5e7eb/6b7280?text=' . urlencode($menu->name) }}" 
                                             alt="{{ $menu->name }}" 
                                             class="w-12 h-12 rounded-lg object-cover">
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $menu->name }}</p>
                                            <p class="text-sm text-gray-500 line-clamp-1">{{ $menu->description }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">
                                        {{ $menu->categoryLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-semibold">
                                    {{ $menu->formattedPrice }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $menu->available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $menu->available ? 'Tersedia' : 'Tidak Tersedia' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.menus.edit', $menu) }}" class="text-gray-900 hover:text-gray-900 mr-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700" onclick="return confirm('Hapus menu ini?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
