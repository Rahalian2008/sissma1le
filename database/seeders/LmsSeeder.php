<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\CourseClass;
use App\Models\LmsAssignment;
use App\Models\LmsAssignmentSubmission;
use App\Models\LmsDiscussion;
use App\Models\LmsMaterial;
use App\Models\LmsMaterialRead;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizQuestion;
use App\Models\LmsSubjectAttendance;
use App\Models\LmsSubjectAttendanceLog;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LmsSeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        $academicYearId = $academicYear?->id;

        // 1. Master Mata Pelajaran Jenjang SMA (Permendikdasmen No. 13 Tahun 2025)
        Subject::seedPermendikdasmenSubjects();
        $subjects = Subject::all()->keyBy('code');

        // 2. Hubungkan Kelas & Guru (Sample 2 Alokasi Penugasan Guru ke Kelas)
        $classes = SchoolClass::all();
        $teachers = Teacher::all();

        if ($classes->isEmpty() || $teachers->isEmpty()) {
            return;
        }

        $targetClass1 = $classes->first();

        // Pilih guru pengampu
        $guruFisika = Teacher::where('name', 'like', '%Bambang%')->first()
            ?? Teacher::where('name', 'like', '%Pian%')->first()
            ?? $teachers->first();
        $guruMatematika = Teacher::where('name', 'like', '%Siti%')->orWhere('name', 'like', '%Eti%')->orWhere('name', 'like', '%Wali%')->first()
            ?? $teachers->skip(1)->first()
            ?? $teachers->first();

        $course1 = CourseClass::updateOrCreate(
            [
                'subject_id' => $subjects['FIS-SMA']->id,
                'class_id' => $targetClass1->id,
                'academic_year_id' => $academicYearId,
            ],
            [
                'teacher_id' => $guruFisika->id,
                'day_of_week' => 'Senin',
                'start_time' => '07:30',
                'end_time' => '09:00',
                'description' => 'Pembelajaran Fisika SMA Fase F: Mekanika, Dinamika Gerak, Gravitasi, dan Energi sesuai Permendikdasmen No. 13 Tahun 2025.',
                'is_active' => true,
            ]
        );

        $course2 = CourseClass::updateOrCreate(
            [
                'subject_id' => $subjects['MAT-SMA']->id,
                'class_id' => $targetClass1->id,
                'academic_year_id' => $academicYearId,
            ],
            [
                'teacher_id' => $guruMatematika->id,
                'day_of_week' => 'Selasa',
                'start_time' => '08:00',
                'end_time' => '09:30',
                'description' => 'Matematika Komprehensif SMA (Wajib): Eksponen, Logaritma, Pemodelan Fungsi, dan Aljabar sesuai Permendikdasmen No. 13 Tahun 2025.',
                'is_active' => true,
            ]
        );

        // Hapus alokasi kelas di luar 2 sampel ini agar bersih
        CourseClass::whereNotIn('id', [$course1->id, $course2->id])->delete();

        // Perbarui kuis di luar kedua sampel jika masih ada
        LmsQuiz::whereNotIn('course_class_id', [$course1->id, $course2->id])->delete();

        // -------------------------------------------------------------------
        // 3. SEED COURSE 1: FISIKA (Fase F - Mata Pelajaran Pilihan)
        // -------------------------------------------------------------------
        $m1_fis = LmsMaterial::updateOrCreate(
            ['course_class_id' => $course1->id, 'title' => 'Modul 01: Vektor dan Hukum Gerak Newton'],
            [
                'topic' => 'Bab 1 - Dinamika Gerak',
                'type' => 'dokumen',
                'content' => 'Hukum I Newton menyatakan bahwa setiap benda akan mempertahankan keadaan diam atau bergerak lurus beraturan jika resultan gaya yang bekerja sama dengan nol. Hukum II Newton menyatakan bahwa percepatan sebanding dengan gaya total dan berbanding terbalik dengan massa (F = m . a). Hukum III Newton menjelaskan tentang aksi dan reaksi.',
                'file_name' => 'Modul_Fisika_Hukum_Newton_SMA.pdf',
                'file_size' => 1845200,
                'is_published' => true,
                'views_count' => 45,
            ]
        );

        $m2_fis = LmsMaterial::updateOrCreate(
            ['course_class_id' => $course1->id, 'title' => 'Video Simulasi Interaktif: Hukum Gravitasi Universal'],
            [
                'topic' => 'Bab 1 - Dinamika Gerak',
                'type' => 'video',
                'content' => 'Saksikan animasi visualisasi orbit planet dan interaksi massa gravitasi bumi-bulan sesuai persamaan F = G (m1 m2) / r^2.',
                'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_published' => true,
                'views_count' => 62,
            ]
        );

        $dueFis = Carbon::now()->addDays(5)->setHour(23)->setMinute(59);
        $asg1_fis = LmsAssignment::updateOrCreate(
            ['course_class_id' => $course1->id, 'title' => 'Tugas Mandiri 01: Analisis Gerak Melingkar Beraturan'],
            [
                'topic' => 'Bab 1 - Dinamika Gerak',
                'instructions' => "Kerjakan soal analisis studi kasus berikut:\n1. Hitung percepatan sentripetal sebuah mobil bermassa 1.200 kg yang melintasi tikungan berjari-jari 50 meter dengan kelajuan konstan 72 km/jam.\n2. Tuliskan kesimpulan dalam format PDF atau foto lembar kerja tulisan tangan yang rapi dan jelas.",
                'attachment_name' => 'Lembar_Kerja_Analisis_GMB_2026.pdf',
                'due_date' => $dueFis,
                'max_score' => 100,
                'allow_late' => true,
                'is_published' => true,
            ]
        );

        // Kuis 1 Fisika: Asesmen Formatif (Formatif / Pemahaman)
        $quiz1_fis = LmsQuiz::updateOrCreate(
            ['course_class_id' => $course1->id, 'title' => 'Asesmen Formatif: Kuis Pemahaman Hukum Newton'],
            [
                'quiz_type' => 'formatif',
                'description' => 'Asesmen formatif pemahaman konsep gaya, resultan gaya, dan hukum inersia Newton.',
                'instructions' => 'Kuis daring 5 butir soal pilihan ganda. Kerjakan dengan teliti untuk mengukur pemahaman awal konsep gerak.',
                'duration_minutes' => 30,
                'start_time' => Carbon::now()->subDay(),
                'end_time' => Carbon::now()->addDays(7),
                'passing_score' => 75,
                'is_randomized' => true,
                'is_published' => true,
            ]
        );

        // Kuis 2 Fisika: Asesmen Sumatif Lingkup Materi
        $quiz2_fis = LmsQuiz::updateOrCreate(
            ['course_class_id' => $course1->id, 'title' => 'Asesmen Sumatif: Lingkup Materi Dinamika Gerak & Gravitasi'],
            [
                'quiz_type' => 'sumatif_materi',
                'description' => 'Penilaian sumatif ketercapaian tujuan pembelajaran (KKTP) dinamika partikel dan hukum gravitasi.',
                'instructions' => 'Asesmen sumatif durasi 60 menit. Dilarang membuka tab lain, kerjakan secara mandiri dan jujur.',
                'duration_minutes' => 60,
                'start_time' => Carbon::now()->subDay(),
                'end_time' => Carbon::now()->addDays(10),
                'passing_score' => 75,
                'is_randomized' => true,
                'is_published' => true,
            ]
        );

        $soalFisika = [
            [
                'question_text' => 'Sebuah benda bermassa 4 kg ditarik dengan gaya mendatar sebesar 20 N pada permukaan licin. Berapakah percepatan yang dialami benda tersebut?',
                'question_type' => 'multiple_choice',
                'options' => ['A' => '2 m/s²', 'B' => '4 m/s²', 'C' => '5 m/s²', 'D' => '8 m/s²', 'E' => '10 m/s²'],
                'correct_answer' => 'C',
                'score_weight' => 20,
                'order_number' => 1,
            ],
            [
                'question_text' => 'Hukum yang menyatakan bahwa gaya aksi dan reaksi selalu sama besar namun berlawanan arah adalah...',
                'question_type' => 'multiple_choice',
                'options' => ['A' => 'Hukum I Kepler', 'B' => 'Hukum I Newton', 'C' => 'Hukum II Newton', 'D' => 'Hukum III Newton', 'E' => 'Hukum Kekekalan Energi'],
                'correct_answer' => 'D',
                'score_weight' => 20,
                'order_number' => 2,
            ],
            [
                'question_text' => 'Satuan SI (Standar Internasional) untuk besaran gaya adalah...',
                'question_type' => 'multiple_choice',
                'options' => ['A' => 'Joule', 'B' => 'Newton', 'C' => 'Watt', 'D' => 'Pascal', 'E' => 'Kilogram'],
                'correct_answer' => 'B',
                'score_weight' => 20,
                'order_number' => 3,
            ],
            [
                'question_text' => 'Apabila resultan gaya yang bekerja pada suatu benda bernilai nol, maka kemungkinan gerak benda tersebut adalah...',
                'question_type' => 'multiple_choice',
                'options' => [
                    'A' => 'Diam atau bergerak lurus dengan kelajuan tetap',
                    'B' => 'Pasti diam dan tidak mungkin bergerak',
                    'C' => 'Bergerak dengan percepatan konstan',
                    'D' => 'Bergerak melingkar beraturan',
                    'E' => 'Bergerak lurus berubah beraturan',
                ],
                'correct_answer' => 'A',
                'score_weight' => 20,
                'order_number' => 4,
            ],
            [
                'question_text' => 'Jelaskan secara ringkas bagaimana gaya gesek statis berbeda dengan gaya gesek kinetis saat sebuah benda mulai ditarik!',
                'question_type' => 'essay',
                'options' => null,
                'correct_answer' => 'Gaya gesek statis bekerja saat benda diam hingga tepat akan bergerak, sedangkan gaya gesek kinetis bekerja saat benda sudah bergerak.',
                'score_weight' => 20,
                'order_number' => 5,
            ],
        ];

        foreach ($soalFisika as $q) {
            LmsQuizQuestion::updateOrCreate(
                ['quiz_id' => $quiz1_fis->id, 'order_number' => $q['order_number']],
                $q
            );
            LmsQuizQuestion::updateOrCreate(
                ['quiz_id' => $quiz2_fis->id, 'order_number' => $q['order_number']],
                $q
            );
        }

        $today = Carbon::today()->toDateString();
        $att1_fis = LmsSubjectAttendance::updateOrCreate(
            ['course_class_id' => $course1->id, 'meeting_date' => $today],
            [
                'title' => 'Pertemuan 01: Dinamika Gerak & Pengantar Hukum Newton',
                'start_time' => '07:30',
                'end_time' => '09:00',
                'is_open' => true,
                'attendance_method' => 'both',
                'notes' => 'Pembahasan konsep dasar percepatan dan resultan gaya. Siswa presensi mandiri (selfie) atau dicatat guru.',
                'created_by' => $guruFisika->id,
            ]
        );

        $disc1_fis = LmsDiscussion::updateOrCreate(
            ['course_class_id' => $course1->id, 'title' => 'Pengumuman: Jadwal Praktikum Gravitasi Pekan Depan'],
            [
                'user_id' => $guruFisika->user_id ?? 1,
                'content' => 'Assalamu’alaikum siswa-siswi sekalian. Pada pertemuan pekan depan, kita akan melaksanakan praktikum bandul sederhana di Laboratorium Fisika. Harap membaca modul 01 dan membawa mistar penggaris.',
                'is_pinned' => true,
            ]
        );

        // -------------------------------------------------------------------
        // 4. SEED COURSE 2: MATEMATIKA (Fase E/F - Mata Pelajaran Wajib)
        // -------------------------------------------------------------------
        $m1_mat = LmsMaterial::updateOrCreate(
            ['course_class_id' => $course2->id, 'title' => 'Modul 01: Eksponen, Bentuk Akar, dan Logaritma'],
            [
                'topic' => 'Bab 1 - Aljabar dan Pemodelan Fungsi',
                'type' => 'dokumen',
                'content' => 'Eksponen adalah perkalian berulang dari suatu bilangan yang sama. Sifat-sifat dasar eksponen meliputi perkalian eksponen ber-basis sama (a^m . a^n = a^(m+n)), pembagian, dan pemangkatan pangkat. Logaritma merupakan invers dari perpangkatan yang sangat berguna dalam memodelkan pertumbuhan penduduk, bunga majemuk, dan skala intensitas gempa (Skala Richter).',
                'file_name' => 'Modul_Matematika_Eksponen_Logaritma_SMA.pdf',
                'file_size' => 2104500,
                'is_published' => true,
                'views_count' => 54,
            ]
        );

        $m2_mat = LmsMaterial::updateOrCreate(
            ['course_class_id' => $course2->id, 'title' => 'Video Pembelajaran: Penerapan Eksponensial dalam Pertumbuhan dan Peluruhan'],
            [
                'topic' => 'Bab 1 - Aljabar dan Pemodelan Fungsi',
                'type' => 'video',
                'content' => 'Video tutorial konsep peluruhan zat radioaktif dan model bunga majemuk menggunakan fungsi eksponensial y = a(1 + r)^t.',
                'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_published' => true,
                'views_count' => 70,
            ]
        );

        $dueMat = Carbon::now()->addDays(6)->setHour(23)->setMinute(59);
        $asg1_mat = LmsAssignment::updateOrCreate(
            ['course_class_id' => $course2->id, 'title' => 'Proyek Mandiri 01: Pemodelan Matematika Aplikasi Eksponensial'],
            [
                'topic' => 'Bab 1 - Aljabar dan Pemodelan Fungsi',
                'instructions' => "Pilihlah salah satu topik pemodelan berikut:\n1. Prediksi pertumbuhan bakteri dalam media kultur selama 24 jam.\n2. Perhitungan depresiasi nilai kendaraan dalam 5 tahun ke depan.\nSusun laporan analisis dalam bentuk PDF maksimal 3 halaman dengan grafik kurva fungsi.",
                'attachment_name' => 'Pedoman_Proyek_Eksponensial_SMA.pdf',
                'due_date' => $dueMat,
                'max_score' => 100,
                'allow_late' => true,
                'is_published' => true,
            ]
        );

        // Kuis 1 Matematika: Asesmen Formatif
        $quiz1_mat = LmsQuiz::updateOrCreate(
            ['course_class_id' => $course2->id, 'title' => 'Asesmen Formatif: Kuis Pemahaman Logaritma & Aljabar Fungsi'],
            [
                'quiz_type' => 'formatif',
                'description' => 'Kuis formatif daring untuk menguji penguasaan sifat-sifat dasar logaritma dan bentuk akar.',
                'instructions' => 'Jawab 5 soal pilihan ganda berikut dalam 30 menit. Fokus pada ketelitian operasi aljabar.',
                'duration_minutes' => 30,
                'start_time' => Carbon::now()->subDay(),
                'end_time' => Carbon::now()->addDays(7),
                'passing_score' => 75,
                'is_randomized' => true,
                'is_published' => true,
            ]
        );

        // Kuis 2 Matematika: Asesmen Sumatif Lingkup Materi
        $quiz2_mat = LmsQuiz::updateOrCreate(
            ['course_class_id' => $course2->id, 'title' => 'Asesmen Sumatif: Lingkup Materi Fungsi Eksponen & Logaritma'],
            [
                'quiz_type' => 'sumatif_materi',
                'description' => 'Penilaian sumatif ketercapaian tujuan pembelajaran (KKTP) materi eksponen, akar, dan logaritma.',
                'instructions' => 'Asesmen sumatif berdurasi 60 menit. Sediakan kertas buram untuk coretan perhitungan.',
                'duration_minutes' => 60,
                'start_time' => Carbon::now()->subDay(),
                'end_time' => Carbon::now()->addDays(10),
                'passing_score' => 75,
                'is_randomized' => true,
                'is_published' => true,
            ]
        );

        $soalMatematika = [
            [
                'question_text' => 'Berapakah nilai dari 2³ × 2⁴ ÷ 2⁵ ?',
                'question_type' => 'multiple_choice',
                'options' => ['A' => '2', 'B' => '4', 'C' => '8', 'D' => '16', 'E' => '32'],
                'correct_answer' => 'B',
                'score_weight' => 20,
                'order_number' => 1,
            ],
            [
                'question_text' => 'Jika ²log 8 = x, maka nilai x yang memenuhi adalah...',
                'question_type' => 'multiple_choice',
                'options' => ['A' => '2', 'B' => '3', 'C' => '4', 'D' => '6', 'E' => '8'],
                'correct_answer' => 'B',
                'score_weight' => 20,
                'order_number' => 2,
            ],
            [
                'question_text' => 'Bentuk sederhana dari √72 adalah...',
                'question_type' => 'multiple_choice',
                'options' => ['A' => '3√2', 'B' => '4√3', 'C' => '6√2', 'D' => '8√2', 'E' => '9√2'],
                'correct_answer' => 'C',
                'score_weight' => 20,
                'order_number' => 3,
            ],
            [
                'question_text' => 'Nilai dari ³log 27 + ³log 9 adalah...',
                'question_type' => 'multiple_choice',
                'options' => ['A' => '3', 'B' => '4', 'C' => '5', 'D' => '6', 'E' => '9'],
                'correct_answer' => 'C',
                'score_weight' => 20,
                'order_number' => 4,
            ],
            [
                'question_text' => 'Tuliskan persamaan umum fungsi eksponensial yang menyatakan pertumbuhan dan jelaskan masing-masing variabelnya!',
                'question_type' => 'essay',
                'options' => null,
                'correct_answer' => 'y = a(1 + r)^t di mana a adalah jumlah awal, r adalah laju pertumbuhan per periode, dan t adalah periode waktu.',
                'score_weight' => 20,
                'order_number' => 5,
            ],
        ];

        foreach ($soalMatematika as $q) {
            LmsQuizQuestion::updateOrCreate(
                ['quiz_id' => $quiz1_mat->id, 'order_number' => $q['order_number']],
                $q
            );
            LmsQuizQuestion::updateOrCreate(
                ['quiz_id' => $quiz2_mat->id, 'order_number' => $q['order_number']],
                $q
            );
        }

        $att1_mat = LmsSubjectAttendance::updateOrCreate(
            ['course_class_id' => $course2->id, 'meeting_date' => $today],
            [
                'title' => 'Pertemuan 01: Eksponen dan Sifat-Sifat Operasinya',
                'start_time' => '08:00',
                'end_time' => '09:30',
                'is_open' => true,
                'attendance_method' => 'both',
                'notes' => 'Pembahasan sifat-sifat eksponen dan bentuk akar. Seluruh siswa wajib mengisi presensi kehadiran.',
                'created_by' => $guruMatematika->id,
            ]
        );

        $disc1_mat = LmsDiscussion::updateOrCreate(
            ['course_class_id' => $course2->id, 'title' => 'Forum Diskusi: Tanya Jawab Sifat Logaritma & Eksponen'],
            [
                'user_id' => $guruMatematika->user_id ?? 1,
                'content' => 'Selamat datang di ruang diskusi Matematika. Silakan ajukan pertanyaan apabila menemui kesulitan pada pemodelan eksponensial maupun sifat logaritma.',
                'is_pinned' => true,
            ]
        );

        // -------------------------------------------------------------------
        // 5. SEED PARTICIPATION SISWA (SUBMISSIONS, QUIZ ATTEMPTS, ATTENDANCE)
        // -------------------------------------------------------------------
        $students = Student::where('class_id', $targetClass1->id)->get();
        if ($students->isNotEmpty()) {
            $s1 = $students->first();

            // Fisika Tracking
            LmsMaterialRead::updateOrCreate(
                ['material_id' => $m1_fis->id, 'student_id' => $s1->id],
                ['read_at' => Carbon::now()->subHours(2)]
            );

            LmsAssignmentSubmission::updateOrCreate(
                ['assignment_id' => $asg1_fis->id, 'student_id' => $s1->id],
                [
                    'content' => 'Berikut lembar pengerjaan tugas analisis gerak melingkar beraturan yang sudah saya selesaikan Pak.',
                    'file_name' => 'Jawaban_Tugas1_Fisika_AhmadRifai.pdf',
                    'submitted_at' => Carbon::now()->subHours(3),
                    'is_late' => false,
                    'score' => 92,
                    'feedback' => 'Analisis sangat baik dan runut. Rumus perhitungan gaya sentripetal tepat.',
                    'graded_at' => Carbon::now()->subHour(),
                    'graded_by' => $guruFisika->id,
                ]
            );

            LmsSubjectAttendanceLog::updateOrCreate(
                ['attendance_id' => $att1_fis->id, 'student_id' => $s1->id],
                [
                    'status' => 'HADIR',
                    'method' => 'SELFIE',
                    'recorded_at' => Carbon::now()->setHour(7)->setMinute(35),
                    'notes' => 'Hadir tepat waktu di kelas Fisika',
                ]
            );

            LmsQuizAttempt::updateOrCreate(
                ['quiz_id' => $quiz1_fis->id, 'student_id' => $s1->id],
                [
                    'started_at' => Carbon::now()->subHours(4),
                    'completed_at' => Carbon::now()->subHours(3)->addMinutes(25),
                    'score' => 90,
                    'is_passed' => true,
                ]
            );

            // Matematika Tracking
            LmsMaterialRead::updateOrCreate(
                ['material_id' => $m1_mat->id, 'student_id' => $s1->id],
                ['read_at' => Carbon::now()->subHours(2)]
            );

            LmsAssignmentSubmission::updateOrCreate(
                ['assignment_id' => $asg1_mat->id, 'student_id' => $s1->id],
                [
                    'content' => 'Berikut berkas proyek pemodelan matematika eksponensial dalam pertumbuhan penduduk yang telah diselesaikan.',
                    'file_name' => 'Proyek_Pemodelan_Eksponensial_AhmadRifai.pdf',
                    'submitted_at' => Carbon::now()->subHours(2),
                    'is_late' => false,
                    'score' => 88,
                    'feedback' => 'Pemodelan kurva sangat baik, kesimpulan logis.',
                    'graded_at' => Carbon::now()->subMinutes(30),
                    'graded_by' => $guruMatematika->id,
                ]
            );

            LmsSubjectAttendanceLog::updateOrCreate(
                ['attendance_id' => $att1_mat->id, 'student_id' => $s1->id],
                [
                    'status' => 'HADIR',
                    'method' => 'SELFIE',
                    'recorded_at' => Carbon::now()->setHour(8)->setMinute(5),
                    'notes' => 'Hadir tepat waktu di kelas Matematika',
                ]
            );

            LmsQuizAttempt::updateOrCreate(
                ['quiz_id' => $quiz1_mat->id, 'student_id' => $s1->id],
                [
                    'started_at' => Carbon::now()->subHours(3),
                    'completed_at' => Carbon::now()->subHours(2)->addMinutes(20),
                    'score' => 85,
                    'is_passed' => true,
                ]
            );

            if ($s1->user_id) {
                LmsDiscussion::updateOrCreate(
                    ['parent_id' => $disc1_fis->id, 'user_id' => $s1->user_id],
                    [
                        'course_class_id' => $course1->id,
                        'content' => 'Baik Pak, terima kasih informasinya. Apakah modul praktikumnya perlu dicetak terlebih dahulu?',
                        'is_pinned' => false,
                    ]
                );

                LmsDiscussion::updateOrCreate(
                    ['parent_id' => $disc1_mat->id, 'user_id' => $s1->user_id],
                    [
                        'course_class_id' => $course2->id,
                        'content' => 'Ibu Guru, untuk nomor 3 apakah boleh menggunakan basis bilangan natural e?',
                        'is_pinned' => false,
                    ]
                );
            }
        }
    }
}
