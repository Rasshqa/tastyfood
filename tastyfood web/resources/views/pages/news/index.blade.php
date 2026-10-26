@extends('layouts.app')

@section('title', 'Berita Kami - Padang Rice')

@section('content')
<x-hero title="BERITA KAMI" />

@if($featuredNews)
<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-10">
            <div class="lg:col-span-3">
                <div class="aspect-[4/3] overflow-hidden rounded-2xl mb-6">
                    <img src="{{ asset($featuredNews->image ?? 'assets/ASET/nasipadang-hero.jpg') }}" 
                         alt="{{ $featuredNews->title }}" 
                         class="w-full h-full object-cover">
                </div>
            </div>
            <div class="lg:col-span-2 flex flex-col justify-center">
                <h2 class="font-display font-bold text-xl md:text-2xl uppercase mb-4">{{ $featuredNews->title }}</h2>
                <p class="text-sm text-gray-600 leading-relaxed mb-6">
                    {{ Str::limit(strip_tags($featuredNews->content), 300) }}
                </p>
                <a href="{{ route('news.show', $featuredNews->slug) }}" class="btn-dark inline-block">BACA SELENGKAPNYA</a>
            </div>
        </div>
    </div>
</section>
@endif

<section class="py-16 lg:py-20 bg-[#f5f5f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="hero-title text-2xl md:text-3xl text-center mb-10">BERITA LAINNYA</h2>

        @if($news->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            @foreach($news as $item)
            <div class="bg-white overflow-hidden shadow-md rounded-lg group">
                <div class="aspect-[4/3] overflow-hidden">
                    <img src="{{ asset($item->image ?? 'assets/ASET/nasipadang-hero.jpg') }}" 
                         alt="{{ $item->title }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-5">
                    <h3 class="font-display font-bold text-sm uppercase mb-2 line-clamp-2">{{ $item->title }}</h3>
                    <p class="text-xs text-gray-500 leading-relaxed mb-3 line-clamp-3">
                        {{ Str::limit(strip_tags($item->content), 100) }}
                    </p>
                    <div class="flex items-center justify-between">
                        <a href="{{ route('news.show', $item->slug) }}" class="text-xs font-bold text-yellow-500 hover:text-gray-900 uppercase tracking-wider">
                            Baca selengkapnya
                        </a>
                        <span class="text-gray-400 text-xs">...</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{ $news->links() }}
        @else
        <p class="text-center text-gray-500">Tidak ada berita lainnya.</p>
        @endif
    </div>
</section>
@endsection
