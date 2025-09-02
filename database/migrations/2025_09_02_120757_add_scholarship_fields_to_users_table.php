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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('scholarship_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('role', ['admin', 'committee', 'student'])->default('student');
            $table->json('profile_data')->nullable(); // Flexible profile fields
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('scholarship_id');
            $table->dropColumn(['role', 'profile_data']);
        });
    }
};