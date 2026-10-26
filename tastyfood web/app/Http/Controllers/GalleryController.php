<?php

namespace App\Http\Controllers;

use App\Models\Gallery;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::where('status', 'active')->orderBy('sort_order')->get();
        
        return view('pages.gallery', compact('galleries'));
    }
}
