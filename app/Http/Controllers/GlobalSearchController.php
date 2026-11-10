<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\EmployeeLeave;
use App\Models\Good;
use App\Models\Guest;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GlobalSearchController extends Controller
{
    public function search(Request $request): JsonResponse|View
    {
        $query = trim($request->input('q', ''));

        if (empty($query)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['results' => []]);
            }

            return view('search.index', ['query' => '', 'results' => []]);
        }

        $results = [];

        // 1. Search Students
        $students = Student::where('name', 'like', "%{$query}%")
            ->orWhere('nis', 'like', "%{$query}%")
            ->orWhere('nisn', 'like', "%{$query}%")
            ->take(5)
            ->get();
        foreach ($students as $s) {
            $results[] = [
                'category' => 'Peserta Didik',
                'title' => $s->name,
                'subtitle' => "NIS: {$s->nis} | Kelas: {$s->current_class_level} ({$s->status})",
                'url' => route('students.show', $s),
                'buku_induk_url' => route('buku-induk.show', $s),
                'icon' => 'user-graduate',
            ];
        }

        // 2. Search Employees (PTK)
        $employees = Employee::where('name', 'like', "%{$query}%")
            ->orWhere('nip', 'like', "%{$query}%")
            ->orWhere('position', 'like', "%{$query}%")
            ->take(5)
            ->get();
        foreach ($employees as $e) {
            $results[] = [
                'category' => 'Pendidik & Tendik',
                'title' => $e->formatted_name,
                'subtitle' => "NIP: {$e->nip} | Jabatan: {$e->position} ({$e->employment_status})",
                'url' => route('employees.show', $e),
                'icon' => 'user-tie',
            ];
        }

        // 3. Search Assets
        $assets = Asset::with('room')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%")
            ->orWhere('sim_bmd_code', 'like', "%{$query}%")
            ->orWhere('brand_model', 'like', "%{$query}%")
            ->take(5)
            ->get();
        foreach ($assets as $a) {
            $kibLabel = $a->kib_type ? "KIB-{$a->kib_type}" : 'Sarpras';
            $codeDisplay = $a->sim_bmd_code ?: $a->code;
            $roomName = $a->room?->name ?? 'Gudang/Belum dialokasikan';
            $results[] = [
                'category' => "Aset Inventaris ({$kibLabel})",
                'title' => $a->name,
                'subtitle' => "SIPD e-BMD: {$codeDisplay} | Ruang: {$roomName} | Kondisi: {$a->condition}",
                'url' => route('sarpras.assets.show', $a),
                'icon' => 'desktop',
            ];
        }

        // 4. Search Goods (Gudang)
        $goods = Good::where('name', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%")
            ->take(5)
            ->get();
        foreach ($goods as $g) {
            $results[] = [
                'category' => 'Persediaan Gudang',
                'title' => $g->name,
                'subtitle' => "Kode: {$g->code} | Stok: {$g->current_stock} {$g->unit}",
                'url' => route('warehouse.goods.card', $g),
                'icon' => 'boxes-stacked',
            ];
        }

        // 5. Search Letters
        $inLetters = IncomingLetter::where('subject', 'like', "%{$query}%")
            ->orWhere('letter_number', 'like', "%{$query}%")
            ->orWhere('sender', 'like', "%{$query}%")
            ->take(5)
            ->get();
        foreach ($inLetters as $l) {
            $results[] = [
                'category' => 'Surat Masuk',
                'title' => $l->subject,
                'subtitle' => "No: {$l->letter_number} | Pengirim: {$l->sender}",
                'url' => route('letters.incoming.show', $l),
                'icon' => 'envelope-open-text',
            ];
        }

        $outLetters = OutgoingLetter::where('subject', 'like', "%{$query}%")
            ->orWhere('letter_number', 'like', "%{$query}%")
            ->orWhere('recipient', 'like', "%{$query}%")
            ->take(5)
            ->get();
        foreach ($outLetters as $ol) {
            $results[] = [
                'category' => 'Surat Keluar',
                'title' => $ol->subject,
                'subtitle' => "No: {$ol->letter_number} | Tujuan: {$ol->recipient}",
                'url' => route('letters.outgoing.show', $ol),
                'icon' => 'paper-plane',
            ];
        }

        // 6. Search Archives
        $archives = Archive::where('title', 'like', "%{$query}%")
            ->orWhere('archive_number', 'like', "%{$query}%")
            ->take(5)
            ->get();
        foreach ($archives as $ar) {
            $results[] = [
                'category' => 'Kearsipan Digital',
                'title' => $ar->title,
                'subtitle' => "No: {$ar->archive_number} | Kategori: {$ar->category} ({$ar->archive_status})",
                'url' => route('archives.show', $ar),
                'icon' => 'box-archive',
            ];
        }

        // 7. Search Guests (Buku Tamu)
        $guests = Guest::where('name', 'like', "%{$query}%")
            ->orWhere('ticket_number', 'like', "%{$query}%")
            ->orWhere('institution', 'like', "%{$query}%")
            ->orWhere('destination_person_name', 'like', "%{$query}%")
            ->orWhere('destination_department', 'like', "%{$query}%")
            ->take(5)
            ->get();
        foreach ($guests as $g) {
            $statusLabel = $g->status === 'checked_in' ? 'Aktif Bertamu' : 'Selesai';
            $instansi = $g->institution ?: 'Pribadi';
            $tujuan = $g->destination_department ?: $g->destination_person_name;
            $results[] = [
                'category' => "Buku Tamu ({$statusLabel})",
                'title' => "{$g->name} [{$g->ticket_number}]",
                'subtitle' => "Instansi: {$instansi} | Tujuan: {$tujuan} | Keperluan: ".Str::limit($g->purpose, 35),
                'url' => route('guests.show', $g),
                'icon' => 'id-card',
            ];
        }

        // 8. Search Employee Leaves (Cuti Pegawai)
        $leaves = EmployeeLeave::with('employee')
            ->where('letter_number', 'like', "%{$query}%")
            ->orWhere('leave_type', 'like', "%{$query}%")
            ->orWhere('reason', 'like', "%{$query}%")
            ->orWhereHas('employee', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->take(5)
            ->get();
        foreach ($leaves as $lv) {
            $results[] = [
                'category' => 'Cuti Pegawai (Kepegawaian)',
                'title' => "{$lv->leave_type} - {$lv->employee?->name}",
                'subtitle' => "No: {$lv->letter_number} | {$lv->days_count} hari ({$lv->status}) | Keperluan: ".Str::limit($lv->reason, 30),
                'url' => route('employees.kontrol-cuti.index', ['search' => $lv->employee?->name]),
                'icon' => 'calendar-check',
            ];
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['results' => $results]);
        }

        return view('search.index', compact('query', 'results'));
    }
}
