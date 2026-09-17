<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the fields the Smart Dental Appointment System needs on top of
     * Laravel's default users table: a role (patient / admin / doctor) and
     * a contact number, since the clinic contacts patients by phone.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['patient', 'admin', 'doctor'])
                  ->default('patient')
                  ->after('email');

            $table->string('phone', 20)->nullable()->after('role');

            // Only relevant for doctors; shown to patients when booking.
            $table->string('specialisation')->nullable()->after('phone');

            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'phone', 'specialisation']);
        });
    }
};