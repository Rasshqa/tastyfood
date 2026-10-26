@extends('layouts.app')

@section('title', $menu->name)

@section('content')
<div class="min-h-screen bg-gray-50 pt-20 pb-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('menu.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Menu
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="grid md:grid-cols-2 gap-0">
                <div class="aspect-square md:aspect-auto">
                    <img src="{{ $menu->image ?? 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=600' }}" 
                         alt="{{ $menu->name }}" 
                         class="w-full h-full object-cover">
                </div>
                <div class="p-8 flex flex-col justify-center">
                    <span class="inline-block w-fit px-3 py-1 bg-gray-100 text-gray-900 rounded-full text-sm font-semibold mb-4">{{ $menu->categoryLabel }}</span>
                    <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $menu->name }}</h1>
                    <p class="text-gray-600 mb-6 leading-relaxed">{{ $menu->description ?? 'Deskripsi belum tersedia.' }}</p>
                    <p class="text-3xl font-bold text-gray-900 mb-8">{{ $menu->formattedPrice }}</p>

                    @if($menu->available)
                        <form action="{{ route('cart.add') }}" method="POST" class="flex items-center gap-4">
                            @csrf
                            <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                            <div class="flex items-center border rounded-lg">
                                <button type="button" onclick="document.getElementById('qty').value = Math.max(1, parseInt(document.getElementById('qty').value) - 1)" class="px-4 py-2 text-gray-600 hover:bg-gray-100">-</button>
                                <input type="number" name="quantity" id="qty" value="1" min="1" class="w-16 text-center border-x py-2 focus:outline-none">
                                <button type="button" onclick="document.getElementById('qty').value = parseInt(document.getElementById('qty').value) + 1" class="px-4 py-2 text-gray-600 hover:bg-gray-100">+</button>
                            </div>
                            <button type="submit" class="flex-1 bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg transition">
                                Tambah ke Keranjang
                            </button>
                        </form>
                    @else
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                            <p class="text-red-700 font-semibold">Menu ini sedang tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
