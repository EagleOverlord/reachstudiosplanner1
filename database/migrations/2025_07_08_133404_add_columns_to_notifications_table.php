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
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('type')->after('id'); // e.g., 'user_created', 'schedule_updated'
            $table->string('title')->after('type');
            $table->text('message')->after('title');
            $table->json('data')->nullable()->after('message'); // Additional data like user_id, etc.
            $table->boolean('is_read')->default(false)->after('data');
            $table->timestamp('read_at')->nullable()->after('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['type', 'title', 'message', 'data', 'is_read', 'read_at']);
        });
    }
};
