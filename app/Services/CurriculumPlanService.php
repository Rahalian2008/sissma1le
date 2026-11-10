<?php

namespace App\Services;

use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Schema;

class CurriculumPlanService
{
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_SUBMITTED = 'SUBMITTED';

    public const STATUS_REVISED = 'REVISED';

    public const STATUS_APPROVED = 'APPROVED';

    /**
     * Daftar status yang sah untuk dokumen perencanaan pembelajaran.
     *
     * @return array<string, string>
     */
    public static function getStatusList(): array
    {
        return [
            self::STATUS_DRAFT => 'Draf Guru',
            self::STATUS_SUBMITTED => 'Menunggu Peninjauan Kurikulum',
            self::STATUS_REVISED => 'Perlu Perbaikan / Revisi',
            self::STATUS_APPROVED => 'Disetujui & Siap KBM',
        ];
    }

    /**
     * Memeriksa apakah alur persetujuan bertingkat aktif di sekolah.
     */
    public function isApprovalRequired(): bool
    {
        $setting = SchoolSetting::get('lms_plan_approval_required');
        if ($setting !== null) {
            return (bool) $setting;
        }

        return (bool) config('services.ai.plan_approval_required', false);
    }

    /**
     * Skema struktur komponen baku Modul Ajar / RPP Kurikulum Merdeka SMAN 1 Lengkong.
     *
     * @return array<string, mixed>
     */
    public function getDefaultModuleStructure(): array
    {
        return [
            'identity' => [
                'phase' => 'E', // Fase E (Kelas X) atau Fase F (Kelas XI/XII)
                'time_allocation' => '2 x 45 Menit (1 Pertemuan)',
                'target_profile' => ['Bernalar Kritis', 'Kreatif', 'Gotong Royong'],
            ],
            'learning_steps' => [
                'introduction' => [
                    'duration' => 15,
                    'activities' => 'Orientasi peserta didik, apersepsi keterkaitan materi sebelumnya, dan pertanyaan pemantik.',
                ],
                'core' => [
                    'duration' => 60,
                    'activities' => 'Eksplorasi konsep terarah, pengerjaan LKPD kolaboratif berpasangan/kelompok, dan presentasi gagasan.',
                ],
                'closing' => [
                    'duration' => 15,
                    'activities' => 'Penyimpulan bersama guru, refleksi pemahaman siswa, dan penyampaian tugas mandiri/materi selanjutnya.',
                ],
            ],
            'assessment' => [
                'diagnostic' => 'Pertanyaan pemantik lisan saat apersepsi.',
                'formative' => 'Observasi keaktifan diskusi dan hasil LKPD siswa.',
                'summative' => 'Tes berbasis soal CBT atau penugasan proyek terstruktur.',
            ],
        ];
    }

    /**
     * Mendapatkan metrik kesiapan sistem perencanaan dan LMS (Health Check & Diagnostics).
     *
     * @return array<string, mixed>
     */
    public function getSystemHealth(): array
    {
        $hasCourseClasses = Schema::hasTable('course_classes');
        $hasMaterials = Schema::hasTable('lms_materials');
        $hasAssignments = Schema::hasTable('lms_assignments');
        $hasQuizzes = Schema::hasTable('lms_quizzes');
        $hasCurriculumUpgrade = Schema::hasTable('learning_objectives');

        return [
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'app_version' => config('app.version', '1.0.0'),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'database' => [
                'connection' => config('database.default'),
                'tables' => [
                    'course_classes' => $hasCourseClasses,
                    'lms_materials' => $hasMaterials,
                    'lms_assignments' => $hasAssignments,
                    'lms_quizzes' => $hasQuizzes,
                    'curriculum_upgrade_ready' => $hasCurriculumUpgrade,
                ],
            ],
            'settings' => [
                'approval_required' => $this->isApprovalRequired(),
                'ai_provider' => config('services.ai.provider', 'gemini'),
                'ai_timeout' => config('services.ai.timeout', 25),
            ],
        ];
    }
}
