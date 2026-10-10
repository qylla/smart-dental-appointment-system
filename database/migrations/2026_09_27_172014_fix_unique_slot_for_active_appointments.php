<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The old unique index (doctor_id, date, time) also counted cancelled
     * appointments, so a cancelled booking kept "holding" its slot: the slot
     * looked free on the booking page, but the database refused a new
     * booking for the same dentist.
     *
     * Fix: a column MySQL computes from status itself. It is 1 for an active
     * booking (pending or confirmed) and NULL otherwise. MySQL never treats
     * two NULLs as equal in a unique index, so only active bookings clash.
     */
    public function up(): void
    {
        // Add the new column and index FIRST. The doctor_id foreign key needs
        // an index that starts with doctor_id, so the old one can only be
        // dropped once the new one exists.
        Schema::table('appointments', function (Blueprint $table) {
            $table->tinyInteger('active_slot')
                  ->nullable()
                  ->storedAs("CASE WHEN status IN ('pending','confirmed') THEN 1 ELSE NULL END")
                  ->after('status');

            $table->unique(
                ['doctor_id', 'appointment_date', 'appointment_time', 'active_slot'],
                'unique_active_doctor_slot'
            );
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique('unique_doctor_slot');
        });
    }

    /**
     * Note: this fails if two appointments now share a dentist and slot
     * (e.g. one cancelled, one active). Clean those up before rolling back.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->unique(
                ['doctor_id', 'appointment_date', 'appointment_time'],
                'unique_doctor_slot'
            );
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique('unique_active_doctor_slot');
            $table->dropColumn('active_slot');
        });
    }
};