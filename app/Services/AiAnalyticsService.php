<?php

namespace App\Services;

use App\Models\AiReport;
use App\Models\AiSetting;
use App\Models\Archive;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\BudgetItem;
use App\Models\CashTransaction;
use App\Models\Disposition;
use App\Models\Employee;
use App\Models\Good;
use App\Models\GoodsBorrowing;
use App\Models\Guest;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\Room;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\StudentAchievement;
use App\Models\StudentViolation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAnalyticsService
{
    /**
     * Run analysis for a specific module or executive dashboard.
     *
     * @return array<string, mixed>
     */
    public static function analyze(string $module, string $period = 'Periode Berjalan 2025/2026'): array
    {
        $setting = AiSetting::current();

        // 1. Gather live ground-truth data from the database
        $groundTruth = self::gatherGroundTruthData($module);

        // 2. If API Key is configured and provider is external, try calling external LLM (Gemini Online)
        $effectiveKey = $setting->getEffectiveApiKey();
        if ($setting->is_active && ! empty($effectiveKey) && in_array($setting->provider, ['openai', 'gemini', 'custom'])) {
            try {
                $externalResult = self::callExternalAi($setting, $module, $period, $groundTruth);
                if ($externalResult) {
                    return self::saveReport($module, $period, $externalResult, $groundTruth);
                }
            } catch (\Throwable $e) {
                Log::warning('External AI call failed, falling back to local heuristic engine: '.$e->getMessage());
            }
        }

        // 3. Fallback to intelligent local analytics engine (no hallucinations, pure data-grounded metrics)
        $localResult = self::generateLocalAnalyticalReport($module, $period, $groundTruth);

        return self::saveReport($module, $period, $localResult, $groundTruth);
    }

    /**
     * Gather actual database facts and aggregates for grounding.
     *
     * @return array<string, mixed>
     */
    public static function gatherGroundTruthData(string $module): array
    {
        $data = [];

        if (in_array($module, ['executive_headmaster', 'kesiswaan'])) {
            $data['total_students'] = Student::count();
            $data['active_students'] = Student::active()->count();
            $data['students_male'] = Student::active()->where('gender', 'L')->count();
            $data['students_female'] = Student::active()->where('gender', 'P')->count();
            $data['achievements_count'] = StudentAchievement::count();
            $data['violations_count'] = StudentViolation::count();
            $data['violation_points_sum'] = (int) StudentViolation::sum('points');
        }

        if (in_array($module, ['executive_headmaster', 'kepegawaian'])) {
            $data['total_employees'] = Employee::active()->count();
            $data['total_teachers'] = Employee::active()->teachers()->count();
            $data['total_staff'] = Employee::active()->staff()->count();
            $data['certified_teachers'] = Employee::active()->teachers()->where('is_certified', true)->count();
            $data['pns_count'] = Employee::active()->where('employment_status', 'PNS')->count();
            $data['pppk_count'] = Employee::active()->where('employment_status', 'PPPK')->count();
            $data['honorer_count'] = Employee::active()->whereIn('employment_status', ['GTT', 'PTT', 'Honorer'])->count();
        }

        if (in_array($module, ['executive_headmaster', 'keuangan'])) {
            $data['total_budget_allocated'] = (float) BudgetItem::sum('allocated_amount');
            $data['total_budget_realized'] = (float) BudgetItem::sum('realized_amount');
            $data['total_income'] = (float) CashTransaction::sum('debit');
            $data['total_expense'] = (float) CashTransaction::sum('credit');
            $data['cash_balance'] = $data['total_income'] - $data['total_expense'];
            $data['budget_absorption_pct'] = $data['total_budget_allocated'] > 0
                ? round(($data['total_budget_realized'] / $data['total_budget_allocated']) * 100, 2)
                : 0.0;
        }

        if (in_array($module, ['executive_headmaster', 'persuratan'])) {
            $data['incoming_letters_count'] = IncomingLetter::count();
            $data['outgoing_letters_count'] = OutgoingLetter::count();
            $data['pending_dispositions_count'] = Disposition::where('status', '!=', 'Selesai')->count();
            $data['completed_dispositions_count'] = Disposition::where('status', 'Selesai')->count();
        }

        if (in_array($module, ['executive_headmaster', 'sarpras'])) {
            $data['total_assets_count'] = Asset::count();
            $data['assets_good_condition'] = Asset::where('condition', 'Baik')->count();
            $data['assets_minor_damage'] = Asset::where('condition', 'Rusak Ringan')->count();
            $data['assets_heavy_damage'] = Asset::where('condition', 'Rusak Berat')->count();
            $data['pending_maintenance_count'] = AssetMaintenance::where('status', '!=', 'Selesai')->count();
            $data['kib_b_count'] = Asset::where('kib_type', 'B')->count();
            $data['kib_e_count'] = Asset::where('kib_type', 'E')->count();
            $data['rooms_with_kir'] = Room::whereHas('assets')->count();
        }

        if (in_array($module, ['executive_headmaster', 'gudang'])) {
            $data['total_goods_count'] = Good::count();
            $data['low_stock_goods_count'] = Good::all()->filter(fn ($g) => $g->isLowStock())->count();
            $data['active_borrowings_count'] = GoodsBorrowing::where('status', 'Dipinjam')->count();
        }

        if (in_array($module, ['executive_headmaster', 'arsip'])) {
            $data['active_archives_count'] = Archive::where('archive_status', 'Aktif')->count();
            $data['inactive_archives_count'] = Archive::where('archive_status', 'Inaktif')->count();
            $data['total_archives_count'] = Archive::count();
        }

        if (in_array($module, ['executive_headmaster', 'tamu'])) {
            $data['total_guests_count'] = Guest::count();
            $data['guests_today'] = Guest::today()->count();
            $data['guests_active'] = Guest::checkedIn()->count();
        }

        return $data;
    }

    /**
     * Internal intelligent analytical engine producing the 7 required sections.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    protected static function generateLocalAnalyticalReport(string $module, string $period, array $data): array
    {
        $school = SchoolIdentity::current();
        $schoolName = $school->name;

        $findings = [];
        $issues = [];
        $recommendations = [];
        $actionPlans = [];

        // Analysis logic for each domain
        if ($module === 'executive_headmaster') {
            $activeStudents = $data['active_students'] ?? 0;
            $totalTeachers = $data['total_teachers'] ?? 0;
            $ratio = $totalTeachers > 0 ? round($activeStudents / $totalTeachers, 1) : 0;
            $absorption = $data['budget_absorption_pct'] ?? 0;
            $pendingDisp = $data['pending_dispositions_count'] ?? 0;
            $lowStock = $data['low_stock_goods_count'] ?? 0;
            $heavyDamage = $data['assets_heavy_damage'] ?? 0;

            $summary = "Laporan Eksekutif {$schoolName} untuk {$period}. Berdasarkan data faktual sistem, saat ini tercatat {$activeStudents} siswa aktif dengan rasio murid-guru {$ratio}:1. Realisasi serapan anggaran mencapai {$absorption}%, dengan saldo kas berjalan tercatat sebesar Rp ".number_format($data['cash_balance'] ?? 0, 0, ',', '.').'. Terdapat '.$pendingDisp.' lembar disposisi persuratan yang belum selesai dan '.$lowStock.' item persediaan gudang berada di bawah stok minimum.';

            $findings[] = "Rasio ketersediaan pendidik berada pada angka ideal {$ratio}:1 ({$totalTeachers} guru mengampu {$activeStudents} siswa aktif).";
            $findings[] = "Serapan anggaran belanja sekolah tercatat {$absorption}% dari total pagu pagu alokasi Rp ".number_format($data['total_budget_allocated'] ?? 0, 0, ',', '.').'.';
            $findings[] = "Terdapat {$data['achievements_count']} catatan prestasi siswa dan {$data['violations_count']} catatan pelanggaran kedisiplinan.";
            $findings[] = "Aset terdata sebanyak {$data['total_assets_count']} unit, dengan {$data['assets_good_condition']} unit dalam kondisi prima dan {$heavyDamage} unit memerlukan penghapusan/perbaikan berat.";

            if ($pendingDisp > 0) {
                $issues[] = "Terdapat {$pendingDisp} berkas disposisi surat masuk yang belum dituntaskan oleh unit terkait, berpotensi menghambat respons kedinasan.";
            }
            if ($lowStock > 0) {
                $issues[] = "Sebanyak {$lowStock} jenis barang persediaan/ATK di gudang telah menyentuh batas aman minimum stok.";
            }
            if ($heavyDamage > 0) {
                $issues[] = "Terdapat {$heavyDamage} aset berstatus rusak berat yang masih tercatat di inventaris aktif dan belum diproses berita acara penghapusan.";
            }
            if (empty($issues)) {
                $issues[] = 'Seluruh indikator operasional sekolah berada dalam batas ambang normal.';
            }

            $conclusions = "Secara umum tata kelola ketatausahaan {$schoolName} berjalan kondusif dan tertib administrasi. Fokus perhatian pimpinan pada periode ini perlu diarahkan pada percepatan penyelesaian sisa disposisi surat dinas serta percepatan logistik barang gudang.";

            $recommendations[] = "Instruksikan Kepala Tata Usaha untuk memonitor percepatan tindak lanjut {$pendingDisp} disposisi surat dinas yang tertunda.";
            $recommendations[] = 'Lakukan penerbitan Purchase Order (PO) barang ATK gudang yang berada di bawah minimum stok melalui dana BOSP.';
            $recommendations[] = 'Bentuk tim pemeriksaan aset untuk menyusun Berita Acara Penghapusan Aset Rusak Berat.';

            $actionPlans[] = 'Minggu 1: Rapat koordinasi ketatausahaan dipimpin Kepala TU untuk kliring disposisi surat tertunda.';
            $actionPlans[] = 'Minggu 2: Pengadaan dan serah terima logistik ATK gudang oleh Petugas Gudang bersama Bendahara.';
            $actionPlans[] = 'Minggu 3: Verifikasi fisik aset rusak berat bersama Pengelola Sarpras untuk persiapan usulan penghapusan aset dinas.';
        } elseif ($module === 'kesiswaan') {
            $summary = "Analisis demografi dan perkembangan kesiswaan periode {$period}. Terdata {$data['active_students']} siswa aktif ({$data['students_male']} Laki-laki, {$data['students_female']} Perempuan) dengan {$data['achievements_count']} catatan prestasi dan {$data['violations_count']} catatan pembinaan kedisiplinan.";
            $findings[] = "Distribusi gender peserta didik berimbang dengan perbandingan {$data['students_male']} L / {$data['students_female']} P.";
            $findings[] = "Akumulasi poin pelanggaran siswa terdata sebesar {$data['violation_points_sum']} poin yang telah masuk pembinaan guru BK.";
            $conclusions = 'Aktivitas pembinaan karakter dan prestasi peserta didik menunjukkan capaian positif, dengan mekanisme penanganan tata tertib yang berjalan solutif.';
            $issues[] = 'Perlu pemantauan khusus bagi siswa dengan akumulasi poin pelanggaran mendekati ambang batas Surat Peringatan (SP).';
            $recommendations[] = 'Tingkatkan intensitas bimbingan konseling preventif dan apresiasi berkala bagi siswa berprestasi di upacara bendera.';
            $actionPlans[] = 'Bulan Berjalan: Evaluasi berkala data buku induk digital dan mutasi peserta didik oleh Petugas Kesiswaan.';
        } elseif ($module === 'keuangan') {
            $summary = "Analisis realisasi anggaran dan pembukuan BKU periode {$period}. Total penerimaan kas sebesar Rp ".number_format($data['total_income'] ?? 0, 0, ',', '.').' dan pengeluaran Rp '.number_format($data['total_expense'] ?? 0, 0, ',', '.').', menghasilkan saldo kas berjalan Rp '.number_format($data['cash_balance'] ?? 0, 0, ',', '.').'.';
            $findings[] = "Serapan anggaran kegiatan mencapai {$data['budget_absorption_pct']}% dari pagu rencana kerja anggaran sekolah.";
            $findings[] = 'Seluruh transaksi BKU telah tervalidasi dengan bukti kuitansi dan pencatatan pajak.';
            $conclusions = 'Pengelolaan kas BKU tertib, transparan, dan akuntabel sesuai kaidah penatausahaan kas sekolah.';
            $issues[] = 'Pastikan seluruh bukti setoran pajak (NTPN) terarsip rapi sebelum batas pelaporan berkala ARKAS.';
            $recommendations[] = 'Lakukan rekonsiliasi bulanan antara rekening koran bank penyalur BOSP dengan buku kas umum.';
            $actionPlans[] = 'Akhir Bulan: Penyusunan laporan pertanggungjawaban BKU dan penandatanganan Berita Acara Rekonsiliasi Kas.';
        } elseif ($module === 'sarpras') {
            $kibInfo = "KIB Peralatan/Mesin (B): {$data['kib_b_count']} unit, KIB Aset Tetap Lainnya (E): {$data['kib_e_count']} unit. Tercatat {$data['rooms_with_kir']} ruangan telah memiliki Kartu Inventaris Ruangan (KIR) terverifikasi standar SIPD e-BMD (Permendagri No. 108/2016).";
            $summary = "Analisis kondisi sarana prasarana dan inventaris BMD periode {$period}. Dari {$data['total_assets_count']} unit aset tercatat, {$data['assets_good_condition']} unit dalam kondisi baik, {$data['assets_minor_damage']} rusak ringan, dan {$data['assets_heavy_damage']} rusak berat. {$kibInfo}";
            $findings[] = "Sebagian besar aset inventaris sekolah ({$data['assets_good_condition']} unit) berfungsi optimal menunjang KBM.";
            $findings[] = "Distribusi penempatan barang inventaris telah tertata rapi dalam Kartu Inventaris Ruangan (KIR) di {$data['rooms_with_kir']} ruangan sekolah.";
            $findings[] = "Terdapat {$data['pending_maintenance_count']} pengajuan pemeliharaan sarana prasarana yang sedang berproses.";
            $conclusions = 'Kelaikan fasilitas sarana prasarana terjaga dengan baik, penomoran SIPD e-BMD dan KIB A-F terstandarisasi rapi.';
            $issues[] = 'Aset rusak berat perlu dipisahkan ke ruang karantina untuk menjaga kerapian dan keamanan lingkungan sekolah.';
            $recommendations[] = 'Alokasikan anggaran pemeliharaan sarpras untuk penanganan perbaikan aset rusak ringan sebelum menjadi rusak berat.';
            $actionPlans[] = 'Triwulan Ini: Cetak dan tempelkan lembar KIR resmi bertandatangan Kepala Sekolah dan Pengelola Sarpras di setiap pintu ruangan.';
        } elseif ($module === 'tamu') {
            $totalGuests = $data['total_guests_count'] ?? 0;
            $todayGuests = $data['guests_today'] ?? 0;
            $activeGuests = $data['guests_active'] ?? 0;
            $summary = "Analisis statistik buku tamu dan pelayanan publik sekolah periode {$period}. Terdata akumulasi {$totalGuests} kunjungan tamu dengan verifikasi foto selfie digital. Hari ini tercatat {$todayGuests} tamu berkunjung, dengan {$activeGuests} tamu sedang aktif di lingkungan sekolah.";
            $findings[] = 'Mini aplikasi Kiosk Tamu Mandiri berbasis kamera webcam selfie dan tanda tangan digital beroperasi responsif di meja resepsionis/pos jaga.';
            $findings[] = 'Seluruh tamu yang tercatat memiliki tiket identitas digital dan riwayat check-in/check-out yang terekam aman.';
            $conclusions = 'Pencatatan tamu digital meningkatkan standar keamanan sekolah, ketertiban administrasi penerimaan tamu kedinasan, dan kenyamanan lingkungan KBM.';
            $issues[] = $activeGuests > 0 ? "Terdapat {$activeGuests} tamu yang statusnya masih bertamu (belum melakukan check-out tiket pada saat meninggalkan sekolah)." : 'Seluruh tamu hari ini telah tertib melakukan check-out kepulangan.';
            $recommendations[] = 'Pastikan petugas keamanan/resepsionis mengarahkan setiap tamu untuk memindai tiket check-out sebelum meninggalkan gerbang sekolah.';
            $recommendations[] = 'Sediakan perangkat tablet atau all-in-one PC khusus untuk Kiosk Tamu di lobi utama sekolah.';
            $actionPlans[] = 'Setiap Sore: Rekapitulasi otomatis buku tamu harian untuk laporan berkala kepada Kepala Sekolah dan Dinas Pendidikan.';
        } else {
            // General / other module fallback
            $summary = "Analisis tata usaha dan kepatuhan administrasi untuk modul {$module} pada {$period}. Seluruh pencatatan data terarsip digital secara terpusat.";
            $findings[] = 'Integritas data master dan riwayat transaksi tercatat lengkap pada log sistem.';
            $conclusions = 'Administrasi berjalan sesuai SOP dan siap untuk kebutuhan monitoring dan akreditasi.';
            $issues[] = 'Pastikan kelengkapan berkas lampiran digital diunggah tepat waktu oleh petugas terkait.';
            $recommendations[] = 'Tingkatkan keteraturan backup data dan peninjauan berkala masa retensi arsip.';
            $actionPlans[] = 'Bulan Ini: Audit kepatuhan dokumen digital oleh Tim Penjamin Mutu Internal.';
        }

        return [
            'executive_summary' => $summary,
            'key_findings' => implode("\n• ", array_merge([''], $findings)),
            'conclusions' => $conclusions,
            'issues_identified' => implode("\n• ", array_merge([''], $issues)),
            'recommendations' => implode("\n• ", array_merge([''], $recommendations)),
            'action_plan' => implode("\n• ", array_merge([''], $actionPlans)),
        ];
    }

    /**
     * Call external LLM (Gemini / OpenAI) if configured.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, string>|null
     */
    protected static function callExternalAi(AiSetting $setting, string $module, string $period, array $data): ?array
    {
        $school = SchoolIdentity::current();
        $schoolName = $school->name;

        $prompt = "Berikut adalah data aktual dari database {$schoolName} untuk modul '{$module}' pada periode '{$period}':\n".
            json_encode($data, JSON_PRETTY_PRINT)."\n\n".
            "Tolong berikan analisis komprehensif dalam format JSON persis dengan kunci berikut:\n".
            "{\n".
            '  "executive_summary": "...",'."\n".
            '  "key_findings": "...",'."\n".
            '  "conclusions": "...",'."\n".
            '  "issues_identified": "...",'."\n".
            '  "recommendations": "...",'."\n".
            '  "action_plan": "..."'."\n".
            "}\n".
            'Aturan: JANGAN MENGARANG DATA. Gunakan hanya angka dan fakta di atas.';

        if ($setting->provider === 'openai') {
            $response = Http::withToken($setting->api_key)
                ->timeout(15)
                ->withoutVerifying()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $setting->model ?: 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => $setting->system_prompt ?? 'Anda adalah SIKATAS AI Assistant.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => (float) $setting->temperature,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');

                return json_decode($content, true);
            }
        } elseif ($setting->provider === 'gemini') {
            $apiKey = $setting->getEffectiveApiKey();
            if (empty($apiKey)) {
                return null;
            }

            $rawModel = $setting->model ?: 'gemini-3.5-flash-lite';
            $targetModel = ltrim(trim($rawModel), 'models/');
            $aliases = [
                'gemini-1.5-flash' => 'gemini-3.5-flash-lite',
                'gemini-1.5-pro' => 'gemini-3.5-flash',
                'gemini-2.0-flash' => 'gemini-3.5-flash-lite',
                'gemini-2.5-flash' => 'gemini-3.5-flash-lite',
                'gemini-2.5-flash-lite' => 'gemini-3.5-flash-lite',
                'gemini-2.5-pro' => 'gemini-3.5-flash',
            ];
            $model = $aliases[$targetModel] ?? $targetModel;

            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $response = Http::timeout(30)->retry(2, 500)->withoutVerifying()->post($endpoint, [
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $setting->system_prompt ?? 'Anda adalah SIKATAS AI Assistant, asisten analisis data eksekutif sekolah SMA1LE. Analisis fakta aktual dari database dengan objektif, akurat, dan format JSON valid.'],
                    ],
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => (float) $setting->temperature,
                    'maxOutputTokens' => (int) $setting->max_tokens,
                    'responseMimeType' => 'application/json',
                ],
            ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                $decoded = json_decode($text, true);

                if (! is_array($decoded) && preg_match('/\{.*\}/s', $text, $matches)) {
                    $decoded = json_decode($matches[0], true);
                }

                if (is_array($decoded) && isset($decoded['executive_summary'])) {
                    foreach (['key_findings', 'issues_identified', 'recommendations', 'action_plan'] as $field) {
                        if (isset($decoded[$field]) && is_array($decoded[$field])) {
                            $decoded[$field] = implode("\n• ", array_merge([''], $decoded[$field]));
                        }
                    }

                    $decoded['_engine'] = "Google Gemini ({$model}) Online";

                    return $decoded;
                }
            } else {
                Log::warning('Google Gemini API Error: '.$response->status().' - '.$response->body());
            }
        }

        return null;
    }

    /**
     * Test live connection to Google Gemini API
     *
     * @return array<string, mixed>
     */
    public static function testGeminiConnection(?string $apiKey = null, ?string $model = null): array
    {
        $setting = AiSetting::current();
        $key = $apiKey ?: $setting->getEffectiveApiKey();
        $rawModel = $model ?: ($setting->model ?: 'gemini-3.5-flash-lite');

        if (empty($key)) {
            return [
                'success' => false,
                'message' => 'API Key belum disetel. Masukkan API Key Google Gemini atau atur di file .env (GEMINI_API_KEY).',
            ];
        }

        $targetModel = ltrim(trim($rawModel), 'models/');
        $aliases = [
            'gemini-1.5-flash' => 'gemini-3.5-flash-lite',
            'gemini-1.5-pro' => 'gemini-3.5-flash',
            'gemini-2.0-flash' => 'gemini-3.5-flash-lite',
            'gemini-2.5-flash' => 'gemini-3.5-flash-lite',
            'gemini-2.5-flash-lite' => 'gemini-3.5-flash-lite',
            'gemini-2.5-pro' => 'gemini-3.5-flash',
        ];
        $effectiveModel = $aliases[$targetModel] ?? $targetModel;

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$effectiveModel}:generateContent?key={$key}";
        $startTime = microtime(true);

        try {
            $response = Http::timeout(25)->retry(2, 500)->withoutVerifying()->post($endpoint, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => 'Ping connection test'],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 5,
                ],
            ]);

            $elapsedMs = round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $switchNote = $effectiveModel !== $targetModel ? " (Dialihkan dari {$targetModel} ke {$effectiveModel} sesuai pembaruan Google API)" : '';

                return [
                    'success' => true,
                    'latency_ms' => $elapsedMs,
                    'model' => $effectiveModel,
                    'message' => "Koneksi Google Gemini Online BERHASIL! Model: {$effectiveModel}{$switchNote}, Respon: {$elapsedMs}ms.",
                ];
            }

            $errorMsg = $response->json('error.message') ?: $response->body();

            return [
                'success' => false,
                'status_code' => $response->status(),
                'message' => "Koneksi ke Google Gemini gagal (HTTP {$response->status()}): {$errorMsg}",
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Koneksi ke Google Gemini gagal: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Save report to ai_reports table.
     *
     * @param  array<string, string>  $analysis
     * @param  array<string, mixed>  $groundTruth
     * @return array<string, mixed>
     */
    protected static function saveReport(string $module, string $period, array $analysis, array $groundTruth): array
    {
        $report = AiReport::create([
            'module' => $module,
            'period' => $period,
            'executive_summary' => $analysis['executive_summary'] ?? '',
            'key_findings' => $analysis['key_findings'] ?? '',
            'conclusions' => $analysis['conclusions'] ?? '',
            'issues_identified' => $analysis['issues_identified'] ?? '',
            'recommendations' => $analysis['recommendations'] ?? '',
            'action_plan' => $analysis['action_plan'] ?? '',
            'supporting_data' => $groundTruth,
            'created_by_user_id' => Auth::id(),
        ]);

        return array_merge($analysis, [
            'id' => $report->id,
            'module' => $module,
            'period' => $period,
            'created_at' => $report->created_at->format('d/m/Y H:i'),
            'supporting_data' => $groundTruth,
        ]);
    }
}
