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
            if (! Schema::hasColumn('students', 'face_photo_path')) {
                $table->string('face_photo_path')->nullable()->after('photo');
            }
            if (! Schema::hasColumn('students', 'face_descriptor')) {
                $table->longText('face_descriptor')->nullable()->after('face_photo_path');
            }
            if (! Schema::hasColumn('students', 'face_registered_at')) {
                $table->timestamp('face_registered_at')->nullable()->after('face_descriptor');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'face_registered_at')) {
                $table->dropColumn('face_registered_at');
            }
            if (Schema::hasColumn('students', 'face_descriptor')) {
                $table->dropColumn('face_descriptor');
            }
            if (Schema::hasColumn('students', 'face_photo_path')) {
                $table->dropColumn('face_photo_path');
            }
        });
    }
};
