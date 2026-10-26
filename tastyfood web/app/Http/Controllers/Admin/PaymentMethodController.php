<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::withTrashed()->latest()->get();
        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('admin.payment-methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank_transfer,qr_payment,e_wallet,cash',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'qr_code' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'instructions' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('qr_code')) {
            $validated['qr_code'] = $request->file('qr_code')->store('payment-qr-codes', 'public');
        }

        PaymentMethod::create($validated);

        return redirect()->route('admin.payment-methods.index')->with('success', 'Metode pembayaran berhasil ditambahkan');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank_transfer,qr_payment,e_wallet,cash',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'qr_code' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'instructions' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('qr_code')) {
            if ($paymentMethod->qr_code) {
                Storage::disk('public')->delete($paymentMethod->qr_code);
            }
            $validated['qr_code'] = $request->file('qr_code')->store('payment-qr-codes', 'public');
        }

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment-methods.index')->with('success', 'Metode pembayaran berhasil diperbarui');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return redirect()->route('admin.payment-methods.index')->with('success', 'Metode pembayaran berhasil dinonaktifkan');
    }
}
