@props(['title', 'background' => 'assets/ASET/sebastian-coman-photography-eBmyH7oO5wY-unsplash.jpg'])

<section class="relative h-[40vh] min-h-[280px] lg:h-[45vh] lg:min-h-[340px] flex items-center overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset($background) }}" alt="" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/70"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <h1 class="hero-title text-4xl md:text-5xl lg:text-6xl text-white">{{ $title }}</h1>
    </div>
</section>