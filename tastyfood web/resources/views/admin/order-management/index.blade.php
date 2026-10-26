@extends('layouts.admin')

@section('header', 'Kelola Pesanan & Pembayaran')

@section('content')
<div class="space-y-6" x-data="orderManagement()" x-init="init()">
    {{-- Auto-refresh indicator --}}
    <div x-show="autoRefreshEnabled" 
         x-transition
         class="bg-blue-50 border border-blue-200 text-blue-700 rounded-lg px-4 py-2 text-sm flex items-center justify-between">
        <span class="flex items-center gap-2">
            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Auto-refresh aktif
        </span>
        <button @click="autoRefreshEnabled = false" class="text-blue-600 hover:text-blue-800 text-xs underline">Nonaktifkan</button>
    </div>
    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="No. pesanan, nama, telepon..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900">
            </div>
            
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pesanan</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900">
                    <option value="semua" {{ request('status') === 'semua' || !request('status') ? 'selected' : '' }}>Semua</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Diproses</option>
                    <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Siap</option>
                    <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>Dalam Perjalanan</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                <select name="payment_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900">
                    <option value="semua" {{ request('payment_status') === 'semua' || !request('payment_status') ? 'selected' : '' }}>Semua</option>
                    <option value="waiting_verification" {{ request('payment_status') === 'waiting_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="rejected" {{ request('payment_status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Filter
                </button>
                <a href="{{ route('admin.order-management.index') }}" class="border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    {{-- Orders Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase">No. Pesanan</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Pelanggan</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Items</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Total</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Status Pesanan</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Pembayaran</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Bukti Bayar</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50" id="order-{{ $order->id }}" data-proof-image="{{ $order->payment && $order->payment->proof_image ? asset('storage/' . $order->payment->proof_image) : '' }}">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.order-management.show', $order) }}" class="font-medium text-gray-900 hover:text-gray-900 transition">{{ $order->order_number }}</a>
                            <div class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $order->customer_name }}</div>
                            <div class="text-sm text-gray-500">{{ $order->customer_phone }}</div>
                            @if($order->location)
                            <div class="text-xs text-gray-400 truncate max-w-[150px]" title="{{ $order->location }}">{{ $order->location }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-700">
                                @foreach($order->items->take(2) as $item)
                                    <div>{{ $item->quantity }}x {{ $item->menu_name }}</div>
                                @endforeach
                                @if($order->items->count() > 2)
                                    <div class="text-xs text-gray-500">+{{ $order->items->count() - 2 }} lainnya</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.order-management.update-status', $order) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" 
                                        class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-gray-900 {{ $order->statusColor }}">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                                    <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Diproses</option>
                                    <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Siap</option>
                                    <option value="in_transit" {{ $order->status === 'in_transit' ? 'selected' : '' }}>Dalam Perjalanan</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            @if($order->payment)
                                @php
                                    $paymentColors = [
                                        'waiting_verification' => 'bg-gray-100 text-yellow-800',
                                        'paid' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                    ];
                                    $paymentLabels = [
                                        'waiting_verification' => 'Menunggu Verifikasi',
                                        'paid' => 'Lunas',
                                        'rejected' => 'Ditolak',
                                    ];
                                @endphp
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full {{ $paymentColors[$order->payment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $paymentLabels[$order->payment->status] ?? $order->payment->status }}
                                </span>
                                @if($order->payment->paymentMethod)
                                    <div class="text-xs text-gray-500 mt-1">{{ $order->payment->paymentMethod->name }}</div>
                                @endif
                            @else
                                <span class="text-gray-400 text-sm">Belum ada</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($order->payment && $order->payment->proof_image)
                                <button type="button" onclick="openPaymentModal({{ $order->id }})" 
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium underline">
                                    Lihat Bukti
                                </button>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($order->payment && $order->payment->status === 'waiting_verification')
                                <div class="flex gap-2">
                                    <button type="button" onclick="approvePayment({{ $order->payment->id }})" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium">
                                        Setujui
                                    </button>
                                    <button type="button" onclick="showRejectModal({{ $order->payment->id }})" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium">
                                        Tolak
                                    </button>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada pesanan ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($orders->hasPages())
        <div class="bg-white border-t px-4 py-3">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Payment Proof Modal --}}
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Bukti Pembayaran</h3>
            <button type="button" onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-4" id="paymentModalContent">
            {{-- Content loaded dynamically --}}
        </div>
    </div>
</div>

{{-- Reject Reason Modal --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-md w-full mx-4">
        <div class="p-4 border-b">
            <h3 class="text-lg font-bold text-gray-900">Tolak Pembayaran</h3>
        </div>
        <form id="rejectForm" class="p-4">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                <textarea name="rejection_reason" rows="3" required
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500"
                          placeholder="Contoh: Bukti transfer tidak jelas / Nama pengirim tidak sesuai"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" 
                        class="flex-1 border border-gray-300 text-gray-700 font-semibold py-2 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg">
                    Tolak
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentPaymentId = null;

function openPaymentModal(orderId) {
    const row = document.getElementById('order-' + orderId);
    const imgSrc = row.dataset.proofImage || '';
    
    const content = document.getElementById('paymentModalContent');
    
    if (imgSrc) {
        content.innerHTML = `
            <img src="${imgSrc}" alt="Bukti Pembayaran" class="w-full rounded-lg border border-gray-300">
            <a href="${imgSrc}" target="_blank" class="mt-4 inline-block text-blue-600 hover:text-blue-800 underline text-sm">
                Buka di tab baru
            </a>
        `;
    } else {
        content.innerHTML = '<p class="text-gray-500">Tidak ada bukti pembayaran</p>';
    }
    
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function showRejectModal(paymentId) {
    currentPaymentId = paymentId;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    currentPaymentId = null;
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectForm').reset();
}

function approvePayment(paymentId) {
    if (!confirm('Setujui pembayaran ini?')) return;
    
    fetch(`{{ url('admin/order-management/payment') }}/${paymentId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan');
    });
}

document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`{{ url('admin/order-management/payment') }}/${currentPaymentId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan');
    });
});

function orderManagement() {
    return {
        autoRefreshEnabled: true,
        newOrders: [],
        refreshTimer: null,

        init() {
            this.startAutoRefresh();
        },

        startAutoRefresh() {
            this.refreshTimer = setInterval(() => {
                if (this.autoRefreshEnabled) this.refresh();
            }, 30000);
        },

        async refresh() {
            try {
                const params = new URLSearchParams(window.location.search);
                const response = await fetch(`{{ route("admin.order-management.index") }}?${params.toString()}`, {
                    headers: {
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });
                const data = await response.json();
                if (data.success) {
                    const currentIds = @json($orders->pluck("id"));
                    const fetchedIds = data.orders.map(o => o.id);
                    const hasNew = fetchedIds.some(id => !currentIds.includes(id));

                    if (hasNew) {
                        const banner = document.createElement("div");
                        banner.className = "fixed top-4 right-4 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg z-50";
                        banner.innerHTML = `<div class="flex items-center gap-3">
                            <span>${data.orders.filter(o => !currentIds.includes(o.id)).length} pesanan baru!</span>
                            <button onclick="window.location.reload()" class="bg-white text-green-600 px-3 py-1 rounded text-sm font-medium">Lihat</button>
                        </div>`;
                        document.body.appendChild(banner);
                        setTimeout(() => banner.remove(), 8000);
                    }
                }
            } catch (e) {
                console.error("Failed to refresh orders:", e);
            }
        }
    };
}
</script>
@endsection
