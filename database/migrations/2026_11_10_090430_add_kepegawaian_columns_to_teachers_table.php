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
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('title_prefix', 50)->nullable()->after('name');
            $table->string('title_suffix', 100)->nullable()->after('title_prefix');
            $table->string('nuptk', 50)->nullable()->after('nip');
            $table->string('nik', 30)->nullable()->after('nuptk');
            $table->string('birth_place', 100)->nullable()->after('gender');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->text('address')->nullable()->after('phone');

            $table->string('employment_type', 50)->default('Guru')->after('title');
            $table->string('employment_status', 50)->default('PNS')->after('employment_type');
            $table->string('position', 150)->nullable()->after('employment_status');
            $table->string('teaching_subject', 100)->nullable()->after('position');
            $table->string('additional_tasks', 255)->nullable()->after('teaching_subject');

            $table->string('rank_group', 20)->nullable()->after('additional_tasks');
            $table->date('rank_tmt')->nullable()->after('rank_group');
            $table->date('position_tmt')->nullable()->after('rank_tmt');
            $table->integer('years_of_service_years')->nullable()->after('position_tmt');
            $table->integer('years_of_service_months')->nullable()->after('years_of_service_years');

            $table->string('education_level', 50)->nullable()->after('years_of_service_months');
            $table->string('education_institution', 255)->nullable()->after('education_level');
            $table->string('education_major', 255)->nullable()->after('education_institution');
            $table->string('education_grad_year', 10)->nullable()->after('education_major');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn([
                'title_prefix',
                'title_suffix',
                'nuptk',
                'nik',
                'birth_place',
                'birth_date',
                'address',
                'employment_type',
                'employment_status',
                'position',
                'teaching_subject',
                'additional_tasks',
                'rank_group',
                'rank_tmt',
                'position_tmt',
                'years_of_service_years',
                'years_of_service_months',
                'education_level',
                'education_institution',
                'education_major',
                'education_grad_year',
            ]);
        });
    }
};
