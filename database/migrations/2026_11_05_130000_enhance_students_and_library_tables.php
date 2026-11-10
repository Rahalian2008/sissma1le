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
        // 1. Tambahkan kolom yang dibutuhkan pada tabel library_books
        if (Schema::hasTable('library_books')) {
            Schema::table('library_books', function (Blueprint $table) {
                if (! Schema::hasColumn('library_books', 'book_code')) {
                    $table->string('book_code', 50)->nullable()->after('id');
                }
                if (! Schema::hasColumn('library_books', 'book_type')) {
                    $table->string('book_type', 30)->default('fisik')->after('title');
                }
                if (! Schema::hasColumn('library_books', 'is_digital_flip')) {
                    $table->boolean('is_digital_flip')->default(false)->after('book_type');
                }
                if (! Schema::hasColumn('library_books', 'digital_file_path')) {
                    $table->string('digital_file_path', 255)->nullable()->after('is_digital_flip');
                }
                if (! Schema::hasColumn('library_books', 'digital_content')) {
                    $table->longText('digital_content')->nullable()->after('digital_file_path');
                }
                if (! Schema::hasColumn('library_books', 'rack_location')) {
                    $table->string('rack_location', 100)->nullable()->after('location_shelf');
                }
                if (! Schema::hasColumn('library_books', 'description')) {
                    $table->text('description')->nullable()->after('synopsis');
                }
                if (! Schema::hasColumn('library_books', 'pages_count')) {
                    $table->integer('pages_count')->default(0)->after('page_count');
                }
                if (! Schema::hasColumn('library_books', 'source')) {
                    $table->string('source', 100)->nullable()->after('publisher');
                }
                if (! Schema::hasColumn('library_books', 'loans_count')) {
                    $table->integer('loans_count')->default(0)->after('reads_count');
                }
            });
        }

        // 2. Tambahkan ddc_code pada library_categories
        if (Schema::hasTable('library_categories')) {
            Schema::table('library_categories', function (Blueprint $table) {
                if (! Schema::hasColumn('library_categories', 'ddc_code')) {
                    $table->string('ddc_code', 50)->nullable()->after('code');
                }
            });
        }

        // 3. Tambahkan member_id pada library_loans
        if (Schema::hasTable('library_loans')) {
            Schema::table('library_loans', function (Blueprint $table) {
                if (! Schema::hasColumn('library_loans', 'member_id')) {
                    $table->unsignedBigInteger('member_id')->nullable()->after('user_id');
                }
            });

            // Hubungkan member_id dari user_id jika ada relasi di library_members
            if (Schema::hasTable('library_members') && DB::getDriverName() === 'mysql') {
                DB::statement('
                    UPDATE library_loans 
                    JOIN library_members ON library_loans.user_id = library_members.user_id 
                    SET library_loans.member_id = library_members.id 
                    WHERE library_loans.member_id IS NULL
                ');
            }
        }

        // 4. Tambahkan kolom data siswa lengkap (SIKATAS & Kesiswaan) pada tabel students
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (! Schema::hasColumn('students', 'current_class_level')) {
                    $table->string('current_class_level', 10)->default('X')->after('class_id');
                }
                if (! Schema::hasColumn('students', 'current_rombel')) {
                    $table->string('current_rombel', 50)->nullable()->after('current_class_level');
                }
                if (! Schema::hasColumn('students', 'status')) {
                    $table->string('status', 30)->default('Aktif')->after('is_active');
                }
                if (! Schema::hasColumn('students', 'entry_path')) {
                    $table->string('entry_path', 191)->nullable()->after('status');
                }
                if (! Schema::hasColumn('students', 'school_origin')) {
                    $table->string('school_origin', 150)->nullable()->after('previous_school');
                }
                if (! Schema::hasColumn('students', 'is_pip')) {
                    $table->boolean('is_pip')->default(false)->after('entry_path');
                }
                if (! Schema::hasColumn('students', 'pip_number')) {
                    $table->string('pip_number', 191)->nullable()->after('is_pip');
                }
                if (! Schema::hasColumn('students', 'admission_date')) {
                    $table->date('admission_date')->nullable()->after('academic_year_id');
                }
                if (! Schema::hasColumn('students', 'admission_class')) {
                    $table->string('admission_class', 50)->nullable()->after('admission_date');
                }
                if (! Schema::hasColumn('students', 'junior_diploma_number')) {
                    $table->string('junior_diploma_number', 50)->nullable()->after('school_origin');
                }
                if (! Schema::hasColumn('students', 'junior_exam_number')) {
                    $table->string('junior_exam_number', 50)->nullable()->after('junior_diploma_number');
                }
                if (! Schema::hasColumn('students', 'diploma_number')) {
                    $table->string('diploma_number', 50)->nullable()->after('junior_exam_number');
                }
                if (! Schema::hasColumn('students', 'graduation_year')) {
                    $table->string('graduation_year', 10)->nullable()->after('diploma_number');
                }
                if (! Schema::hasColumn('students', 'photo_path')) {
                    $table->string('photo_path', 191)->nullable()->after('photo');
                }
                if (! Schema::hasColumn('students', 'qr_code_token')) {
                    $table->string('qr_code_token', 100)->nullable()->after('rfid_uid');
                }
                if (! Schema::hasColumn('students', 'notes')) {
                    $table->text('notes')->nullable()->after('address');
                }
                if (! Schema::hasColumn('students', 'residence_district')) {
                    $table->string('residence_district', 191)->nullable()->after('address');
                }
                if (! Schema::hasColumn('students', 'transportation')) {
                    $table->string('transportation', 191)->nullable()->after('residence_district');
                }
                if (! Schema::hasColumn('students', 'distance_to_school')) {
                    $table->string('distance_to_school', 191)->nullable()->after('transportation');
                }
                if (! Schema::hasColumn('students', 'special_needs')) {
                    $table->string('special_needs', 191)->default('Tidak Ada')->after('distance_to_school');
                }
                if (! Schema::hasColumn('students', 'living_with')) {
                    $table->string('living_with', 50)->default('Bersama Orang Tua')->after('special_needs');
                }
                if (! Schema::hasColumn('students', 'child_order')) {
                    $table->smallInteger('child_order')->unsigned()->nullable()->after('religion');
                }
                if (! Schema::hasColumn('students', 'siblings_count')) {
                    $table->smallInteger('siblings_count')->unsigned()->nullable()->after('child_order');
                }
                if (! Schema::hasColumn('students', 'blood_type')) {
                    $table->string('blood_type', 10)->default('Tidak Tahu')->after('siblings_count');
                }
                if (! Schema::hasColumn('students', 'height')) {
                    $table->smallInteger('height')->unsigned()->nullable()->after('blood_type');
                }
                if (! Schema::hasColumn('students', 'weight')) {
                    $table->smallInteger('weight')->unsigned()->nullable()->after('height');
                }
                if (! Schema::hasColumn('students', 'rt_rw')) {
                    $table->string('rt_rw', 20)->nullable()->after('address');
                }
                if (! Schema::hasColumn('students', 'postal_code')) {
                    $table->string('postal_code', 10)->nullable()->after('rt_rw');
                }
                if (! Schema::hasColumn('students', 'family_card_number')) {
                    $table->string('family_card_number', 30)->nullable()->after('nik');
                }
            });

            // Backfill current_class_level & current_rombel dari tabel school_classes
            if (Schema::hasTable('school_classes') && DB::getDriverName() === 'mysql') {
                DB::statement("
                    UPDATE students
                    JOIN school_classes ON students.class_id = school_classes.id
                    SET students.current_class_level = COALESCE(school_classes.grade, 'X'),
                        students.current_rombel = school_classes.name
                    WHERE students.current_rombel IS NULL OR students.current_rombel = ''
                ");
            }

            // Salin data detail siswa dari sikatas1 jika tabelnya ada
            if (DB::getDriverName() === 'mysql') {
                try {
                    $hasSikatas = DB::select("SHOW TABLES LIKE 'students'");
                    $hasSikatasDb = DB::select("SHOW DATABASES LIKE 'u432977099_sikatas1'");
                    if (! empty($hasSikatasDb)) {
                        DB::statement('
                            UPDATE students s
                            JOIN u432977099_sikatas1.students sk ON s.nis = sk.nis OR (s.nisn IS NOT NULL AND s.nisn = sk.nisn)
                            SET s.entry_path = COALESCE(s.entry_path, sk.entry_path),
                                s.school_origin = COALESCE(s.school_origin, sk.school_origin),
                                s.family_card_number = COALESCE(s.family_card_number, sk.family_card_number),
                                s.child_order = COALESCE(s.child_order, sk.child_order),
                                s.siblings_count = COALESCE(s.siblings_count, sk.siblings_count),
                                s.blood_type = COALESCE(s.blood_type, sk.blood_type),
                                s.height = COALESCE(s.height, sk.height),
                                s.weight = COALESCE(s.weight, sk.weight),
                                s.rt_rw = COALESCE(s.rt_rw, sk.rt_rw),
                                s.postal_code = COALESCE(s.postal_code, sk.postal_code),
                                s.junior_diploma_number = COALESCE(s.junior_diploma_number, sk.junior_diploma_number),
                                s.junior_exam_number = COALESCE(s.junior_exam_number, sk.junior_exam_number),
                                s.admission_date = COALESCE(s.admission_date, sk.admission_date),
                                s.admission_class = COALESCE(s.admission_class, sk.admission_class),
                                s.diploma_number = COALESCE(s.diploma_number, sk.diploma_number),
                                s.graduation_year = COALESCE(s.graduation_year, sk.graduation_year),
                                s.is_pip = COALESCE(s.is_pip, sk.is_pip),
                                s.pip_number = COALESCE(s.pip_number, sk.pip_number),
                                s.residence_district = COALESCE(s.residence_district, sk.residence_district),
                                s.transportation = COALESCE(s.transportation, sk.transportation),
                                s.distance_to_school = COALESCE(s.distance_to_school, sk.distance_to_school),
                                s.special_needs = COALESCE(s.special_needs, sk.special_needs),
                                s.living_with = COALESCE(s.living_with, sk.living_with)
                        ');
                    }
                } catch (Throwable) {
                    // Ignore cross-database sync issues if permission restricted
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
