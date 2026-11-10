<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Models\Disposition;
use App\Models\IncomingLetter;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DispositionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Disposition::with(['incomingLetter', 'giver']);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $dispositions = $query->latest('disposition_date')->paginate(15)->withQueryString();

        return view('dispositions.index', compact('dispositions'));
    }

    public function create(IncomingLetter $letter): View
    {
        $targetRoles = [
            'Kepala Urusan Tata Usaha',
            'Wakil Kepala Sekolah Bidang Kurikulum',
            'Wakil Kepala Sekolah Bidang Kesiswaan',
            'Wakil Kepala Sekolah Bidang Sarpras',
            'Bendahara Sekolah',
            'Koordinator Bimbingan Konseling (BK)',
            'Staf Administrasi Kepegawaian',
            'Petugas Arsip',
        ];

        return view('dispositions.create', compact('letter', 'targetRoles'));
    }

    public function store(Request $request, IncomingLetter $letter): RedirectResponse
    {
        $validated = $request->validate([
            'target_role_or_user' => ['required', 'string', 'max:100'],
            'disposition_date' => ['required', 'date'],
            'instructions' => ['required', 'string'],
            'due_date' => ['nullable', 'date'],
        ]);

        $validated['incoming_letter_id'] = $letter->id;
        $validated['given_by_user_id'] = Auth::id();
        $validated['status'] = 'Belum Diproses';

        $disposition = Disposition::create($validated);
        $letter->update(['status' => 'Didisposisikan']);

        AuditTrailService::log('create', 'disposisi', "Memberikan lembar disposisi surat [{$letter->letter_number}] kepada {$disposition->target_role_or_user}");

        return redirect()->route('letters.incoming.show', $letter)->with('success', 'Disposisi berhasil dibuat dan diteruskan ke petugas terkait.');
    }

    public function updateFollowUp(Request $request, Disposition $disposition): RedirectResponse
    {
        $validated = $request->validate([
            'follow_up_notes' => ['required', 'string'],
            'status' => ['required', 'string', 'in:Sedang Dikerjakan,Selesai'],
        ]);

        if ($validated['status'] === 'Selesai') {
            $validated['completed_at'] = now();
            $disposition->incomingLetter->update(['status' => 'Selesai']);
        } else {
            $disposition->incomingLetter->update(['status' => 'Ditindaklanjuti']);
        }

        $disposition->update($validated);

        AuditTrailService::log('update', 'disposisi', "Menindaklanjuti disposisi surat [{$disposition->incomingLetter->letter_number}] -> {$disposition->status}");

        return back()->with('success', 'Tindak lanjut disposisi berhasil disimpan.');
    }

    public function update(Request $request, Disposition $disposition): RedirectResponse
    {
        $validated = $request->validate([
            'target_role_or_user' => ['required', 'string', 'max:100'],
            'disposition_date' => ['required', 'date'],
            'instructions' => ['required', 'string'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'in:Belum Diproses,Sedang Dikerjakan,Selesai'],
        ]);

        $disposition->update($validated);

        AuditTrailService::log('update', 'disposisi', "Memperbarui disposisi surat [{$disposition->incomingLetter->letter_number}] kepada {$disposition->target_role_or_user}");

        return back()->with('success', 'Disposisi berhasil diperbarui.');
    }

    public function destroy(Disposition $disposition): RedirectResponse
    {
        $letter = $disposition->incomingLetter;
        $disposition->delete();

        if ($letter && $letter->dispositions()->count() === 0) {
            $letter->update(['status' => 'Diterima']);
        }

        AuditTrailService::log('delete', 'disposisi', "Menghapus lembar disposisi surat [{$letter?->letter_number}]");

        return back()->with('success', 'Lembar disposisi berhasil dihapus.');
    }

    public function print(Disposition $disposition): View
    {
        $disposition->load(['incomingLetter', 'giver']);
        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'disposisi', "Mencetak Lembar Disposisi Surat: {$disposition->incomingLetter->letter_number}");

        return view('dispositions.print', compact('disposition', 'school'));
    }
}
