@extends('layouts.app')

@section('title', 'Daftar - Padang Rice')

@section('content')
<div class="min-h-screen bg-[#f5f4f1] flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 font-display">Daftar</h1>
            <p class="text-gray-600 mt-2">Buat akun baru Anda</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('register') }}" method="POST">
                @csrf

                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" required autofocus
                           value="{{ old('name') }}"
                           class="w-full border {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent transition outline-none"
                           placeholder="Nama lengkap Anda">
                    @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Email</label>
                    <input type="email" name="email" required
                           value="{{ old('email') }}"
                           class="w-full border {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent transition outline-none"
                           placeholder="email@contoh.com">
                    @error('email') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">No. WhatsApp</label>
                    <input type="text" name="phone" required
                           value="{{ old('phone') }}"
                           class="w-full border {{ $errors->has('phone') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent transition outline-none"
                           placeholder="08xxxxxxxxxx">
                    @error('phone') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                               class="w-full border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl px-4 py-3 pr-12 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent transition outline-none"
                               placeholder="Minimal 8 karakter">
                        <button type="button" onclick="togglePassword('password', 'toggleIcon')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg id="toggleIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-3 pr-12 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent transition outline-none"
                               placeholder="Ketik ulang password">
                        <button type="button" onclick="togglePassword('password_confirmation', 'toggleIconConfirm')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg id="toggleIconConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-3 rounded-xl transition">
                    Daftar
                </button>

                <p class="text-center text-sm text-gray-600 mt-6">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-gray-900 hover:text-gray-900 font-semibold">Masuk</a>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}
</script>
@endsection
