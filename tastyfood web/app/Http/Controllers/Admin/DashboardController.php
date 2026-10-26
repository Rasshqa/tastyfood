<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Gallery;
use App\Models\News;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total_news' => News::count(),
            'total_galleries' => Gallery::count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
        ];

        $recentNews = News::latest()->take(5)->get();
        $recentMessages = ContactMessage::latest()->take(5)->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'recentNews' => $recentNews->map(fn ($news) => [
                    'id' => $news->id,
                    'title' => $news->title,
                    'image' => $news->image,
                    'created_at_human' => $news->created_at->diffForHumans(),
                ]),
                'recentMessages' => $recentMessages->map(fn ($msg) => [
                    'id' => $msg->id,
                    'name' => $msg->name,
                    'subject' => $msg->subject,
                    'is_read' => $msg->is_read,
                    'created_at_human' => $msg->created_at->diffForHumans(),
                ]),
            ]);
        }

        return view('admin.dashboard', compact('stats', 'recentNews', 'recentMessages'));
    }
}
