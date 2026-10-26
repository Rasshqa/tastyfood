@extends('layouts.app')

@section('title', 'Edit Profil - Padang Rice')

@section('content')
<div class="min-h-screen bg-[#f5f4f1] pt-20 pb-16">
    <div class="max-w-2xl mx-auto px-4">
        <div class="py-8">
            <a href="{{ route('profile.show') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Kembali
            </a>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-display">Edit Profil</h1>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" required
                           value="{{ old('name', $user->name) }}"
                           class="w-full border {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent transition outline-none">
                    @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1.5">Email tidak dapat diubah</p>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">No. WhatsApp</label>
                    <input type="text" name="phone" required
                           value="{{ old('phone', $user->phone) }}"
                           class="w-full border {{ $errors->has('phone') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent transition outline-none">
                    @error('phone') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="my-8 border-t border-gray-200"></div>

                <p class="text-sm font-bold text-gray-600 mb-4">Ubah Password (opsional)</p>

                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Password Baru</label>
                    <input type="password" name="password"
                           class="w-full border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent transition outline-none"
                           placeholder="Kosongkan jika tidak ingin ubah password">
                    @error('password') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="mb-8">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent transition outline-none"
                           placeholder="Ketik ulang password baru">
                </div>

                <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-3 rounded-xl transition">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
