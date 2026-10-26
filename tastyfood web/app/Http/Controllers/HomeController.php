<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Gallery;

class HomeController extends Controller
{
    public function index()
    {
        $latestNews = News::published()->latest('published_at')->take(5)->get();
        $galleries = Gallery::where('status', 'active')->orderBy('sort_order')->take(6)->get();
        
        return view('pages.home', compact('latestNews', 'galleries'));
    }
}
