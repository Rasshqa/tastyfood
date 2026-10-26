<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;

class OrderObserver
{
    public function created(Order $order)
    {
        if (! $order->user_id) {
            return;
        }

        $conversation = Conversation::where('user_id', $order->user_id)
            ->where('status', 'open')
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'status' => 'open',
                'last_message_at' => now(),
            ]);
        } elseif (! $conversation->order_id) {
            $conversation->update(['order_id' => $order->id]);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => 0,
            'sender_type' => 'system',
            'message' => "Pesanan #{$order->order_number} telah dibuat.",
            'message_type' => 'system',
        ]);

        $conversation->update(['last_message_at' => now()]);

        broadcast(new MessageSent($message));
    }

    public function updated(Order $order)
    {
        if (! $order->user_id || ! $order->wasChanged('status')) {
            return;
        }

        $conversation = Conversation::where('user_id', $order->user_id)
            ->where('status', 'open')
            ->first();

        if (! $conversation) {
            return;
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => 0,
            'sender_type' => 'system',
            'message' => "Status pesanan #{$order->order_number} diubah menjadi {$order->statusLabel}.",
            'message_type' => 'system',
        ]);

        $conversation->update(['last_message_at' => now()]);

        broadcast(new MessageSent($message));
    }
}
