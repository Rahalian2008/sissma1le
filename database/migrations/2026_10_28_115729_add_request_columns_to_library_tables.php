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
        // 1. Tambah user_id pada tabel library_members untuk menautkan akun pengguna langsung
        if (Schema::hasTable('library_members')) {
            Schema::table('library_members', function (Blueprint $table) {
                if (! Schema::hasColumn('library_members', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('member_type')->constrained('users')->nullOnDelete();
                }
            });
        }

        // 2. Tambah kolom pengajuan online & persetujuan pada library_loans
        if (Schema::hasTable('library_loans')) {
            Schema::table('library_loans', function (Blueprint $table) {
                if (! Schema::hasColumn('library_loans', 'request_date')) {
                    $table->timestamp('request_date')->nullable()->after('user_id');
                }
                if (! Schema::hasColumn('library_loans', 'pickup_deadline')) {
                    $table->date('pickup_deadline')->nullable()->after('due_date');
                }
                if (! Schema::hasColumn('library_loans', 'approved_by')) {
                    $table->foreignId('approved_by')->nullable()->after('return_date')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('library_loans', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('approved_by');
                }
                if (! Schema::hasColumn('library_loans', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('approved_at');
                }
                if (! Schema::hasColumn('library_loans', 'request_notes')) {
                    $table->text('request_notes')->nullable()->after('rejection_reason');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('library_loans')) {
            Schema::table('library_loans', function (Blueprint $table) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn(['request_date', 'pickup_deadline', 'approved_by', 'approved_at', 'rejection_reason', 'request_notes']);
            });
        }

        if (Schema::hasTable('library_members')) {
            Schema::table('library_members', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn(['user_id']);
            });
        }
    }
};
