@extends('layouts.app')

@section('title', 'Keranjang - Padang Rice')

@section('content')
<x-hero title="KERANJANG" />

<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
        @endif

        @if(count($cartItems) > 0)
        <div class="space-y-4 mb-8">
            @foreach($cartItems as $item)
            <div class="flex gap-4 bg-gray-50 p-4 rounded-lg">
                <img src="{{ $item['menu']->image ? asset('storage/' . $item['menu']->image) : asset('assets/ASET/nasipadang-1.jpg') }}" alt="{{ $item['menu']->name }}" class="w-24 h-24 object-cover rounded">
                <div class="flex-1">
                    <h3 class="font-bold">{{ $item['menu']->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $item['menu']->formatted_price }}</p>
                    @if($item['note'])
                    <p class="text-xs text-gray-500 italic">Catatan: {{ $item['note'] }}</p>
                    @endif
                    <div class="flex items-center gap-2 mt-2">
                        <form action="{{ route('cart.update', $item['menu']->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="w-16 px-2 py-1 border rounded text-sm">
                            <button type="submit" class="text-xs text-blue-600">Update</button>
                        </form>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold">{{ 'Rp ' . number_format($item['subtotal'], 0, ',', '.') }}</p>
                    <form action="{{ route('cart.remove', $item['menu']->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-600 mt-2">Hapus</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <div class="border-t pt-4">
            <div class="flex justify-between mb-4">
                <span class="font-bold">Total</span>
                <span class="font-bold text-xl">{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <a href="{{ route('orders.checkout') }}" class="block w-full px-6 py-3 bg-black text-white text-center rounded-lg hover:bg-gray-800">Checkout</a>
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-500 mb-4">Keranjang kosong</p>
            <a href="{{ route('menu.index') }}" class="inline-block px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800">Lihat Menu</a>
        </div>
        @endif
    </div>
</section>
@endsection
