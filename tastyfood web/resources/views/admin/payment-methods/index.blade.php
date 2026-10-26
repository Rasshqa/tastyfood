@extends('layouts.admin')

@section('title', 'Metode Pembayaran')
@section('header', 'Metode Pembayaran')

@section('content')
@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-6">
        {{ session('success') }}
    </div>
@endif

<div class="mb-6">
    <a href="{{ route('admin.payment-methods.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Metode Pembayaran
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($paymentMethods as $method)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $method->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600">{{ $method->typeLabel }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            @if($method->account_name)
                                <div>{{ $method->account_name }}</div>
                            @endif
                            @if($method->account_number)
                                <div class="text-gray-600">{{ $method->account_number }}</div>
                            @endif
                            @if($method->qr_code)
                                <div class="text-gray-500 text-xs">QR Code tersedia</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($method->trashed())
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Dihapus</span>
                        @elseif($method->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Aktif</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if(!$method->trashed())
                            <a href="{{ route('admin.payment-methods.edit', $method) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <form action="{{ route('admin.payment-methods.destroy', $method) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus metode pembayaran ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        Belum ada metode pembayaran
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
