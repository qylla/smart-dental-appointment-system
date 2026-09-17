<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * What the dentist recorded after seeing the patient.
     * One consultation per appointment, which is what builds up the
     * patient history shown in the doctor portal.
     */
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('appointment_id')
                  ->unique()
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('doctor_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('diagnosis')->nullable();
            $table->text('treatment')->nullable();

            $table->enum('followup', ['none', '1w', '2w', '1m', '6m'])->default('none');
            $table->enum('outcome', ['resolved', 'ongoing', 'referred'])->nullable();

            // Visible to clinical staff only, never shown to the patient.
            $table->text('private_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};