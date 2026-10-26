<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;

class SendOrderSystemMessage
{
    public function handle($event)
    {
        if (! method_exists($event, 'order')) {
            return;
        }

        $order = $event->order ?? null;
        if (! $order || ! $order->user_id) {
            return;
        }

        $conversation = Conversation::firstOrCreate(
            ['user_id' => $order->user_id, 'status' => 'open'],
            ['order_id' => $order->id, 'last_message_at' => now()]
        );

        $messageText = $this->getSystemMessage($event);
        if (! $messageText) {
            return;
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => 0,
            'sender_type' => 'system',
            'message' => $messageText,
            'message_type' => 'system',
        ]);

        $conversation->update(['last_message_at' => now()]);

        broadcast(new MessageSent($message));
    }

    protected function getSystemMessage($event): ?string
    {
        $className = class_basename($event);
        $order = $event->order ?? null;

        if (! $order) {
            return null;
        }

        return match ($className) {
            'OrderCreated' => "Pesanan #{$order->order_number} telah dibuat.",
            'OrderStatusUpdated' => "Status pesanan #{$order->order_number} diubah menjadi {$order->statusLabel}.",
            default => null,
        };
    }
}
