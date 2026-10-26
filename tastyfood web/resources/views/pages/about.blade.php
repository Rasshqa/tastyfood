@extends('layouts.app')

@section('title', 'Tentang Kami - Padang Rice')

@section('content')
<x-hero title="TENTANG KAMI" />

{{-- PADANG RICE Section --}}
<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <div>
                <h2 class="hero-title text-2xl md:text-3xl mb-4">PADANG RICE</h2>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    Padang Rice berdiri sejak tahun 2010 dengan komitmen menghadirkan pengalaman kuliner Minangkabau yang autentik. Didirikan oleh keluarga yang telah mewarisi resep turun-temurun selama tiga generasi, kami mempertahankan cita rasa asli masakan Padang dengan standar kualitas modern.
                </p>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Setiap hidangan diolah oleh tim koki berpengalaman yang memahami filosofi masakan Minang: keseimbangan rasa pedas, gurih, dan manis yang sempurna. Kami hanya menggunakan bahan baku pilihan yang dipilih langsung dari pemasok terpercaya untuk menjamin kesegaran dan kualitas.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <img src="{{ asset('assets/ASET/nasipadang-1.jpg') }}" alt="Padang Rice 1" class="w-full aspect-square object-cover rounded-lg">
                <img src="{{ asset('assets/ASET/nasipadang-2.jpg') }}" alt="Padang Rice 2" class="w-full aspect-square object-cover rounded-lg">
            </div>
        </div>
    </div>
</section>

{{-- VISI Section --}}
<section class="py-16 lg:py-24 bg-[#f5f5f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <div class="order-2 lg:order-1 grid grid-cols-2 gap-4">
                <img src="{{ asset('assets/ASET/nasipadang-3.jpg') }}" alt="Visi 1" class="w-full aspect-square object-cover rounded-lg">
                <img src="{{ asset('assets/ASET/nasipadang-4.jpg') }}" alt="Visi 2" class="w-full aspect-square object-cover rounded-lg">
            </div>
            <div class="order-1 lg:order-2">
                <h2 class="hero-title text-2xl md:text-3xl mb-4">VISI</h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Menjadi referensi utama rumah makan Padang yang menghadirkan pengalaman kuliner Minangkabau terbaik, dengan mempertahankan keaslian resep tradisional sambil menerapkan standar layanan dan kebersihan kelas dunia. Kami bercita-cita menjadi jembatan yang memperkenalkan kekayaan kuliner Indonesia ke generasi masa depan dan pasar global, dengan tetap menghormati warisan budaya yang kami bawa.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- MISI Section --}}
<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <div>
                <h2 class="hero-title text-2xl md:text-3xl mb-4">MISI</h2>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    <strong>1. Mempertahankan Keaslian:</strong> Menjaga resep tradisional Minangkabau dengan menggunakan teknik memasak autentik dan bahan-bahan pilihan yang sesuai standar.
                </p>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    <strong>2. Standar Kualitas Tinggi:</strong> Menerapkan sistem kontrol kualitas ketat mulai dari pemilihan bahan baku, proses memasak, hingga penyajian untuk memastikan konsistensi rasa di setiap hidangan.
                </p>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    <strong>3. Pengalaman Pelanggan:</strong> Memberikan pelayanan terbaik dengan menciptakan suasana yang nyaman, bersih, dan ramah untuk seluruh keluarga.
                </p>
                <p class="text-sm text-gray-600 leading-relaxed">
                    <strong>4. Pemberdayaan SDM:</strong> Melatih dan mengembangkan tim yang kompeten, profesional, serta memiliki pemahaman mendalam tentang budaya kuliner Minangkabau.
                </p>
            </div>
            <div class="aspect-video overflow-hidden rounded-lg">
                <img src="{{ asset('assets/ASET/nasipadang-5.jpg') }}" alt="Misi" class="w-full h-full object-cover">
            </div>
        </div>
    </div>
</section>
@endsection
