<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('sender_id');
            $table->enum('sender_type', ['user', 'admin', 'system'])->default('user');
            $table->text('message');
            $table->enum('message_type', ['text', 'system'])->default('text');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('conversation_id');
            $table->index('sender_id');
            $table->index('created_at');
            $table->index('is_read');
            $table->index(['conversation_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
