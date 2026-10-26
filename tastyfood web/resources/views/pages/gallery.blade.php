@extends('layouts.app')

@section('title', 'Galeri Kami - Padang Rice')

@section('content')
<x-hero title="GALERI KAMI" />

{{-- Carousel Section --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative">
            <div id="carousel" class="aspect-video overflow-hidden rounded-2xl bg-gray-100">
                @foreach($galleries->take(5) as $index => $gallery)
                <div class="carousel-item {{ $index === 0 ? 'active' : 'hidden' }} w-full h-full">
                    <img src="{{ asset($gallery->image) }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover">
                </div>
                @endforeach
            </div>

            <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>

            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                @foreach($galleries->take(5) as $index => $gallery)
                <button onclick="goToSlide({{ $index }})" class="indicator w-2 h-2 rounded-full transition {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}"></button>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Gallery Grid --}}
<section class="py-16 bg-[#f5f5f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($galleries as $gallery)
            <div class="aspect-square overflow-hidden rounded-lg cursor-pointer group" onclick="openLightbox('{{ asset($gallery->image) }}', '{{ $gallery->title }}')">
                <img src="{{ asset($gallery->image) }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Lightbox --}}
<div id="lightbox" class="hidden fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4" onclick="closeLightbox()">
    <button class="absolute top-4 right-4 text-white text-4xl hover:text-gray-300" onclick="closeLightbox()">&times;</button>
    <img id="lightbox-img" src="" alt="" class="max-w-full max-h-full object-contain" onclick="event.stopPropagation()">
</div>

<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-item');
const indicators = document.querySelectorAll('.indicator');

function showSlide(n) {
    slides.forEach(s => s.classList.add('hidden'));
    indicators.forEach(i => i.classList.remove('bg-white'));
    indicators.forEach(i => i.classList.add('bg-white/50'));
    
    slides[n].classList.remove('hidden');
    indicators[n].classList.add('bg-white');
    indicators[n].classList.remove('bg-white/50');
    currentSlide = n;
}

function nextSlide() {
    let n = currentSlide + 1;
    if (n >= slides.length) n = 0;
    showSlide(n);
}

function prevSlide() {
    let n = currentSlide - 1;
    if (n < 0) n = slides.length - 1;
    showSlide(n);
}

function goToSlide(n) {
    showSlide(n);
}

// Auto-advance
setInterval(nextSlide, 5000);

function openLightbox(src, alt) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox-img').alt = alt;
    document.getElementById('lightbox').classList.remove('hidden');
}

function closeLightbox() {
    document.getElementById('lightbox').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
});
</script>
@endsection
