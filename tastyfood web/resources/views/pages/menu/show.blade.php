@extends('layouts.app')

@section('title', $menu->name . ' - Padang Rice')

@section('content')
<x-hero :title="$menu->name" />

<section class="py-16 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-6">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <img src="{{ $menu->image ? asset('storage/' . $menu->image) : asset('assets/ASET/nasipadang-1.jpg') }}" alt="{{ $menu->name }}" class="w-full rounded-lg">
            </div>
            <div>
                <span class="text-sm text-gray-500 uppercase">{{ $menu->category_label }}</span>
                <h1 class="text-3xl font-bold mt-2 mb-4">{{ $menu->name }}</h1>
                <p class="text-gray-600 mb-6">{{ $menu->description }}</p>
                <div class="text-3xl font-bold mb-6">{{ $menu->formatted_price }}</div>
                
                @if($menu->is_available)
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Jumlah</label>
                        <input type="number" name="quantity" value="1" min="1" class="w-24 px-4 py-2 border rounded">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Catatan (opsional)</label>
                        <input type="text" name="note" placeholder="Contoh: Tidak pedas" class="w-full px-4 py-2 border rounded">
                    </div>
                    <button type="submit" class="w-full px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800">Tambah ke Keranjang</button>
                </form>
                @else
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">Menu tidak tersedia</div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
