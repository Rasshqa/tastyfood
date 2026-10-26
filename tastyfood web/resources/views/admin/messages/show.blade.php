@extends('layouts.admin')

@section('header', 'Detail Pesan')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6 pb-6 border-b">
            <h2 class="text-xl font-semibold mb-2">{{ $message->subject }}</h2>
            <div class="flex items-center gap-4 text-sm text-gray-600">
                <span>{{ $message->name }}</span>
                <span>{{ $message->email }}</span>
                <span>{{ $message->created_at->format('d M Y H:i') }}</span>
            </div>
        </div>
        <div class="prose max-w-none">
            <p class="whitespace-pre-wrap">{{ $message->message }}</p>
        </div>
        <div class="mt-6 pt-6 border-t">
            <a href="{{ route('admin.pesan.index') }}" class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 inline-block">Kembali</a>
        </div>
    </div>
</div>
@endsection
