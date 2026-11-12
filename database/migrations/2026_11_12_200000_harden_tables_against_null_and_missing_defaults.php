<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        // 1. library_loans: borrower_name, borrower_identifier, user_id
        if (Schema::hasTable('library_loans')) {
            Schema::table('library_loans', function (Blueprint $table) {
                if (! Schema::hasColumn('library_loans', 'borrower_name')) {
                    $table->string('borrower_name', 191)->nullable()->default('Peminjam');
                } else {
                    $table->string('borrower_name', 191)->nullable()->default('Peminjam')->change();
                }
                if (! Schema::hasColumn('library_loans', 'borrower_identifier')) {
                    $table->string('borrower_identifier', 50)->nullable()->default('-');
                } else {
                    $table->string('borrower_identifier', 50)->nullable()->default('-')->change();
                }
                if (! Schema::hasColumn('library_loans', 'borrower_type')) {
                    $table->string('borrower_type', 50)->nullable()->default('student');
                }
                if (! Schema::hasColumn('library_loans', 'borrower_class_or_dept')) {
                    $table->string('borrower_class_or_dept', 100)->nullable();
                }
                if (Schema::hasColumn('library_loans', 'notes')) {
                    $table->text('notes')->nullable()->change();
                }
                if (Schema::hasColumn('library_loans', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->change();
                }
            });
        }

        // 2. library_books: slug, pages_count, page_count, language, curriculum, category_name
        if (Schema::hasTable('library_books')) {
            Schema::table('library_books', function (Blueprint $table) {
                if (Schema::hasColumn('library_books', 'slug')) {
                    $table->string('slug', 255)->nullable()->change();
                }
                if (Schema::hasColumn('library_books', 'pages_count')) {
                    $table->integer('pages_count')->nullable()->default(0)->change();
                }
                if (Schema::hasColumn('library_books', 'page_count')) {
                    $table->unsignedInteger('page_count')->nullable()->default(0)->change();
                }
                if (Schema::hasColumn('library_books', 'language')) {
                    $table->string('language', 50)->nullable()->default('Indonesia')->change();
                }
                if (Schema::hasColumn('library_books', 'curriculum')) {
                    $table->string('curriculum', 50)->nullable()->default('Kurikulum Merdeka')->change();
                }
                if (Schema::hasColumn('library_books', 'category_name')) {
                    $table->string('category_name', 100)->nullable()->default('Umum')->change();
                }
            });
        }

        // 3. guests: identity_number, phone, institution, destination_department, purpose
        if (Schema::hasTable('guests')) {
            Schema::table('guests', function (Blueprint $table) {
                if (Schema::hasColumn('guests', 'identity_number')) {
                    $table->string('identity_number', 50)->nullable()->default('-')->change();
                }
                if (Schema::hasColumn('guests', 'phone')) {
                    $table->string('phone', 30)->nullable()->default('-')->change();
                }
                if (Schema::hasColumn('guests', 'institution')) {
                    $table->string('institution', 191)->nullable()->default('Masyarakat Umum')->change();
                }
                if (Schema::hasColumn('guests', 'destination_department')) {
                    $table->string('destination_department', 60)->nullable()->default('Tata Usaha')->change();
                }
                if (Schema::hasColumn('guests', 'purpose')) {
                    $table->string('purpose', 100)->nullable()->default('Kunjungan')->change();
                }
            });
        }

        // 4. picket_schedules: day_name, person_name
        if (Schema::hasTable('picket_schedules')) {
            Schema::table('picket_schedules', function (Blueprint $table) {
                if (Schema::hasColumn('picket_schedules', 'day_name')) {
                    $table->string('day_name', 20)->nullable()->default('Senin')->change();
                }
                if (Schema::hasColumn('picket_schedules', 'person_name')) {
                    $table->string('person_name', 150)->nullable()->default('Petugas Piket')->change();
                }
            });
        }

        // 5. student_legalizations: document_type
        if (Schema::hasTable('student_legalizations')) {
            Schema::table('student_legalizations', function (Blueprint $table) {
                if (Schema::hasColumn('student_legalizations', 'document_type')) {
                    $table->string('document_type', 100)->nullable()->default('Ijazah')->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op down to preserve data integrity
    }
};
