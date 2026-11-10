<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('library_loans')) {
            Schema::table('library_loans', function (Blueprint $table) {
                if (! Schema::hasColumn('library_loans', 'fine_amount')) {
                    $table->decimal('fine_amount', 12, 2)->default(0)->after('status');
                }
                if (! Schema::hasColumn('library_loans', 'is_fine_paid')) {
                    $table->boolean('is_fine_paid')->default(false)->after('fine_amount');
                }
                if (! Schema::hasColumn('library_loans', 'request_date')) {
                    $table->dateTime('request_date')->nullable()->after('user_id');
                }
                if (! Schema::hasColumn('library_loans', 'pickup_deadline')) {
                    $table->date('pickup_deadline')->nullable()->after('due_date');
                }
                if (! Schema::hasColumn('library_loans', 'approved_at')) {
                    $table->dateTime('approved_at')->nullable()->after('approved_by');
                }
                if (! Schema::hasColumn('library_loans', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('approved_at');
                }
                if (! Schema::hasColumn('library_loans', 'request_notes')) {
                    $table->text('request_notes')->nullable()->after('rejection_reason');
                }
            });

            if (Schema::hasColumn('library_loans', 'fines_amount') && Schema::hasColumn('library_loans', 'fine_amount')) {
                DB::statement('UPDATE library_loans SET fine_amount = fines_amount WHERE fine_amount = 0 AND fines_amount > 0');
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('library_loans')) {
            Schema::table('library_loans', function (Blueprint $table) {
                $columns = ['fine_amount', 'is_fine_paid', 'request_date', 'pickup_deadline', 'approved_at', 'rejection_reason', 'request_notes'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('library_loans', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
