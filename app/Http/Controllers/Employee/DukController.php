<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use App\Services\DukCalculationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DukController extends Controller
{
    public function index(Request $request): View
    {
        $filterType = $request->query('type', 'all');
        $rankedEmployees = DukCalculationService::getRankedEmployees($filterType);

        return view('duk.index', compact('rankedEmployees', 'filterType'));
    }

    public function print(Request $request): View
    {
        $filterType = $request->query('type', 'all');
        $rankedEmployees = DukCalculationService::getRankedEmployees($filterType);
        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'duk', 'Mencetak Daftar Urut Kepangkatan (DUK) Pegawai');

        return view('duk.print', compact('rankedEmployees', 'school', 'filterType'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $filterType = $request->query('type', 'all');
        $rankedEmployees = DukCalculationService::getRankedEmployees($filterType);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="duk-pegawai-sma1le-'.date('Y-m-d').'.csv"',
        ];

        return response()->stream(function () use ($rankedEmployees) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'No Urut', 'NIP', 'Nama Lengkap', 'Pangkat / Golongan',
                'TMT Pangkat', 'Jabatan', 'TMT Jabatan', 'Masa Kerja Thn',
                'Masa Kerja Bln', 'Pendidikan Terakhir', 'Status Pegawai', 'Keterangan',
            ]);

            foreach ($rankedEmployees as $index => $emp) {
                fputcsv($output, [
                    $index + 1,
                    $emp->nip ?? '-',
                    $emp->formatted_name,
                    $emp->rank_group ?? '-',
                    $emp->rank_tmt ? $emp->rank_tmt->format('d/m/Y') : '-',
                    $emp->position ?? '-',
                    $emp->position_tmt ? $emp->position_tmt->format('d/m/Y') : '-',
                    $emp->years_of_service_years,
                    $emp->years_of_service_months,
                    $emp->education_level.' '.($emp->education_major ?? ''),
                    $emp->employment_status,
                    $emp->additional_tasks ?? '-',
                ]);
            }

            fclose($output);
        }, 200, $headers);
    }
}
