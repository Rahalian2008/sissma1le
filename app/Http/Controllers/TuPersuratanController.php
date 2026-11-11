<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Disposition;
use App\Models\Guest;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\Sppd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TuPersuratanController extends Controller
{
    public function index(Request $request): View
    {
        if (! Auth::user()?->canAccessTuSubmodule('persuratan')) {
            abort(403, 'Akses tidak diizinkan. Menu ini dikhususkan untuk staf Persuratan & Arsip.');
        }

        $hasIncoming = Schema::hasTable('incoming_letters');
        $hasOutgoing = Schema::hasTable('outgoing_letters');
        $hasDispositions = Schema::hasTable('dispositions');
        $hasSppd = class_exists(Sppd::class) && Schema::hasTable('sppds');
        $hasArchives = Schema::hasTable('archives');
        $hasGuests = Schema::hasTable('guests');

        // 1. Metrik Utama Persuratan
        $incomingCount = $hasIncoming ? IncomingLetter::count() : 0;
        $outgoingCount = $hasOutgoing ? OutgoingLetter::count() : 0;
        $pendingDispositionsCount = $hasDispositions ? Disposition::where('status', '!=', 'Selesai')->count() : 0;
        $sppdCount = $hasSppd ? Sppd::count() : 0;
        $activeArchivesCount = $hasArchives ? Archive::where('archive_status', 'Aktif')->count() : 0;
        $guestsTodayCount = $hasGuests ? Guest::whereDate('created_at', today())->count() : 0;

        // 2. Surat Masuk Terbaru
        $recentIncoming = $hasIncoming ? IncomingLetter::latest('received_date')->take(6)->get() : collect();

        // 3. Surat Keluar Terbaru
        $recentOutgoing = $hasOutgoing ? OutgoingLetter::latest('letter_date')->take(6)->get() : collect();

        // 4. Disposisi Berjalan
        $activeDispositions = ($hasDispositions && $hasIncoming) ? Disposition::with('incomingLetter')
            ->where('status', '!=', 'Selesai')
            ->latest('disposition_date')
            ->take(6)
            ->get() : collect();

        return view('tu.persuratan.index', compact(
            'incomingCount',
            'outgoingCount',
            'pendingDispositionsCount',
            'sppdCount',
            'activeArchivesCount',
            'guestsTodayCount',
            'recentIncoming',
            'recentOutgoing',
            'activeDispositions'
        ));
    }
}
