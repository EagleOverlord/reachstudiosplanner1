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
        Schema::table('shifts', function (Blueprint $table) {
            // Add type field to distinguish between work, holiday, and meeting
            $table->enum('type', ['work', 'holiday', 'meeting'])->default('work')->after('location');
            
            // Update location enum to include meeting
            $table->dropColumn('location');
        });
        
        // Add the updated location column
        Schema::table('shifts', function (Blueprint $table) {
            $table->enum('location', ['home', 'office', 'meeting'])->default('office')->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('location');
        });
        
        // Restore original location column
        Schema::table('shifts', function (Blueprint $table) {
            $table->enum('location', ['home', 'office', 'holiday'])->default('office')->after('end_time');
        });
    }
};
