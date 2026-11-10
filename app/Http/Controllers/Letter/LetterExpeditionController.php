<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Models\LetterExpedition;
use App\Models\OutgoingLetter;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LetterExpeditionController extends Controller
{
    public function index(Request $request): View
    {
        $query = LetterExpedition::with('outgoingLetter');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('expedition_number', 'like', "%{$search}%")
                    ->orWhere('letter_number', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('receiver_name', 'like', "%{$search}%");
            });
        }

        if ($type = $request->query('destination_type')) {
            $query->where('destination_type', $type);
        }

        if ($status = $request->query('receipt_status')) {
            $query->where('receipt_status', $status);
        }

        $expeditions = $query->latest('delivery_date')->latest('id')->paginate(15)->withQueryString();

        $totalExpeditions = LetterExpedition::count();
        $receivedCount = LetterExpedition::where('receipt_status', 'Diterima')->count();
        $pendingCount = LetterExpedition::where('receipt_status', '!=', 'Diterima')->count();
        $internalCount = LetterExpedition::where('destination_type', 'Internal')->count();

        // Get recent outgoing letters for autocomplete / select
        $recentOutgoingLetters = OutgoingLetter::latest('letter_date')->take(20)->get();

        return view('letters.expeditions.index', compact(
            'expeditions',
            'totalExpeditions',
            'receivedCount',
            'pendingCount',
            'internalCount',
            'recentOutgoingLetters'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'outgoing_letter_id' => ['nullable', 'exists:outgoing_letters,id'],
            'letter_number' => ['required', 'string', 'max:100'],
            'letter_date' => ['required', 'date'],
            'delivery_date' => ['required', 'date'],
            'recipient_name' => ['required', 'string', 'max:150'],
            'destination_type' => ['required', 'string', 'in:Internal,Eksternal'],
            'subject' => ['required', 'string', 'max:255'],
            'courier_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['expedition_number'] = LetterExpedition::generateExpeditionNumber();
        $validated['receipt_status'] = 'Terkirim';

        $expedition = LetterExpedition::create($validated);

        AuditTrailService::log('create', 'persuratan', "Mencatat Buku Ekspedisi No. {$expedition->expedition_number} untuk {$expedition->recipient_name}");

        return redirect()->route('letters.expeditions.index')->with('success', "Pengiriman naskah dinas berhasil dicatat di Buku Ekspedisi dengan No. {$expedition->expedition_number}");
    }

    public function update(Request $request, LetterExpedition $expedition): RedirectResponse
    {
        $validated = $request->validate([
            'letter_number' => ['required', 'string', 'max:100'],
            'letter_date' => ['required', 'date'],
            'delivery_date' => ['required', 'date'],
            'recipient_name' => ['required', 'string', 'max:150'],
            'destination_type' => ['required', 'string', 'in:Internal,Eksternal'],
            'subject' => ['required', 'string', 'max:255'],
            'courier_name' => ['nullable', 'string', 'max:100'],
            'receipt_status' => ['required', 'string', 'in:Terkirim,Diterima,Pending'],
            'receiver_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $expedition->update($validated);

        AuditTrailService::log('update', 'persuratan', "Memperbarui catatan ekspedisi {$expedition->expedition_number}");

        return redirect()->route('letters.expeditions.index')->with('success', "Catatan ekspedisi No. {$expedition->expedition_number} berhasil diperbarui.");
    }

    public function updateReceipt(Request $request, LetterExpedition $expedition): RedirectResponse
    {
        $validated = $request->validate([
            'receiver_name' => ['required', 'string', 'max:100'],
            'received_at' => ['required', 'date'],
            'receipt_status' => ['required', 'string', 'in:Diterima,Pending,Terkirim'],
            'receipt_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:3072'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('receipt_file')) {
            $validated['receipt_file_path'] = $request->file('receipt_file')->store('receipt_proofs', 'public');
        }

        $expedition->update([
            'receiver_name' => $validated['receiver_name'],
            'received_at' => $validated['received_at'],
            'receipt_status' => $validated['receipt_status'],
            'receipt_file_path' => $validated['receipt_file_path'] ?? $expedition->receipt_file_path,
            'notes' => $validated['notes'] ?? $expedition->notes,
        ]);

        AuditTrailService::log('update', 'persuratan', "Memperbarui tanda terima Ekspedisi {$expedition->expedition_number} oleh {$validated['receiver_name']}");

        return back()->with('success', "Tanda terima Buku Ekspedisi No. {$expedition->expedition_number} berhasil dikonfirmasi.");
    }

    public function destroy(LetterExpedition $expedition): RedirectResponse
    {
        $number = $expedition->expedition_number;
        $expedition->delete();

        AuditTrailService::log('delete', 'persuratan', "Menghapus catatan ekspedisi: {$number}");

        return back()->with('success', 'Catatan ekspedisi surat berhasil dihapus.');
    }

    public function print(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = LetterExpedition::query();

        if ($month = $request->query('month')) {
            $query->whereMonth('delivery_date', $month);
        }
        if ($year = $request->query('year')) {
            $query->whereYear('delivery_date', $year);
        }
        if ($type = $request->query('destination_type')) {
            $query->where('destination_type', $type);
        }

        $expeditions = $query->orderBy('delivery_date', 'asc')->orderBy('id', 'asc')->get();
        $filterMonth = $request->query('month', date('n'));
        $filterYear = $request->query('year', date('Y'));

        AuditTrailService::log('print', 'persuratan', "Mencetak Buku Ekspedisi Surat Periode {$filterMonth}/{$filterYear}");

        return view('letters.expeditions.print', compact('school', 'expeditions', 'filterMonth', 'filterYear', 'type'));
    }
}
