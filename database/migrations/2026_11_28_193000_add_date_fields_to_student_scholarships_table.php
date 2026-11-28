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
        if (Schema::hasTable('student_scholarships')) {
            Schema::table('student_scholarships', function (Blueprint $table) {
                if (! Schema::hasColumn('student_scholarships', 'proposal_date')) {
                    $table->date('proposal_date')->nullable()->after('period');
                }
                if (! Schema::hasColumn('student_scholarships', 'sk_date')) {
                    $table->date('sk_date')->nullable()->after('proposal_date');
                }
                if (! Schema::hasColumn('student_scholarships', 'disbursement_date')) {
                    $table->date('disbursement_date')->nullable()->after('sk_date');
                }
                if (! Schema::hasColumn('student_scholarships', 'sk_number')) {
                    $table->string('sk_number', 100)->nullable()->after('disbursement_date');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_scholarships')) {
            Schema::table('student_scholarships', function (Blueprint $table) {
                if (Schema::hasColumn('student_scholarships', 'proposal_date')) {
                    $table->dropColumn('proposal_date');
                }
                if (Schema::hasColumn('student_scholarships', 'sk_date')) {
                    $table->dropColumn('sk_date');
                }
                if (Schema::hasColumn('student_scholarships', 'disbursement_date')) {
                    $table->dropColumn('disbursement_date');
                }
                if (Schema::hasColumn('student_scholarships', 'sk_number')) {
                    $table->dropColumn('sk_number');
                }
            });
        }
    }
};
