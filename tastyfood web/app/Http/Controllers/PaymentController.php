<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function show($orderId)
    {
        $order = auth()->user()->orders()->with('items')->findOrFail($orderId);

        if ($order->payment) {
            return redirect()->route('payments.status', $order->id);
        }

        $paymentMethods = PaymentMethod::active()->get();

        return view('payments.show', compact('order', 'paymentMethods'));
    }

    public function store(Request $request, $orderId)
    {
        $validated = $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $order = auth()->user()->orders()->findOrFail($orderId);

        if ($order->payment) {
            return redirect()->route('payments.status', $order->id)->with('error', 'Pembayaran sudah ada');
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method_id' => $validated['payment_method_id'],
            'user_id' => auth()->id(),
            'amount' => $order->total,
            'status' => 'unpaid',
        ]);

        return redirect()->route('payments.upload', $order->id);
    }

    public function uploadForm($orderId)
    {
        $order = auth()->user()->orders()->with('payment.paymentMethod')->findOrFail($orderId);

        if (!$order->payment) {
            return redirect()->route('payments.show', $order->id);
        }

        if ($order->payment->status === 'paid') {
            return redirect()->route('orders.show', $order->id);
        }

        return view('payments.upload', compact('order'));
    }

    public function uploadProof(Request $request, $orderId)
    {
        $validated = $request->validate([
            'proof_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'user_notes' => 'nullable|string|max:500',
        ]);

        $order = auth()->user()->orders()->with('payment')->findOrFail($orderId);

        if (!$order->payment) {
            return back()->with('error', 'Payment record not found');
        }

        if ($order->payment->proof_image) {
            Storage::disk('public')->delete($order->payment->proof_image);
        }

        $path = $request->file('proof_image')->store('payment-proofs', 'public');

        $order->payment->update([
            'proof_image' => $path,
            'user_notes' => $validated['user_notes'] ?? null,
            'status' => 'waiting_verification',
        ]);

        return redirect()->route('orders.show', $order->id)->with('success', 'Bukti pembayaran berhasil diupload');
    }

    public function status($orderId)
    {
        $order = auth()->user()->orders()->with('payment.paymentMethod')->findOrFail($orderId);
        return view('payments.status', compact('order'));
    }
}
