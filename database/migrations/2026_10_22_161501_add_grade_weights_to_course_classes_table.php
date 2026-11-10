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
        Schema::table('course_classes', function (Blueprint $table) {
            $table->decimal('weight_material', 5, 2)->default(20.00)->after('schedules');
            $table->decimal('weight_assignment', 5, 2)->default(30.00)->after('weight_material');
            $table->decimal('weight_quiz', 5, 2)->default(30.00)->after('weight_assignment');
            $table->decimal('weight_attendance', 5, 2)->default(20.00)->after('weight_quiz');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropColumn([
                'weight_material',
                'weight_assignment',
                'weight_quiz',
                'weight_attendance',
            ]);
        });
    }
};
