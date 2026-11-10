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
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'nik')) {
                $table->string('nik', 30)->nullable()->after('birth_date');
            }
            if (! Schema::hasColumn('students', 'religion')) {
                $table->string('religion', 50)->nullable()->after('birth_date');
            }
            if (! Schema::hasColumn('students', 'father_name')) {
                $table->string('father_name', 255)->nullable()->after('address');
            }
            if (! Schema::hasColumn('students', 'mother_name')) {
                $table->string('mother_name', 255)->nullable()->after('father_name');
            }
            if (! Schema::hasColumn('students', 'previous_school')) {
                $table->string('previous_school', 255)->nullable()->after('mother_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['nik', 'religion', 'father_name', 'mother_name', 'previous_school'] as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
