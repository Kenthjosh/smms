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
        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'deleted_at')) {
                $table->softDeletes();
            }

            if (! Schema::hasColumn('applications', 'score')) {
                $table->decimal('score', 5, 2)->nullable()->after('reviewed_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'score')) {
                $table->dropColumn('score');
            }

            if (Schema::hasColumn('applications', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};


