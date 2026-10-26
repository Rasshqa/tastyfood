@extends('layouts.admin')

@section('header', 'Kelola Pesan')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($messages as $message)
            <tr class="{{ !$message->is_read ? 'bg-blue-50' : '' }}">
                <td class="px-6 py-4">
                    <div class="text-sm font-medium">{{ $message->name }}</div>
                    <div class="text-xs text-gray-500">{{ $message->email }}</div>
                </td>
                <td class="px-6 py-4 text-sm">{{ Str::limit($message->subject, 40) }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $message->created_at->format('d M Y') }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded {{ $message->is_read ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $message->is_read ? 'Dibaca' : 'Baru' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('admin.pesan.show', $message) }}" class="text-blue-600 hover:text-blue-800 text-sm">Lihat</a>
                    <form action="{{ route('admin.pesan.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada pesan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $messages->links() }}</div>
@endsection
