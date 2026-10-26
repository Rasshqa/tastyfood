<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    // User guard
    if (auth()->guard('web')->check()) {
        $conversation = Conversation::find($conversationId);

        return $conversation && $conversation->user_id === $user->id;
    }

    // Admin guard
    if (auth()->guard('admin')->check()) {
        return Conversation::find($conversationId) !== null;
    }

    return false;
});

Broadcast::channel('admin-chat', function ($user) {
    // Only admins can subscribe to admin-chat channel
    return auth()->guard('admin')->check();
});

Broadcast::channel('admin-notifications', function ($user) {
    return auth()->guard('admin')->check();
});

Broadcast::channel('chat-online', function ($user) {
    if (auth()->guard('web')->check()) {
        return ['id' => $user->id, 'name' => $user->name, 'type' => 'user'];
    }
    if (auth()->guard('admin')->check()) {
        return ['id' => $user->id, 'name' => $user->name, 'type' => 'admin'];
    }

    return null;
});
