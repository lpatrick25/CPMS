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
        Schema::create('equipment_borrowing_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')->constrained('equipment_borrowings');
            $table->foreignId('serial_id')->constrained('equipment_serials');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_borrowing_serials');
    }
};
