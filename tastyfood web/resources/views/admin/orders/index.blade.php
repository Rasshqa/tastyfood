@extends('layouts.admin')

@section('title', 'Kelola Pesanan')

@section('content')
<div class="min-h-screen bg-gray-100 py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Kelola Pesanan</h1>
            <div class="flex items-center gap-3">
                <select id="statusFilter" onchange="filterByStatus(this.value)" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Diproses</option>
                    <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Siap</option>
                    <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>Dalam Perjalanan</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
        </div>

        @if($orders->isEmpty())
            <div class="bg-white rounded-xl shadow p-12 text-center">
                <p class="text-gray-500">Belum ada pesanan</p>
            </div>
        @else
            <div class="bg-white rounded-xl shadow overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">No. Pesanan</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Pemesan</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Pengiriman</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Total</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Status</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Tanggal</th>
                            <th class="text-right px-6 py-4 text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ $order->order_number }}</td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-gray-800">{{ $order->customer_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $order->customer_phone }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $order->delivery_method === 'delivery' ? 'Diantar' : 'Ambil Sendiri' }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ $order->formattedTotal }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $order->statusColor }}">
                                        {{ $order->statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-sm">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-gray-900 hover:text-gray-900 font-semibold">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function filterByStatus(status) {
    const url = new URL(window.location.href);
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    window.location.href = url.toString();
}
</script>
@endsection
