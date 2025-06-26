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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            // Reference to the user who has this shift
            $table->foreignId('user_id')
                  ->constrained('users') // Explicitly state the table being referenced
                  ->onDelete('cascade');

            // Start and end times for the shift
            $table->dateTime('start_time');
            $table->dateTime('end_time');

            // Location type for where the staff member is working
            $table->enum('location', ['home', 'office', 'holiday'])->default('office');

            // Standard timestamps for tracking created and updated times
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};