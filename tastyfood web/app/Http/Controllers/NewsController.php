<?php

namespace App\Http\Controllers;

use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $featuredNews = News::published()->latest('published_at')->first();
        $news = News::published()->latest('published_at')->skip(1)->paginate(8);
        
        return view('pages.news.index', compact('featuredNews', 'news'));
    }

    public function show($slug)
    {
        $news = News::where('slug', $slug)->where('status', 'published')->firstOrFail();
        $relatedNews = News::published()
            ->where('id', '!=', $news->id)
            ->latest('published_at')
            ->take(4)
            ->get();
        
        return view('pages.news.show', compact('news', 'relatedNews'));
    }
}
