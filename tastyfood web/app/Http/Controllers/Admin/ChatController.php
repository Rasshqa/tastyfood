<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Setting;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $conversations = Conversation::with(['user', 'latestMessage'])
            ->withCount(['messages as unread_count' => function ($query) {
                $query->where('sender_type', 'user')->where('is_read', false);
            }])
            ->recent()
            ->paginate(20);

        $chatEnabled = Setting::get('chat_enabled', '1') === '1';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'conversations' => $conversations->items(),
                'chatEnabled' => $chatEnabled,
            ]);
        }

        return view('admin.chat.index', compact('conversations', 'chatEnabled'));
    }

    public function show($conversationId)
    {
        $conversation = Conversation::with(['user', 'order'])->findOrFail($conversationId);

        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    public function getMessages(Request $request, $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        $page = $request->input('page', 1);
        $perPage = 30;

        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'has_more' => Message::where('conversation_id', $conversationId)->count() > ($page * $perPage),
        ]);
    }

    public function sendMessage(Request $request, $conversationId)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:1|max:2000',
        ]);

        $conversation = Conversation::findOrFail($conversationId);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth('admin')->id(),
            'sender_type' => 'admin',
            'message' => trim($validated['message']),
            'message_type' => 'text',
        ]);

        $conversation->update(['last_message_at' => now()]);

        broadcast(new MessageSent($message));

        return response()->json([
            'success' => true,
            'message' => $message->fresh(),
        ]);
    }

    public function markAsRead($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        Message::where('conversation_id', $conversationId)
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        broadcast(new MessageRead($conversationId, 'admin'));

        return response()->json(['success' => true]);
    }

    public function typing(Request $request, $conversationId)
    {
        $validated = $request->validate([
            'is_typing' => 'required|boolean',
        ]);

        $conversation = Conversation::findOrFail($conversationId);

        broadcast(new UserTyping($conversationId, 'admin', $validated['is_typing']));

        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        $conversations = Conversation::with(['user', 'latestMessage'])
            ->whereHas('user', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%");
            })
            ->recent()
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'conversations' => $conversations,
        ]);
    }

    public function close($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $conversation->update(['status' => 'closed']);

        return response()->json(['success' => true]);
    }

    public function toggleAvailability(Request $request)
    {
        $enabled = $request->input('enabled') === 'true' || $request->input('enabled') === true;
        Setting::set('chat_enabled', $enabled ? '1' : '0');

        return response()->json(['success' => true, 'enabled' => $enabled]);
    }

    public function getTotalUnread()
    {
        $total = Message::where('sender_type', 'user')
            ->where('is_read', false)
            ->count();

        return response()->json(['unread' => $total]);
    }
}
