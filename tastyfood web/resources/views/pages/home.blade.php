@extends('layouts.app')

@section('title', 'Home - Padang Rice')

@section('content')
{{-- Hero Section --}}
<section class="relative min-h-screen flex items-center bg-[#f9f9f9] overflow-hidden pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative z-10">
        <div class="max-w-xl lg:max-w-2xl">
            <div class="w-16 h-0.5 bg-black mb-6"></div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl text-gray-900 mb-6 font-normal tracking-wide">
                CITA RASA NUSANTARA<br>
                <span class="font-extrabold font-display">DALAM SETIAP SAJIAN</span>
            </h1>
            <p class="text-gray-700 text-sm md:text-base leading-relaxed mb-8 font-medium">
                Nikmati hidangan khas Indonesia yang diolah dari rempah-rempah pilihan, resep autentik, dan bahan-bahan segar untuk menghadirkan pengalaman kuliner yang tak terlupakan. Indonesia memiliki kekayaan kuliner yang beragam dengan cita rasa khas dari setiap daerah
            </p>
            <a href="/tentang" class="inline-block px-10 py-4 bg-black text-white text-xs tracking-widest font-bold uppercase transition-transform hover:scale-105">TENTANG KAMI</a>
        </div>
    </div>
    
    <div class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-[15%] w-[280px] sm:w-[350px] md:w-[500px] lg:w-[700px] xl:w-[850px] pointer-events-none z-0 opacity-40 md:opacity-60 lg:opacity-80">
        <img src="{{ asset('assets/UI/naspad.png') }}" alt="Padang Rice Hero" class="w-full h-auto object-contain drop-shadow-2xl">
    </div>
</section>

{{-- Tentang Kami Section --}}
<section class="pt-24 pb-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto flex flex-col items-center">
            <h2 class="text-2xl md:text-3xl lg:text-4xl text-black font-extrabold font-display tracking-wide mb-6 uppercase">TENTANG KAMI</h2>
            <p class="text-sm md:text-base text-gray-800 leading-relaxed font-medium mb-8">
                Nikmati hidangan khas Indonesia yang diolah dari rempah-rempah pilihan, resep autentik, dan bahan-bahan segar untuk menghadirkan pengalaman kuliner yang tak terlupakan. Indonesia memiliki kekayaan kuliner yang beragam dengan cita rasa khas dari setiap daerah
            </p>
            <div class="w-24 h-0.5 bg-black"></div>
        </div>
    </div>
</section>

<section class="relative py-24 bg-black">
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('assets/ASET/brooke-lark-1Rm9GLHV0UA-unsplash.jpg') }}" alt="Background" class="w-full h-full object-cover opacity-30">
    </div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-20 mt-8">
            @foreach([
                ['img' => 'assets/ASET/nasipadang-1.jpg', 'title' => 'RENDANG OTENTIK', 'desc' => 'Rendang daging sapi pilihan dimasak dengan 15 rempah tradisional selama 8 jam untuk mencapai tekstur empuk dan rasa mendalam.'],
                ['img' => 'assets/ASET/nasipadang-2.jpg', 'title' => 'AYAM POP', 'desc' => 'Ayam kampung direbus dengan daun singkong dan rempah pilihan, menghasilkan daging yang lembut dengan kuah kaldu yang gurih.'],
                ['img' => 'assets/ASET/nasipadang-3.jpg', 'title' => 'GULAI TUNJANG', 'desc' => 'Kikil sapi dimasak dalam kuah santan kental dengan campuran cabai merah dan rempah khas Minang.'],
                ['img' => 'assets/ASET/nasipadang-4.jpg', 'title' => 'DAUN SINGKONG', 'desc' => 'Daun singkong segar direbus dengan santan dan bumbu tradisional, menjadi pelengkap sempurna untuk setiap hidangan.'],
            ] as $item)
            <div class="bg-white rounded-[2rem] shadow-2xl p-8 text-center pt-28 relative mt-16 lg:mt-0">
                <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-40 h-40 rounded-full overflow-hidden border-4 border-white shadow-xl bg-white">
                    <img src="{{ asset($item['img']) }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover">
                </div>
                <h3 class="font-display font-extrabold text-lg mb-4 text-gray-900">{{ $item['title'] }}</h3>
                <p class="text-xs text-gray-600 leading-relaxed font-medium">
                    {{ $item['desc'] }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Berita Kami Section --}}
<section class="py-16 lg:py-24 bg-[#f5f5f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="hero-title text-2xl md:text-3xl text-center text-gray-900 mb-10">BERITA KAMI</h2>

        @if($latestNews->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">
            <div class="bg-white overflow-hidden shadow-md">
                <div class="aspect-[4/3] overflow-hidden">
                    <img src="{{ asset($latestNews->first()->image ?? 'assets/ASET/nasipadang-hero.jpg') }}" alt="{{ $latestNews->first()->title }}" class="w-full h-full object-cover">
                </div>
            </div>

            <div class="bg-white p-6 lg:p-8 shadow-md flex flex-col justify-center">
                <h3 class="font-display font-bold text-lg md:text-xl uppercase mb-3">{{ $latestNews->first()->title }}</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    {{ Str::limit(strip_tags($latestNews->first()->content), 220) }}
                </p>
                <a href="{{ route('news.show', $latestNews->first()->slug) }}" class="text-xs font-bold text-yellow-500 hover:text-gray-900 uppercase tracking-wider flex items-center gap-1">
                    Baca selengkapnya <span class="text-lg">...</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($latestNews->skip(1)->take(4) as $news)
            <div class="bg-white overflow-hidden shadow-md">
                <div class="aspect-[4/3] overflow-hidden">
                    <img src="{{ asset($news->image ?? 'assets/ASET/nasipadang-1.jpg') }}" alt="{{ $news->title }}" class="w-full h-full object-cover">
                </div>
                <div class="p-4">
                    <h3 class="font-display font-bold text-sm uppercase mb-2">{{ $news->title }}</h3>
                    <p class="text-xs text-gray-500 leading-relaxed mb-3 line-clamp-3">
                        {{ Str::limit(strip_tags($news->content), 100) }}
                    </p>
                    <a href="{{ route('news.show', $news->slug) }}" class="text-xs font-bold text-yellow-500 hover:text-gray-900 uppercase tracking-wider flex items-center gap-1">
                        Baca selengkapnya <span class="text-lg">...</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- Galeri Kami Section --}}
<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="hero-title text-2xl md:text-3xl text-center text-gray-900 mb-10">GALERI KAMI</h2>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-10">
            @foreach($galleries as $gallery)
            <div class="aspect-square overflow-hidden rounded-lg">
                <img src="{{ asset($gallery->image) }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            </div>
            @endforeach
        </div>

        <div class="text-center">
            <a href="/galeri" class="btn-dark">LIHAT LEBIH BANYAK</a>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        navbar.classList.add('text-gray-900');
        navbar.classList.remove('text-white');
        
        if (window.scrollY > 50) {
            navbar.classList.add('bg-white/95', 'shadow-sm');
            navbar.classList.remove('bg-[#f9f9f9]');
        } else {
            navbar.classList.add('bg-[#f9f9f9]');
            navbar.classList.remove('bg-white/95', 'shadow-sm');
        }
    });
    // Trigger on load
    window.dispatchEvent(new Event('scroll'));
</script>
@endsection
