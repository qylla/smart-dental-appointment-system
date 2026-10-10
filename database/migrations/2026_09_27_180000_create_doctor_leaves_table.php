<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Times a dentist is away (leave, course, half day).
     *
     * session = 'day'   -> away for the day session  (before 6 PM)
     * session = 'night' -> away for the night session (6 PM onwards)
     * session = null    -> away the whole day
     *
     * SlotService reads this table, so online booking, walk-in booking and
     * the landing page all stop offering that dentist at those times.
     */
    public function up(): void
    {
        Schema::create('doctor_leaves', function (Blueprint $table) {
            $table->id();

            $table->foreignId('doctor_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->date('date');
            $table->enum('session', ['day', 'night'])->nullable();
            $table->string('reason', 100)->nullable();

            $table->timestamps();

            $table->index(['date', 'doctor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_leaves');
    }
};