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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // The receiver of the notification
            $table->foreignId('sender_id')->nullable()->constrained('users'); // Optional: Who triggered the notification
            $table->string('type'); // e.g., 'item_request', 'system_update', etc.
            $table->string('title')->nullable(); // Short notification title
            $table->text('message'); // Notification body/message
            $table->string('reference_number')->nullable(); // Transaction number (e.g., for item requests)
            $table->json('data')->nullable(); // Optional extra data for flexibility (e.g., serialized info, links, custom fields)
            $table->boolean('is_read')->default(false); // Read/Unread status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
