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
        Schema::create('item_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests');
            $table->foreignId('employee_id')->constrained('users');
            $table->foreignId('item_id')->constrained('items');
            $table->integer('quantity');
            $table->integer('release_quantity')->nullable();
            $table->date('date_requested');
            $table->enum('status', ['Custodian Approval', 'President Approval', 'Approved', 'Rejected', 'Released'])->default('Custodian Approval');
            $table->foreignId('approved_by_custodian')->nullable()->constrained('users');
            $table->foreignId('approved_by_president')->nullable()->constrained('users');
            $table->foreignId('released_by_custodian')->nullable()->constrained('users');
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_requests');
    }
};
