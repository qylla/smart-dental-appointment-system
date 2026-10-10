<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Records how each appointment reached the clinic, so the admin can
     * separate online bookings from walk-ins and phone calls.
     *
     * Every existing row becomes 'online', which is correct: before the
     * walk-in form existed, online was the only way to book.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('channel', ['online', 'walk_in', 'phone'])
                  ->default('online')
                  ->after('status');

            $table->index('channel');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['channel']);
            $table->dropColumn('channel');
        });
    }
};