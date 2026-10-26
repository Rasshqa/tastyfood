<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $id => $item) {
            $menu = Menu::find($id);
            if ($menu) {
                $cartItems[] = [
                    'menu' => $menu,
                    'quantity' => $item['quantity'],
                    'subtotal' => $menu->price * $item['quantity'],
                ];
                $subtotal += $menu->price * $item['quantity'];
            }
        }

        return view('cart.index', compact('cartItems', 'subtotal'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $menu = Menu::findOrFail($validated['menu_id']);

        if (!$menu->available) {
            return back()->with('error', 'Menu tidak tersedia');
        }

        $cart = session('cart', []);
        
        if (isset($cart[$menu->id])) {
            $cart[$menu->id]['quantity'] += $validated['quantity'];
        } else {
            $cart[$menu->id] = [
                'quantity' => $validated['quantity'],
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Menu ditambahkan ke keranjang');
    }

    public function update(Request $request, $menuId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session('cart', []);

        if (isset($cart[$menuId])) {
            $cart[$menuId]['quantity'] = $validated['quantity'];
            session(['cart' => $cart]);
        }

        return back()->with('success', 'Keranjang diperbarui');
    }

    public function remove($menuId)
    {
        $cart = session('cart', []);

        if (isset($cart[$menuId])) {
            unset($cart[$menuId]);
            session(['cart' => $cart]);
        }

        return back()->with('success', 'Item dihapus dari keranjang');
    }

    public function addAjax(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $menu = Menu::findOrFail($validated['menu_id']);

        if (!$menu->available) {
            return response()->json(['success' => false, 'message' => 'Menu tidak tersedia'], 422);
        }

        $cart = session('cart', []);

        if (isset($cart[$menu->id])) {
            $cart[$menu->id]['quantity'] += $validated['quantity'];
        } else {
            $cart[$menu->id] = ['quantity' => $validated['quantity']];
        }

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => $menu->name . ' ditambahkan ke keranjang',
            'cart_count' => count($cart),
            'menu_id' => $menu->id,
            'menu' => [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'image' => $menu->image,
                'category' => $menu->category->name ?? null,
            ],
        ]);
    }

    public function updateAjax(Request $request, $menuId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = session('cart', []);

        if ($validated['quantity'] <= 0) {
            unset($cart[$menuId]);
        } elseif (isset($cart[$menuId])) {
            $cart[$menuId]['quantity'] = $validated['quantity'];
        }

        session(['cart' => $cart]);

        // Recalculate subtotal
        $subtotal = 0;
        foreach ($cart as $id => $item) {
            $m = Menu::find($id);
            if ($m) $subtotal += $m->price * $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'cart_count' => count($cart),
            'subtotal' => $subtotal,
            'subtotal_formatted' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
        ]);
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Keranjang dikosongkan');
    }
}
