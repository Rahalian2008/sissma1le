<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiReport;
use App\Models\AiSetting;
use App\Models\Archive;
use App\Models\SchoolIdentity;
use App\Services\AiAnalyticsService;
use App\Services\AuditTrailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AiAnalyticsController extends Controller
{
    public function index(): View
    {
        $reports = AiReport::with('creator')->latest()->paginate(10);
        $setting = AiSetting::current();

        return view('ai.index', compact('reports', 'setting'));
    }

    public function generate(Request $request): JsonResponse|View
    {
        $module = $request->input('module', 'executive_headmaster');
        $period = $request->input('period', 'Periode Berjalan '.date('Y'));

        $analysis = AiAnalyticsService::analyze($module, $period);

        AuditTrailService::log('ai_analysis', 'ai', "Menjalankan analisis AI untuk modul [{$module}] pada {$period}");

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $analysis,
            ]);
        }

        return view('ai.show', ['report' => (object) $analysis]);
    }

    public function show(AiReport $aiReport): View
    {
        return view('ai.show', ['report' => $aiReport]);
    }

    public function print(AiReport $aiReport): View
    {
        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'ai', "Mencetak Laporan Analisis Eksekutif AI #{$aiReport->id}");

        return view('ai.print', compact('aiReport', 'school'));
    }

    public function saveToArchive(AiReport $aiReport): RedirectResponse
    {
        $year = date('Y');
        $count = Archive::where('year', $year)->count() + 1;

        $archive = Archive::create([
            'archive_number' => "AI/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT),
            'title' => 'Laporan Analisis AI Eksekutif - Modul '.ucwords(str_replace('_', ' ', $aiReport->module))." ({$aiReport->period})",
            'category' => 'Laporan Eksekutif',
            'classification_code' => 'AI.01',
            'origin_unit' => 'Pimpinan Sekolah / Tata Usaha',
            'year' => (int) $year,
            'archive_status' => 'Aktif',
            'retention_period_years' => 5,
            'retention_expire_date' => now()->addYears(5)->toDateString(),
            'location_rack' => 'Digital Vault AI',
            'qr_code_token' => Str::uuid()->toString(),
            'notes' => "Dihasilkan secara otomatis oleh SIKATAS AI Assistant pada {$aiReport->created_at->format('d/m/Y H:i')}",
        ]);

        AuditTrailService::log('create', 'kearsipan', "Menyimpan Laporan AI #{$aiReport->id} ke kearsipan digital (No: {$archive->archive_number})");

        return back()->with('success', "Laporan AI berhasil diarsipkan ke Kearsipan Digital dengan nomor {$archive->archive_number}");
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'provider' => ['required', 'string', 'in:local_heuristic,openai,gemini,custom'],
            'api_key' => ['nullable', 'string'],
            'api_endpoint' => ['nullable', 'string'],
            'model' => ['required', 'string'],
            'temperature' => ['required', 'numeric', 'min:0', 'max:2'],
            'max_tokens' => ['required', 'integer', 'min:256'],
            'system_prompt' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $setting = AiSetting::current();
        $validated['is_active'] = $request->boolean('is_active');

        $setting->update($validated);

        AuditTrailService::log('update', 'ai', "Memperbarui konfigurasi AI Provider ke: {$setting->provider}");

        return back()->with('success', 'Konfigurasi provider AI berhasil diperbarui.');
    }

    public function testConnection(Request $request): JsonResponse
    {
        $apiKey = $request->input('api_key');
        $model = $request->input('model');

        $result = AiAnalyticsService::testGeminiConnection($apiKey, $model);

        return response()->json($result);
    }

    public function destroy(AiReport $aiReport): RedirectResponse
    {
        $id = $aiReport->id;
        $aiReport->delete();

        AuditTrailService::log('delete', 'ai', "Menghapus dokumen kajian AI #{$id}");

        return back()->with('success', 'Dokumen kajian AI berhasil dihapus.');
    }
}
