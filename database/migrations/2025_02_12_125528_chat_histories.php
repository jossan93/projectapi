<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_history', function (Blueprint $table) {
            $table->id(); // Primärnyckel (auto-increment)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Koppling till users-tabellen
            $table->foreignId('session_id')->constrained('sessions')->onDelete('cascade'); // Koppling till sessions-tabellen
            $table->string('user_message');
            $table->string('bot_response');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
