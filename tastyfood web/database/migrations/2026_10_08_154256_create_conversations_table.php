<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->index('user_id', 'idx_conversations_user');
            $table->index('last_message_at', 'idx_conversations_last_msg');
            $table->index('status', 'idx_conversations_status');
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex('idx_conversations_user');
            $table->dropIndex('idx_conversations_last_msg');
            $table->dropIndex('idx_conversations_status');
        });

        Schema::dropIfExists('conversations');
    }
};
