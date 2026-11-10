<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use App\Models\SchoolIdentity;
use App\Services\SipdBmdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SipdBmdController extends Controller
{
    /**
     * Tampilkan halaman utama Pencarian & Katalog Kodefikasi SIPD e-BMD (Permendagri No. 108/2016)
     */
    public function index(Request $request): View
    {
        $school = SchoolIdentity::first() ?? new SchoolIdentity;
        $kib = $request->query('kib', 'ALL');
        $q = $request->query('q', '');

        $kibCategories = SipdBmdService::getKibCategories();
        $items = SipdBmdService::searchCodes($q, $kib, 500);
        $totalItems = count(SipdBmdService::getStandardCodes());
        $instansi = SipdBmdService::getOfficialSchoolHierarchy();

        return view('sarpras.sipd_bmd.index', compact('school', 'kibCategories', 'items', 'kib', 'q', 'totalItems', 'instansi'));
    }

    /**
     * API Pencarian Real-Time SIPD e-BMD via JSON
     */
    public function search(Request $request): JsonResponse
    {
        $q = $request->query('q');
        $kib = $request->query('kib');
        $results = SipdBmdService::searchCodes($q, $kib, 500);

        return response()->json([
            'status' => 'success',
            'total' => count($results),
            'data' => $results,
        ]);
    }

    /**
     * API Pencarian Online / Cerdas Permendagri No. 108/2016
     * Tidak terbatas pada data database lokal, mampu memetakan secara cerdas input bebas apapun
     */
    public function onlineSearch(Request $request): JsonResponse
    {
        $q = (string) $request->query('q', '');
        $kib = $request->query('kib');
        $results = SipdBmdService::searchOnline($q, $kib, 100);

        return response()->json([
            'status' => 'success',
            'query' => $q,
            'kib' => $kib ?: 'ALL',
            'is_online' => true,
            'source' => 'Permendagri 108/2016 & BAS 1.1.7 Live Engine',
            'total' => count($results),
            'data' => $results,
        ]);
    }
}
