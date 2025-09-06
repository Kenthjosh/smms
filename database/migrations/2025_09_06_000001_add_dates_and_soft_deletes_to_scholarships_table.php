<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            if (! Schema::hasColumn('scholarships', 'start_date')) {
                $table->date('start_date')->nullable()->after('application_deadline');
            }

            if (! Schema::hasColumn('scholarships', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }

            if (! Schema::hasColumn('scholarships', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            if (Schema::hasColumn('scholarships', 'start_date')) {
                $table->dropColumn('start_date');
            }

            if (Schema::hasColumn('scholarships', 'end_date')) {
                $table->dropColumn('end_date');
            }

            if (Schema::hasColumn('scholarships', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
