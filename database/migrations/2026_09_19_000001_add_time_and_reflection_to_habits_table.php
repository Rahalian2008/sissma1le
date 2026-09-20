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
        Schema::table('habits', function (Blueprint $table) {
            $table->string('start_time', 10)->nullable()->after('target_time'); // e.g. 04:30
            $table->string('end_time', 10)->nullable()->after('start_time'); // e.g. 05:30
            $table->string('default_activity')->nullable()->after('end_time');
            $table->text('reflection_prompt')->nullable()->after('default_activity');
            $table->boolean('is_time_restricted')->default(true)->after('reflection_prompt');
        });

        // Populate initial time ranges & reflection prompts based on standard 7 Kebiasaan Anak Hebat
        $initialData = [
            1 => [
                'start_time' => '04:30',
                'end_time' => '05:30',
                'default_activity' => 'Bangun sebelum subuh, berdoa, dan merapikan tempat tidur',
                'reflection_prompt' => 'Apa yang Anda rasakan setelah berhasil bangun pagi tepat waktu hari ini?',
            ],
            2 => [
                'start_time' => '05:00',
                'end_time' => '06:00',
                'default_activity' => 'Sholat Subuh berjamaah / Berdoa pagi sesuai agama masing-masing',
                'reflection_prompt' => 'Hikmah atau doa kebaikan apa yang Anda panjatkan dalam ibadah pagi ini?',
            ],
            3 => [
                'start_time' => '05:30',
                'end_time' => '06:15',
                'default_activity' => 'Peregangan tubuh, jalan pagi, lari santai, atau senam ringan',
                'reflection_prompt' => 'Bagaimana kebugaran fisik dan kesiapan Anda menyongsong aktivitas sekolah hari ini?',
            ],
            4 => [
                'start_time' => '06:30',
                'end_time' => '07:00',
                'default_activity' => 'Sarapan pagi menu gizi seimbang dan minum air putih cukup',
                'reflection_prompt' => 'Menu sarapan sehat apa yang Anda konsumsi untuk energi belajar hari ini?',
            ],
            5 => [
                'start_time' => '19:00',
                'end_time' => '20:30',
                'default_activity' => 'Mengulang materi pelajaran KBM, mengerjakan tugas, atau membaca buku',
                'reflection_prompt' => 'Wawasan atau keterampilan baru apa yang paling berkesan Anda pelajari hari ini?',
            ],
            6 => [
                'start_time' => '16:00',
                'end_time' => '17:30',
                'default_activity' => 'Membantu pekerjaan orang tua di rumah atau berinteraksi santun di lingkungan',
                'reflection_prompt' => 'Kebaikan atau kepedulian apa yang telah Anda bagikan kepada orang di sekitar hari ini?',
            ],
            7 => [
                'start_time' => '21:30',
                'end_time' => '23:59',
                'default_activity' => 'Mematikan gawai/layar 30 menit sebelum tidur dan istirahat berkualitas',
                'reflection_prompt' => 'Apakah Anda siap beristirahat dengan tenang demi pemulihan tubuh untuk hari esok?',
            ],
        ];

        foreach ($initialData as $order => $data) {
            DB::table('habits')->where('order_number', $order)->update([
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'default_activity' => $data['default_activity'],
                'reflection_prompt' => $data['reflection_prompt'],
                'is_time_restricted' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            $table->dropColumn([
                'start_time',
                'end_time',
                'default_activity',
                'reflection_prompt',
                'is_time_restricted',
            ]);
        });
    }
};
