<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SchoolBell;
use App\Models\SchoolSetting;
use App\Services\NationalAnthemService;
use App\Services\QuranDataService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SchoolBellController extends Controller
{
    /**
     * Tampilan Utama Aplikasi Bel Sekolah & Audio Murottal Al-Qur'an 114 Surah
     */
    public function index(Request $request): View
    {
        $hasTable = Schema::hasTable('school_bells');
        if ($hasTable) {
            SchoolBell::ensureDefaultSchedules();
        }

        $todayName = SchoolBell::getCurrentDayName();
        $currentTime = Carbon::now()->format('H:i');

        $allBells = $hasTable ? SchoolBell::orderBy('time', 'asc')->get() : collect();
        $todayBells = $hasTable ? SchoolBell::active()->forDay($todayName)->orderBy('time', 'asc')->get() : collect();

        // Cari bel berikutnya hari ini
        $nextBell = $todayBells->first(fn ($b) => $b->time >= $currentTime) ?: $todayBells->first();

        $allSurahs = QuranDataService::getAllSurahs();
        $featuredSurahs = QuranDataService::getFeaturedSurahs();
        $nationalAnthems = NationalAnthemService::getNationalAnthems();
        $instantSounds = NationalAnthemService::getInstantSounds();

        $stats = [
            'total' => $allBells->count(),
            'active_today' => $todayBells->count(),
            'murottal_count' => $allBells->where('sound_type', 'murottal')->count(),
            'chime_count' => $allBells->where('sound_type', '!=', 'murottal')->count(),
            'anthem_count' => count($nationalAnthems),
            'soundboard_count' => count($instantSounds),
        ];

        return view('school_bell.index', compact(
            'allBells',
            'todayBells',
            'nextBell',
            'todayName',
            'currentTime',
            'allSurahs',
            'featuredSurahs',
            'nationalAnthems',
            'instantSounds',
            'stats'
        ));
    }

    /**
     * Tampilan Kios Bel Sekolah Standalone (Kiosk Display untuk Bel Otomatis & Putar Audio di Perangkat)
     */
    public function kiosk(Request $request): View
    {
        $hasTable = Schema::hasTable('school_bells');
        if ($hasTable) {
            SchoolBell::ensureDefaultSchedules();
        }

        $todayName = SchoolBell::getCurrentDayName();
        $currentTime = Carbon::now()->format('H:i');

        $allBells = $hasTable ? SchoolBell::orderBy('time', 'asc')->get() : collect();
        $todayBells = $hasTable ? SchoolBell::active()->forDay($todayName)->orderBy('time', 'asc')->get() : collect();

        $nextBell = $todayBells->first(fn ($b) => $b->time >= $currentTime) ?: $todayBells->first();
        $allSurahs = QuranDataService::getAllSurahs();
        $nationalAnthems = NationalAnthemService::getNationalAnthems();
        $instantSounds = NationalAnthemService::getInstantSounds();

        return view('school_bell.kiosk', compact(
            'allBells',
            'todayBells',
            'nextBell',
            'todayName',
            'currentTime',
            'allSurahs',
            'nationalAnthems',
            'instantSounds'
        ));
    }

    /**
     * Tambah Jadwal Bel Sekolah Baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'time' => ['required', 'string', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['string', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'sound_type' => ['required', 'string', 'in:chime,murottal,voice,siren,custom,song'],
            'surah_number' => ['nullable', 'integer', 'between:1,114'],
            'duration_seconds' => ['nullable', 'integer', 'min:5', 'max:3600'],
            'volume' => ['nullable', 'integer', 'between:10,100'],
            'description' => ['nullable', 'string', 'max:500'],
            'voice_text' => ['nullable', 'string', 'max:500'],
            'audio_file' => ['nullable', 'file', 'mimes:mp3,wav,ogg,m4a,aac', 'max:25600'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $audioUrl = null;
        $surahName = null;

        if ($request->hasFile('audio_file')) {
            $path = $request->file('audio_file')->store('school_bells', 'public');
            $audioUrl = asset('storage/'.$path);
        } elseif ($validated['sound_type'] === 'murottal' && ! empty($validated['surah_number'])) {
            $surah = QuranDataService::getSurah((int) $validated['surah_number']);
            if ($surah) {
                $surahName = $surah['name'];
                $audioUrl = $surah['audio_url'];
            }
        } elseif ($validated['sound_type'] === 'song' && $request->filled('anthem_id')) {
            $anthems = NationalAnthemService::getNationalAnthems();
            $anthem = collect($anthems)->firstWhere('id', $request->input('anthem_id'));
            if ($anthem) {
                $audioUrl = $anthem['audio_url'];
            }
        }

        $time = substr($validated['time'], 0, 5);

        $bell = SchoolBell::create([
            'title' => $validated['title'],
            'time' => $time,
            'days' => $validated['days'],
            'sound_type' => $validated['sound_type'],
            'audio_url' => $audioUrl,
            'surah_number' => $validated['surah_number'] ?? null,
            'surah_name' => $surahName,
            'volume' => $validated['volume'] ?? 100,
            'duration_seconds' => $validated['duration_seconds'] ?? ($validated['sound_type'] === 'murottal' ? 900 : 15),
            'description' => $validated['description'] ?? null,
            'voice_text' => $validated['voice_text'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        SchoolSetting::set('school_bells_initialized', '1', 'school_bell', 'Status inisialisasi jadwal bel sekolah');
        AuditLog::log('CREATE_SCHOOL_BELL', 'SchoolBell', $bell->id, null, $validated);

        return back()->with('success', "Jadwal Bel '{$bell->title}' pada pukul {$bell->time} WIB berhasil disimpan.");
    }

    /**
     * Kembalikan seluruh jadwal ke setelan default/standar sekolah (13 jadwal).
     */
    public function resetDefaults(): RedirectResponse
    {
        SchoolBell::query()->delete();
        SchoolBell::ensureDefaultSchedules(force: true);
        AuditLog::log('RESET_DEFAULT_SCHOOL_BELLS', 'SchoolBell');

        return back()->with('success', 'Jadwal bel sekolah berhasil dikembalikan ke jadwal standar (13 jadwal).');
    }

    /**
     * Hapus semua jadwal bel sekaligus.
     */
    public function clearAll(): RedirectResponse
    {
        $count = SchoolBell::count();
        SchoolBell::query()->delete();
        SchoolSetting::set('school_bells_initialized', '1', 'school_bell', 'Status inisialisasi jadwal bel sekolah');
        AuditLog::log('CLEAR_ALL_SCHOOL_BELLS', 'SchoolBell');

        return back()->with('success', "Seluruh jadwal bel ({$count} jadwal) berhasil dihapus.");
    }

    /**
     * Perbarui Jadwal Bel Sekolah
     */
    public function update(Request $request, SchoolBell $schoolBell): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'time' => ['required', 'string', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['string', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'sound_type' => ['required', 'string', 'in:chime,murottal,voice,siren,custom,song'],
            'surah_number' => ['nullable', 'integer', 'between:1,114'],
            'duration_seconds' => ['nullable', 'integer', 'min:5', 'max:3600'],
            'volume' => ['nullable', 'integer', 'between:10,100'],
            'description' => ['nullable', 'string', 'max:500'],
            'voice_text' => ['nullable', 'string', 'max:500'],
            'audio_file' => ['nullable', 'file', 'mimes:mp3,wav,ogg,m4a,aac', 'max:25600'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $audioUrl = $schoolBell->audio_url;
        $surahName = $schoolBell->surah_name;

        if ($request->hasFile('audio_file')) {
            $path = $request->file('audio_file')->store('school_bells', 'public');
            $audioUrl = asset('storage/'.$path);
            $surahName = null;
        } elseif ($validated['sound_type'] === 'murottal' && ! empty($validated['surah_number'])) {
            $surah = QuranDataService::getSurah((int) $validated['surah_number']);
            if ($surah) {
                $surahName = $surah['name'];
                $audioUrl = $surah['audio_url'];
            }
        } elseif ($validated['sound_type'] === 'song' && $request->filled('anthem_id')) {
            $anthems = NationalAnthemService::getNationalAnthems();
            $anthem = collect($anthems)->firstWhere('id', $request->input('anthem_id'));
            if ($anthem) {
                $audioUrl = $anthem['audio_url'];
            }
            $surahName = null;
        } elseif (in_array($validated['sound_type'], ['custom', 'song'], true)) {
            // Pertahankan audio sebelumnya jika tidak ada file baru yang diunggah
            $surahName = null;
        } else {
            if ($schoolBell->sound_type !== $validated['sound_type']) {
                $audioUrl = null;
            }
            $surahName = null;
        }

        $time = substr($validated['time'], 0, 5);

        $schoolBell->update([
            'title' => $validated['title'],
            'time' => $time,
            'days' => $validated['days'],
            'sound_type' => $validated['sound_type'],
            'audio_url' => $audioUrl,
            'surah_number' => $validated['surah_number'] ?? null,
            'surah_name' => $surahName,
            'volume' => $validated['volume'] ?? 100,
            'duration_seconds' => $validated['duration_seconds'] ?? $schoolBell->duration_seconds,
            'description' => $validated['description'] ?? null,
            'voice_text' => $validated['voice_text'] ?? $schoolBell->voice_text,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        AuditLog::log('UPDATE_SCHOOL_BELL', 'SchoolBell', $schoolBell->id, null, $validated);

        return back()->with('success', "Jadwal Bel '{$schoolBell->title}' ({$schoolBell->time} WIB) berhasil diperbarui.");
    }

    /**
     * Toggle status aktif / non-aktif bel
     */
    public function toggle(SchoolBell $schoolBell): JsonResponse|RedirectResponse
    {
        $schoolBell->update(['is_active' => ! $schoolBell->is_active]);

        $statusStr = $schoolBell->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $message = "Jadwal Bel '{$schoolBell->title}' berhasil {$statusStr}.";

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $schoolBell->is_active,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Hapus jadwal bel
     */
    public function destroy(SchoolBell $schoolBell): RedirectResponse
    {
        $title = $schoolBell->title;
        $schoolBell->delete();

        SchoolSetting::set('school_bells_initialized', '1', 'school_bell', 'Status inisialisasi jadwal bel sekolah');
        AuditLog::log('DELETE_SCHOOL_BELL', 'SchoolBell', $schoolBell->id);

        return back()->with('success', "Jadwal Bel '{$title}' berhasil dihapus.");
    }

    /**
     * API JSON: Daftar Jadwal Bel Hari Ini untuk Live Player Auto-Scheduler di Frontend
     */
    public function schedulesJson(Request $request): JsonResponse
    {
        $dayName = $request->query('day') ?: SchoolBell::getCurrentDayName();
        $bells = SchoolBell::active()->forDay($dayName)->orderBy('time', 'asc')->get();

        return response()->json([
            'success' => true,
            'day' => $dayName,
            'current_time' => Carbon::now()->format('H:i:s'),
            'data' => $bells,
        ]);
    }

    /**
     * API JSON: Daftar 114 Surah Al-Qur'an lengkap untuk Murottal Player
     */
    public function surahsJson(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'total' => 114,
            'data' => array_values(QuranDataService::getAllSurahs()),
        ]);
    }
}
