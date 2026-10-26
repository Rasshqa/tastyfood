<?php

namespace App\Http\Controllers;

use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function getOrCreateConversation(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $user = auth()->user();

        $conversation = Conversation::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'user_id' => $user->id,
                'order_id' => $validated['order_id'] ?? null,
                'status' => 'open',
                'last_message_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'conversation' => [
                'id' => $conversation->id,
                'order_id' => $conversation->order_id,
                'status' => $conversation->status,
            ],
        ]);
    }

    public function getMessages(Request $request, $conversationId)
    {
        $conversation = Conversation::where('user_id', auth()->id())
            ->findOrFail($conversationId);

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

        $conversation = Conversation::where('user_id', auth()->id())
            ->findOrFail($conversationId);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'sender_type' => 'user',
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
        $conversation = Conversation::where('user_id', auth()->id())
            ->findOrFail($conversationId);

        Message::where('conversation_id', $conversationId)
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        broadcast(new MessageRead($conversationId, 'user'));

        return response()->json(['success' => true]);
    }

    public function typing(Request $request, $conversationId)
    {
        $validated = $request->validate([
            'is_typing' => 'required|boolean',
        ]);

        $conversation = Conversation::where('user_id', auth()->id())
            ->findOrFail($conversationId);

        broadcast(new UserTyping($conversationId, 'user', $validated['is_typing']));

        return response()->json(['success' => true]);
    }
}
