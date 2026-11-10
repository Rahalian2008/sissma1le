<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PpdbRegistration;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\SchoolSetting;
use App\Models\SpmbTrack;
use App\Models\SpmbWave;
use App\Models\Student;
use App\Services\AuditTrailService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpmbController extends Controller
{
    /**
     * Dashboard & Manajemen Utama Seleksi Penerimaan Murid Baru (SPMB SMA)
     */
    public function index(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = PpdbRegistration::query();

        // Filter Jalur SPMB
        if ($track = $request->query('track')) {
            if ($track === 'TKA') {
                $query->where(function ($q) {
                    $q->where('selection_track', 'Tes Kemampuan Akademik (TKA)')
                        ->orWhere('selection_track', 'like', '%TKA%');
                });
            } else {
                $query->where('selection_track', $track);
            }
        }

        // Filter Status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Filter Gelombang Pendaftaran
        if ($waveId = $request->query('wave_id')) {
            $query->where('spmb_wave_id', $waveId);
        }

        // Pencarian Nama / NISN / No. Registrasi
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('tka_exam_number', 'like', "%{$search}%")
                    ->orWhere('school_origin', 'like', "%{$search}%");
            });
        }

        $registrations = $query->latest()->paginate(15)->withQueryString();

        // 1. Daya Tampung & Kuota Resmi SPMB Jenjang SMA
        // Daya Tampung Total: 10 Rombel x 36 Siswa = 360 Kuota
        $totalCapacity = (int) SchoolSetting::get('spmb_total_capacity', 360);

        // Ambil Seluruh Jalur SPMB (Dapat Diedit / Custom)
        $tracks = SpmbTrack::orderBy('sort_order')->orderBy('id')->get();

        // Alokasi Kuota Persentase Sesuai Aturan Terbaru + Jalur TKA
        $quotaZonasi = (int) round($totalCapacity * 0.50); // Min 50%
        $quotaAfirmasi = (int) round($totalCapacity * 0.15); // Min 15%
        $quotaPerpindahan = (int) round($totalCapacity * 0.05); // Max 5%
        $quotaPrestasi = (int) round($totalCapacity * 0.10); // 10%
        $quotaTka = (int) round($totalCapacity * 0.20); // 20% Khusus Jalur Nilai TKA

        // 2. Statistik Realtime Pendaftar
        $totalRegistered = PpdbRegistration::count();
        $totalVerified = PpdbRegistration::whereIn('status', ['Terverifikasi', 'Lolos Seleksi', 'Daftar Ulang'])->count();
        $totalAdmitted = PpdbRegistration::whereIn('status', ['Lolos Seleksi', 'Daftar Ulang'])->count();

        $countZonasi = PpdbRegistration::where('selection_track', 'like', '%Zonasi%')->count();
        $countAfirmasi = PpdbRegistration::where('selection_track', 'like', '%Afirmasi%')->count();
        $countPerpindahan = PpdbRegistration::where('selection_track', 'like', '%Perpindahan%')->count();
        $countPrestasi = PpdbRegistration::where('selection_track', 'like', '%Prestasi%')->count();
        $countTka = PpdbRegistration::tkaTrack()->count();

        // Counter dinamis untuk semua jalur (termasuk custom track)
        $countPerTrack = [];
        foreach ($tracks as $t) {
            $countPerTrack[$t->id] = PpdbRegistration::where(function ($q) use ($t) {
                $q->where('spmb_track_id', $t->id)
                    ->orWhere('selection_track', $t->name)
                    ->orWhere('selection_track', $t->code);
            })->count();
        }

        // Rombel Kelas X untuk Penempatan Siswa Baru
        $availableClasses = SchoolClass::where('grade', 'X')->orderBy('name')->get();
        $waves = SpmbWave::withCount('registrations')->orderBy('wave_number')->get();

        return view('spmb.index', compact(
            'school',
            'registrations',
            'waves',
            'tracks',
            'countPerTrack',
            'totalCapacity',
            'quotaZonasi',
            'quotaAfirmasi',
            'quotaPerpindahan',
            'quotaPrestasi',
            'quotaTka',
            'totalRegistered',
            'totalVerified',
            'totalAdmitted',
            'countZonasi',
            'countAfirmasi',
            'countPerpindahan',
            'countPrestasi',
            'countTka',
            'availableClasses'
        ));
    }

    /**
     * Portal Khusus Jalur SPMB Melalui Nilai TKA (Tes Kemampuan Akademik)
     */
    public function tkaIndex(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = PpdbRegistration::tkaTrack();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($tkaStatus = $request->query('tka_status')) {
            $query->where('tka_status', $tkaStatus);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('tka_exam_number', 'like', "%{$search}%")
                    ->orWhere('school_origin', 'like', "%{$search}%");
            });
        }

        // Urutkan berdasarkan Peringkat / Skor Akhir Terbobot
        $tkaParticipants = $query->orderByRaw('COALESCE(tka_weighted_score, 0) DESC')
            ->orderByRaw('COALESCE(tka_score_total, 0) DESC')
            ->orderBy('average_score', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Statistik Jalur TKA
        $totalTka = PpdbRegistration::tkaTrack()->count();
        $completedExamCount = PpdbRegistration::tkaTrack()->where('tka_status', 'Selesai Ujian')->count();
        $admittedTkaCount = PpdbRegistration::tkaTrack()->whereIn('status', ['Lolos Seleksi', 'Daftar Ulang'])->count();

        $avgLiteracy = round((float) PpdbRegistration::tkaTrack()->whereNotNull('tka_score_literacy')->avg('tka_score_literacy'), 2);
        $avgNumeracy = round((float) PpdbRegistration::tkaTrack()->whereNotNull('tka_score_numeracy')->avg('tka_score_numeracy'), 2);
        $avgScience = round((float) PpdbRegistration::tkaTrack()->whereNotNull('tka_score_science')->avg('tka_score_science'), 2);
        $avgOverallTka = round((float) PpdbRegistration::tkaTrack()->whereNotNull('tka_score_total')->avg('tka_score_total'), 2);

        $quotaTka = (int) round(((int) SchoolSetting::get('spmb_total_capacity', 360)) * 0.20);

        return view('spmb.tka', compact(
            'school',
            'tkaParticipants',
            'totalTka',
            'completedExamCount',
            'admittedTkaCount',
            'avgLiteracy',
            'avgNumeracy',
            'avgScience',
            'avgOverallTka',
            'quotaTka'
        ));
    }

    /**
     * Simpan Pendaftaran Baru Calon Siswa SPMB
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:30'],
            'nik' => ['nullable', 'string', 'max:30'],
            'gender' => ['required', 'in:L,P'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'school_origin' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string'],
            'distance_km' => ['nullable', 'numeric', 'min:0'],
            'parent_name' => ['nullable', 'string', 'max:150'],
            'parent_phone' => ['nullable', 'string', 'max:30'],
            'average_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'selection_track' => ['required', 'string'],
            'achievement_name' => ['nullable', 'string', 'max:255'],
            'achievement_level' => ['nullable', 'string', 'max:50'],
            'kip_pip_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $trackObj = null;
        if ($request->filled('spmb_track_id')) {
            $trackObj = SpmbTrack::find($request->input('spmb_track_id'));
        }
        if (! $trackObj) {
            $trackObj = SpmbTrack::where('name', $validated['selection_track'])
                ->orWhere('code', $validated['selection_track'])
                ->first();
        }

        if ($trackObj) {
            $validated['spmb_track_id'] = $trackObj->id;
            $validated['selection_track'] = $trackObj->name;
        }

        $track = $validated['selection_track'];
        $validated['registration_number'] = PpdbRegistration::generateRegistrationNumber($track);
        $validated['status'] = 'Terdaftar';
        $validated['qr_code_token'] = Str::uuid()->toString();

        // Jika pendaftar memilih jalur TKA, otomatis alokasikan nomor ujian & sesi
        if (($trackObj && $trackObj->is_tka) || str_contains($track, 'TKA')) {
            $validated['tka_exam_number'] = PpdbRegistration::generateTkaExamNumber();
            $validated['tka_exam_date'] = Carbon::now()->addDays(7)->toDateString();
            $validated['tka_exam_session'] = 'Sesi 1 (Lab Komputer CBT SMA)';
            $validated['tka_status'] = 'Belum Ujian';
        }

        $reg = PpdbRegistration::create($validated);

        AuditTrailService::log('create', 'spmb', "Mendaftarkan calon siswa baru jalur {$reg->selection_track}: {$reg->full_name} ({$reg->registration_number})");

        return back()->with('success', "Pendaftaran SPMB berhasil disimpan dengan Nomor Registrasi: {$reg->registration_number}".(! empty($reg->tka_exam_number) ? " dan No. Ujian TKA: {$reg->tka_exam_number}" : ''));
    }

    /**
     * Tampilkan Detail Lengkap Calon Siswa SPMB & Hasil TKA
     */
    public function show(PpdbRegistration $spmb): View
    {
        $school = SchoolIdentity::current();
        $spmb->load(['spmbTrack', 'spmbWave', 'verifier']);
        $availableClasses = SchoolClass::where('grade', 'X')->orderBy('name')->get();

        return view('spmb.show', compact('spmb', 'school', 'availableClasses'));
    }

    /**
     * Perbarui Data Calon Siswa SPMB
     */
    public function update(Request $request, PpdbRegistration $spmb): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:30'],
            'nik' => ['nullable', 'string', 'max:30'],
            'gender' => ['required', 'in:L,P'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'school_origin' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string'],
            'distance_km' => ['nullable', 'numeric', 'min:0'],
            'parent_name' => ['nullable', 'string', 'max:150'],
            'parent_phone' => ['nullable', 'string', 'max:30'],
            'average_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'selection_track' => ['required', 'string'],
            'status' => ['required', 'string'],
            'assigned_class' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $spmb->update($validated);

        AuditTrailService::log('update', 'spmb', "Memperbarui data calon siswa SPMB: {$spmb->full_name} ({$spmb->registration_number})");

        return back()->with('success', 'Data calon siswa SPMB berhasil diperbarui.');
    }

    /**
     * Input / Update Skor Nilai TKA (Tes Kemampuan Akademik)
     */
    public function updateTkaScore(Request $request, PpdbRegistration $spmb): RedirectResponse
    {
        $validated = $request->validate([
            'tka_score_literacy' => ['required', 'numeric', 'min:0', 'max:100'],
            'tka_score_numeracy' => ['required', 'numeric', 'min:0', 'max:100'],
            'tka_score_science' => ['required', 'numeric', 'min:0', 'max:100'],
            'tka_exam_session' => ['nullable', 'string', 'max:100'],
            'tka_exam_date' => ['nullable', 'date'],
            'tka_status' => ['nullable', 'string'],
        ]);

        $spmb->fill($validated);
        $spmb->computeTkaScores(0.60, 0.40); // 60% Nilai TKA + 40% Rapor SMP
        $spmb->save();

        // Otomatis hitung ulang perangkingan peserta TKA
        $this->recalculateTkaRanks();

        AuditTrailService::log('update', 'spmb_tka', "Memperbarui skor nilai TKA siswa: {$spmb->full_name} -> Skor Terbobot: {$spmb->tka_weighted_score}");

        return back()->with('success', "Nilai TKA berhasil disimpan! Rata-rata TKA: {$spmb->tka_score_total}, Skor Akhir Terbobot: {$spmb->tka_weighted_score}");
    }

    /**
     * Hitung Ulang Perangkingan Otomatis Peserta Jalur TKA
     */
    public function calculateTkaRankings(): RedirectResponse
    {
        $this->recalculateTkaRanks();

        AuditTrailService::log('update', 'spmb_tka', 'Menghitung ulang perangkingan seleksi seluruh peserta Jalur TKA');

        return back()->with('success', 'Perangkingan peserta seleksi jalur TKA berhasil diperbarui secara otomatis.');
    }

    /**
     * Penetapan Otomatis Kelulusan Peserta Jalur TKA Berdasarkan Kuota
     */
    public function autoAdmitTka(Request $request): RedirectResponse
    {
        $this->recalculateTkaRanks();

        $quota = (int) $request->input('quota', round(((int) SchoolSetting::get('spmb_total_capacity', 360)) * 0.20));

        // Peringkat 1 s/d kuota ditetapkan "Lolos Seleksi"
        PpdbRegistration::tkaTrack()
            ->whereNotNull('tka_rank')
            ->where('tka_rank', '<=', $quota)
            ->where('status', '!=', 'Daftar Ulang')
            ->update([
                'status' => 'Lolos Seleksi',
                'passed_at' => now(),
            ]);

        // Peringkat di atas kuota ditetapkan "Cadangan" / "Tidak Lolos"
        PpdbRegistration::tkaTrack()
            ->whereNotNull('tka_rank')
            ->where('tka_rank', '>', $quota)
            ->whereNotIn('status', ['Lolos Seleksi', 'Daftar Ulang'])
            ->update([
                'status' => 'Cadangan',
            ]);

        AuditTrailService::log('update', 'spmb_tka', "Penetapan otomatis kelulusan jalur TKA untuk kuota {$quota} peserta teratas.");

        return back()->with('success', "Penetapan hasil seleksi TKA selesai! Peserta ranking 1 s/d {$quota} dinyatakan Lolos Seleksi.");
    }

    /**
     * Update Status Verifikasi / Seleksi Pendaftar
     */
    public function updateStatus(Request $request, PpdbRegistration $spmb): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'assigned_class' => ['nullable', 'string', 'max:50'],
            'rejection_reason' => ['nullable', 'string'],
        ]);

        $status = $validated['status'];
        $spmb->status = $status;
        $spmb->assigned_class = $validated['assigned_class'] ?? $spmb->assigned_class;
        $spmb->rejection_reason = $validated['rejection_reason'] ?? null;

        if ($status === 'Terverifikasi' && ! $spmb->verified_at) {
            $spmb->verified_at = now();
            $spmb->verified_by = Auth::id();
        }

        if (in_array($status, ['Lolos Seleksi', 'Diterima']) && ! $spmb->passed_at) {
            $spmb->passed_at = now();
        }

        $spmb->save();

        // Jika status Daftar Ulang, otomatis integrasikan menjadi Siswa Aktif SISSMA1LE jika belum ada
        if ($status === 'Daftar Ulang') {
            $this->convertPpdbToStudent($spmb);
        }

        AuditTrailService::log('update', 'spmb', "Mengubah status pendaftaran SPMB: {$spmb->full_name} -> {$status}");

        return back()->with('success', "Status calon siswa berhasil diperbarui menjadi {$status}".($status === 'Daftar Ulang' ? ' dan telah dikonversi ke Master Siswa Aktif SISSMA1LE!' : ''));
    }

    /**
     * Konversi Calon Siswa SPMB Menjadi Siswa Aktif SISSMA1LE
     */
    public function promoteToStudent(Request $request, PpdbRegistration $spmb): RedirectResponse
    {
        $student = $this->convertPpdbToStudent($spmb, $request->input('class_name'));

        AuditTrailService::log('create', 'spmb_promotion', "Mengonversi calon siswa SPMB {$spmb->full_name} ({$spmb->registration_number}) menjadi siswa aktif: {$student->name} (NIS: {$student->nis})");

        return back()->with('success', "Calon siswa {$spmb->full_name} berhasil ditetapkan sebagai Siswa Aktif SMAN 1 Lengkong dengan NIS {$student->nis} di rombel {$student->current_rombel}.");
    }

    /**
     * Cetak Kartu Peserta Ujian TKA / Formulir Pendaftaran SPMB
     */
    public function printCard(PpdbRegistration $spmb): View
    {
        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'spmb', "Mencetak kartu peserta/pendaftaran SPMB: {$spmb->full_name}");

        return view('spmb.print_card', compact('spmb', 'school'));
    }

    /**
     * Cetak Surat Keterangan Hasil Seleksi / Pengumuman SPMB
     */
    public function printLetter(PpdbRegistration $spmb): View
    {
        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'spmb', "Mencetak SK hasil seleksi SPMB: {$spmb->full_name}");

        return view('spmb.print_letter', compact('spmb', 'school'));
    }

    /**
     * Cetak Rekapitulasi Hasil SPMB & Perangkingan TKA
     */
    public function printRecap(Request $request): View
    {
        $school = SchoolIdentity::current();
        $track = $request->query('track', 'all');

        $query = PpdbRegistration::query();
        if ($track === 'TKA') {
            $query->tkaTrack()->orderByRaw('COALESCE(tka_weighted_score, 0) DESC');
        } elseif ($track !== 'all') {
            $query->where('selection_track', $track)->orderBy('average_score', 'desc');
        } else {
            $query->orderBy('selection_track')->orderBy('average_score', 'desc');
        }

        $participants = $query->get();

        AuditTrailService::log('print', 'spmb', "Mencetak rekapitulasi penetapan hasil seleksi SPMB (Jalur: {$track})");

        return view('spmb.print_recap', compact('school', 'participants', 'track'));
    }

    /**
     * Ekspor Data SPMB & Nilai TKA ke CSV
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $track = $request->query('track');
        $query = PpdbRegistration::query();

        if ($track === 'TKA') {
            $query->tkaTrack();
        } elseif ($track) {
            $query->where('selection_track', $track);
        }

        $rows = $query->orderBy('selection_track')->orderBy('full_name')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="data_spmb_sma1le_'.date('Ymd_His').'.csv"',
        ];

        return response()->stream(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM UTF-8

            fputcsv($handle, [
                'No. Registrasi',
                'No. Ujian TKA',
                'Nama Lengkap',
                'NISN',
                'NIK',
                'L/P',
                'Asal Sekolah',
                'Jalur SPMB',
                'Nilai Rapor SMP',
                'Skor TKA Literasi',
                'Skor TKA Numerasi',
                'Skor TKA Sains/Logika',
                'Rata-rata TKA',
                'Skor Akhir Terbobot TKA',
                'Peringkat TKA',
                'Status Seleksi',
                'Penempatan Rombel',
                'Nama Orang Tua',
                'No. HP',
            ]);

            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r->registration_number,
                    $r->tka_exam_number ?? '-',
                    $r->full_name,
                    $r->nisn,
                    $r->nik ?? '-',
                    $r->gender,
                    $r->school_origin ?? '-',
                    $r->selection_track,
                    number_format((float) $r->average_score, 2),
                    $r->tka_score_literacy ? number_format((float) $r->tka_score_literacy, 2) : '-',
                    $r->tka_score_numeracy ? number_format((float) $r->tka_score_numeracy, 2) : '-',
                    $r->tka_score_science ? number_format((float) $r->tka_score_science, 2) : '-',
                    $r->tka_score_total ? number_format((float) $r->tka_score_total, 2) : '-',
                    $r->tka_weighted_score ? number_format((float) $r->tka_weighted_score, 2) : '-',
                    $r->tka_rank ?? '-',
                    $r->status,
                    $r->assigned_class ?? '-',
                    $r->parent_name ?? '-',
                    $r->parent_phone ?? '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Hapus Pendaftar SPMB
     */
    public function destroy(PpdbRegistration $spmb): RedirectResponse
    {
        $name = $spmb->full_name;
        $spmb->delete();

        AuditTrailService::log('delete', 'spmb', "Menghapus pendaftar SPMB: {$name}");

        return back()->with('success', "Data pendaftaran calon siswa {$name} berhasil dihapus.");
    }

    /**
     * Halaman Kelola & Kustomisasi Jalur Seleksi SPMB
     */
    public function tracksIndex(Request $request): View
    {
        $school = SchoolIdentity::current();
        $totalCapacity = (int) SchoolSetting::get('spmb_total_capacity', 360);
        $tracks = SpmbTrack::withCount('registrations')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $totalAllocatedPercentage = $tracks->sum('quota_percentage');
        $totalAllocatedSeats = $tracks->sum(fn ($t) => $t->getCalculatedSeats($totalCapacity));

        return view('spmb.tracks', compact(
            'school',
            'tracks',
            'totalCapacity',
            'totalAllocatedPercentage',
            'totalAllocatedSeats'
        ));
    }

    /**
     * Tambah Jalur Seleksi SPMB Baru (Custom Track)
     */
    public function storeTrack(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50', 'unique:spmb_tracks,code'],
            'quota_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'quota_seats' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'is_tka' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (empty($validated['code'])) {
            $baseCode = Str::slug($validated['name']);
            $code = $baseCode;
            $counter = 1;
            while (SpmbTrack::where('code', $code)->exists()) {
                $code = $baseCode.'_'.$counter++;
            }
            $validated['code'] = $code;
        }

        $validated['is_tka'] = $request->boolean('is_tka', false);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['quota_percentage'] = (float) ($validated['quota_percentage'] ?? 0);
        $validated['quota_seats'] = (int) ($validated['quota_seats'] ?? 0);

        $track = SpmbTrack::create($validated);

        AuditTrailService::log('create', 'spmb_track', "Menambahkan jalur seleksi SPMB: {$track->name} ({$track->code})");

        return back()->with('success', "Jalur seleksi '{$track->name}' berhasil ditambahkan.");
    }

    /**
     * Perbarui / Edit Jalur Seleksi SPMB (Custom Track)
     */
    public function updateTrack(Request $request, SpmbTrack $track): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', Rule::unique('spmb_tracks', 'code')->ignore($track->id)],
            'quota_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'quota_seats' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'is_tka' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_tka'] = $request->boolean('is_tka', false);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? $track->sort_order);
        $validated['quota_percentage'] = (float) ($validated['quota_percentage'] ?? 0);
        $validated['quota_seats'] = (int) ($validated['quota_seats'] ?? 0);

        $track->update($validated);

        AuditTrailService::log('update', 'spmb_track', "Memperbarui jalur seleksi SPMB: {$track->name}");

        return back()->with('success', "Jalur seleksi '{$track->name}' berhasil diperbarui.");
    }

    /**
     * Hapus Jalur Seleksi SPMB
     */
    public function destroyTrack(SpmbTrack $track): RedirectResponse
    {
        $regCount = $track->registrations()->count();
        if ($regCount > 0) {
            $track->update(['is_active' => false]);
            AuditTrailService::log('update', 'spmb_track', "Menonaktifkan jalur seleksi SPMB {$track->name} karena memiliki {$regCount} pendaftar.");

            return back()->with('info', "Jalur '{$track->name}' memiliki {$regCount} pendaftar aktif, sehingga dinonaktifkan (tidak dihapus permanen).");
        }

        $name = $track->name;
        $track->delete();

        AuditTrailService::log('delete', 'spmb_track', "Menghapus jalur seleksi SPMB: {$name}");

        return back()->with('success', "Jalur seleksi '{$name}' berhasil dihapus.");
    }

    /**
     * Manajemen Jadwal Gelombang Pendaftaran SPMB & Alokasi Jalur per Gelombang
     */
    public function wavesIndex(Request $request): View
    {
        $school = SchoolIdentity::current();
        $waves = SpmbWave::withCount('registrations')->orderBy('wave_number')->get();
        $tracks = SpmbTrack::active()->orderBy('sort_order')->orderBy('id')->get();
        $totalCapacity = (int) SchoolSetting::get('spmb_total_capacity', 360);

        return view('spmb.waves', compact('school', 'waves', 'tracks', 'totalCapacity'));
    }

    /**
     * Simpan Gelombang Pendaftaran Baru
     */
    public function storeWave(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'wave_number' => ['required', 'integer', 'min:1'],
            'academic_year' => ['required', 'string', 'max:20'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'announcement_date' => ['nullable', 'date'],
            're_registration_start' => ['nullable', 'date'],
            're_registration_end' => ['nullable', 'date'],
            'quota_seats' => ['required', 'integer', 'min:0'],
            'allowed_tracks' => ['nullable', 'array'],
            'allowed_tracks.*' => ['integer', 'exists:spmb_tracks,id'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['allowed_tracks'] = ! empty($validated['allowed_tracks']) ? array_map('intval', $validated['allowed_tracks']) : [];

        $wave = SpmbWave::create($validated);

        AuditTrailService::log('create', 'spmb_wave', "Menambah gelombang pendaftaran SPMB: {$wave->name}");

        return back()->with('success', "Gelombang pendaftaran '{$wave->name}' berhasil ditambahkan.");
    }

    /**
     * Perbarui Data Gelombang Pendaftaran SPMB
     */
    public function updateWave(Request $request, SpmbWave $wave): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'wave_number' => ['required', 'integer', 'min:1'],
            'academic_year' => ['required', 'string', 'max:20'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'announcement_date' => ['nullable', 'date'],
            're_registration_start' => ['nullable', 'date'],
            're_registration_end' => ['nullable', 'date'],
            'quota_seats' => ['required', 'integer', 'min:0'],
            'allowed_tracks' => ['nullable', 'array'],
            'allowed_tracks.*' => ['integer', 'exists:spmb_tracks,id'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['allowed_tracks'] = ! empty($validated['allowed_tracks']) ? array_map('intval', $validated['allowed_tracks']) : [];

        $wave->update($validated);

        AuditTrailService::log('update', 'spmb_wave', "Memperbarui gelombang pendaftaran SPMB: {$wave->name}");

        return back()->with('success', "Gelombang pendaftaran '{$wave->name}' berhasil diperbarui.");
    }

    /**
     * Aktifkan / Nonaktifkan Status Gelombang Pendaftaran
     */
    public function toggleWaveStatus(SpmbWave $wave): RedirectResponse
    {
        $wave->update(['is_active' => ! $wave->is_active]);

        $statusStr = $wave->is_active ? 'diaktifkan' : 'dinonaktifkan';
        AuditTrailService::log('update', 'spmb_wave', "Mengubah status gelombang {$wave->name} menjadi {$statusStr}");

        return back()->with('success', "Gelombang '{$wave->name}' berhasil {$statusStr}.");
    }

    /**
     * Hapus Gelombang Pendaftaran
     */
    public function destroyWave(SpmbWave $wave): RedirectResponse
    {
        $regCount = $wave->registrations()->count();
        if ($regCount > 0) {
            $wave->update(['is_active' => false]);
            AuditTrailService::log('update', 'spmb_wave', "Menonaktifkan gelombang {$wave->name} karena memiliki {$regCount} pendaftar.");

            return back()->with('info', "Gelombang '{$wave->name}' memiliki {$regCount} pendaftar, sehingga statusnya dinonaktifkan (tidak dihapus permanen).");
        }

        $name = $wave->name;
        $wave->delete();

        AuditTrailService::log('delete', 'spmb_wave', "Menghapus gelombang pendaftaran SPMB: {$name}");

        return back()->with('success', "Gelombang pendaftaran '{$name}' berhasil dihapus.");
    }

    /**
     * Update Daya Tampung Total Sekolah & Kuota Jalur
     */
    public function updateCapacity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'total_capacity' => ['required', 'integer', 'min:10', 'max:2000'],
            'track_quotas' => ['nullable', 'array'],
            'track_quotas.*' => ['nullable', 'integer', 'min:0'],
        ]);

        SchoolSetting::set('spmb_total_capacity', $validated['total_capacity']);

        if (! empty($validated['track_quotas'])) {
            foreach ($validated['track_quotas'] as $trackId => $quotaSeats) {
                if ($track = SpmbTrack::find($trackId)) {
                    $track->update(['quota_seats' => (int) $quotaSeats]);
                }
            }
        }

        AuditTrailService::log('update', 'spmb_capacity', "Memperbarui daya tampung total SPMB: {$validated['total_capacity']} kursi");

        return back()->with('success', "Daya tampung total sekolah dan alokasi kuota jalur SPMB berhasil diperbarui menjadi {$validated['total_capacity']} kursi.");
    }

    /**
     * Halaman Publik Pendaftaran SPMB untuk Calon Siswa & Orang Tua
     */
    public function publicRegister(Request $request): View
    {
        $school = SchoolIdentity::current();
        $waves = SpmbWave::where('is_active', true)->orderBy('wave_number')->get();
        $allTracks = SpmbTrack::active()->orderBy('sort_order')->orderBy('id')->get();
        $totalCapacity = (int) SchoolSetting::get('spmb_total_capacity', 360);

        // Pilih gelombang yang diminta atau yang saat ini sedang buka
        $selectedWaveId = $request->query('wave_id');
        $selectedWave = null;
        if ($selectedWaveId) {
            $selectedWave = $waves->firstWhere('id', (int) $selectedWaveId);
        }
        if (! $selectedWave) {
            $selectedWave = $waves->first(fn ($w) => $w->isOpen()) ?? $waves->first();
        }

        // Filter jalur yang diizinkan pada gelombang terpilih
        $tracks = $selectedWave ? $selectedWave->allowedTracksCollection() : $allTracks;

        return view('spmb.public_register', compact('school', 'waves', 'selectedWave', 'tracks', 'allTracks', 'totalCapacity'));
    }

    /**
     * Proses Simpan Pendaftaran Online Publik Lengkap dengan Upload Berkas & Entri Nilai Rapor
     */
    public function storePublicRegister(Request $request): RedirectResponse
    {
        $isPublicForm = $request->boolean('is_public_form') || $request->hasFile('ktp_parent_file') || $request->has('report_score_sem1');

        $validated = $request->validate([
            'spmb_wave_id' => ['nullable', 'exists:spmb_waves,id'],
            'spmb_track_id' => ['required', 'exists:spmb_tracks,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:20'],
            'nik' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'in:L,P'],
            'birth_place' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date'],
            'school_origin' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string'],
            'distance_km' => ['nullable', 'numeric', 'min:0'],
            'parent_name' => ['required', 'string', 'max:150'],
            'parent_phone' => ['required', 'string', 'max:30'],
            'parent_job' => ['nullable', 'string', 'max:100'],
            'parent_transfer_institution' => ['nullable', 'string', 'max:150'],
            'achievement_name' => ['nullable', 'string', 'max:255'],
            'achievement_level' => ['nullable', 'string', 'max:50'],
            'kip_pip_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'average_score' => ['nullable', 'numeric', 'min:0', 'max:100'],

            // Entri Nilai Rapor SMP/MTs (Semester 1 - 5 Wajib, Semester 6 Opsional)
            'report_score_sem1' => [$isPublicForm ? 'required' : 'nullable', 'numeric', 'min:0', 'max:100'],
            'report_score_sem2' => [$isPublicForm ? 'required' : 'nullable', 'numeric', 'min:0', 'max:100'],
            'report_score_sem3' => [$isPublicForm ? 'required' : 'nullable', 'numeric', 'min:0', 'max:100'],
            'report_score_sem4' => [$isPublicForm ? 'required' : 'nullable', 'numeric', 'min:0', 'max:100'],
            'report_score_sem5' => [$isPublicForm ? 'required' : 'nullable', 'numeric', 'min:0', 'max:100'],
            'report_score_sem6' => ['nullable', 'numeric', 'min:0', 'max:100'],

            // Nilai TKA (Jika ada / Jalur TKA)
            'tka_score_total' => ['nullable', 'numeric', 'min:0', 'max:100'],

            // Upload Berkas Dokumen Utama Wajib
            'ktp_parent_file' => [$isPublicForm ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'kk_file' => [$isPublicForm ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'birth_cert_file' => [$isPublicForm ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'pip_kip_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],

            // Upload Berkas Rapor SMP/MTs (Semester 1 - 5 Wajib, Semester 6 Opsional)
            'report_file_sem1' => [$isPublicForm ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'report_file_sem2' => [$isPublicForm ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'report_file_sem3' => [$isPublicForm ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'report_file_sem4' => [$isPublicForm ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'report_file_sem5' => [$isPublicForm ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'report_file_sem6' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],

            // Upload Sertifikat / Bukti TKA
            'tka_certificate_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ]);

        // Cek apakah NISN sudah pernah terdaftar
        $existing = PpdbRegistration::where('nisn', $validated['nisn'])->first();
        if ($existing) {
            return redirect()->route('spmb.public.success', $existing->registration_number)
                ->with('info', "NISN {$validated['nisn']} sudah terdaftar sebelumnya dengan No. Registrasi {$existing->registration_number}. Berikut adalah kartu bukti pendaftaran Anda.");
        }

        $track = SpmbTrack::findOrFail($validated['spmb_track_id']);
        $validated['selection_track'] = $track->name;
        $validated['registration_number'] = PpdbRegistration::generateRegistrationNumber($track->name);
        $validated['status'] = 'Terdaftar';
        $validated['qr_code_token'] = Str::uuid()->toString();

        // Hitung Rata-rata Nilai Rapor SMP/MTs dari Semester 1 - 5 (+ 6 jika diisi)
        if (isset($validated['report_score_sem1']) && is_numeric($validated['report_score_sem1'])) {
            $semScores = [
                (float) $validated['report_score_sem1'],
                (float) $validated['report_score_sem2'],
                (float) $validated['report_score_sem3'],
                (float) $validated['report_score_sem4'],
                (float) $validated['report_score_sem5'],
            ];
            if (isset($validated['report_score_sem6']) && is_numeric($validated['report_score_sem6'])) {
                $semScores[] = (float) $validated['report_score_sem6'];
            }
            $validated['average_score'] = round(array_sum($semScores) / count($semScores), 2);
        } else {
            $validated['average_score'] = (float) $request->input('average_score', 0);
        }

        // Upload Berkas Dokumen ke Storage Public
        $nisnSanitized = preg_replace('/[^0-9]/', '', $validated['nisn']);
        $fileFields = [
            'ktp_parent_file',
            'kk_file',
            'birth_cert_file',
            'pip_kip_file',
            'report_file_sem1',
            'report_file_sem2',
            'report_file_sem3',
            'report_file_sem4',
            'report_file_sem5',
            'report_file_sem6',
            'tka_certificate_file',
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $ext = $file->getClientOriginalExtension();
                $filename = "{$field}_{$nisnSanitized}_".time().'.'.$ext;
                $path = $file->storeAs("spmb_documents/{$nisnSanitized}", $filename, 'public');
                $validated[$field] = $path;
            }
        }

        // Alokasikan Ujian TKA / Skor Awal TKA jika jalur TKA
        if ($track->is_tka || str_contains($track->name, 'TKA')) {
            $validated['tka_exam_number'] = PpdbRegistration::generateTkaExamNumber();
            $validated['tka_exam_date'] = Carbon::now()->addDays(7)->toDateString();
            $validated['tka_exam_session'] = 'Sesi 1 (08.00 - 10.00 WIB) - Lab Komputer CBT SMA';
            $validated['tka_status'] = ! empty($validated['tka_score_total']) ? 'Selesai Ujian' : 'Belum Ujian';

            if (! empty($validated['tka_score_total'])) {
                $tka = (float) $validated['tka_score_total'];
                $validated['tka_score_total'] = $tka;
                $validated['tka_weighted_score'] = round(($tka * 0.60) + ($validated['average_score'] * 0.40), 2);
            }
        }

        $reg = PpdbRegistration::create($validated);

        AuditTrailService::log('create', 'spmb_public', "Calon siswa mendaftar online jalur {$reg->selection_track}: {$reg->full_name} ({$reg->registration_number})");

        return redirect()->route('spmb.public.success', $reg->registration_number)
            ->with('success', "Pendaftaran SPMB berhasil! Nomor Registrasi Anda: {$reg->registration_number}. Berkas dan nilai rapor telah diarsipkan.");
    }

    /**
     * Halaman Bukti Pendaftaran Sukses untuk Calon Siswa (Publik)
     */
    public function publicSuccess(string $registrationNumber): View
    {
        $school = SchoolIdentity::current();
        $spmb = PpdbRegistration::where('registration_number', $registrationNumber)->firstOrFail();

        return view('spmb.public_success', compact('spmb', 'school'));
    }

    /**
     * Cetak Bukti Kartu Pendaftaran Publik
     */
    public function publicPrintCard(string $registrationNumber): View
    {
        $school = SchoolIdentity::current();
        $spmb = PpdbRegistration::where('registration_number', $registrationNumber)->firstOrFail();

        AuditTrailService::log('print', 'spmb_public', "Mencetak kartu bukti pendaftaran SPMB online: {$spmb->full_name} ({$spmb->registration_number})");

        return view('spmb.print_card', compact('spmb', 'school'));
    }

    /**
     * Internal: Hitung Ulang Peringkat TKA
     */
    private function recalculateTkaRanks(): void
    {
        $participants = PpdbRegistration::tkaTrack()
            ->orderByRaw('COALESCE(tka_weighted_score, 0) DESC')
            ->orderByRaw('COALESCE(tka_score_total, 0) DESC')
            ->orderBy('average_score', 'desc')
            ->get();

        $rank = 1;
        foreach ($participants as $p) {
            $p->tka_rank = $rank++;
            $p->saveQuietly();
        }
    }

    /**
     * Internal: Konversi Pendaftar Lolos/Daftar Ulang ke Tabel students
     */
    private function convertPpdbToStudent(PpdbRegistration $spmb, ?string $assignedClass = null): Student
    {
        $existing = Student::where('nisn', $spmb->nisn)->first();
        if ($existing) {
            return $existing;
        }

        $className = $assignedClass ?? $spmb->assigned_class ?? 'X Merdeka 1';
        $class = SchoolClass::where('name', $className)->first()
            ?? SchoolClass::where('grade', 'X')->first();

        $nextNis = 'SMA-'.date('y').str_pad((string) (Student::count() + 1), 4, '0', STR_PAD_LEFT);

        return Student::create([
            'class_id' => $class?->id,
            'nis' => $nextNis,
            'nisn' => $spmb->nisn,
            'nik' => $spmb->nik,
            'name' => $spmb->full_name,
            'gender' => $spmb->gender,
            'birth_place' => $spmb->birth_place,
            'birth_date' => $spmb->birth_date,
            'school_origin' => $spmb->school_origin,
            'address' => $spmb->address,
            'entry_path' => $spmb->selection_track,
            'admission_date' => now()->toDateString(),
            'admission_class' => $className,
            'current_class_level' => 'X',
            'current_rombel' => $className,
            'status' => 'Aktif',
            'qr_code_token' => Str::uuid()->toString(),
        ]);
    }
}
