<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category') && $request->category !== 'semua') {
            $query->where('category', $request->category);
        }

        $menus = $query->orderBy('sort_order')->orderBy('name')->paginate(15);

        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $categories = [
            'nasi' => 'Nasi',
            'lauk' => 'Lauk',
            'sayur' => 'Sayur',
            'minuman' => 'Minuman',
        ];
        return view('admin.menus.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'category' => 'required|in:nasi,lauk,sayur,minuman',
            'available' => 'boolean',
        ]);

        $validated['available'] = $request->has('available');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        $validated['sort_order'] = Menu::max('sort_order') + 1;

        Menu::create($validated);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan');
    }

    public function edit(Menu $menu)
    {
        $categories = [
            'nasi' => 'Nasi',
            'lauk' => 'Lauk',
            'sayur' => 'Sayur',
            'minuman' => 'Minuman',
        ];
        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'category' => 'required|in:nasi,lauk,sayur,minuman',
            'available' => 'boolean',
        ]);

        $validated['available'] = $request->has('available');

        if ($request->hasFile('image')) {
            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu->update($validated);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diperbarui');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->image && Storage::disk('public')->exists($menu->image)) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil dihapus');
    }
}
