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
        // 1. Tabel Jadwal Gelombang Pendaftaran SPMB
        if (! Schema::hasTable('spmb_waves')) {
            Schema::create('spmb_waves', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->unsignedInteger('wave_number')->default(1);
                $table->string('academic_year', 20)->default('2026/2027');
                $table->date('start_date');
                $table->date('end_date');
                $table->date('announcement_date')->nullable();
                $table->date('re_registration_start')->nullable();
                $table->date('re_registration_end')->nullable();
                $table->unsignedInteger('quota_seats')->default(180);
                $table->json('allowed_tracks')->nullable(); // Track IDs atau Kode Jalur yang dibuka pada gelombang ini
                $table->boolean('is_active')->default(true);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. Tambah Kolom Berkas & Nilai Rapor ke ppdb_registrations
        if (Schema::hasTable('ppdb_registrations')) {
            Schema::table('ppdb_registrations', function (Blueprint $table) {
                if (! Schema::hasColumn('ppdb_registrations', 'spmb_wave_id')) {
                    $table->foreignId('spmb_wave_id')->nullable()->after('spmb_track_id')->constrained('spmb_waves')->nullOnDelete();
                }

                // Berkas Utama Pendaftar
                if (! Schema::hasColumn('ppdb_registrations', 'ktp_parent_file')) {
                    $table->string('ktp_parent_file')->nullable()->after('parent_phone');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'kk_file')) {
                    $table->string('kk_file')->nullable()->after('ktp_parent_file');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'birth_cert_file')) {
                    $table->string('birth_cert_file')->nullable()->after('kk_file');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'pip_kip_file')) {
                    $table->string('pip_kip_file')->nullable()->after('birth_cert_file');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'tka_certificate_file')) {
                    $table->string('tka_certificate_file')->nullable()->after('tka_status');
                }

                // Entri Nilai Rapor SMP/MTs (Semester 1 - 5 Wajib, Semester 6 Opsional)
                if (! Schema::hasColumn('ppdb_registrations', 'report_score_sem1')) {
                    $table->decimal('report_score_sem1', 5, 2)->nullable()->after('average_score');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'report_score_sem2')) {
                    $table->decimal('report_score_sem2', 5, 2)->nullable()->after('report_score_sem1');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'report_score_sem3')) {
                    $table->decimal('report_score_sem3', 5, 2)->nullable()->after('report_score_sem2');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'report_score_sem4')) {
                    $table->decimal('report_score_sem4', 5, 2)->nullable()->after('report_score_sem3');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'report_score_sem5')) {
                    $table->decimal('report_score_sem5', 5, 2)->nullable()->after('report_score_sem4');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'report_score_sem6')) {
                    $table->decimal('report_score_sem6', 5, 2)->nullable()->after('report_score_sem5');
                }

                // Upload Berkas Rapor SMP/MTs (Semester 1 - 5 Wajib, Semester 6 Opsional)
                if (! Schema::hasColumn('ppdb_registrations', 'report_file_sem1')) {
                    $table->string('report_file_sem1')->nullable()->after('report_score_sem6');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'report_file_sem2')) {
                    $table->string('report_file_sem2')->nullable()->after('report_file_sem1');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'report_file_sem3')) {
                    $table->string('report_file_sem3')->nullable()->after('report_file_sem2');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'report_file_sem4')) {
                    $table->string('report_file_sem4')->nullable()->after('report_file_sem3');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'report_file_sem5')) {
                    $table->string('report_file_sem5')->nullable()->after('report_file_sem4');
                }
                if (! Schema::hasColumn('ppdb_registrations', 'report_file_sem6')) {
                    $table->string('report_file_sem6')->nullable()->after('report_file_sem5');
                }
            });
        }

        // 3. Seed Default Gelombang Pendaftaran
        if (Schema::hasTable('spmb_waves') && DB::table('spmb_waves')->count() === 0) {
            $tracks = DB::table('spmb_tracks')->pluck('id')->toArray();
            $tkaPrestasiAfirmasi = DB::table('spmb_tracks')
                ->where('name', 'like', '%TKA%')
                ->orWhere('name', 'like', '%Prestasi%')
                ->orWhere('name', 'like', '%Afirmasi%')
                ->pluck('id')
                ->toArray();

            $zonasiMutasi = DB::table('spmb_tracks')
                ->where('name', 'like', '%Zonasi%')
                ->orWhere('name', 'like', '%Perpindahan%')
                ->pluck('id')
                ->toArray();

            DB::table('spmb_waves')->insert([
                [
                    'name' => 'Gelombang 1 - Jalur Prestasi, Afirmasi & Tes Kemampuan Akademik (TKA)',
                    'wave_number' => 1,
                    'academic_year' => '2026/2027',
                    'start_date' => now()->toDateString(),
                    'end_date' => now()->addDays(20)->toDateString(),
                    'announcement_date' => now()->addDays(23)->toDateString(),
                    're_registration_start' => now()->addDays(24)->toDateString(),
                    're_registration_end' => now()->addDays(26)->toDateString(),
                    'quota_seats' => 180,
                    'allowed_tracks' => json_encode(! empty($tkaPrestasiAfirmasi) ? $tkaPrestasiAfirmasi : $tracks),
                    'is_active' => true,
                    'description' => 'Tahap 1 untuk pendaftar jalur Prestasi (Akademik/Non-Akademik), Afirmasi (KIP/PIP), dan Jalur Nilai Tes Kemampuan Akademik (TKA).',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Gelombang 2 - Jalur Zonasi Domisili & Perpindahan Tugas Orang Tua',
                    'wave_number' => 2,
                    'academic_year' => '2026/2027',
                    'start_date' => now()->addDays(28)->toDateString(),
                    'end_date' => now()->addDays(42)->toDateString(),
                    'announcement_date' => now()->addDays(45)->toDateString(),
                    're_registration_start' => now()->addDays(46)->toDateString(),
                    're_registration_end' => now()->addDays(48)->toDateString(),
                    'quota_seats' => 180,
                    'allowed_tracks' => json_encode(! empty($zonasiMutasi) ? $zonasiMutasi : $tracks),
                    'is_active' => true,
                    'description' => 'Tahap 2 untuk pendaftar jalur Zonasi Radius Domisili dan Jalur Perpindahan Tugas Orang Tua / Wali.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ppdb_registrations')) {
            Schema::table('ppdb_registrations', function (Blueprint $table) {
                $table->dropForeign(['spmb_wave_id']);
                $table->dropColumn([
                    'spmb_wave_id',
                    'ktp_parent_file',
                    'kk_file',
                    'birth_cert_file',
                    'pip_kip_file',
                    'tka_certificate_file',
                    'report_score_sem1',
                    'report_score_sem2',
                    'report_score_sem3',
                    'report_score_sem4',
                    'report_score_sem5',
                    'report_score_sem6',
                    'report_file_sem1',
                    'report_file_sem2',
                    'report_file_sem3',
                    'report_file_sem4',
                    'report_file_sem5',
                    'report_file_sem6',
                ]);
            });
        }

        Schema::dropIfExists('spmb_waves');
    }
};
