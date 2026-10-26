@extends('layouts.admin')

@section('header', 'Verifikasi Pembayaran')

@section('content')
<div class="mb-6">
    <form method="GET" class="flex flex-col sm:flex-row gap-4">
        <select name="status" class="rounded-lg border-gray-300 focus:ring-gray-900 focus:border-gray-500">
            <option value="semua" {{ request('status') === 'semua' ? 'selected' : '' }}>Semua Status</option>
            <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
            <option value="waiting_verification" {{ request('status') === 'waiting_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <input type="text" name="search" placeholder="Cari nomor pesanan..." value="{{ request('search') }}"
               class="rounded-lg border-gray-300 focus:ring-gray-900 focus:border-gray-500">
        <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">
            Filter
        </button>
        @if(request()->hasAny(['status', 'search']))
            <a href="{{ route('admin.payments.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Reset
            </a>
        @endif
    </form>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-6">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Pesanan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($payments as $payment)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-medium text-gray-900">{{ $payment->order->order_number }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $payment->order->customer_name }}</div>
                        <div class="text-xs text-gray-500">{{ $payment->order->customer_phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $payment->paymentMethod->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        {{ $payment->formattedAmount }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $payment->statusColor }}">
                            {{ $payment->statusLabel }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $payment->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.payments.show', $payment) }}" 
                           class="text-gray-900 hover:text-gray-900 font-medium">
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        Belum ada data pembayaran
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $payments->links() }}
</div>
@endsection
