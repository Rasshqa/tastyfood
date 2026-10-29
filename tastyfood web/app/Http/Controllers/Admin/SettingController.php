<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $settings = Setting::all()->pluck('setting_value', 'setting_key');

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'settings' => $settings,
            ]);
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|max:255',
            'site_description' => 'nullable',
            'email' => 'required|email|max:255',
            'phone' => 'required|max:255',
            'location' => 'required|max:255',
            'contact_latitude' => 'required|numeric',
            'contact_longitude' => 'required|numeric',
            'chat_enabled' => 'nullable',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'chat_enabled') {
                $value = $request->has('chat_enabled') ? '1' : '0';
            }
            Setting::set($key, $value);
        }

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan berhasil diperbarui!',
                'settings' => Setting::all()->pluck('setting_value', 'setting_key'),
            ]);
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
