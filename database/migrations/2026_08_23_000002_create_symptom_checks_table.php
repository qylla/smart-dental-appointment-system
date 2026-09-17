<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stores every symptom check submitted through the AI symptom checker.
     *
     * user_id is nullable on purpose: the symptom checker is a public tool,
     * so guests can use it without an account. When a guest later registers
     * and books, their check can be linked back via guest_token.
     */
    public function up(): void
    {
        Schema::create('symptom_checks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            // Identifies a guest session before the user has an account.
            $table->string('guest_token', 64)->nullable()->index();

            // The symptom chips the patient selected, e.g. ["Toothache","Swollen gums"]
            $table->json('symptoms');

            // Free-text description the patient typed (or dictated).
            $table->text('description')->nullable();

            // Result of the analysis.
            $table->enum('severity', ['routine', 'urgent', 'critical'])->default('routine');
            $table->string('verdict')->nullable();
            $table->text('advice')->nullable();

            // Raw Gemini response kept for auditing / debugging the AI output.
            $table->json('ai_response')->nullable();

            $table->timestamps();

            $table->index(['severity', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('symptom_checks');
    }
};