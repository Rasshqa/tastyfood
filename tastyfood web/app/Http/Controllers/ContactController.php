<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $latitude = Setting::get('contact_latitude', -6.9175);
        $longitude = Setting::get('contact_longitude', 107.6191);

        return view('pages.contact', compact('latitude', 'longitude'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|max:255',
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required',
        ]);

        ContactMessage::create($validated);

        return back()->with('success', 'Pesan Anda berhasil terkirim!');
    }
}
