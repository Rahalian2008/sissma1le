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
        Schema::table('academic_years', function (Blueprint $table) {
            $table->string('semester')->nullable()->default(null)->change();
        });

        if (Schema::hasTable('student_ai_raports')) {
            Schema::table('student_ai_raports', function (Blueprint $table) {
                if (! Schema::hasColumn('student_ai_raports', 'extracurricular_rate')) {
                    $table->decimal('extracurricular_rate', 5, 2)->default(0)->after('worship_keputrian_rate');
                }
                if (! Schema::hasColumn('student_ai_raports', 'extracurricular_notes')) {
                    $table->text('extracurricular_notes')->nullable()->after('extracurricular_rate');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->enum('semester', ['Ganjil', 'Genap'])->default('Ganjil')->change();
        });

        if (Schema::hasTable('student_ai_raports')) {
            Schema::table('student_ai_raports', function (Blueprint $table) {
                if (Schema::hasColumn('student_ai_raports', 'extracurricular_rate')) {
                    $table->dropColumn(['extracurricular_rate', 'extracurricular_notes']);
                }
            });
        }
    }
};
