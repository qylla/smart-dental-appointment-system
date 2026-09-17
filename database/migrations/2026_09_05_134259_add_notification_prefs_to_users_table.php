<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_new_appointment')->default(true)->after('specialisation');
            $table->boolean('notify_cancellation')->default(true)->after('notify_new_appointment');
            $table->boolean('notify_daily_summary')->default(false)->after('notify_cancellation');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['notify_new_appointment', 'notify_cancellation', 'notify_daily_summary']);
        });
    }
};