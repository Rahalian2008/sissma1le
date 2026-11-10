<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\LetterExpedition;
use App\Models\OutgoingLetter;
use App\Models\ReferenceItem;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use App\Services\LetterNumberService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OutgoingLetterController extends Controller
{
    public function index(Request $request): View
    {
        $query = OutgoingLetter::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('recipient', 'like', "%{$search}%")
                    ->orWhere('letter_number', 'like', "%{$search}%");
            });
        }

        $letters = $query->latest('letter_date')->paginate(15)->withQueryString();
        $classifications = ReferenceItem::category('klasifikasi_surat')->get();

        return view('letters.outgoing.index', compact('letters', 'classifications'));
    }

    public function create(): View
    {
        $school = SchoolIdentity::current();
        $classifications = ReferenceItem::category('klasifikasi_surat')->get();

        return view('letters.outgoing.create', compact('school', 'classifications'));
    }

    public function store(Request $request): RedirectResponse
    {
        $school = SchoolIdentity::current();

        $validated = $request->validate([
            'recipient' => ['required', 'string', 'max:150'],
            'letter_date' => ['required', 'date'],
            'subject' => ['required', 'string', 'max:255'],
            'classification_code' => ['nullable', 'string', 'max:50'],
            'signer_name' => ['required', 'string', 'max:100'],
            'signer_nip' => ['nullable', 'string', 'max:30'],
        ]);

        $letterDate = ! empty($validated['letter_date']) ? Carbon::parse($validated['letter_date']) : now();
        $year = $letterDate->year;
        $count = OutgoingLetter::whereYear('letter_date', $year)->count() + 1;
        $classCode = $validated['classification_code'] ?: 'PK.03.03';

        $validated['agenda_number'] = 'SK/'.$year.'/'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
        $validated['letter_number'] = LetterNumberService::formatNumber($count, $classCode, $school?->name, $letterDate);
        $validated['status'] = 'Disetujui';

        $letter = OutgoingLetter::create($validated);

        AuditTrailService::log('create', 'persuratan', "Menerbitkan surat keluar resmi: {$letter->letter_number} untuk {$letter->recipient}");

        return redirect()->route('letters.outgoing.index')->with('success', "Surat keluar berhasil dibuat dengan No. {$letter->letter_number}");
    }

    public function show(OutgoingLetter $outgoingLetter): View
    {
        $school = SchoolIdentity::current();
        $isArchived = Archive::where('source_type', 'outgoing_letter')->where('source_id', $outgoingLetter->id)->exists();
        $isExpedited = LetterExpedition::where('outgoing_letter_id', $outgoingLetter->id)->exists();

        return view('letters.outgoing.show', compact('outgoingLetter', 'school', 'isArchived', 'isExpedited'));
    }

    public function archive(OutgoingLetter $outgoingLetter): RedirectResponse
    {
        $existing = Archive::where('source_type', 'outgoing_letter')->where('source_id', $outgoingLetter->id)->first();
        if ($existing) {
            return back()->with('info', "Surat keluar ini sudah diarsipkan sebelumnya dengan No. Arsip {$existing->archive_number}");
        }

        $year = $outgoingLetter->letter_date ? $outgoingLetter->letter_date->year : (int) date('Y');
        $count = Archive::where('year', $year)->count() + 1;
        $class = $outgoingLetter->classification_code ?: '400.3.8';

        $archive = Archive::create([
            'archive_number' => "{$class}/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT),
            'title' => "Surat Keluar: {$outgoingLetter->subject} (Tujuan: {$outgoingLetter->recipient})",
            'category' => 'Persuratan',
            'classification_code' => $class,
            'origin_unit' => 'Tata Usaha SMA Negeri 1 Lengkong',
            'year' => $year,
            'archive_status' => 'Aktif',
            'retention_period_years' => 5,
            'retention_expire_date' => now()->addYears(5)->toDateString(),
            'qr_code_token' => Str::uuid()->toString(),
            'notes' => "Diarsipkan secara otomatis dari Surat Keluar No. {$outgoingLetter->letter_number} (Agenda: {$outgoingLetter->agenda_number})",
            'source_type' => 'outgoing_letter',
            'source_id' => $outgoingLetter->id,
        ]);

        AuditTrailService::log('create', 'kearsipan', "Mengarsipkan surat keluar {$outgoingLetter->agenda_number} ke No. Arsip {$archive->archive_number}");

        return back()->with('success', "Surat keluar berhasil diarsipkan ke Kearsipan Dokumen Digital dengan No. {$archive->archive_number}");
    }

    public function sendToExpedition(Request $request, OutgoingLetter $outgoingLetter): RedirectResponse
    {
        $existing = LetterExpedition::where('outgoing_letter_id', $outgoingLetter->id)->first();
        if ($existing) {
            return redirect()->route('letters.expeditions.index')->with('info', "Surat ini sudah tercatat dalam Buku Ekspedisi No. {$existing->expedition_number}");
        }

        $expedition = LetterExpedition::create([
            'expedition_number' => LetterExpedition::generateExpeditionNumber(),
            'outgoing_letter_id' => $outgoingLetter->id,
            'letter_number' => $outgoingLetter->letter_number,
            'letter_date' => $outgoingLetter->letter_date,
            'delivery_date' => $request->input('delivery_date', now()->toDateString()),
            'recipient_name' => $outgoingLetter->recipient,
            'destination_type' => 'Eksternal',
            'subject' => $outgoingLetter->subject,
            'courier_name' => $request->input('courier_name') ?: (auth()->user()?->name ?? 'Staf Tata Usaha'),
            'receipt_status' => 'Terkirim',
            'notes' => "Didaftarkan otomatis dari Surat Keluar No. Agenda {$outgoingLetter->agenda_number}",
        ]);

        AuditTrailService::log('create', 'persuratan', "Mendaftarkan surat keluar {$outgoingLetter->letter_number} ke Buku Ekspedisi {$expedition->expedition_number}");

        return redirect()->route('letters.expeditions.index')->with('success', "Surat keluar berhasil didaftarkan ke Buku Ekspedisi No. {$expedition->expedition_number}");
    }

    public function printAgenda(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = OutgoingLetter::query();

        if ($year = $request->query('year')) {
            $query->whereYear('letter_date', $year);
        }
        if ($month = $request->query('month')) {
            $query->whereMonth('letter_date', $month);
        }

        $letters = $query->orderBy('agenda_number', 'asc')->get();
        $selectedYear = $request->query('year', date('Y'));
        $selectedMonth = $request->query('month');

        AuditTrailService::log('print', 'persuratan', "Mencetak Buku Agenda Surat Keluar Tahun {$selectedYear}");

        return view('letters.outgoing.agenda_print', compact('school', 'letters', 'selectedYear', 'selectedMonth'));
    }

    public function printBook(Request $request): View
    {
        return $this->printAgenda($request);
    }

    public function edit(OutgoingLetter $outgoingLetter): View
    {
        $school = SchoolIdentity::current();
        $classifications = ReferenceItem::category('klasifikasi_surat')->get();

        return view('letters.outgoing.edit', compact('outgoingLetter', 'school', 'classifications'));
    }

    public function update(Request $request, OutgoingLetter $outgoingLetter): RedirectResponse
    {
        $validated = $request->validate([
            'recipient' => ['required', 'string', 'max:150'],
            'letter_date' => ['required', 'date'],
            'subject' => ['required', 'string', 'max:255'],
            'classification_code' => ['nullable', 'string', 'max:50'],
            'signer_name' => ['required', 'string', 'max:100'],
            'signer_nip' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'string'],
        ]);

        $outgoingLetter->update($validated);

        AuditTrailService::log('update', 'persuratan', "Memperbarui surat keluar: {$outgoingLetter->letter_number}");

        return redirect()->route('letters.outgoing.index')->with('success', 'Data surat keluar berhasil diperbarui.');
    }

    public function print(OutgoingLetter $outgoingLetter): View
    {
        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'persuratan', "Mencetak surat keluar: {$outgoingLetter->letter_number}");

        return view('letters.outgoing.print', compact('outgoingLetter', 'school'));
    }

    public function destroy(OutgoingLetter $outgoingLetter): RedirectResponse
    {
        $info = $outgoingLetter->letter_number;
        $outgoingLetter->delete();

        AuditTrailService::log('delete', 'persuratan', "Menghapus surat keluar: {$info}");

        return redirect()->route('letters.outgoing.index')->with('success', 'Surat keluar berhasil dihapus.');
    }
}
