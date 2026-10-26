@extends('layouts.app')

@section('title', $news->title . ' - Padang Rice')

@section('content')
<x-hero :title="$news->title" />

<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                <span>{{ $news->author }}</span>
                <span>•</span>
                <span>{{ $news->published_at?->format('d M Y') }}</span>
            </div>
        </div>

        <div class="aspect-video overflow-hidden rounded-xl mb-8">
            <img src="{{ asset($news->image ?? 'assets/ASET/nasipadang-hero.jpg') }}" 
                 alt="{{ $news->title }}" 
                 class="w-full h-full object-cover">
        </div>

        <div class="prose prose-sm md:prose max-w-none">
            {!! nl2br(e($news->content)) !!}
        </div>
    </div>
</section>

@if($relatedNews->count() > 0)
<section class="py-16 bg-[#f5f5f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="hero-title text-xl md:text-2xl mb-8">BERITA TERKAIT</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedNews as $item)
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="aspect-[4/3] overflow-hidden">
                    <img src="{{ asset($item->image ?? 'assets/ASET/nasipadang-hero.jpg') }}" 
                         alt="{{ $item->title }}" 
                         class="w-full h-full object-cover">
                </div>
                <div class="p-4">
                    <h3 class="font-display font-bold text-sm uppercase mb-2 line-clamp-2">{{ $item->title }}</h3>
                    <a href="{{ route('news.show', $item->slug) }}" class="text-xs font-bold text-yellow-500 hover:text-gray-900 uppercase tracking-wider">
                        Baca selengkapnya
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
