@extends('layouts.app')

@section('title', 'Profil - Padang Rice')

@section('content')
<div class="min-h-screen bg-[#f5f4f1] pt-20 pb-16">
    <div class="max-w-4xl mx-auto px-4">
        <div class="py-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-display">Profil Saya</h1>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 mb-6 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Nama</label>
                    <p class="text-gray-900 text-lg">{{ $user->name }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Email</label>
                    <p class="text-gray-900 text-lg">{{ $user->email }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">No. WhatsApp</label>
                    <p class="text-gray-900 text-lg">{{ $user->phone }}</p>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-200">
                <a href="{{ route('profile.edit') }}" class="inline-block bg-gray-900 hover:bg-gray-800 text-white font-bold px-8 py-3 rounded-xl transition">
                    Edit Profil
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
