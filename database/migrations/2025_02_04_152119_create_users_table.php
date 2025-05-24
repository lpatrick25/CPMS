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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('last_name', 50);
            $table->string('extension_name', 6)->nullable();
            $table->string('contact_no', 20)->unique();
            $table->string('email', 50)->unique();
            $table->string('password');
            $table->enum('role', ['Custodian', 'President', 'Facilities In-charge', 'Equipment In-charge', 'Employee', 'System Admin']);
            $table->boolean('allow_login')->default(1);
            $table->string('department')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
