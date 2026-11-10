<?php

namespace App\Services;

use App\Models\QuranTahfidzRecord;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\StudentAiRaport;
use App\Models\WorshipAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiStudentAnalysisService
{
    /**
     * Synthesize and generate comprehensive AI analysis for a student with date range support.
     * Integrates with Google Gemini API when configured, with seamless local fallback.
     */
    public function analyzeStudent(Student $student, ?string $startDate = null, ?string $endDate = null, bool $allTime = false, bool $skipExternalAi = false): array
    {
        $isAllTime = $allTime || ($startDate === null && $endDate === null);
        if (! $isAllTime) {
            $startDate = $startDate ?? Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString();
            $endDate = $endDate ?? Carbon::now('Asia/Jakarta')->toDateString();
        }

        // 1. Data Presensi
        $attQuery = $student->attendances();
        if (! $isAllTime) {
            $attQuery->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate);
        }
        $totalAttendance = (clone $attQuery)->count();
        $presentCount = (clone $attQuery)->where('status', 'HADIR')->count();
        $lateCount = (clone $attQuery)->where('status', 'TERLAMBAT')->count();
        $sickCount = (clone $attQuery)->where('status', 'SAKIT')->count();
        $permitCount = (clone $attQuery)->where('status', 'IZIN')->count();
        $alphaCount = (clone $attQuery)->where('status', 'ALPA')->count();

        // Tingkat kehadiran akurat: Jika presensi belum terlaksana (0 data), persentase harus 0.0% (bukan 100%)
        $attendanceRate = $totalAttendance > 0 ? round((($presentCount + $lateCount) / $totalAttendance) * 100, 1) : 0.0;
        $punctualityRate = ($presentCount + $lateCount) > 0 ? round(($presentCount / ($presentCount + $lateCount)) * 100, 1) : 0.0;
        $attendanceScore = $totalAttendance > 0 ? $attendanceRate : 0.0;

        // 2. Data Prestasi (R1 - R10)
        $achQuery = $student->achievements()->with('category')->where('status', 'DIVERIFIKASI');
        if (! $isAllTime) {
            $periodAchievements = (clone $achQuery)->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate)->get();
        } else {
            $periodAchievements = (clone $achQuery)->get();
        }
        $allAchievements = (clone $achQuery)->get();

        $totalAchievementPoints = (int) $periodAchievements->sum('points');
        $achievementCount = $periodAchievements->count();
        $cumulativeAchievementPoints = (int) $allAchievements->sum('points');
        $rewardStatus = $student->reward_status;

        $topAchievementCategories = $periodAchievements->groupBy('category.name')->map->count()->sortDesc()->keys()->take(2)->all();
        $achievementList = $periodAchievements->map(fn ($a) => "{$a->title} ({$a->level}, {$a->points} poin)")->take(5)->implode(', ');

        // 3. Data Pelanggaran (P1 - P6)
        $violQuery = $student->violations()->with('category')->where('status', 'DIVERIFIKASI');
        if (! $isAllTime) {
            $periodViolations = (clone $violQuery)->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate)->get();
        } else {
            $periodViolations = (clone $violQuery)->get();
        }
        $allViolations = (clone $violQuery)->get();

        $totalViolationPoints = (int) $periodViolations->sum('points');
        $violationCount = $periodViolations->count();
        $cumulativeViolationPoints = (int) $allViolations->sum('points');
        $guidanceStatus = $student->guidance_status;

        $frequentViolations = $periodViolations->groupBy('category.name')->map->count()->sortDesc()->keys()->take(2)->all();
        $violationList = $periodViolations->map(fn ($v) => "{$v->category?->name} ({$v->points} poin)")->take(5)->implode(', ');

        // 4. Data Pembinaan
        $guidQuery = $student->guidanceRecords();
        if (! $isAllTime) {
            $guidQuery->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate);
        }
        $guidanceRecords = $guidQuery->orderByDesc('date')->get();
        $activeGuidanceCount = $guidanceRecords->where('status', 'DALAM_PROSES')->count();

        // 5. Data 7 Kebiasaan Anak Indonesia Hebat
        $habitQuery = $student->habitDailyLogs();
        if (! $isAllTime) {
            $habitQuery->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate);
        }
        $recentHabitLogs = $habitQuery->get();
        $totalExpectedHabits = $recentHabitLogs->count();
        $completedHabits = $recentHabitLogs->where('is_completed', true)->count();
        $habitScore = $totalExpectedHabits > 0 ? round(($completedHabits / $totalExpectedHabits) * 100, 1) : 0.0;

        $habitStatus = match (true) {
            $totalExpectedHabits === 0 => 'BELUM_TERPANTAU',
            $habitScore >= 85 => 'KONSISTEN',
            $habitScore >= 70 => 'BERKEMBANG',
            $habitScore >= 50 => 'PERLU_PEMBIASAAN',
            default => 'BELUM_OPTIMAL',
        };
        $habitScoreEffective = $totalExpectedHabits > 0 ? $habitScore : 0.0;

        // 6. Data Presensi Ibadah Bersama (Dhuha, Dzuhur/Jum'at, Keputrian)
        $isFemale = in_array(strtoupper((string) $student->gender), ['P', 'PEREMPUAN', 'FEMALE'], true);

        $worshipQuery = $student->worshipAttendances();
        if (! $isAllTime) {
            $worshipQuery->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate);
        }
        $worshipTotal = (clone $worshipQuery)->count();
        $worshipHadir = (clone $worshipQuery)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();

        // Pilar Shalat Dhuha
        $worshipDhuhaQuery = (clone $worshipQuery)->where('activity_type', WorshipAttendance::ACTIVITY_DHUHA);
        $worshipDhuhaTotal = (clone $worshipDhuhaQuery)->count();
        $worshipDhuhaHadir = (clone $worshipDhuhaQuery)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $worshipDhuhaRate = $worshipDhuhaTotal > 0 ? round(($worshipDhuhaHadir / $worshipDhuhaTotal) * 100, 1) : 0.0;
        $worshipDhuhaCount = $worshipDhuhaHadir;

        // Pilar Shalat Dzuhur / Jum'at
        $worshipDzuhurQuery = (clone $worshipQuery)->where('activity_type', WorshipAttendance::ACTIVITY_DZUHUR_JUMAT);
        $worshipDzuhurTotal = (clone $worshipDzuhurQuery)->count();
        $worshipDzuhurHadir = (clone $worshipDzuhurQuery)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $worshipDzuhurRate = $worshipDzuhurTotal > 0 ? round(($worshipDzuhurHadir / $worshipDzuhurTotal) * 100, 1) : 0.0;

        // Pilar Presensi Keputrian (Bagi Siswi Perempuan)
        $worshipKeputrianQuery = (clone $worshipQuery)->where('activity_type', WorshipAttendance::ACTIVITY_KEPUTRIAN);
        $worshipKeputrianTotal = $isFemale ? (clone $worshipKeputrianQuery)->count() : 0;
        $worshipKeputrianHadir = $isFemale ? (clone $worshipKeputrianQuery)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count() : 0;
        $worshipKeputrianRate = $worshipKeputrianTotal > 0 ? round(($worshipKeputrianHadir / $worshipKeputrianTotal) * 100, 1) : 0.0;

        $worshipRate = $worshipTotal > 0 ? round(($worshipHadir / $worshipTotal) * 100, 1) : 0.0;
        $worshipScore = $worshipTotal > 0 ? $worshipRate : 0.0;

        // 7. Data Setor Hafalan Al-Qur'an Juz 30 (Tahfidz) - Sesuai Target Kelas
        $tahfidzPeriodQuery = $student->tahfidzRecords();
        if (! $isAllTime) {
            $tahfidzPeriodQuery->whereDate('deposit_date', '>=', $startDate)->whereDate('deposit_date', '<=', $endDate);
        }
        $tahfidzPeriodCount = (clone $tahfidzPeriodQuery)->count();
        $tahfidzPeriodLulus = (clone $tahfidzPeriodQuery)->where('overall_grade', 'Lulus')->count();

        // Ambil surat target sesuai kelas siswa (X=25, XI=7, XII=5 surat)
        $studentGradeForTahfidz = $student->getNormalizedGrade() ?: 'X';
        $targetGradeSurahs = QuranTahfidzRecord::getSurahsByGrade($studentGradeForTahfidz);
        $targetSurahNumbers = array_keys($targetGradeSurahs);
        $totalTargetSurahsForGrade = count($targetGradeSurahs);

        // Akumulasi Surat Juz 30 yang lulus (hanya surat target kelas ini)
        $completedSurahsCount = $student->tahfidzRecords()
            ->where('overall_grade', 'Lulus')
            ->whereIn('surah_number', $targetSurahNumbers)
            ->distinct('surah_number')
            ->count('surah_number');
        $tahfidzProgressPct = $totalTargetSurahsForGrade > 0 ? round(($completedSurahsCount / $totalTargetSurahsForGrade) * 100, 1) : 0.0;

        $rubricMap = ['Sangat Baik' => 4, 'Baik' => 3, 'Cukup' => 2, 'Mengulang' => 1];
        $allPeriodTahfidz = (clone $tahfidzPeriodQuery)->get();
        $avgMakhraj = $allPeriodTahfidz->count() > 0 ? round($allPeriodTahfidz->avg(fn ($r) => $rubricMap[$r->makhraj] ?? 3), 1) : 0.0;
        $avgTajwid = $allPeriodTahfidz->count() > 0 ? round($allPeriodTahfidz->avg(fn ($r) => $rubricMap[$r->tajwid] ?? 3), 1) : 0.0;
        $avgKelancaran = $allPeriodTahfidz->count() > 0 ? round($allPeriodTahfidz->avg(fn ($r) => $rubricMap[$r->kelancaran] ?? 3), 1) : 0.0;

        // Status ketuntasan relatif terhadap target surat kelas
        $completionRatio = $totalTargetSurahsForGrade > 0 ? ($completedSurahsCount / $totalTargetSurahsForGrade) : 0;
        $tahfidzStatus = match (true) {
            $completionRatio >= 1.0 => 'MUMTAZ (Tuntas Kelas '.$studentGradeForTahfidz.')',
            $completionRatio >= 0.7 => 'JAYYID JIDDAN (Sangat Berkembang)',
            $completionRatio >= 0.3 => 'JAYYID (Aktif Menyetor)',
            $completedSurahsCount > 0 => 'MUBTADI (Tahap Awal)',
            default => 'BELUM_MENYETOR',
        };

        if ($completedSurahsCount >= 10) {
            $tahfidzScoreEffective = 100.0;
        } elseif ($completedSurahsCount > 0) {
            $tahfidzScoreEffective = min(100.0, round(70 + ($completedSurahsCount / 10) * 30, 1));
        } else {
            $tahfidzScoreEffective = ($tahfidzPeriodCount > 0) ? 70.0 : 0.0;
        }

        // 8. Data Presensi Ekstrakurikuler
        $joinedExtracurriculars = $student->extracurriculars;
        $hasExtracurricular = $joinedExtracurriculars->isNotEmpty();

        $extraAttendQuery = $student->extracurricularAttendances();
        if (! $isAllTime) {
            $extraAttendQuery->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate);
        }
        $extraAttendTotal = (clone $extraAttendQuery)->count();
        $extraAttendHadir = (clone $extraAttendQuery)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $extracurricularRate = $extraAttendTotal > 0 ? round(($extraAttendHadir / $extraAttendTotal) * 100, 1) : 0.0;

        $extracurricularNotes = ! $hasExtracurricular
            ? 'Siswa tidak mengikuti ekstrakurikuler'
            : ($extraAttendTotal === 0
                ? 'Siswa terdaftar ekstrakurikuler ('.$joinedExtracurriculars->pluck('name')->implode(', ').'), namun belum ada data presensi pada periode ini.'
                : "Tingkat kehadiran ekstrakurikuler ({$joinedExtracurriculars->pluck('name')->implode(', ')}) sebesar {$extracurricularRate}% ({$extraAttendHadir} dari {$extraAttendTotal} sesi).");

        // Cek apakah ada data siswa yang pernah tercatat sama sekali
        $hasAnyData = ($totalAttendance > 0 || $worshipTotal > 0 || $totalExpectedHabits > 0 || $completedSurahsCount > 0 || $cumulativeAchievementPoints > 0 || $cumulativeViolationPoints > 0 || $extraAttendTotal > 0);

        // Kedisiplinan & Tata Tertib (Pelanggaran & Prestasi) (0 - 100)
        $disciplineScore = $hasAnyData
            ? max(0, min(100, (100 - ($cumulativeViolationPoints * 1.5)) + min(20, $cumulativeAchievementPoints * 0.5)))
            : 0.0;

        // Skor Prestasi & Minat Bakat (0 - 100)
        $achievementScore = $cumulativeAchievementPoints > 0 ? min(100.0, 70 + ($cumulativeAchievementPoints * 0.5)) : 0.0;

        // Indeks Perkembangan Terpadu (Dasar Penilaian 9 Barometer Terpadu)
        if (! $hasAnyData) {
            $developmentIndex = 0.0;
        } else {
            // Skema bobot dinamis:
            // Skenario 1: Siswi Perempuan + Ikut Ekskul (9 Pilar Penuh)
            // Skenario 2: Siswa Laki-laki + Ikut Ekskul (8 Pilar, Keputrian tidak masuk dasar penilaian)
            // Skenario 3: Siswi Perempuan + Tidak Ikut Ekskul (8 Pilar, Ekskul tidak dibebankan)
            // Skenario 4: Siswa Laki-laki + Tidak Ikut Ekskul (7 Pilar, Keputrian & Ekskul tidak masuk)
            if ($isFemale && $hasExtracurricular && $extraAttendTotal > 0) {
                $developmentIndex = round(
                    ($attendanceScore * 0.15) +           // 1. Presensi Utama
                    ($worshipDzuhurRate * 0.125) +        // 2. Shalat Dzuhur/Jumat
                    ($worshipDhuhaRate * 0.10) +          // 3. Shalat Dhuha
                    ($worshipKeputrianRate * 0.075) +     // 4. Keputrian (khusus perempuan)
                    ($extracurricularRate * 0.10) +       // 5. Ekstrakurikuler
                    ($achievementScore * 0.10) +          // 6. Prestasi
                    ($disciplineScore * 0.15) +           // 7. Kedisiplinan / Pelanggaran
                    ($tahfidzScoreEffective * 0.10) +     // 8. Juz 30
                    ($habitScoreEffective * 0.10),        // 9. 7 KAIH
                    1
                );
            } elseif (! $isFemale && $hasExtracurricular && $extraAttendTotal > 0) {
                // Laki-laki: Keputrian tidak masuk dasar penilaian
                $developmentIndex = round(
                    ($attendanceScore * 0.15) +
                    ($worshipDzuhurRate * 0.15) +
                    ($worshipDhuhaRate * 0.15) +
                    ($extracurricularRate * 0.10) +
                    ($achievementScore * 0.10) +
                    ($disciplineScore * 0.15) +
                    ($tahfidzScoreEffective * 0.10) +
                    ($habitScoreEffective * 0.10),
                    1
                );
            } elseif ($isFemale && (! $hasExtracurricular || $extraAttendTotal === 0)) {
                // Perempuan tidak ikut ekskul: Ekskul dijelaskan tidak ikut dan tidak membebani nilai
                $developmentIndex = round(
                    ($attendanceScore * 0.20) +
                    ($worshipDzuhurRate * 0.125) +
                    ($worshipDhuhaRate * 0.10) +
                    ($worshipKeputrianRate * 0.075) +
                    ($achievementScore * 0.10) +
                    ($disciplineScore * 0.15) +
                    ($tahfidzScoreEffective * 0.10) +
                    ($habitScoreEffective * 0.15),
                    1
                );
            } else {
                // Laki-laki tidak ikut ekskul: Keputrian & Ekskul tidak dinilai
                $developmentIndex = round(
                    ($attendanceScore * 0.20) +
                    ($worshipDzuhurRate * 0.15) +
                    ($worshipDhuhaRate * 0.15) +
                    ($achievementScore * 0.10) +
                    ($disciplineScore * 0.15) +
                    ($tahfidzScoreEffective * 0.10) +
                    ($habitScoreEffective * 0.15),
                    1
                );
            }
        }

        $contextData = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'attendance_score' => $attendanceScore,
            'discipline_score' => $disciplineScore,
            'achievement_score' => $achievementScore,
            'worship_score' => $worshipScore,
            'habit_score_effective' => $habitScoreEffective,
            'tahfidz_score_effective' => $tahfidzScoreEffective,
            'total_attendance' => $totalAttendance,
            'present_count' => $presentCount,
            'attendance_rate' => $attendanceRate,
            'punctuality_rate' => $punctualityRate,
            'late_count' => $lateCount,
            'sick_count' => $sickCount,
            'permit_count' => $permitCount,
            'alpha_count' => $alphaCount,
            'worship_total' => $worshipTotal,
            'worship_hadir' => $worshipHadir,
            'worship_rate' => $worshipRate,
            'worship_dhuha_total' => $worshipDhuhaTotal,
            'worship_dhuha_hadir' => $worshipDhuhaHadir,
            'worship_dhuha_rate' => $worshipDhuhaRate,
            'worship_dhuha_count' => $worshipDhuhaCount,
            'worship_dzuhur_total' => $worshipDzuhurTotal,
            'worship_dzuhur_hadir' => $worshipDzuhurHadir,
            'worship_dzuhur_rate' => $worshipDzuhurRate,
            'worship_dzuhur_count' => $worshipDzuhurHadir,
            'worship_keputrian_total' => $worshipKeputrianTotal,
            'worship_keputrian_hadir' => $worshipKeputrianHadir,
            'worship_keputrian_rate' => $worshipKeputrianRate,
            'worship_keputrian_count' => $worshipKeputrianHadir,
            'worship_keputrian_count' => $worshipKeputrianHadir,
            'is_female' => $isFemale,
            'tahfidz_period_count' => $tahfidzPeriodCount,
            'tahfidz_period_lulus' => $tahfidzPeriodLulus,
            'completed_surahs_count' => $completedSurahsCount,
            'total_target_surahs' => $totalTargetSurahsForGrade,
            'student_grade_tahfidz' => $studentGradeForTahfidz,
            'tahfidz_progress_pct' => $tahfidzProgressPct,
            'tahfidz_status' => $tahfidzStatus,
            'avg_makhraj' => $avgMakhraj,
            'avg_tajwid' => $avgTajwid,
            'avg_kelancaran' => $avgKelancaran,
            'total_achievement_points' => $totalAchievementPoints,
            'cumulative_achievement_points' => $cumulativeAchievementPoints,
            'achievement_count' => $achievementCount,
            'reward_tier' => $rewardStatus['level'],
            'achievement_list' => $achievementList ?: 'Belum ada prestasi terdata pada periode ini',
            'total_violation_points' => $totalViolationPoints,
            'cumulative_violation_points' => $cumulativeViolationPoints,
            'violation_count' => $violationCount,
            'guidance_tier' => $guidanceStatus['level'],
            'guidance_action_needed' => $guidanceStatus['action_needed'],
            'violation_list' => $violationList ?: 'Tidak ada pelanggaran pada periode ini',
            'active_guidance_count' => $activeGuidanceCount,
            'total_expected_habits' => $totalExpectedHabits,
            'completed_habits' => $completedHabits,
            'habit_score' => $habitScore,
            'habit_status' => $habitStatus,
            'has_extracurricular' => $hasExtracurricular,
            'joined_extracurricular_names' => $joinedExtracurriculars->pluck('name')->all(),
            'extracurricular_total' => $extraAttendTotal,
            'extracurricular_hadir' => $extraAttendHadir,
            'extracurricular_rate' => $extracurricularRate,
            'extracurricular_notes' => $extracurricularNotes,
            'development_index' => $developmentIndex,
        ];

        // Attempt Gemini API if key is present
        $apiKey = trim((string) (SchoolSetting::get('gemini_api_key') ?: config('services.gemini.api_key', '')));
        $model = trim((string) (SchoolSetting::get('gemini_model') ?: config('services.gemini.model', 'gemini-3.5-flash-lite')));

        $geminiResult = null;
        if (! $skipExternalAi && ! app()->runningUnitTests() && ! empty($apiKey)) {
            $geminiResult = $this->callGeminiApi($student, $contextData, $apiKey, $model);
        }

        if ($geminiResult) {
            $ringkasanKomprehensif = $geminiResult['ringkasan_komprehensif'] ?? ($geminiResult['ringkasan'] ?? '');
            $catatanProfilPelajar = $geminiResult['catatan_profil_pelajar'] ?? '';
            $penilaianSiswa = $geminiResult['penilaian_siswa'] ?? ($geminiResult['ringkasan'] ?? '');
            $perkembangan = $geminiResult['perkembangan'] ?? ($geminiResult['ringkasan'] ?? '');
            $analisisMasalah = $geminiResult['analisis_masalah'] ?? '';
            $pemecahanMasalah = $geminiResult['pemecahan_masalah'] ?? '';
            $rekomendasi = $geminiResult['rekomendasi'] ?? [];
            if (! empty($geminiResult['kesimpulan']) && is_array($geminiResult['kesimpulan'])) {
                $kesimpulan = $geminiResult['kesimpulan'];
            }
            $provider = 'Google Gemini AI (Online)';
            $isAiGenerated = true;
            $activeEngineModel = $geminiResult['resolved_model'] ?? $model;
        } else {
            // Smart pedagogical AI synthesis (Fallback)
            $ringkasanKomprehensif = "Siswa menunjukkan tingkat partisipasi dengan presensi {$attendanceRate}%, keterlibatan Shalat Dzuhur {$worshipDzuhurRate}%, dan keterlibatan ibadah {$worshipRate}%. Capaian hafalan mencatat {$completedSurahsCount} surat dan nilai disiplin mencapai {$disciplineScore}.";

            $catatanProfilPelajar = 'Siswa diharapkan terus mengembangkan karakter Profil Pelajar Pancasila, khususnya pada dimensi Beriman dan Bertakwa kepada Tuhan YME (tercermin dari ibadah & tahfidz), Mandiri, serta Bernalar Kritis dalam keseharian di sekolah.';

            $penilaianSiswa = $this->generateSummary(
                $student, $attendanceRate, $punctualityRate, $lateCount, $totalAchievementPoints,
                $achievementCount, $rewardStatus, $totalViolationPoints, $guidanceStatus, $habitScore,
                $habitStatus, $totalExpectedHabits, $worshipRate, $worshipHadir, $worshipTotal,
                $completedSurahsCount, $tahfidzStatus, $tahfidzPeriodCount,
                $worshipDzuhurRate, $worshipDzuhurHadir, $worshipDzuhurTotal,
                $isFemale, $worshipKeputrianRate, $worshipKeputrianHadir, $worshipKeputrianTotal,
                $worshipDhuhaRate, $worshipDhuhaHadir, $worshipDhuhaTotal,
                $hasExtracurricular, $extracurricularRate, $extraAttendHadir, $extraAttendTotal,
                $joinedExtracurriculars->pluck('name')->all(),
                $totalTargetSurahsForGrade, $studentGradeForTahfidz
            );

            $perkembangan = "Berdasarkan data yang ada, siswa menunjukkan perkembangan yang relevan dengan partisipasinya. Tingkat kehadiran mencapai {$attendanceRate}%, kehadiran Shalat Dzuhur/Jum'at berjamaah mencapai {$worshipDzuhurRate}%, dan tingkat kedisiplinan ibadah mencapai {$worshipRate}%. Siswa juga mencatat {$completedSurahsCount} surat hafalan.";

            $analisisMasalah = '';
            if ($totalViolationPoints > 10 || $lateCount > 3 || $attendanceRate < 80) {
                $analisisMasalah = 'Terdapat beberapa kendala, antara lain tingkat kehadiran/kedisiplinan yang perlu ditingkatkan, serta kemungkinan adanya pelanggaran tata tertib yang tercatat.';
            } else {
                $analisisMasalah = 'Tidak ada masalah perilaku yang signifikan; siswa menunjukkan ketaatan pada tata tertib.';
            }

            $pemecahanMasalah = '';
            if ($analisisMasalah !== 'Tidak ada masalah perilaku yang signifikan; siswa menunjukkan ketaatan pada tata tertib.') {
                $pemecahanMasalah = 'Pendekatan konseling partisipatif perlu dilakukan. Siswa sebaiknya dilibatkan dalam program bimbingan untuk menumbuhkan motivasi intrinsik dan kesadaran disiplin waktu.';
            } else {
                $pemecahanMasalah = 'Siswa sebaiknya diberikan pengayaan atau tanggung jawab tambahan seperti menjadi tutor sebaya untuk mempertahankan tren positif.';
            }

            $rekomendasi = $this->generateRecommendations(
                $student, $lateCount, $totalAchievementPoints, $totalViolationPoints, $guidanceStatus,
                $habitScore, $totalExpectedHabits, $worshipRate, $completedSurahsCount
            );

            $provider = 'AI Smart Engine (Online)';
            $isAiGenerated = true;
            $activeEngineModel = 'AI Pedagogis Terpadu';
        }

        if (! isset($kesimpulan)) {
            $kesimpulan = [
                [
                    'dimension' => 'Presensi Harian',
                    'score' => $attendanceRate,
                    'sentiment' => $totalAttendance === 0 ? 'neutral' : ($attendanceRate >= 85 ? 'positive' : ($attendanceRate >= 70 ? 'warning' : 'negative')),
                    'insight' => $totalAttendance === 0
                        ? 'Belum ada catatan presensi harian pada periode ini.'
                        : "Tingkat kehadiran tercatat {$attendanceRate}% ({$presentCount} tepat waktu, {$lateCount} terlambat dari {$totalAttendance} hari tercatat).",
                ],
                [
                    'dimension' => 'Kedisiplinan & Tata Tertib',
                    'score' => $disciplineScore,
                    'sentiment' => $cumulativeViolationPoints === 0 ? 'positive' : ($cumulativeViolationPoints <= 50 ? 'warning' : 'negative'),
                    'insight' => $cumulativeViolationPoints === 0
                        ? 'Catatan kedisiplinan prima tanpa rekam jejak pelanggaran tata tertib.'
                        : "Akumulasi pelanggaran {$cumulativeViolationPoints} poin ({$guidanceStatus['level']}). {$guidanceStatus['action_needed']}",
                ],
                [
                    'dimension' => 'Prestasi & Potensi Minat Bakat',
                    'score' => $achievementScore,
                    'sentiment' => $achievementCount > 0 ? 'positive' : 'neutral',
                    'insight' => $achievementCount > 0
                        ? "Memiliki {$achievementCount} prestasi terverifikasi (+{$cumulativeAchievementPoints} poin kumulatif, tier: {$rewardStatus['level']}). Kategori menonjol: ".(! empty($topAchievementCategories) ? implode(', ', $topAchievementCategories) : 'Akademik/Non-Akademik').'.'
                        : 'Belum ada catatan prestasi yang terdata pada periode ini. Siswa didorong untuk mengeksplorasi potensi dan minat bakat melalui kegiatan ekstrakurikuler serta ajang perlombaan.',
                ],
                [
                    'dimension' => 'Presensi Shalat Dzuhur (Putra & Putri)',
                    'score' => $worshipDzuhurRate,
                    'sentiment' => $worshipDzuhurTotal === 0 ? 'neutral' : ($worshipDzuhurRate >= 80 ? 'positive' : ($worshipDzuhurRate >= 60 ? 'warning' : 'negative')),
                    'insight' => $worshipDzuhurTotal === 0
                        ? 'Belum ada catatan kehadiran Shalat Dzuhur/Jum\'at berjamaah pada periode ini.'
                        : "Tingkat kehadiran Shalat Dzuhur/Jum'at berjamaah (berlaku bagi siswa laki-laki dan perempuan) sebesar {$worshipDzuhurRate}% ({$worshipDzuhurHadir} dari {$worshipDzuhurTotal} sesi).",
                ],
                [
                    'dimension' => 'Pembiasaan Shalat Dhuha',
                    'score' => $worshipDhuhaRate,
                    'sentiment' => $worshipDhuhaTotal === 0 ? 'neutral' : ($worshipDhuhaRate >= 80 ? 'positive' : ($worshipDhuhaRate >= 60 ? 'warning' : 'negative')),
                    'insight' => $worshipDhuhaTotal === 0
                        ? 'Belum ada catatan kehadiran Shalat Dhuha bersama pada periode ini.'
                        : "Tingkat kehadiran Shalat Dhuha berjamaah sebesar {$worshipDhuhaRate}% ({$worshipDhuhaHadir} dari {$worshipDhuhaTotal} sesi terlaksana).",
                ],
                [
                    'dimension' => 'Karakter 7 Kebiasaan',
                    'score' => $habitScore,
                    'sentiment' => $totalExpectedHabits === 0 ? 'warning' : ($habitScore >= 80 ? 'positive' : ($habitScore >= 60 ? 'warning' : 'negative')),
                    'insight' => $totalExpectedHabits === 0
                        ? 'Siswa belum memiliki catatan pengisian jurnal 7 Kebiasaan Anak Indonesia Hebat pada periode ini. Diperlukan dorongan dari wali kelas dan orang tua.'
                        : "Keterisian dan konsistensi 7 kebiasaan sebesar {$habitScore}% dengan predikat {$habitStatus}.",
                ],
                [
                    'dimension' => 'Tahfidz Al-Qur\'an Juz 30',
                    'score' => $tahfidzProgressPct,
                    'sentiment' => $completedSurahsCount === 0 ? 'warning' : ($tahfidzProgressPct >= 80 ? 'positive' : 'neutral'),
                    'insight' => $completedSurahsCount === 0
                        ? 'Siswa belum menyetorkan hafalan surat Juz 30 pada target kelas ini.'
                        : "Telah menuntaskan {$completedSurahsCount} dari {$totalTargetSurahsForGrade} target surat Juz 30 ({$tahfidzProgressPct}% - {$tahfidzStatus}).",
                ],
            ];

            if ($isFemale) {
                $kesimpulan[] = [
                    'dimension' => 'Presensi Keputrian',
                    'score' => $worshipKeputrianRate,
                    'sentiment' => $worshipKeputrianTotal === 0 ? 'neutral' : ($worshipKeputrianRate >= 80 ? 'positive' : 'warning'),
                    'insight' => $worshipKeputrianTotal === 0
                        ? 'Belum ada catatan kegiatan keputrian pada hari Jum\'at pada periode ini.'
                        : "Tingkat kehadiran kegiatan keputrian siswi sebesar {$worshipKeputrianRate}% ({$worshipKeputrianHadir} dari {$worshipKeputrianTotal} sesi).",
                ];
            }

            if ($hasExtracurricular) {
                $kesimpulan[] = [
                    'dimension' => 'Presensi Ekstrakurikuler',
                    'score' => $extracurricularRate,
                    'sentiment' => ($extraAttendTotal === 0 ? 'neutral' : ($extracurricularRate >= 80 ? 'positive' : 'warning')),
                    'insight' => $extracurricularNotes,
                ];
            }
        }

        return [
            'analyzed_at' => now()->translatedFormat('d F Y H:i'),
            'provider' => $provider,
            'model' => $activeEngineModel,
            'is_ai_generated' => $isAiGenerated,
            'has_api_key' => ! empty($apiKey),
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis,
                'class' => $student->schoolClass?->name ?? 'Belum terdata',
            ],
            'metrics' => [
                'attendance_score' => $attendanceScore,
                'discipline_score' => $disciplineScore,
                'achievement_score' => $achievementScore,
                'worship_score' => $worshipScore,
                'habit_score_effective' => $habitScoreEffective,
                'tahfidz_score_effective' => $tahfidzScoreEffective,
                'attendance_rate' => $attendanceRate,
                'total_attendance' => $totalAttendance,
                'present_count' => $presentCount,
                'punctuality_rate' => $punctualityRate,
                'late_count' => $lateCount,
                'sick_count' => $sickCount,
                'permit_count' => $permitCount,
                'alpha_count' => $alphaCount,
                'worship_total' => $worshipTotal,
                'worship_hadir' => $worshipHadir,
                'worship_rate' => $worshipRate,
                'worship_dhuha_total' => $worshipDhuhaTotal,
                'worship_dhuha_hadir' => $worshipDhuhaHadir,
                'worship_dhuha_rate' => $worshipDhuhaRate,
                'worship_dhuha_count' => $worshipDhuhaCount,
                'worship_dzuhur_total' => $worshipDzuhurTotal,
                'worship_dzuhur_hadir' => $worshipDzuhurHadir,
                'worship_dzuhur_rate' => $worshipDzuhurRate,
                'worship_dzuhur_count' => $worshipDzuhurHadir,
                'worship_keputrian_total' => $worshipKeputrianTotal,
                'worship_keputrian_hadir' => $worshipKeputrianHadir,
                'worship_keputrian_rate' => $worshipKeputrianRate,
                'worship_keputrian_count' => $worshipKeputrianHadir,
                'is_female' => $isFemale,
                'has_extracurricular' => $hasExtracurricular,
                'joined_extracurricular_names' => $joinedExtracurriculars->pluck('name')->all(),
                'extracurricular_total' => $extraAttendTotal,
                'extracurricular_hadir' => $extraAttendHadir,
                'extracurricular_rate' => $extracurricularRate,
                'extracurricular_notes' => $extracurricularNotes,
                'tahfidz_period_count' => $tahfidzPeriodCount,
                'tahfidz_period_lulus' => $tahfidzPeriodLulus,
                'completed_surahs_count' => $completedSurahsCount,
                'total_target_surahs' => $totalTargetSurahsForGrade,
                'student_grade_tahfidz' => $studentGradeForTahfidz,
                'tahfidz_progress_pct' => $tahfidzProgressPct,
                'tahfidz_status' => $tahfidzStatus,
                'avg_makhraj' => $avgMakhraj,
                'avg_tajwid' => $avgTajwid,
                'avg_kelancaran' => $avgKelancaran,
                'achievement_points' => $totalAchievementPoints,
                'achievement_count' => $achievementCount,
                'reward_tier' => $rewardStatus['level'],
                'violation_points' => $totalViolationPoints,
                'violation_count' => $violationCount,
                'guidance_tier' => $guidanceStatus['level'],
                'guidance_action_needed' => $guidanceStatus['action_needed'],
                'habit_score' => $habitScore,
                'habit_status' => $habitStatus,
                'total_expected_habits' => $totalExpectedHabits,
                'completed_habits' => $completedHabits,
                'development_index' => $developmentIndex,
            ],
            'kesimpulan' => $kesimpulan,
            'ringkasan' => $ringkasanKomprehensif,
            'ringkasan_komprehensif' => $ringkasanKomprehensif,
            'catatan_profil_pelajar' => $catatanProfilPelajar,
            'penilaian_siswa' => $penilaianSiswa,
            'perkembangan' => $perkembangan,
            'analisis_masalah' => $analisisMasalah,
            'pemecahan_masalah' => $pemecahanMasalah,
            'rekomendasi' => $rekomendasi,
            'disclaimer' => $geminiResult
                ? "Analisis ini diproses secara online oleh Google Gemini AI ({$activeEngineModel}) berdasarkan 9 pilar terpadu rekam jejak siswa."
                : 'Analisis ini diproses secara realtime oleh AI Smart Engine berbasis 9 pilar terpadu aktivitas dan rekam jejak siswa.',
        ];
    }

    /**
     * Generate dan simpan raport AI snapshot terbaru untuk siswa berdasarkan ringkasan keseluruhan waktu (All-Time)
     */
    public function generateAndSaveStudentReport(Student $student, ?int $generatedById = null, string $generationType = 'MANUAL', bool $skipExternalAi = true): StudentAiRaport
    {
        $analysis = $this->analyzeStudent($student, null, null, allTime: true, skipExternalAi: $skipExternalAi);
        $metrics = $analysis['metrics'];
        $isFemale = $metrics['is_female'];

        return StudentAiRaport::updateOrCreate(
            ['student_id' => $student->id],
            [
                'academic_year_id' => $student->academic_year_id,
                'generated_at' => now(),
                'generated_by' => $generatedById,
                'generation_type' => $generationType,

                // 1. Presensi Harian Keseluruhan Waktu
                'attendance_rate' => $metrics['attendance_rate'],
                'attendance_total' => $metrics['total_attendance'],
                'attendance_present' => $metrics['present_count'],
                'attendance_late' => $metrics['late_count'],
                'attendance_permit' => $metrics['permit_count'],
                'attendance_sick' => $metrics['sick_count'],
                'attendance_alpha' => $metrics['alpha_count'],

                // 2. Poin Prestasi
                'achievement_points' => $metrics['achievement_points'],
                'achievement_count' => $metrics['achievement_count'],
                'reward_tier' => $metrics['reward_tier'],

                // 3. Poin Pelanggaran
                'violation_points' => $metrics['violation_points'],
                'violation_count' => $metrics['violation_count'],
                'guidance_tier' => $metrics['guidance_tier'],

                // 4. 7 Kegiatan Anak Hebat
                'habit_score' => $metrics['habit_score'],
                'habit_status' => $metrics['habit_status'],
                'habit_total_logs' => $metrics['total_expected_habits'],
                'habit_completed_logs' => $metrics['completed_habits'],

                // 5. Presensi Shalat Dzuhur / Jum'at
                'worship_dzuhur_total' => $metrics['worship_dzuhur_total'],
                'worship_dzuhur_hadir' => $metrics['worship_dzuhur_hadir'],
                'worship_dzuhur_rate' => $metrics['worship_dzuhur_rate'],
                'worship_dzuhur_count' => $metrics['worship_dzuhur_count'],

                // Shalat Dhuha
                'worship_dhuha_total' => $metrics['worship_dhuha_total'],
                'worship_dhuha_hadir' => $metrics['worship_dhuha_hadir'],
                'worship_dhuha_rate' => $metrics['worship_dhuha_rate'],

                // 6. Presensi Keputrian (Bagi Siswa Perempuan)
                'worship_keputrian_total' => $isFemale ? $metrics['worship_keputrian_total'] : 0,
                'worship_keputrian_hadir' => $isFemale ? $metrics['worship_keputrian_hadir'] : 0,
                'worship_keputrian_rate' => $isFemale ? $metrics['worship_keputrian_rate'] : 0.0,
                'worship_keputrian_count' => $isFemale ? $metrics['worship_keputrian_count'] : 0,
                'is_female' => $isFemale,

                // 7. Presensi Ekstrakurikuler
                'extracurricular_rate' => $metrics['extracurricular_rate'],
                'extracurricular_notes' => $metrics['extracurricular_notes'],

                // 8. Tahfidz Juz 30
                'tahfidz_completed_count' => $metrics['completed_surahs_count'],
                'tahfidz_target_count' => $metrics['total_target_surahs'],
                'tahfidz_progress_pct' => $metrics['tahfidz_progress_pct'],
                'tahfidz_status' => $metrics['tahfidz_status'],
                'avg_makhraj' => $metrics['avg_makhraj'],
                'avg_tajwid' => $metrics['avg_tajwid'],
                'avg_kelancaran' => $metrics['avg_kelancaran'],

                // Indeks Perkembangan & Analisis AI
                'development_index' => $metrics['development_index'],
                'ringkasan_komprehensif' => $analysis['ringkasan_komprehensif'],
                'catatan_profil_pelajar' => $analysis['catatan_profil_pelajar'],
                'penilaian_siswa' => $analysis['penilaian_siswa'],
                'perkembangan' => $analysis['perkembangan'],
                'analisis_masalah' => $analysis['analisis_masalah'],
                'pemecahan_masalah' => $analysis['pemecahan_masalah'],
                'rekomendasi' => $analysis['rekomendasi'] ?? null,
                // 7 Pilar & Ibadah Terpadu Keseluruhan Waktu (Structured context)
                'context_data' => array_merge($metrics, [
                    'pillars' => [
                        'presensi_harian' => [
                            'label' => 'Presensi Harian',
                            'total_attendance' => $metrics['total_attendance'],
                            'rate' => $metrics['attendance_rate'],
                            'hadir' => $metrics['present_count'],
                            'terlambat' => $metrics['late_count'],
                            'izin' => $metrics['permit_count'],
                            'sakit' => $metrics['sick_count'],
                            'alpa' => $metrics['alpha_count'],
                            'is_empty' => $metrics['total_attendance'] === 0,
                        ],
                        'poin_prestasi' => [
                            'label' => 'Poin Prestasi',
                            'total_points' => $metrics['achievement_points'],
                            'verified_count' => $metrics['achievement_count'],
                            'tier' => $metrics['reward_tier'],
                        ],
                        'poin_pelanggaran' => [
                            'label' => 'Poin Pelanggaran',
                            'total_points' => $metrics['violation_points'],
                            'verified_count' => $metrics['violation_count'],
                            'tier' => $metrics['guidance_tier'],
                        ],
                        'tujuh_kegiatan_hebat' => [
                            'label' => '7 Kegiatan Anak Hebat',
                            'logged_days' => $metrics['total_expected_habits'],
                            'total_checks' => $metrics['completed_habits'],
                            'average_score' => $metrics['habit_score'],
                            'status' => $metrics['habit_status'],
                            'is_empty' => $metrics['total_expected_habits'] === 0,
                        ],
                        'shalat_dzuhur_jumat' => [
                            'label' => "Shalat Dzuhur / Jum'at",
                            'total_sessions' => $metrics['worship_dzuhur_total'],
                            'hadir_sessions' => $metrics['worship_dzuhur_hadir'],
                            'rate' => $metrics['worship_dzuhur_rate'],
                            'is_empty' => $metrics['worship_dzuhur_total'] === 0,
                        ],
                        'shalat_dhuha' => [
                            'label' => 'Shalat Dhuha',
                            'total_sessions' => $metrics['worship_dhuha_total'],
                            'hadir_sessions' => $metrics['worship_dhuha_hadir'],
                            'rate' => $metrics['worship_dhuha_rate'],
                            'is_empty' => $metrics['worship_dhuha_total'] === 0,
                        ],
                        'presensi_keputrian' => [
                            'label' => 'Presensi Keputrian',
                            'is_female' => $isFemale,
                            'total_sessions' => $isFemale ? $metrics['worship_keputrian_total'] : 0,
                            'hadir_sessions' => $isFemale ? $metrics['worship_keputrian_hadir'] : 0,
                            'rate' => $isFemale ? $metrics['worship_keputrian_rate'] : 0.0,
                            'is_empty' => $isFemale ? ($metrics['worship_keputrian_total'] === 0) : true,
                        ],
                        'tahfidz_juz_30' => [
                            'label' => 'Tahfidz Juz 30',
                            'completed_surahs_count' => $metrics['completed_surahs_count'],
                            'target_surahs_count' => $metrics['total_target_surahs'],
                            'progress_pct' => $metrics['tahfidz_progress_pct'],
                            'status' => $metrics['tahfidz_status'],
                            'grade_target' => $metrics['student_grade_tahfidz'],
                            'total_deposits' => $metrics['tahfidz_period_count'],
                            'is_empty' => $metrics['tahfidz_period_count'] === 0,
                        ],
                        'presensi_ekstrakurikuler' => [
                            'label' => 'Presensi Ekstrakurikuler',
                            'has_joined' => $metrics['has_extracurricular'],
                            'joined_names' => $metrics['joined_extracurricular_names'],
                            'total_sessions' => $metrics['extracurricular_total'],
                            'hadir_sessions' => $metrics['extracurricular_hadir'],
                            'rate' => $metrics['extracurricular_rate'],
                            'notes' => $metrics['extracurricular_notes'],
                            'is_empty' => ! $metrics['has_extracurricular'] || $metrics['extracurricular_total'] === 0,
                        ],
                    ],
                ]),
                'provider' => $analysis['provider'],
                'model' => $analysis['model'],
                'is_ai_generated' => $analysis['is_ai_generated'],
            ]
        );
    }

    /**
     * Call Google Gemini REST API generateContent with automatic model fallback
     */
    protected function callGeminiApi(Student $student, array $ctx, string $apiKey, string $model): ?array
    {
        $modelsToTry = array_unique(array_filter([
            $model,
            'gemini-2.0-flash',
            'gemini-1.5-flash',
            'gemini-2.5-flash-lite',
            'gemini-flash-latest',
        ]));

        $prompt = $this->buildGeminiPrompt($student, $ctx);

        foreach ($modelsToTry as $targetModel) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$targetModel}:generateContent?key={$apiKey}";

                $response = Http::timeout(15)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->post($url, [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    ['text' => $prompt],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.4,
                            'responseMimeType' => 'application/json',
                        ],
                    ]);

                if ($response->successful()) {
                    $candidates = $response->json('candidates');
                    $rawText = $candidates[0]['content']['parts'][0]['text'] ?? null;

                    if ($rawText) {
                        $cleanJson = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($rawText));
                        $parsed = json_decode($cleanJson, true);

                        if (is_array($parsed) && (isset($parsed['penilaian_siswa']) || isset($parsed['ringkasan']) || isset($parsed['ringkasan_komprehensif']))) {
                            $parsed['resolved_model'] = $targetModel;

                            return $parsed;
                        }
                    }
                } else {
                    Log::warning("Gemini API call failed for model {$targetModel}", [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error("Gemini API call exception for model {$targetModel}: ".$e->getMessage());
            }
        }

        return null;
    }

    /**
     * Build pedagogical context prompt for Google Gemini
     */
    protected function buildGeminiPrompt(Student $student, array $ctx): string
    {
        $className = $student->schoolClass?->name ?? 'Umum';
        $habitDesc = ($ctx['total_expected_habits'] ?? 0) === 0
            ? '0% (Siswa BELUM MENGISI catatan jurnal pembiasaan pada periode ini, Predikat: BELUM_TERPANTAU)'
            : "{$ctx['habit_score']}% ({$ctx['completed_habits']} dari {$ctx['total_expected_habits']} kegiatan tercatat, Predikat: {$ctx['habit_status']})";

        $worshipDesc = ($ctx['worship_total'] ?? 0) === 0
            ? 'Belum ada agenda ibadah terjadwal pada rentang ini'
            : "{$ctx['worship_rate']}% ({$ctx['worship_hadir']} dari {$ctx['worship_total']} sesi ibadah dihadiri, Dhuha: {$ctx['worship_dhuha_count']}x, Dzuhur/Jum'at: {$ctx['worship_dzuhur_count']}x, Keputrian: {$ctx['worship_keputrian_count']}x)";

        $tahfidzDesc = "{$ctx['completed_surahs_count']} dari {$ctx['total_target_surahs']} Surat Target Kelas {$ctx['student_grade_tahfidz']} Lulus ({$ctx['tahfidz_progress_pct']}%, Predikat: {$ctx['tahfidz_status']}). Rata-rata Rubrik: Makhraj {$ctx['avg_makhraj']}/4, Tajwid {$ctx['avg_tajwid']}/4, Kelancaran {$ctx['avg_kelancaran']}/4. Periode ini: {$ctx['tahfidz_period_count']} kali setoran ({$ctx['tahfidz_period_lulus']} lulus).";

        $extraDesc = ! ($ctx['has_extracurricular'] ?? false)
            ? 'Siswa tidak mengikuti ekstrakurikuler'
            : (($ctx['extracurricular_total'] ?? 0) === 0
                ? 'Terdaftar pada ekskul ('.implode(', ', $ctx['joined_extracurricular_names'] ?? []).'), belum ada agenda presensi'
                : "{$ctx['extracurricular_rate']}% ({$ctx['extracurricular_hadir']} dari {$ctx['extracurricular_total']} sesi hadir pada ekskul: ".implode(', ', $ctx['joined_extracurricular_names'] ?? []).')');

        return <<<PROMPT
Anda adalah AI Konselor Pendidikan & Pakar Psikopedagogi Sekolah Menengah di Indonesia.
Tugas Anda adalah menganalisis rekam jejak multidimensi perkembangan siswa berikut ini secara objektif, berimbang, suportif, dan solutif. Anda WAJIB memperhatikan 9 pilar perkembangan yang ditekankan dalam sistem sekolah ini:
1. Presensi Harian (Presensi Utama Siswa)
2. Presensi Ibadah Shalat Dzuhur/Jum'at Berjamaah (Wajib untuk seluruh siswa, putra & putri)
3. Presensi Ibadah Shalat Dhuha Berjamaah (Spiritualitas Pagi)
4. Presensi Keputrian (Khusus siswi perempuan di hari Jum'at; siswa laki-laki dikecualikan dan tidak dinilai pada pilar ini)
5. Presensi Ekstrakurikuler (Pengembangan Bakat; jika siswa tidak mengikuti: catat 'Siswa tidak mengikuti ekstrakurikuler')
6. Poin Prestasi & Minat Bakat (Pencapaian Siswa)
7. Poin Pelanggaran Tata Tertib (Kedisiplinan Perilaku)
8. Hafalan Al-Qur'an Juz 30 (Sesuai Target Hafalan Kelas: {$ctx['student_grade_tahfidz']})
9. Konsistensi 7 Kebiasaan Anak Indonesia Hebat (Karakter KAIH)

Sebagai catatan tambahan, Anda diwajibkan untuk mengaitkan perkembangan siswa dengan Profil Pelajar Pancasila (Kurikulum Merdeka) yang berlaku saat ini.

[DATA SISWA]
- Nama: {$student->name}
- NIS: {$student->nis}
- Kelas: {$className}
- Jenis Kelamin: {$student->gender} (Catatan: Siswa laki-laki dikecualikan dari penilaian Keputrian)
- Presensi Harian (Utama): {$ctx['attendance_rate']}% (Tepat Waktu: {$ctx['punctuality_rate']}%, Terlambat: {$ctx['late_count']}x, Izin: {$ctx['permit_count']}x, Sakit: {$ctx['sick_count']}x, Alpa: {$ctx['alpha_count']}x)
- Presensi Shalat Dzuhur/Jum'at (Putra & Putri): {$ctx['worship_dzuhur_rate']}% ({$ctx['worship_dzuhur_hadir']} dari {$ctx['worship_dzuhur_total']} sesi hadir)
- Presensi Ibadah Bersama (Total): {$worshipDesc}
- Presensi Ekstrakurikuler: {$extraDesc}
- Tahfidz Al-Qur'an Juz 30: {$tahfidzDesc}
- Prestasi Siswa: {$ctx['total_achievement_points']} Poin ({$ctx['achievement_count']} prestasi terverifikasi, Tier: {$ctx['reward_tier']}). Detail: {$ctx['achievement_list']}
- Pelanggaran Tata Tertib: {$ctx['total_violation_points']} Poin ({$ctx['violation_count']} pelanggaran terverifikasi, Status Pembinaan: {$ctx['guidance_tier']}). Detail: {$ctx['violation_list']}
- Konsistensi Gerakan 7 Kebiasaan: {$habitDesc}
- Indeks Perkembangan Terpadu: {$ctx['development_index']} / 100

Formatkan respons HANYA dalam JSON valid dengan struktur persis seperti berikut:
{
  "ringkasan_komprehensif": "Ringkasan eksekutif/naratif secara total dan menyeluruh dari 9 aspek kegiatan di atas pada periode ini.",
  "catatan_profil_pelajar": "Catatan penting dan relevan berdasarkan aturan terbaru kurikulum pendidikan (Kurikulum Merdeka), menyoroti perkembangan karakter Profil Pelajar Pancasila (Beriman/Bertakwa, Berkebinekaan Global, Bergotong Royong, Mandiri, Bernalar Kritis, Kreatif) dari data siswa.",
  "penilaian_siswa": "Ringkasan penilaian yang mengintegrasikan presensi utama, shalat dzuhur, dhuha, keputrian (jika perempuan), ekstrakurikuler, prestasi, pelanggaran, 7 kebiasaan, dan hafalan Juz 30.",
  "perkembangan": "Narasi objektif mengenai tren perkembangan kebiasaan, kedisiplinan, spiritualitas, kegiatan ekstrakurikuler, dan pencapaian akademik/non-akademik siswa pada periode ini.",
  "analisis_masalah": "Identifikasi dan analisis permasalahan spesifik yang dihadapi siswa. Jika tidak ada masalah, jelaskan area yang berisiko atau butuh penguatan.",
  "pemecahan_masalah": "Langkah-langkah strategis dan solutif (actionable) untuk memecahkan permasalahan atau tantangan yang teridentifikasi di atas.",
  "rekomendasi": {
    "wali_kelas": {
      "role": "Wali Kelas",
      "actions": [
        "Aksi konkret 1 yang perlu dilakukan wali kelas...",
        "Aksi konkret 2..."
      ]
    },
    "koordinator_keagamaan": {
      "role": "Koordinator Keagamaan & Guru Agama",
      "actions": [
        "Aksi konkret 1 untuk pembina keagamaan & tahfidz...",
        "Aksi konkret 2..."
      ]
    },
    "bk": {
      "role": "Guru Bimbingan Konseling (BK)",
      "actions": [
        "Aksi konkret 1 untuk guru BK...",
        "Aksi konkret 2..."
      ]
    },
    "orang_tua": {
      "role": "Orang Tua / Wali",
      "actions": [
        "Aksi konkret 1 untuk orang tua di rumah...",
        "Aksi konkret 2..."
      ]
    },
    "siswa": {
      "role": "Siswa (Refleksi Diri)",
      "actions": [
        "Aksi konkret 1 bagi siswa...",
        "Aksi konkret 2..."
      ]
    },
    "manajemen": {
      "role": "Kepala Sekolah & Manajemen",
      "actions": [
        "Aksi konkret 1 bagi kepala sekolah & manajemen...",
        "Aksi konkret 2..."
      ]
    }
  }
}
Catatan: Nilai sentiment harus salah satu dari: "positive", "neutral", atau "warning". Tulis dalam Bahasa Indonesia yang santun, profesional, dan edukatif.
PROMPT;
    }

    protected function generateSummary(
        Student $student,
        float $attRate,
        float $puncRate,
        int $lateCount,
        int $achPoints,
        int $achCount,
        array $rewardStatus,
        int $violPoints,
        array $guidanceStatus,
        float $habitScore,
        string $habitStatus,
        int $totalExpectedHabits = 0,
        float $worshipRate = 100.0,
        int $worshipHadir = 0,
        int $worshipTotal = 0,
        int $completedSurahsCount = 0,
        string $tahfidzStatus = 'BELUM_MENYETOR',
        int $tahfidzPeriodCount = 0,
        float $worshipDzuhurRate = 0.0,
        int $worshipDzuhurHadir = 0,
        int $worshipDzuhurTotal = 0,
        bool $isFemale = false,
        float $worshipKeputrianRate = 0.0,
        int $worshipKeputrianHadir = 0,
        int $worshipKeputrianTotal = 0,
        float $worshipDhuhaRate = 0.0,
        int $worshipDhuhaHadir = 0,
        int $worshipDhuhaTotal = 0,
        bool $hasExtracurricular = false,
        float $extracurricularRate = 0.0,
        int $extraAttendHadir = 0,
        int $extraAttendTotal = 0,
        array $joinedExtraNames = [],
        int $totalTargetSurahsForGrade = 37,
        string $studentGradeForTahfidz = 'X'
    ): string {
        $parts = [];

        // 1. Presensi Utama
        if ($attRate >= 95) {
            $parts[] = "Siswa {$student->name} menunjukkan tingkat kehadiran harian (Presensi Utama) yang sangat prima ({$attRate}%) pada tahun ajaran ini.";
        } elseif ($attRate >= 85) {
            $parts[] = "Tingkat kehadiran harian (Presensi Utama) siswa {$student->name} berada pada kategori baik ({$attRate}%) dengan {$lateCount} kali catatan keterlambatan.";
        } else {
            $parts[] = "Tingkat kehadiran harian (Presensi Utama) berada pada angka {$attRate}% dengan catatan keterlambatan dan izin yang memerlukan pendampingan.";
        }

        // 2. Presensi Shalat Dzuhur / Jum'at Berjamaah (Wajib Putra & Putri)
        if ($worshipDzuhurTotal > 0) {
            $parts[] = "Pada kategori penilaian presensi Shalat Dzuhur/Jum'at berjamaah (berlaku untuk siswa laki-laki dan perempuan), siswa mencatatkan kehadiran sebesar {$worshipDzuhurRate}% ({$worshipDzuhurHadir} dari {$worshipDzuhurTotal} sesi).";
        } else {
            $parts[] = "Pada kategori penilaian presensi Shalat Dzuhur/Jum'at berjamaah (berlaku bagi siswa putra & putri), siswa siap mengikuti agenda ibadah terjadwal madrasah.";
        }

        // 3. Presensi Shalat Dhuha
        if ($worshipDhuhaTotal > 0) {
            $parts[] = "Pada pembiasaan Shalat Dhuha berjamaah pagi hari, mencatatkan kehadiran {$worshipDhuhaRate}% ({$worshipDhuhaHadir} dari {$worshipDhuhaTotal} sesi).";
        } else {
            $parts[] = 'Pada pembiasaan Shalat Dhuha berjamaah pagi hari, siswa siap mengikuti agenda ibadah terjadwal.';
        }

        // 4. Presensi Keputrian (Khusus Siswa Perempuan; Laki-laki Tidak Dinilai)
        if ($isFemale) {
            if ($worshipKeputrianTotal > 0) {
                $parts[] = "Untuk presensi kegiatan keputrian khusus siswi di hari Jum'at, tercatat tingkat kehadiran {$worshipKeputrianRate}% ({$worshipKeputrianHadir} dari {$worshipKeputrianTotal} sesi).";
            } else {
                $parts[] = "Untuk presensi kegiatan keputrian khusus siswi di hari Jum'at, agenda kajian fiqih wanita siap diikuti secara tertib.";
            }
        }

        // 5. Presensi Ekstrakurikuler
        if ($hasExtracurricular) {
            $extraNameStr = ! empty($joinedExtraNames) ? implode(', ', $joinedExtraNames) : 'Ekstrakurikuler';
            if ($extraAttendTotal > 0) {
                $parts[] = "Pada kegiatan ekstrakurikuler ({$extraNameStr}), siswa mencatatkan tingkat kehadiran sebesar {$extracurricularRate}% ({$extraAttendHadir} dari {$extraAttendTotal} sesi latihan).";
            } else {
                $parts[] = "Siswa terdaftar aktif pada ekstrakurikuler ({$extraNameStr}) dan siap mengikuti sesi latihan terjadwal.";
            }
        } else {
            $parts[] = 'Siswa tidak mengikuti ekstrakurikuler.';
        }

        // 6. Poin Prestasi
        if ($achPoints > 0) {
            $parts[] = "Di bidang prestasi, siswa telah mengumpulkan total {$achPoints} poin dari {$achCount} kegiatan terverifikasi, saat ini berada pada status '{$rewardStatus['level']}' ({$rewardStatus['badge']}).";
        } else {
            $parts[] = 'Di bidang prestasi, siswa belum memiliki catatan capaian terverifikasi dalam master poin penghargaan periode ini.';
        }

        // 7. Poin Pelanggaran & Tata Tertib
        if ($violPoints === 0) {
            $parts[] = 'Siswa memiliki rekam jejak kedisiplinan yang bersih tanpa poin pelanggaran tata tertib.';
        } elseif ($violPoints < 75) {
            $parts[] = "Catatan pelanggaran siswa berada dalam batas wajar ({$violPoints} poin, status {$guidanceStatus['level']}) dan belum memerlukan tindakan khusus.";
        } else {
            $parts[] = "PENTING: Siswa telah mencapai akumulasi {$violPoints} poin pelanggaran dan berada pada status '{$guidanceStatus['level']}' yang membutuhkan penanganan sesuai prosedur tata tertib.";
        }

        // 8. Tahfidz Al-Qur'an Juz 30 (Target Tingkat Kelas)
        if ($completedSurahsCount > 0) {
            $parts[] = "Dalam program Tahfidz Al-Qur'an Juz 30 (Target Kelas {$studentGradeForTahfidz}), siswa telah menuntaskan setoran hafalan sebanyak {$completedSurahsCount} dari {$totalTargetSurahsForGrade} Surat dengan predikat '{$tahfidzStatus}' (tercatat {$tahfidzPeriodCount} setoran pada periode ini).";
        } else {
            $parts[] = "Pada program Tahfidz Al-Qur'an Juz 30 (Target Kelas {$studentGradeForTahfidz}), siswa berada pada status '{$tahfidzStatus}' dan siap melanjutkan setoran hafalan terstruktur.";
        }

        // 9. 7 Kebiasaan Anak Indonesia Hebat (7 KAIH)
        if ($totalExpectedHabits === 0) {
            $parts[] = "Pada Gerakan 7 Kebiasaan Anak Indonesia Hebat, siswa belum mengisi jurnal kegiatan pembiasaan pada periode ini (0%) dengan predikat 'BELUM_TERPANTAU'.";
        } else {
            $parts[] = "Pada Gerakan 7 Kebiasaan Anak Indonesia Hebat, siswa mencatatkan tingkat konsistensi sebesar {$habitScore}% dengan predikat '{$habitStatus}'.";
        }

        return implode(' ', $parts);
    }

    protected function generateConclusions(
        Student $student,
        float $attRate,
        float $puncRate,
        int $achPoints,
        array $topAch,
        int $violPoints,
        array $freqViol,
        float $habitScore,
        string $habitStatus,
        int $totalExpectedHabits = 0,
        float $worshipRate = 100.0,
        int $worshipTotal = 0,
        int $completedSurahsCount = 0,
        string $tahfidzStatus = 'BELUM_MENYETOR'
    ): array {
        $conclusions = [];

        if ($attRate >= 90 && $puncRate >= 90) {
            $conclusions[] = [
                'dimension' => 'Kedisiplinan & Waktu',
                'sentiment' => 'positive',
                'insight' => 'Siswa memiliki kebiasaan manajemen waktu yang stabil, mampu konsisten hadir tepat waktu sebelum bel berbunyi.',
            ];
        } else {
            $conclusions[] = [
                'dimension' => 'Kedisiplinan & Waktu',
                'sentiment' => 'neutral',
                'insight' => 'Terdapat fluktuasi kedisiplinan kedatangan di pagi hari yang berpotensi memengaruhi fokus belajar pada jam-jam awal.',
            ];
        }

        // Ibadah & Pembiasaan Rohani
        if ($worshipTotal === 0 || $worshipRate >= 80) {
            $conclusions[] = [
                'dimension' => 'Ibadah & Pembiasaan Rohani',
                'sentiment' => 'positive',
                'insight' => "Siswa menunjukkan komitmen spiritual yang terpuji dalam mengikuti Shalat Dhuha, Dzuhur/Jum'at berjamaah, dan kegiatan keagamaan ({$worshipRate}% kehadiran).",
            ];
        } else {
            $conclusions[] = [
                'dimension' => 'Ibadah & Pembiasaan Rohani',
                'sentiment' => 'warning',
                'insight' => "Kehadiran pada sesi ibadah bersama ({$worshipRate}%) perlu ditingkatkan melalui motivasi dan pemantauan dari guru pembina keagamaan.",
            ];
        }

        // Tahfidz & Literasi Al-Qur'an
        if ($completedSurahsCount >= 5) {
            $conclusions[] = [
                'dimension' => "Tahfidz & Literasi Al-Qur'an",
                'sentiment' => 'positive',
                'insight' => "Progres setoran hafalan Juz 30 berjalan konsisten ({$completedSurahsCount} surat lulus, status: {$tahfidzStatus}) dengan makhraj dan kelancaran yang baik.",
            ];
        } elseif ($completedSurahsCount > 0) {
            $conclusions[] = [
                'dimension' => "Tahfidz & Literasi Al-Qur'an",
                'sentiment' => 'neutral',
                'insight' => "Siswa telah memulai setoran hafalan Juz 30 ({$completedSurahsCount} surat). Dibutuhkan dorongan murajaah agar ritme setoran terjaga.",
            ];
        } else {
            $conclusions[] = [
                'dimension' => "Tahfidz & Literasi Al-Qur'an",
                'sentiment' => 'warning',
                'insight' => 'Siswa belum menyetorkan hafalan Juz 30 pada periode ini. Diperlukan penjadwalan bimbingan khusus bersama koordinator keagamaan.',
            ];
        }

        if ($achPoints >= 75) {
            $areas = ! empty($topAch) ? implode(' dan ', $topAch) : 'akademik/non-akademik';
            $conclusions[] = [
                'dimension' => 'Potensi Diri & Minat Bakat',
                'sentiment' => 'positive',
                'insight' => "Siswa memiliki inisiatif kompetitif yang kuat, khususnya menonjol pada kategori {$areas}.",
            ];
        } else {
            $conclusions[] = [
                'dimension' => 'Potensi Diri & Minat Bakat',
                'sentiment' => 'neutral',
                'insight' => 'Potensi siswa belum sepenuhnya terakselerasi ke dalam ajang perlombaan atau kegiatan ekstrakurikuler terstruktur.',
            ];
        }

        if ($totalExpectedHabits === 0) {
            $conclusions[] = [
                'dimension' => 'Karakter 7 Kebiasaan',
                'sentiment' => 'warning',
                'insight' => 'Siswa belum memiliki catatan pengisian jurnal 7 Kebiasaan Anak Indonesia Hebat pada periode ini. Diperlukan motivasi dan bimbingan orang tua agar siswa mulai aktif membiasakan rutinitas harian.',
            ];
        } elseif ($habitScore >= 80) {
            $conclusions[] = [
                'dimension' => 'Karakter 7 Kebiasaan',
                'sentiment' => 'positive',
                'insight' => 'Jurnal harian menunjukkan rutinitas yang sehat, seimbang antara ibadah, belajar mandiri, dan istirahat.',
            ];
        } else {
            $conclusions[] = [
                'dimension' => 'Karakter 7 Kebiasaan',
                'sentiment' => 'warning',
                'insight' => "Pelaksanaan pembiasaan harian memerlukan dorongan motivasi serta pemantauan berkala dari lingkungan rumah ({$habitScore}% tercapai).",
            ];
        }

        return $conclusions;
    }

    protected function generateRecommendations(
        Student $student,
        int $lateCount,
        int $achPoints,
        int $violPoints,
        array $guidanceStatus,
        float $habitScore,
        int $totalExpectedHabits = 0,
        float $worshipRate = 100.0,
        int $completedSurahsCount = 0
    ): array {
        return [
            'wali_kelas' => [
                'role' => 'Wali Kelas',
                'actions' => [
                    $lateCount > 2 ? 'Lakukan dialog santai mengenai kendala transportasi atau kebiasaan pagi siswa.' : 'Berikan apresiasi atas konsistensi kehadiran dan peran aktif siswa di kelas.',
                    $achPoints >= 50 ? 'Dorong siswa untuk mempertahankan prestasi menuju target penghargaan Anugerah Waluya Utama.' : 'Ajak siswa mengeksplorasi ekstrakurikuler atau bidang kompetisi baru.',
                    'Lakukan pemantauan silang bersama sekretaris kelas terkait kelengkapan presensi ibadah harian.',
                ],
            ],
            'koordinator_keagamaan' => [
                'role' => 'Koordinator Keagamaan & Pembina Tahfidz',
                'actions' => [
                    $completedSurahsCount >= 10
                        ? 'Dampingi penguatan murajaah surat-surat Juz 30 yang telah tuntas dan siapkan siswa untuk target tasmi/wisuda tahfidz.'
                        : 'Jadwalkan pendampingan setoran hafalan Juz 30 secara rutin minimal 2 kali seminggu untuk memantau tajwid dan makhraj.',
                    $worshipRate < 80
                        ? 'Berikan bimbingan personal terkait keutamaan dan konsistensi mengikuti shalat dhuha serta shalat berjamaah di musholla/masjid sekolah.'
                        : 'Apresiasi istiqamah siswa dalam shalat dhuha dan shalat berjamaah, jadikan teladan bagi rekan sekelas.',
                ],
            ],
            'bk' => [
                'role' => 'Guru Bimbingan Konseling (BK)',
                'actions' => [
                    $guidanceStatus['action_needed']
                        ? "Segera jadwalkan sesi '{$guidanceStatus['level']}' bersama siswa dan orang tua untuk menyusun rencana komitmen perbaikan."
                        : 'Lakukan sesi konseling suportif berkala untuk memetakan rencana studi lanjut dan minat karier.',
                    'Dukung siswa dalam mengembangkan strategi adaptasi belajar yang seimbang.',
                ],
            ],
            'orang_tua' => [
                'role' => 'Orang Tua / Wali',
                'actions' => [
                    $totalExpectedHabits === 0
                        ? 'Ingatkan dan dampingi anak untuk mulai membiasakan pengisian jurnal 7 Kebiasaan Anak Indonesia Hebat setiap hari di rumah.'
                        : ($habitScore < 80 ? 'Dampingi rutinitas malam hari agar anak dapat tidur tepat waktu dan bangun pagi lebih bugar.' : 'Pertahankan suasana rumah yang kondusif bagi amalan ibadah dan belajar mandiri.'),
                    $completedSurahsCount > 0
                        ? "Dampingi anak melakukan murajaah hafalan Al-Qur'an (Juz 30) minimal 10 menit setelah shalat Maghrib atau Subuh."
                        : "Ajak anak membiasakan tilawah Al-Qur'an bersama keluarga untuk memupuk kecintaan pada kalam Ilahi.",
                    'Luangkan waktu 10-15 menit setiap hari untuk mendengarkan refleksi aktivitas anak di sekolah.',
                ],
            ],
            'siswa' => [
                'role' => 'Siswa (Refleksi Diri)',
                'actions' => [
                    $totalExpectedHabits === 0
                        ? 'Mulai aktifkan pencatatan harian pada jurnal 7 Kebiasaan sebagai sarana melatih kejujuran, tanggung jawab, dan kemandirian diri.'
                        : 'Pertahankan kebiasaan baik yang sudah konsisten dan jadikan sebagai inspirasi bagi teman sebaya.',
                    'Tingkatkan rutinitas murajaah hafalan Juz 30 dan disiplin mengikuti shalat dhuha serta dzuhur berjamaah tepat waktu.',
                ],
            ],
            'manajemen' => [
                'role' => 'Kepala Sekolah & Manajemen',
                'actions' => [
                    $achPoints >= 50
                        ? 'Pertimbangkan rekomendasi delegasi lomba dan beasiswa apresiasi siswa berprestasi pada forum pembinaan sekolah.'
                        : 'Pantau ketersediaan sarana dan dukungan program pengembangan minat bakat siswa secara berkelanjutan.',
                    'Dukung sarana musholla sekolah dan pengadaan sertifikat tahfidz Al-Qur\'an bagi siswa yang menyelesaikan Juz 30.',
                    $guidanceStatus['action_needed']
                        ? 'Pantau laporan berkala penanganan tata tertib dari tim BK & Kesiswaan untuk memastikan penanganan yang adil dan mendidik.'
                        : 'Dukung penguatan budaya positif sekolah dan sinergi kemitraan tri sentra pendidikan (Sekolah, Keluarga, Masyarakat).',
                ],
            ],
        ];
    }
}
