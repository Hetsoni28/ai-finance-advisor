<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('ai_chats', function (Blueprint $table) {
            $table->id();

            // 🔗 User reference (Modern Laravel 8+ strict constraints)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 🔥 BEAST MODE: Groups messages together for context windowing
            $table->string('session_id')->index();

            // 💬 Stores the actual Markdown message
            $table->text('message');

            // 🤖 Identifies if the message is from 'user' or 'ai'
            $table->string('sender');

            // 💰 BEAST MODE: Tracks API cost/usage per message
            $table->integer('tokens')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chats');
    }
}