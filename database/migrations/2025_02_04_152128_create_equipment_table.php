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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('quantity');
            $table->integer('remaining_quantity');
            $table->boolean('has_serial')->default(false);
            $table->foreignId('equipment_in_charge')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('category', ['Tool', 'Equipment'])->default('Equipment'); // 👈 Added category
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
