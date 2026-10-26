<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['order', 'paymentMethod', 'user']);

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('order', function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%');
            });
        }

        $payments = $query->latest()->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['order.items', 'paymentMethod', 'user', 'verifier']);
        return view('admin.payments.show', compact('payment'));
    }

    public function approve(Payment $payment)
    {
        if ($payment->status !== 'waiting_verification') {
            return back()->with('error', 'Hanya pembayaran dengan status menunggu verifikasi yang bisa disetujui');
        }

        $payment->update([
            'status' => 'paid',
            'verified_by' => auth('admin')->id(),
            'verified_at' => now(),
        ]);

        $payment->order->update(['status' => 'confirmed']);

        return back()->with('success', 'Pembayaran berhasil disetujui');
    }

    public function reject(Request $request, Payment $payment)
    {
        if ($payment->status !== 'waiting_verification') {
            return back()->with('error', 'Hanya pembayaran dengan status menunggu verifikasi yang bisa ditolak');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $payment->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'verified_by' => auth('admin')->id(),
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Pembayaran berhasil ditolak');
    }
}
