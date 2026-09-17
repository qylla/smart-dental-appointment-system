<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Core booking table.
     *
     * The unique index on (doctor_id, appointment_date, appointment_time)
     * is what actually prevents double booking at the database level, which
     * is the problem the clinic currently solves by hand at the front desk.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // The patient who booked.
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Assigned dentist. Nullable so admin can assign later.
            $table->foreignId('doctor_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Links the booking to whatever the patient reported beforehand.
            $table->foreignId('symptom_check_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->date('appointment_date');
            $table->time('appointment_time');

            $table->enum('status', [
                'pending',    // submitted, waiting for clinic approval
                'confirmed',  // approved by admin
                'completed',  // patient attended and was treated
                'cancelled',  // cancelled by patient or clinic
                'no_show',    // patient did not turn up
            ])->default('pending');

            // Carried over from the symptom check so admin can triage quickly.
            $table->enum('urgency', ['routine', 'urgent', 'critical'])->default('routine');

            $table->text('patient_note')->nullable();
            $table->text('admin_note')->nullable();

            // Supports the appointment reminder feature.
            $table->timestamp('reminder_sent_at')->nullable();

            $table->timestamps();

            // Prevents two patients holding the same dentist + slot.
            $table->unique(
                ['doctor_id', 'appointment_date', 'appointment_time'],
                'unique_doctor_slot'
            );

            $table->index(['appointment_date', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};