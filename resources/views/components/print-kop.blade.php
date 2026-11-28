@props(['school' => null])

@php
    $schoolObj = $school instanceof \App\Models\SchoolIdentity ? $school : \App\Models\SchoolIdentity::current();
    $schoolArr = is_array($school) ? $school : [];

    // Resolusi Gambar Kop Surat dari Master Data Logo & KOP Surat (SchoolIdentity / SchoolSetting)
    $kopPath = null;
    if ($schoolObj && !empty($schoolObj->header_image_path)) {
        $kopPath = $schoolObj->header_image_path;
    } elseif (!empty($schoolArr['kop_image'])) {
        $kopPath = $schoolArr['kop_image'];
    } elseif (!empty($schoolArr['header_image_path'])) {
        $kopPath = $schoolArr['header_image_path'];
    }

    if (!$kopPath) {
        $kopPath = \App\Models\SchoolSetting::get('school_kop')
            ?: (\App\Models\SchoolSetting::get('kop_surat_image')
            ?: \App\Models\SchoolSetting::get('kop_image'));
    }

    $headerImageUrl = null;
    if ($kopPath) {
        $cleanKop = ltrim($kopPath, '/');
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanKop) || file_exists(public_path('storage/' . $cleanKop))) {
            $headerImageUrl = asset('storage/' . $cleanKop);
        } elseif (str_starts_with($kopPath, 'http')) {
            $headerImageUrl = $kopPath;
        } elseif (file_exists(public_path($cleanKop))) {
            $headerImageUrl = asset($cleanKop);
        }
    }

    // Resolusi Logo Sekolah dari Master Data Logo & KOP Surat
    $logoPath = null;
    if ($schoolObj && !empty($schoolObj->logo_path)) {
        $logoPath = $schoolObj->logo_path;
    } elseif (!empty($schoolArr['logo'])) {
        $logoPath = $schoolArr['logo'];
    } elseif (!empty($schoolArr['logo_path'])) {
        $logoPath = $schoolArr['logo_path'];
    }

    if (!$logoPath) {
        $logoPath = \App\Models\SchoolSetting::get('school_logo');
    }

    $logoUrl = null;
    if ($logoPath) {
        $cleanLogo = ltrim($logoPath, '/');
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanLogo) || file_exists(public_path('storage/' . $cleanLogo))) {
            $logoUrl = asset('storage/' . $cleanLogo);
        } elseif (str_starts_with($logoPath, 'http')) {
            $logoUrl = $logoPath;
        } elseif (file_exists(public_path($cleanLogo))) {
            $logoUrl = asset($cleanLogo);
        }
    }
    if (!$logoUrl && file_exists(public_path('images/logo.png'))) {
        $logoUrl = asset('images/logo.png');
    }

    $schoolName = $schoolArr['name'] ?? ($schoolObj->name ?? 'SMA NEGERI 1 LENGKONG');
    $schoolProvince = $schoolArr['province'] ?? ($schoolObj->province ?? 'JAWA BARAT');
    $schoolBranch = $schoolArr['branch_office'] ?? ($schoolObj->branch_office ?? 'CABANG DINAS PENDIDIKAN WILAYAH V');
    $schoolCity = $schoolArr['city'] ?? ($schoolObj->city ?? 'KABUPATEN SUKABUMI');
    $schoolAddress = $schoolArr['address'] ?? ($schoolObj->address ?? 'Jl. Raya Lengkong No. 01 Kec. Lengkong Kab. Sukabumi');
    $schoolDistrict = $schoolArr['district'] ?? ($schoolObj->district ?? 'Kecamatan Lengkong');
    $schoolNpsn = $schoolArr['npsn'] ?? ($schoolObj->npsn ?? '20202265');
    $schoolPhone = $schoolArr['phone'] ?? ($schoolObj->phone ?? '-');
    $schoolEmail = $schoolArr['email'] ?? ($schoolObj->email ?? 'sman1lengkong@gmail.com');
@endphp

@if($headerImageUrl)
    {{-- Gunakan Kop Surat yang diupload di Master Data Logo & Kop Surat --}}
    <div class="header-kop-banner" style="text-align: center; margin-bottom: 12px; border-bottom: 3px double #000; padding-bottom: 4px;">
        <img src="{{ $headerImageUrl }}" alt="Kop Surat Resmi {{ $schoolName }}" style="max-width: 100%; width: 100%; height: auto; max-height: 145px; object-fit: contain; display: block; margin: 0 auto;">
    </div>
@else
    {{-- Jika belum upload gambar kop surat, gunakan format kop teks resmi standar Jawa Barat Wilayah V --}}
    <div class="header-kop" style="text-align: center; border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 14px;">
        <table style="width: 100%; border: none; border-collapse: collapse; margin: 0; padding: 0;">
            <tr style="border: none;">
                <td style="width: 80px; text-align: center; vertical-align: middle; border: none; padding: 0 10px 0 0;">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" style="width: 75px; height: 75px; object-fit: contain;">
                    @endif
                </td>
                <td style="text-align: center; border: none; vertical-align: middle; padding: 0;">
                    <p style="margin: 0; font-size: 10.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; line-height: 1.25;">PEMERINTAH DAERAH PROVINSI {{ strtoupper($schoolProvince) }}</p>
                    <p style="margin: 1px 0 0 0; font-size: 11.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; line-height: 1.25;">DINAS PENDIDIKAN</p>
                    <p style="margin: 1px 0 0 0; font-size: 10pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.25;">{{ strtoupper($schoolBranch) }}</p>
                    <p style="margin: 1px 0 0 0; font-size: 10pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.25;">{{ strtoupper($schoolCity) }}</p>
                    <h2 style="font-size: 15pt; font-weight: 800; margin: 2px 0; text-transform: uppercase; letter-spacing: 1.2px; line-height: 1.2;">{{ strtoupper($schoolName) }}</h2>
                    <p style="margin: 2px 0 0 0; font-size: 8.5pt; color: #111; line-height: 1.25;">{{ $schoolAddress }} &bull; {{ $schoolDistrict }} &bull; {{ $schoolCity }} &bull; NPSN: {{ $schoolNpsn }} &bull; Telp: {{ $schoolPhone }} &bull; Email: {{ $schoolEmail }}</p>
                </td>
                <td style="width: 80px; border: none;"></td>
            </tr>
        </table>
    </div>
@endif
