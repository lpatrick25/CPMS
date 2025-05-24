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
        Schema::create('equipment_borrowings', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->foreignId('employee_id')->constrained('users');
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->integer('quantity')->nullable(); // Only for non-serialized items
            $table->date('date_of_usage');
            $table->date('date_of_return');
            $table->enum('status', ['In-charge Approval', 'President Approval', 'Approved', 'Rejected', 'Released', 'Returned'])->default('In-charge Approval');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('released_by')->nullable()->constrained('users');
            $table->foreignId('returned_by')->nullable()->constrained('users');
            $table->timestamp('released_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_borrowings');
    }
};
