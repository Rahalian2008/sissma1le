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
        if (! Schema::hasTable('spmb_tracks')) {
            Schema::create('spmb_tracks', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('code', 50)->unique();
                $table->decimal('quota_percentage', 5, 2)->default(0.00);
                $table->integer('quota_seats')->default(0);
                $table->text('description')->nullable();
                $table->text('requirements')->nullable();
                $table->boolean('is_tka')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('ppdb_registrations') && ! Schema::hasColumn('ppdb_registrations', 'spmb_track_id')) {
            Schema::table('ppdb_registrations', function (Blueprint $table) {
                $table->foreignId('spmb_track_id')->nullable()->after('selection_track')->constrained('spmb_tracks')->nullOnDelete();
            });
        }

        // Seed 5 Jalur Baku SPMB Sesuai Aturan Terbaru + Jalur Nilai TKA
        $defaultTracks = [
            [
                'name' => 'Jalur Zonasi',
                'code' => 'zonasi',
                'quota_percentage' => 50.00,
                'quota_seats' => 180,
                'description' => 'Jalur penerimaan berdasarkan radius jarak tempat tinggal / domisili calon peserta didik ke sekolah sesuai Kartu Keluarga (KK) yang diterbitkan minimal 1 tahun.',
                'requirements' => "1. Kartu Keluarga (KK) min. 1 tahun\n2. Akta Kelahiran\n3. Ijazah/SKL SMP/MTs\n4. Titik koordinat tempat tinggal",
                'is_tka' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Jalur Tes Kemampuan Akademik (TKA)',
                'code' => 'tka',
                'quota_percentage' => 20.00,
                'quota_seats' => 72,
                'description' => 'Jalur seleksi objektif melalui asesmen terstandar TKA (Literasi Bahasa & Penalaran, Numerasi Matematika, dan Literasi Sains) yang diselenggarakan oleh panitia sekolah.',
                'requirements' => "1. SKL/Surat Keterangan Siswa Kelas 9\n2. Rapor SMP semester 1-5\n3. Wajib mengikuti Asesmen TKA Berbasis Komputer di Lab SMAN 1 Lengkong",
                'is_tka' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Jalur Afirmasi KETM & Disabilitas',
                'code' => 'afirmasi',
                'quota_percentage' => 15.00,
                'quota_seats' => 54,
                'description' => 'Jalur diperuntukkan bagi calon peserta didik dari keluarga ekonomi tidak mampu (KETM) terdaftar DTKS/P3KE dan penyandang disabilitas.',
                'requirements' => "1. Kartu KIP / PKH / KKS terdaftar DTKS\n2. Kartu Keluarga & Akta Kelahiran\n3. Surat Pernyataan Tanggung Jawab Mutlak (SPTJM) Orang Tua",
                'is_tka' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Jalur Prestasi Nilai Rapor & Kejuaraan',
                'code' => 'prestasi',
                'quota_percentage' => 10.00,
                'quota_seats' => 36,
                'description' => 'Jalur seleksi berbasis nilai rata-rata rapor semester 1-5 dan/atau piagam sertifikat kejuaraan akademik, sains, seni budaya, dan olahraga min. tingkat kabupaten/kota.',
                'requirements' => "1. Buku Rapor SMP/MTs Semester 1 s.d. 5\n2. Sertifikat/Piagam Kejuaraan Asli (OSN, O2SN, FLS2N, dll.) jika ada",
                'is_tka' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Jalur Perpindahan Tugas Orang Tua & Anak Guru',
                'code' => 'mutasi',
                'quota_percentage' => 5.00,
                'quota_seats' => 18,
                'description' => 'Jalur bagi anak dari orang tua yang berpindah tugas kedinasan (instansi pemerintah/BUMN/TNI/Polri) atau putra-putri pendidik dan tenaga kependidikan SMAN 1 Lengkong.',
                'requirements' => "1. SK Penugasan Mutasi dari Instansi/Lembaga berwenang\n2. Surat Keterangan Domisili baru dari instansi\n3. SK Mengajar bagi Anak Guru/Tendik",
                'is_tka' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($defaultTracks as $track) {
            $existing = DB::table('spmb_tracks')->where('code', $track['code'])->first();
            if (! $existing) {
                DB::table('spmb_tracks')->insert(array_merge($track, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('ppdb_registrations', 'spmb_track_id')) {
            Schema::table('ppdb_registrations', function (Blueprint $table) {
                $table->dropConstrainedForeignId('spmb_track_id');
            });
        }

        Schema::dropIfExists('spmb_tracks');
    }
};
