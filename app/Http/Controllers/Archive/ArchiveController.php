<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\ReferenceItem;
use App\Services\AuditTrailService;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArchiveController extends Controller
{
    public function index(Request $request): View
    {
        $query = Archive::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('archive_number', 'like', "%{$search}%")
                    ->orWhere('classification_code', 'like', "%{$search}%");
            });
        }

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        if ($status = $request->query('status')) {
            $query->where('archive_status', $status);
        }

        $archives = $query->latest('year')->paginate(15)->withQueryString();
        $categories = ReferenceItem::category('jenis_arsip')->get();
        $classifications = ReferenceItem::category('klasifikasi_surat')->get();

        return view('archives.index', compact('archives', 'categories', 'classifications'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'classification_code' => ['nullable', 'string', 'max:50'],
            'origin_unit' => ['nullable', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:2000', 'max:'.(date('Y') + 1)],
            'archive_status' => ['required', 'string'],
            'retention_period_years' => ['required', 'integer', 'min:1'],
            'location_rack' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $year = $validated['year'];
        $count = Archive::where('year', $year)->count() + 1;
        $class = $validated['classification_code'] ?: 'ARS';

        $validated['archive_number'] = "{$class}/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT);
        $validated['retention_expire_date'] = now()->addYears($validated['retention_period_years'])->toDateString();
        $validated['qr_code_token'] = Str::uuid()->toString();

        $archive = Archive::create($validated);

        AuditTrailService::log('create', 'kearsipan', "Menyimpan berkas arsip digital: {$archive->title} (No: {$archive->archive_number})");

        return back()->with('success', "Berkas arsip berhasil didaftarkan dengan No. {$archive->archive_number}");
    }

    public function show(Archive $archive): View
    {
        $verificationUrl = route('verify.public', ['type' => 'arsip', 'token' => $archive->qr_code_token ?? $archive->archive_number]);
        $qrCodeSvg = QrCodeService::svg($verificationUrl, 140);

        return view('archives.show', compact('archive', 'qrCodeSvg', 'verificationUrl'));
    }

    public function update(Request $request, Archive $archive): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'classification_code' => ['nullable', 'string', 'max:50'],
            'origin_unit' => ['nullable', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:2000', 'max:'.(date('Y') + 1)],
            'archive_status' => ['required', 'string'],
            'retention_period_years' => ['required', 'integer', 'min:1'],
            'location_rack' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($archive->retention_period_years != $validated['retention_period_years']) {
            $validated['retention_expire_date'] = now()->addYears($validated['retention_period_years'])->toDateString();
        }

        $archive->update($validated);

        AuditTrailService::log('update', 'kearsipan', "Memperbarui berkas arsip digital: {$archive->title} (No: {$archive->archive_number})");

        return back()->with('success', "Berkas arsip {$archive->archive_number} berhasil diperbarui.");
    }

    public function destroy(Archive $archive): RedirectResponse
    {
        $title = $archive->title;
        $archive->delete();

        AuditTrailService::log('delete', 'kearsipan', "Menghapus berkas arsip digital: {$title}");

        return back()->with('success', 'Berkas arsip berhasil dihapus.');
    }
}
