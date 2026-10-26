@extends('layouts.app')

@section('title', 'Menu - Padang Rice')

@section('content')
<x-hero title="MENU KAMI" />

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form method="GET" class="mb-8">
            <div class="flex flex-col md:flex-row gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari menu..." class="flex-1 px-4 py-2 border rounded-lg">
                <select name="category" class="px-4 py-2 border rounded-lg">
                    <option value="semua" {{ request('category') === 'semua' ? 'selected' : '' }}>Semua Kategori</option>
                    <option value="makanan" {{ request('category') === 'makanan' ? 'selected' : '' }}>Makanan</option>
                    <option value="minuman" {{ request('category') === 'minuman' ? 'selected' : '' }}>Minuman</option>
                    <option value="snack" {{ request('category') === 'snack' ? 'selected' : '' }}>Snack</option>
                    <option value="paket" {{ request('category') === 'paket' ? 'selected' : '' }}>Paket</option>
                </select>
                <select name="sort" class="px-4 py-2 border rounded-lg">
                    <option value="">Urutkan</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Termurah</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Termahal</option>
                </select>
                <button type="submit" class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800">Filter</button>
            </div>
        </form>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-6">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($menus as $menu)
            <div class="bg-white border rounded-lg overflow-hidden shadow hover:shadow-lg transition {{ !$menu->is_available ? 'opacity-60' : '' }}">
                <a href="{{ route('menu.show', $menu->id) }}">
                    <img src="{{ $menu->image ? asset('storage/' . $menu->image) : asset('assets/ASET/nasipadang-1.jpg') }}" alt="{{ $menu->name }}" class="w-full h-48 object-cover">
                </a>
                <div class="p-4">
                    <span class="text-xs text-gray-500 uppercase">{{ $menu->category_label }}</span>
                    <h3 class="font-bold text-sm mt-1 mb-2">{{ $menu->name }}</h3>
                    <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $menu->description }}</p>
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-lg">{{ $menu->formatted_price }}</span>
                        @if($menu->is_available)
                        <a href="{{ route('menu.show', $menu->id) }}" class="px-4 py-2 bg-black text-white text-xs rounded hover:bg-gray-800">Pesan</a>
                        @else
                        <span class="text-xs text-red-600 font-semibold">Habis</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <p class="col-span-full text-center text-gray-500 py-8">Tidak ada menu.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $menus->links() }}</div>
    </div>
</section>
@endsection
