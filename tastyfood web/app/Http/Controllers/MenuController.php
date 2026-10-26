<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::query()->available();

        if ($request->filled('category') && $request->category !== 'semua') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', (int) $request->price_max);
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', (int) $request->price_min);
        }

        if ($request->filled('sort')) {
            if ($request->sort === 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort === 'price_desc') {
                $query->orderBy('price', 'desc');
            } elseif ($request->sort === 'name_asc') {
                $query->orderBy('name', 'asc');
            }
        } else {
            $query->popular()->orderBy('sort_order')->orderBy('name');
        }

        $totalMenus = Menu::available()->count();
        $minPrice = Menu::available()->min('price');
        $maxPrice = Menu::available()->max('price');
        $menus = $query->paginate(16)->withQueryString();

        // Pass cart with menu details to view
        $cart = session('cart', []);
        $cartWithMenus = [];
        foreach ($cart as $menuId => $item) {
            $menu = Menu::find($menuId);
            if ($menu) {
                $cartWithMenus[$menuId] = [
                    'menu' => $menu,
                    'quantity' => $item['quantity'],
                ];
            }
        }

        return view('menu.index', compact('menus', 'totalMenus', 'minPrice', 'maxPrice', 'cartWithMenus'));
    }

    public function show($id)
    {
        $menu = Menu::findOrFail($id);
        return view('menu.show', compact('menu'));
    }
}
