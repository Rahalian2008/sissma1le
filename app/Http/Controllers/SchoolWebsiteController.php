<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\BkCounselingSession;
use App\Models\BkPsychologicalTest;
use App\Models\Extracurricular;
use App\Models\Habit;
use App\Models\HabitDailyLog;
use App\Models\LibraryBook;
use App\Models\LibraryLoan;
use App\Models\LmsMaterial;
use App\Models\LmsQuiz;
use App\Models\PpdbRegistration;
use App\Models\QuranTahfidzRecord;
use App\Models\SchoolClass;
use App\Models\SchoolHoliday;
use App\Models\SchoolSetting;
use App\Models\SpmbTrack;
use App\Models\SpmbWave;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\WorshipAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SchoolWebsiteController extends Controller
{
    /**
     * Display the official public school website with real-time SIS-SMA1LE data.
     */
    public function index(Request $request): Response
    {
        $settings = SchoolSetting::allAsAssoc();

        // 1. Data Statistik Riil Sekolah (Live Database)
        $studentCount = Student::count();
        $teacherCount = Teacher::where('is_active', true)->count() ?: Teacher::count();
        $classCount = SchoolClass::count();
        $extracurricularCount = Extracurricular::count();

        // 2. Data LMS Terpadu
        $subjectCount = Subject::count();
        $materialCount = LmsMaterial::where('is_published', true)->count();
        $quizCount = LmsQuiz::where('is_published', true)->count();

        // 2b. Data Perpustakaan Digital & E-Book
        $libraryBookCount = Schema::hasTable('library_books') ? LibraryBook::count() : 0;
        $libraryEbookCount = Schema::hasTable('library_books')
            ? LibraryBook::where(function ($q) {
                $hasCond = false;
                if (Schema::hasColumn('library_books', 'book_type')) {
                    $q->where('book_type', 'digital');
                    $hasCond = true;
                }
                if (Schema::hasColumn('library_books', 'digital_file_path')) {
                    $hasCond ? $q->orWhereNotNull('digital_file_path') : $q->whereNotNull('digital_file_path');
                    $hasCond = true;
                }
                if (Schema::hasColumn('library_books', 'ebook_file')) {
                    $hasCond ? $q->orWhereNotNull('ebook_file') : $q->whereNotNull('ebook_file');
                    $hasCond = true;
                }
                if (Schema::hasColumn('library_books', 'is_digital_flip')) {
                    $hasCond ? $q->orWhere('is_digital_flip', true) : $q->where('is_digital_flip', true);
                    $hasCond = true;
                }
                if (Schema::hasColumn('library_books', 'file_path')) {
                    $hasCond ? $q->orWhereNotNull('file_path') : $q->whereNotNull('file_path');
                    $hasCond = true;
                }
                if (Schema::hasColumn('library_books', 'is_ebook')) {
                    $hasCond ? $q->orWhere('is_ebook', true) : $q->where('is_ebook', true);
                    $hasCond = true;
                }
                if (! $hasCond) {
                    $q->whereRaw('1 = 0');
                }
            })->count()
            : 0;
        $libraryLoanCount = Schema::hasTable('library_loans') ? LibraryLoan::count() : 0;

        // 2c. Data SPMB Baru & Seleksi TKA
        $spmbActiveWaves = Schema::hasTable('spmb_waves') ? SpmbWave::active()->get() : collect();
        $spmbActiveWave = $spmbActiveWaves->first();
        $spmbTotalQuota = $spmbActiveWaves->sum('quota_seats');
        $spmbTracksCount = Schema::hasTable('spmb_tracks') ? SpmbTrack::where('is_active', true)->count() : 5;
        $spmbTotalRegistrants = Schema::hasTable('ppdb_registrations') ? PpdbRegistration::count() : 0;

        // 2d. Data Bimbingan Konseling 360° & Psikotes
        $bkSessionCount = Schema::hasTable('bk_counseling_sessions') ? BkCounselingSession::count() : 0;
        $bkPsychTestCount = Schema::hasTable('bk_psychological_tests') ? BkPsychologicalTest::count() : 0;

        // 3. Daftar Ekstrakurikuler Pilihan (Live Database)
        $customStaff = SchoolSetting::get('website_staff_json');
        $staffMap = [];
        if ($customStaff) {
            $decodedStaff = json_decode($customStaff, true);
            if (is_array($decodedStaff)) {
                foreach ($decodedStaff as $st) {
                    if (! empty($st['photo'])) {
                        $staffMap[$st['name']] = $st['photo'];
                        if (! empty($st['nip'])) {
                            $staffMap[$st['nip']] = $st['photo'];
                        }
                        if (! empty($st['id'])) {
                            $staffMap[$st['id']] = $st['photo'];
                        }
                    }
                }
            }
        }

        $extracurriculars = Extracurricular::with([
            'teacher.user',
            'teachers.user',
            'students' => fn ($q) => $q->with('schoolClass')->orderBy('name'),
        ])
            ->withCount('students')
            ->orderBy('name')
            ->get();

        $extracurriculars->transform(function ($ekskul) use ($staffMap) {
            $teacher = $ekskul->teacher;
            $photo = null;
            if ($teacher) {
                $photo = $staffMap[$teacher->name]
                    ?? ($staffMap[$teacher->nip]
                    ?? ($staffMap['teacher_'.$teacher->id]
                    ?? ($teacher->user?->avatar ? 'storage/'.$teacher->user->avatar : null)));
            }

            $ekskul->pembina_photo_url = $photo ? asset($photo) : null;
            $ekskul->pembina_name = $teacher?->name ?? 'Dewan Guru Pembina';
            $ekskul->pembina_nip = $teacher?->nip ?: '-';

            $ekskul->members_data = $ekskul->students->map(function ($s) {
                return [
                    'name' => $s->name,
                    'nisn' => $s->nisn ?: ($s->nis ?: '-'),
                    'class_name' => $s->schoolClass?->name ?? 'Kelas Belum Ditentukan',
                    'role' => $s->pivot?->role ?: 'Anggota Aktif',
                    'gender' => $s->gender ?: 'L',
                ];
            })->values()->all();

            return $ekskul;
        });

        // 4. Data Peringkat Prestasi Siswa (Top 10 Leaderboard)
        $topStudents = Student::query()
            ->with('schoolClass')
            ->withSum(['achievements' => fn ($q) => $q->where('status', 'DIVERIFIKASI')], 'points')
            ->withCount(['achievements' => fn ($q) => $q->where('status', 'DIVERIFIKASI')])
            ->whereHas('achievements', fn ($q) => $q->where('status', 'DIVERIFIKASI'))
            ->orderByDesc('achievements_sum_points')
            ->orderByDesc('achievements_count')
            ->orderBy('name')
            ->take(10)
            ->get();

        if ($topStudents->isEmpty()) {
            $topStudents = Student::query()
                ->with('schoolClass')
                ->withSum(['achievements' => fn ($q) => $q->where('status', 'DIVERIFIKASI')], 'points')
                ->withCount(['achievements' => fn ($q) => $q->where('status', 'DIVERIFIKASI')])
                ->orderByDesc('achievements_sum_points')
                ->orderByDesc('achievements_count')
                ->take(10)
                ->get();
        }

        // 5. Data Prestasi Siswa Terbaru (Live Database Terverifikasi)
        $achievements = Achievement::with(['student.schoolClass'])
            ->where('status', 'DIVERIFIKASI')
            ->latest('date')
            ->latest('id')
            ->take(6)
            ->get();

        // 6. Sambutan Kepala Sekolah (Multi-speech Support)
        $customSpeeches = SchoolSetting::get('website_speeches_json');
        $decodedSpeeches = $customSpeeches !== null ? json_decode($customSpeeches, true) : null;
        $speeches = is_array($decodedSpeeches) ? $decodedSpeeches : WebsiteContentController::DEFAULT_SPEECHES;
        $activeSpeech = collect($speeches)->firstWhere('is_active', true) ?: ($speeches[0] ?? null);

        // 7. Berita & Agenda Akademik Terkini
        $customAgendas = SchoolSetting::get('website_agendas_json');
        $decodedAgendas = $customAgendas !== null ? json_decode($customAgendas, true) : null;
        $agendas = is_array($decodedAgendas) ? $decodedAgendas : WebsiteContentController::DEFAULT_AGENDAS;
        foreach ($agendas as $i => &$a) {
            if (empty($a['id'])) {
                $a['id'] = 'agenda_'.($i + 1);
            }
            if (! array_key_exists('image', $a)) {
                $a['image'] = null;
            }
            if (! array_key_exists('end_date', $a)) {
                $a['end_date'] = null;
            }
        }
        unset($a);

        // 8. Fasilitas Unggulan Sekolah (Dinamis atau Bawaan)
        $customFacilities = SchoolSetting::get('website_facilities_json');
        $decodedFacilities = $customFacilities !== null ? json_decode($customFacilities, true) : null;
        $facilities = is_array($decodedFacilities) ? $decodedFacilities : WebsiteContentController::DEFAULT_FACILITIES;
        foreach ($facilities as $i => &$f) {
            if (empty($f['id'])) {
                $f['id'] = 'facility_'.($i + 1);
            }
            if (! array_key_exists('image', $f)) {
                $f['image'] = null;
            }
        }
        unset($f);

        // 9. Misi Sekolah (Dinamis atau Bawaan)
        $customMissions = SchoolSetting::get('website_misi_json');
        $missions = $customMissions ? (json_decode($customMissions, true) ?: WebsiteContentController::DEFAULT_MISSIONS) : WebsiteContentController::DEFAULT_MISSIONS;

        // 10. Kabar & Berita Sekolah Terkini
        $customNews = SchoolSetting::get('website_news_json');
        $newsArticles = $customNews ? (json_decode($customNews, true) ?: WebsiteContentController::DEFAULT_NEWS) : WebsiteContentController::DEFAULT_NEWS;

        // 11. Data Dewan Guru & Staf Tata Usaha (TU) - Sinkronisasi Real-Time dengan Database SIS-SMA1LE
        $staffMembers = WebsiteContentController::getLiveStaff();

        // 12. Data Pembiasaan Positif Harian & Karakter (Live Database)
        $habits = Habit::orderBy('order_number')->get();
        $habitStats = [
            'daily_logs' => HabitDailyLog::count(),
            'tahfidz_records' => QuranTahfidzRecord::count(),
            'total_tahfidz_records' => QuranTahfidzRecord::count(),
            'worship_attendance' => WorshipAttendance::count(),
            'total_worship_records' => WorshipAttendance::count(),
        ];

        // 13. Hari Libur / Agenda Akademik Terdekat
        $upcomingHolidays = SchoolHoliday::where('end_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->take(3)
            ->get();

        $appLogoUrl = ! empty($settings['school_logo']) ? asset('storage/'.$settings['school_logo']) : null;
        $appKopUrl = ! empty($settings['school_kop']) ? asset('storage/'.$settings['school_kop']) : null;

        // 14. Data Target Sharing Link & Open Graph Meta Tags (Rich Preview WhatsApp, Telegram, FB, Twitter)
        $shareNewsParam = $request->query('berita') ?? ($request->query('news') ?? ($request->query('id') ?? ($request->query('artikel') ?? $request->query('pengumuman'))));
        $targetArticle = null;
        if ($shareNewsParam) {
            $targetArticle = collect($newsArticles)->first(function ($item) use ($shareNewsParam) {
                return ($item['slug'] ?? '') === $shareNewsParam
                    || ($item['id'] ?? '') === $shareNewsParam
                    || Str::slug($item['title'] ?? '') === Str::slug($shareNewsParam);
            });
        }

        $shareAgendaParam = $request->query('agenda') ?? $request->query('kegiatan');
        $targetAgenda = null;
        if ($shareAgendaParam) {
            $targetAgenda = collect($agendas)->first(function ($item) use ($shareAgendaParam) {
                return ($item['id'] ?? '') === $shareAgendaParam
                    || Str::slug($item['title'] ?? '') === Str::slug($shareAgendaParam);
            });
        }

        $schoolName = $settings['school_name'] ?? 'SMAN 1 Lengkong';

        if ($targetArticle) {
            $metaTitle = ($targetArticle['title'] ?? 'Kabar Sekolah').' | '.$schoolName;
            $metaDescription = ! empty($targetArticle['excerpt'])
                ? strip_tags($targetArticle['excerpt'])
                : Str::limit(strip_tags($targetArticle['content'] ?? ''), 160);
            $rawImg = ! empty($targetArticle['image'])
                ? $targetArticle['image']
                : (! empty($settings['website_hero_photo']) ? $settings['website_hero_photo'] : 'images/website/hero_school.jpg');
            $metaImage = filter_var($rawImg, FILTER_VALIDATE_URL) ? $rawImg : asset($rawImg);
            if (! str_starts_with($metaImage, 'http://') && ! str_starts_with($metaImage, 'https://')) {
                $metaImage = url($metaImage);
            }
            $metaUrl = url('/?berita='.($targetArticle['slug'] ?? $targetArticle['id']));
            $metaType = 'article';
        } elseif ($targetAgenda) {
            $metaTitle = ($targetAgenda['title'] ?? 'Agenda Sekolah').' | '.$schoolName;
            $metaDescription = ! empty($targetAgenda['description'])
                ? strip_tags($targetAgenda['description'])
                : 'Agenda dan kegiatan resmi '.$schoolName;
            $rawImg = ! empty($targetAgenda['image'])
                ? $targetAgenda['image']
                : (! empty($settings['website_hero_photo']) ? $settings['website_hero_photo'] : 'images/website/hero_school.jpg');
            $metaImage = filter_var($rawImg, FILTER_VALIDATE_URL) ? $rawImg : asset($rawImg);
            if (! str_starts_with($metaImage, 'http://') && ! str_starts_with($metaImage, 'https://')) {
                $metaImage = url($metaImage);
            }
            $metaUrl = url('/?agenda='.($targetAgenda['id'] ?? ''));
            $metaType = 'event';
        } else {
            $metaTitle = $schoolName.' - '.($settings['website_hero_headline'] ?? 'Unggul, Berkarakter & Berprestasi di Era Digital');
            $metaDescription = $settings['website_hero_subheadline'] ?? ('Official Web Portal & SIAKAD Terpadu '.$schoolName.' Sukabumi. Sistem Pembelajaran Modern & Karakter Unggul.');
            $rawImg = ! empty($settings['website_hero_photo']) ? $settings['website_hero_photo'] : 'images/website/hero_school.jpg';
            $metaImage = filter_var($rawImg, FILTER_VALIDATE_URL) ? $rawImg : asset($rawImg);
            if (! str_starts_with($metaImage, 'http://') && ! str_starts_with($metaImage, 'https://')) {
                $metaImage = url($metaImage);
            }
            $metaUrl = url('/');
            $metaType = 'website';
        }

        $ext = strtolower(pathinfo(parse_url($metaImage, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
        $metaImageMime = match ($ext) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'jfif', 'jpe', 'jpg', 'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            default => 'image/jpeg',
        };

        return response()
            ->view('website.index', compact(
                'settings',
                'studentCount',
                'teacherCount',
                'classCount',
                'extracurricularCount',
                'subjectCount',
                'materialCount',
                'quizCount',
                'libraryBookCount',
                'libraryEbookCount',
                'libraryLoanCount',
                'spmbActiveWaves',
                'spmbActiveWave',
                'spmbTotalQuota',
                'spmbTracksCount',
                'spmbTotalRegistrants',
                'bkSessionCount',
                'bkPsychTestCount',
                'extracurriculars',
                'topStudents',
                'achievements',
                'speeches',
                'activeSpeech',
                'agendas',
                'facilities',
                'missions',
                'newsArticles',
                'staffMembers',
                'habits',
                'habitStats',
                'upcomingHolidays',
                'appLogoUrl',
                'appKopUrl',
                'metaTitle',
                'metaDescription',
                'metaImage',
                'metaImageMime',
                'metaUrl',
                'metaType',
                'targetArticle',
                'targetAgenda'
            ))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    /**
     * Endpoint API JSON untuk sinkronisasi data statistik real-time website.
     */
    public function liveStats(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'server_time' => now()->toIso8601String(),
            'student_count' => Student::count(),
            'teacher_count' => Teacher::where('is_active', true)->count() ?: Teacher::count(),
            'class_count' => SchoolClass::count(),
            'extracurricular_count' => Extracurricular::count(),
            'subject_count' => Subject::count(),
            'material_count' => LmsMaterial::where('is_published', true)->count(),
            'quiz_count' => LmsQuiz::where('is_published', true)->count(),
            'library_books_count' => Schema::hasTable('library_books') ? LibraryBook::count() : 0,
            'spmb_active_waves_count' => Schema::hasTable('spmb_waves') ? SpmbWave::active()->count() : 0,
            'bk_sessions_count' => Schema::hasTable('bk_counseling_sessions') ? BkCounselingSession::count() : 0,
            'tahfidz_count' => QuranTahfidzRecord::count(),
            'worship_count' => WorshipAttendance::count(),
            'daily_logs_count' => HabitDailyLog::count(),
            'achievements_count' => Achievement::where('status', 'DIVERIFIKASI')->count(),
        ]);
    }
}
