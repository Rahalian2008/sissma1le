<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Asset;
use App\Models\AssetDisposal;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetDisposalController extends Controller
{
    public function index(Request $request): View
    {
        $query = AssetDisposal::with(['asset.room']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('disposal_number', 'like', "%{$search}%")
                    ->orWhere('official_report_number', 'like', "%{$search}%")
                    ->orWhereHas('asset', function ($qa) use ($search) {
                        $qa->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($reason = $request->query('reason')) {
            $query->where('reason', $reason);
        }

        $disposals = $query->latest('disposal_date')->paginate(15)->withQueryString();

        // Assets eligible for disposal (prefer damaged or lost, but allow any)
        $assets = Asset::with('room')->whereNotIn('condition', ['Dihapus/Afkir'])->orderBy('name')->get();

        $stats = [
            'total' => AssetDisposal::count(),
            'approved' => AssetDisposal::where('status', 'Disetujui Disdik/BPKAD')->count(),
            'pending' => AssetDisposal::whereIn('status', ['Diusulkan', 'Diverifikasi Kepala Sekolah'])->count(),
            'total_scrap_value' => (float) AssetDisposal::where('status', 'Disetujui Disdik/BPKAD')->sum('estimated_scrap_value'),
        ];

        return view('sarpras.disposals.index', compact('disposals', 'assets', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'disposal_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'in:Rusak Berat,Hilang,Kadaluarsa / Usang,Bencana Alam,Renovasi / Pembongkaran'],
            'estimated_scrap_value' => ['nullable', 'numeric', 'min:0'],
            'chronology_description' => ['required', 'string'],
            'proposed_by_name' => ['required', 'string', 'max:150'],
            'inspector_names' => ['nullable', 'string', 'max:255'],
            'official_report_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['disposal_number'] = AssetDisposal::generateDisposalNumber();
        $validated['status'] = 'Diusulkan';
        $validated['estimated_scrap_value'] = $validated['estimated_scrap_value'] ?? 0;

        $disposal = AssetDisposal::create($validated);
        $asset = Asset::find($validated['asset_id']);

        // Update asset condition if still marked as Baik
        if ($asset && in_array($asset->condition, ['Baik', 'Rusak Ringan'])) {
            $asset->update(['condition' => $validated['reason'] === 'Hilang' ? 'Hilang' : 'Rusak Berat']);
        }

        AuditTrailService::log('create', 'sarpras', "Mengajukan usulan penghapusan aset: {$asset->name} (No. {$disposal->disposal_number})");

        return redirect()->route('sarpras.disposals.index')->with('success', "Usulan penghapusan aset berhasil dibuat dengan No. {$disposal->disposal_number}");
    }

    public function updateStatus(Request $request, AssetDisposal $disposal): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Diusulkan,Diverifikasi Kepala Sekolah,Disetujui Disdik/BPKAD,Ditolak'],
            'official_report_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldStatus = $disposal->status;
        $newStatus = $validated['status'];

        if ($newStatus === 'Disetujui Disdik/BPKAD') {
            $validated['approved_at'] = now();
            // Free asset from active room & mark condition as Dihapus/Afkir
            $disposal->asset->update([
                'condition' => 'Dihapus/Afkir',
                'room_id' => null,
            ]);
        }

        $disposal->update($validated);

        AuditTrailService::log('update', 'sarpras', "Memperbarui status usulan penghapusan {$disposal->disposal_number} dari {$oldStatus} ke {$newStatus}");

        return back()->with('success', "Status usulan penghapusan aset berhasil diperbarui menjadi {$newStatus}.");
    }

    public function print(AssetDisposal $disposal): View
    {
        $disposal->load(['asset.room']);
        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'sarpras', "Mencetak Berita Acara Usulan Penghapusan Aset: {$disposal->disposal_number}");

        return view('sarpras.disposals.print', compact('disposal', 'school'));
    }

    public function archive(AssetDisposal $disposal): RedirectResponse
    {
        $existing = Archive::where('source_type', 'asset_disposal')
            ->where('source_id', $disposal->id)
            ->first();

        if ($existing) {
            return back()->with('info', "Berita Acara ini sudah diarsipkan ke Kearsipan Digital dengan No. {$existing->archive_number}");
        }

        $year = date('Y', strtotime($disposal->disposal_date));
        $count = Archive::where('year', $year)->count() + 1;
        $archiveNumber = "ARS/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        $archive = Archive::create([
            'archive_number' => $archiveNumber,
            'title' => 'BA Usulan Penghapusan Aset: '.$disposal->asset->name.' ('.$disposal->disposal_number.')',
            'category' => 'Sarpras',
            'classification_code' => '028', // Kode BMD / Perlengkapan
            'origin_unit' => 'Pengurus Barang / Sarpras',
            'year' => $year,
            'archive_status' => 'Aktif',
            'retention_period_years' => 10,
            'retention_expire_date' => now()->addYears(10)->toDateString(),
            'location_rack' => 'Lemari Dokumen Aset & Sarpras',
            'file_path' => $disposal->file_path,
            'source_type' => 'asset_disposal',
            'source_id' => $disposal->id,
            'notes' => "Diarsipkan otomatis dari Usulan Penghapusan No. {$disposal->disposal_number} - Status: {$disposal->status}",
        ]);

        AuditTrailService::log('create', 'kearsipan', "Mengarsipkan Berita Acara Penghapusan Aset {$disposal->disposal_number} ke No. Arsip {$archive->archive_number}");

        return back()->with('success', "Berita Acara Penghapusan Aset berhasil disimpan ke Kearsipan Digital dengan No. {$archive->archive_number}");
    }

    public function destroy(AssetDisposal $disposal): RedirectResponse
    {
        if ($disposal->status === 'Disetujui Disdik/BPKAD') {
            return back()->with('error', 'Usulan penghapusan yang telah disetujui BPKAD/Disdik tidak dapat dihapus.');
        }

        $num = $disposal->disposal_number;
        $disposal->delete();

        AuditTrailService::log('delete', 'sarpras', "Menghapus usulan penghapusan aset: {$num}");

        return redirect()->route('sarpras.disposals.index')->with('success', "Data usulan penghapusan {$num} berhasil dihapus.");
    }
}
