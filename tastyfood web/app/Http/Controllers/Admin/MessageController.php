<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%')
                    ->orWhere('subject', 'like', '%'.$request->search.'%');
            });
        }

        $messages = $query->latest()->paginate(15);

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'messages' => $messages->items(),
            ]);
        }

        return view('admin.messages.index', compact('messages'));
    }

    public function show(Request $request, ContactMessage $message)
    {
        $message->update(['is_read' => true]);

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return view('admin.messages.show', compact('message'));
    }

    public function update(Request $request, ContactMessage $message)
    {
        if ($request->has('is_read')) {
            $message->update(['is_read' => (bool)$request->is_read]);
        }

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('admin.pesan.index')->with('success', 'Status pesan diperbarui!');
    }

    public function destroy(Request $request, ContactMessage $message)
    {
        $message->delete();

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dihapus!',
            ]);
        }

        return redirect()->route('admin.pesan.index')->with('success', 'Pesan berhasil dihapus!');
    }
}
