<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Asset;
use App\Models\OutgoingLetter;
use App\Models\SchoolIdentity;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicVerifyController extends Controller
{
    /**
     * Public validation endpoint for scanned QR codes.
     */
    public function verify(Request $request, string $type, string $token): View
    {
        $school = SchoolIdentity::current();
        $isValid = false;
        $title = 'Verifikasi Dokumen Digital';
        $entity = null;
        $details = [];

        if ($type === 'student' || $type === 'buku-induk') {
            $student = Student::where('qr_code_token', $token)->orWhere('nis', $token)->first();
            if ($student) {
                $isValid = true;
                $title = 'Verifikasi Lembar Buku Induk Siswa';
                $entity = $student;
                $details = [
                    'Nama Siswa' => $student->name,
                    'NIS / NISN' => "{$student->nis} / ".($student->nisn ?? '-'),
                    'Tingkat Kelas' => $student->current_class_level.' '.($student->current_rombel ?? ''),
                    'Status Terdaftar' => $student->status,
                    'Asal Sekolah' => $student->school_origin ?? '-',
                    'Diterbitkan Oleh' => $school->name,
                ];
            }
        } elseif ($type === 'aset') {
            $asset = Asset::where('qr_code_token', $token)->orWhere('code', $token)->first();
            if ($asset) {
                $isValid = true;
                $title = 'Verifikasi Aset Inventaris Sekolah';
                $entity = $asset;
                $details = [
                    'Nama Aset' => $asset->name,
                    'Kode Inventaris' => $asset->code,
                    'Merk / Model' => $asset->brand_model ?? '-',
                    'Tahun Pengadaan' => (string) $asset->procurement_year,
                    'Kondisi Fisik' => $asset->condition,
                    'Lokasi Ruang' => $asset->room ? $asset->room->name : 'Gudang Utama',
                    'Kepemilikan' => $school->name,
                ];
            }
        } elseif ($type === 'arsip') {
            $archive = Archive::where('qr_code_token', $token)->orWhere('archive_number', $token)->first();
            if ($archive) {
                $isValid = true;
                $title = 'Verifikasi Berkas Kearsipan Digital';
                $entity = $archive;
                $details = [
                    'Perihal Berkas' => $archive->title,
                    'Nomor Berkas' => $archive->archive_number,
                    'Kategori Arsip' => $archive->category,
                    'Tahun Penciptaan' => (string) $archive->year,
                    'Status Berkas' => $archive->archive_status,
                    'Masa Retensi' => "{$archive->retention_period_years} Tahun",
                ];
            }
        } elseif ($type === 'surat') {
            $letter = OutgoingLetter::where('letter_number', $token)->first();
            if ($letter) {
                $isValid = true;
                $title = 'Verifikasi Keaslian Surat Keluar';
                $entity = $letter;
                $details = [
                    'Nomor Surat' => $letter->letter_number,
                    'Perihal' => $letter->subject,
                    'Tujuan' => $letter->recipient,
                    'Tanggal Surat' => $letter->letter_date->format('d F Y'),
                    'Penandatangan' => $letter->signer_name,
                    'Status Dokumen' => $letter->status,
                ];
            }
        }

        return view('public.verify', compact('school', 'isValid', 'title', 'type', 'details'));
    }
}
