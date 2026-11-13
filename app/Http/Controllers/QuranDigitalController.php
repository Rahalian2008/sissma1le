<?php

namespace App\Http\Controllers;

use App\Services\QuranDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuranDigitalController extends Controller
{
    /**
     * Tampilan Indeks Al-Qur'an Digital 114 Surah (Bisa diakses oleh semua role)
     */
    public function index(Request $request): View
    {
        $allSurahs = QuranDataService::getAllSurahs();
        $featuredSurahs = QuranDataService::getFeaturedSurahs();

        // Statistik Al-Qur'an
        $stats = [
            'total_surahs' => count($allSurahs),
            'total_juz' => 30,
            'total_ayahs' => 6236,
            'makkiyah_count' => collect($allSurahs)->where('type', 'Makkiyah')->count(),
            'madaniyah_count' => collect($allSurahs)->where('type', 'Madaniyah')->count(),
        ];

        return view('quran.index', compact('allSurahs', 'featuredSurahs', 'stats'));
    }

    /**
     * Tampilan Baca Lengkap Ayat demi Ayat Suatu Surah (Reader Mode)
     */
    public function show(Request $request, int $number): View
    {
        if ($number < 1 || $number > 114) {
            abort(404, 'Surat Al-Qur\'an tidak ditemukan. Nomor surat valid adalah 1 sampai 114.');
        }

        $allSurahs = QuranDataService::getAllSurahs();
        $surah = QuranDataService::getSurahDetail($number);

        if (! $surah) {
            abort(404, 'Data surat tidak berhasil dimuat.');
        }

        $tafsir = QuranDataService::getSurahTafsir($number);

        return view('quran.show', compact('surah', 'allSurahs', 'tafsir'));
    }

    /**
     * Endpoint API JSON untuk data ayat surah
     */
    public function apiSurah(int $number): JsonResponse
    {
        if ($number < 1 || $number > 114) {
            return response()->json(['success' => false, 'message' => 'Nomor surat tidak valid'], 404);
        }

        $surah = QuranDataService::getSurahDetail($number);

        return response()->json([
            'success' => true,
            'data' => $surah,
        ]);
    }

    /**
     * Endpoint API JSON untuk data tafsir ringkas surah
     */
    public function apiTafsir(int $number): JsonResponse
    {
        if ($number < 1 || $number > 114) {
            return response()->json(['success' => false, 'message' => 'Nomor surat tidak valid'], 404);
        }

        $tafsir = QuranDataService::getSurahTafsir($number);

        return response()->json([
            'success' => true,
            'surah_number' => $number,
            'tafsir' => $tafsir,
        ]);
    }
}
