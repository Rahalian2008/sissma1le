<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Disposition;
use App\Models\Guest;
use App\Models\IncomingLetter;
use App\Models\ReferenceItem;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class IncomingLetterController extends Controller
{
    public function index(Request $request): View
    {
        $query = IncomingLetter::with('dispositions');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('sender', 'like', "%{$search}%")
                    ->orWhere('letter_number', 'like', "%{$search}%")
                    ->orWhere('agenda_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $letters = $query->latest('received_date')->paginate(15)->withQueryString();
        $classifications = ReferenceItem::category('klasifikasi_surat')->get();

        return view('letters.incoming.index', compact('letters', 'classifications'));
    }

    public function create(Request $request): View
    {
        $classifications = ReferenceItem::category('klasifikasi_surat')->get();
        $guest = null;
        if ($guestId = $request->query('guest_id')) {
            $guest = Guest::find($guestId);
        }

        return view('letters.incoming.create', compact('classifications', 'guest'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'letter_number' => ['required', 'string', 'max:100'],
            'sender' => ['required', 'string', 'max:150'],
            'letter_date' => ['required', 'date'],
            'received_date' => ['required', 'date'],
            'subject' => ['required', 'string', 'max:255'],
            'classification_code' => ['nullable', 'string', 'max:50'],
            'character' => ['required', 'string'],
            'attachment_count' => ['nullable', 'string', 'max:50'],
            'guest_id' => ['nullable', 'exists:guests,id'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('letters/incoming', 'public');
        }
        unset($validated['file']);

        // Auto-generate agenda number
        $year = date('Y');
        $count = IncomingLetter::whereYear('received_date', $year)->count() + 1;
        $validated['agenda_number'] = 'SM/'.$year.'/'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'Menunggu Disposisi';

        $letter = IncomingLetter::create($validated);

        AuditTrailService::log('create', 'persuratan', "Menerima surat masuk: {$letter->letter_number} dari {$letter->sender}");

        return redirect()->route('letters.incoming.index')->with('success', "Surat masuk berhasil dicatat dengan No. Agenda {$letter->agenda_number}");
    }

    public function edit(IncomingLetter $incomingLetter): View
    {
        $classifications = ReferenceItem::category('klasifikasi_surat')->get();

        return view('letters.incoming.edit', compact('incomingLetter', 'classifications'));
    }

    public function update(Request $request, IncomingLetter $incomingLetter): RedirectResponse
    {
        $validated = $request->validate([
            'letter_number' => ['required', 'string', 'max:100'],
            'sender' => ['required', 'string', 'max:150'],
            'letter_date' => ['required', 'date'],
            'received_date' => ['required', 'date'],
            'subject' => ['required', 'string', 'max:255'],
            'classification_code' => ['nullable', 'string', 'max:50'],
            'character' => ['required', 'string'],
            'attachment_count' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ]);

        if ($request->hasFile('file')) {
            if ($incomingLetter->file_path) {
                Storage::disk('public')->delete($incomingLetter->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('letters/incoming', 'public');
        }
        unset($validated['file']);

        $incomingLetter->update($validated);

        AuditTrailService::log('update', 'persuratan', "Memperbarui surat masuk: {$incomingLetter->letter_number}");

        return redirect()->route('letters.incoming.index')->with('success', 'Data surat masuk berhasil diperbarui.');
    }

    public function show(IncomingLetter $incomingLetter): View
    {
        $incomingLetter->load(['dispositions.giver']);
        $isArchived = Archive::where('source_type', 'incoming_letter')->where('source_id', $incomingLetter->id)->exists();

        return view('letters.incoming.show', compact('incomingLetter', 'isArchived'));
    }

    public function archive(IncomingLetter $incomingLetter): RedirectResponse
    {
        $existing = Archive::where('source_type', 'incoming_letter')->where('source_id', $incomingLetter->id)->first();
        if ($existing) {
            return back()->with('info', "Surat masuk ini sudah diarsipkan sebelumnya dengan No. Arsip {$existing->archive_number}");
        }

        $year = $incomingLetter->received_date ? $incomingLetter->received_date->year : (int) date('Y');
        $count = Archive::where('year', $year)->count() + 1;
        $class = $incomingLetter->classification_code ?: '421.3';

        $archive = Archive::create([
            'archive_number' => "{$class}/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT),
            'title' => "Surat Masuk: {$incomingLetter->subject} (Dari {$incomingLetter->sender})",
            'category' => 'Persuratan',
            'classification_code' => $class,
            'origin_unit' => $incomingLetter->sender,
            'year' => $year,
            'archive_status' => 'Aktif',
            'retention_period_years' => 5,
            'retention_expire_date' => now()->addYears(5)->toDateString(),
            'qr_code_token' => Str::uuid()->toString(),
            'notes' => "Diarsipkan secara otomatis dari Surat Masuk No. {$incomingLetter->letter_number} (Agenda: {$incomingLetter->agenda_number})",
            'source_type' => 'incoming_letter',
            'source_id' => $incomingLetter->id,
        ]);

        AuditTrailService::log('create', 'kearsipan', "Mengarsipkan surat masuk {$incomingLetter->agenda_number} ke No. Arsip {$archive->archive_number}");

        return back()->with('success', "Surat masuk berhasil diarsipkan ke Kearsipan Dokumen Digital dengan No. {$archive->archive_number}");
    }

    public function printAgenda(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = IncomingLetter::with('dispositions');

        if ($year = $request->query('year')) {
            $query->whereYear('received_date', $year);
        }
        if ($month = $request->query('month')) {
            $query->whereMonth('received_date', $month);
        }

        $letters = $query->orderBy('agenda_number', 'asc')->get();
        $selectedYear = $request->query('year', date('Y'));
        $selectedMonth = $request->query('month');

        AuditTrailService::log('print', 'persuratan', "Mencetak Buku Agenda Surat Masuk Tahun {$selectedYear}");

        return view('letters.incoming.agenda_print', compact('school', 'letters', 'selectedYear', 'selectedMonth'));
    }

    public function printBook(Request $request): View
    {
        return $this->printAgenda($request);
    }

    public function printDisposition(IncomingLetter $incomingLetter): View
    {
        $school = SchoolIdentity::current();
        $disposition = $incomingLetter->dispositions()->latest()->first();

        if (! $disposition) {
            $disposition = new Disposition([
                'incoming_letter_id' => $incomingLetter->id,
                'action_needed' => 'Tindak lanjuti sesuai ketentuan.',
                'target_role' => 'Wakasek / Guru Terkait',
                'status' => 'PENDING',
                'created_at' => now(),
            ]);
            $disposition->setRelation('incomingLetter', $incomingLetter);
        }

        return view('dispositions.print', compact('disposition', 'incomingLetter', 'school'));
    }

    public function destroy(IncomingLetter $incomingLetter): RedirectResponse
    {
        $info = $incomingLetter->letter_number;
        if ($incomingLetter->file_path) {
            Storage::disk('public')->delete($incomingLetter->file_path);
        }
        $incomingLetter->delete();

        AuditTrailService::log('delete', 'persuratan', "Menghapus surat masuk: {$info}");

        return redirect()->route('letters.incoming.index')->with('success', 'Surat masuk berhasil dihapus.');
    }
}
