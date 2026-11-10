<!-- KOP SEKOLAH -->
<table class="kop-table" style="width: 100%; border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 12px;">
    <tr>
        <td style="width: 70px; text-align: center;">
            <img src="{{ asset('images/logo-jabar.png') }}" onerror="this.src='{{ asset('images/logo-sma1.png') }}'" style="width: 60px; height: auto;">
        </td>
        <td style="text-align: center;">
            <h4 style="margin: 0; font-size: 10.5pt; font-weight: bold;">PEMERINTAH DAERAH PROVINSI JAWA BARAT</h4>
            <h3 style="margin: 1px 0; font-size: 11.5pt; font-weight: bold;">DINAS PENDIDIKAN</h3>
            <h4 style="margin: 0; font-size: 10.5pt; font-weight: bold;">CABANG DINAS PENDIDIKAN WILAYAH V</h4>
            <h2 style="margin: 2px 0; font-size: 13.5pt; font-weight: bold;">SMA NEGERI 1 LENGKONG</h2>
            <p style="margin: 0; font-size: 8.5pt;">Jl. Raya Tegallega Kec. Lengkong Kabupaten Sukabumi</p>
            <p style="margin: 0; font-size: 8.5pt;">Email: sman1lengkong@gmail.com | Website: https://sman1lengkong.school</p>
        </td>
        <td style="width: 70px; text-align: center;">
            <img src="{{ asset('images/logo-sma1.png') }}" onerror="this.src='{{ asset('images/logo-jabar.png') }}'" style="width: 60px; height: auto;">
        </td>
    </tr>
</table>

<div style="text-align: center; margin-bottom: 12px;">
    <h3 style="margin: 0; font-size: 12pt; font-weight: bold; text-decoration: underline;">
        RAPOR PROJEK PENGUATAN PROFIL PELAJAR PANCASILA (P5)
    </h3>
    <p style="margin: 2px 0 0 0; font-size: 9.5pt;">TAHUN PELAJARAN {{ $setting->academicYear->name ?? '2025/2026' }}</p>
</div>

<table style="width: 100%; margin-bottom: 15px; font-size: 9.5pt;">
    <tr>
        <td style="width: 18%;">Nama Peserta Didik</td>
        <td style="width: 2%;">:</td>
        <td style="width: 40%;"><strong>{{ strtoupper($student->name) }}</strong></td>
        <td style="width: 18%;">Kelas</td>
        <td style="width: 2%;">:</td>
        <td style="width: 20%;">{{ $schoolClass->name ?? '-' }}</td>
    </tr>
    <tr>
        <td>NIS / NISN</td>
        <td>:</td>
        <td>{{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}</td>
        <td>Fase / Semester</td>
        <td>:</td>
        <td>{{ ($schoolClass->grade ?? 'X') === 'X' ? 'E' : 'F' }} / {{ $setting->semester == '1' ? '1 (Ganjil)' : '2 (Genap)' }}</td>
    </tr>
</table>

<!-- DAFTAR PROJEK KOKURIKULER -->
@forelse($projects as $pIdx => $project)
    @php
        $pScore = isset($cocurricularScores) ? $cocurricularScores->get($project->id) : (isset($scores) ? $scores->get($project->id) : null);
        $dimScores = $pScore?->dimension_scores ?? [];
    @endphp
    <div style="border: 1px solid #000; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #666; padding-bottom: 5px; margin-bottom: 6px;">
            <strong style="font-size: 10.5pt;">PROJEK {{ $pIdx + 1 }}: {{ strtoupper($project->title) }}</strong>
            <span style="font-size: 9pt; background: #eee; padding: 2px 8px; border-radius: 3px;">Tema: {{ $project->theme }}</span>
        </div>
        <p style="font-size: 8.5pt; text-align: justify; margin: 4px 0 8px 0; color: #333;">
            {{ $project->description }}
        </p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 8px; font-size: 9pt;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="border: 1px solid #000; padding: 5px; width: 50%; text-align: left;">Dimensi Profil Pelajar Pancasila</th>
                    <th style="border: 1px solid #000; padding: 5px; width: 12.5%; text-align: center;">MB</th>
                    <th style="border: 1px solid #000; padding: 5px; width: 12.5%; text-align: center;">SB</th>
                    <th style="border: 1px solid #000; padding: 5px; width: 12.5%; text-align: center;">BSH</th>
                    <th style="border: 1px solid #000; padding: 5px; width: 12.5%; text-align: center;">SAB</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->target_dimensions ?? [] as $dim)
                    @php
                        $val = $dimScores[$dim] ?? 'BSH';
                    @endphp
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px;">{{ $dim }}</td>
                        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $val === 'MB' ? '✓' : '' }}</td>
                        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $val === 'SB' ? '✓' : '' }}</td>
                        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $val === 'BSH' ? '✓' : '' }}</td>
                        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $val === 'SAB' ? '✓' : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="font-size: 8.5pt; margin-top: 4px;">
            <strong>Catatan Proses Projek:</strong>
            <p style="margin: 2px 0 0 0; font-style: italic;">
                {{ $pScore?->process_notes ?? 'Peserta didik aktif bergotong royong, menunjukkan kreativitas, dan bernalar kritis dalam menyelesaikan seluruh tahapan karya projek.' }}
            </p>
        </div>
    </div>
@empty
    <div style="border: 1px dashed #999; padding: 25px; text-align: center; color: #666;">
        Belum ada kegiatan projek kokurikuler yang aktif untuk tingkat ini.
    </div>
@endforelse

<div style="font-size: 8pt; color: #555; margin-top: 5px;">
    <em>Keterangan: MB = Mulai Berkembang | SB = Sedang Berkembang | BSH = Berkembang Sesuai Harapan | SAB = Sangat Berkembang</em>
</div>

<!-- TANDA TANGAN (KEPALA SEKOLAH DI KIRI!) -->
<table style="width: 100%; margin-top: 25px; font-size: 9.5pt; text-align: center;">
    <tr>
        <!-- KIRI: KEPALA SEKOLAH -->
        <td style="width: 50%; vertical-align: top;">
            <p style="margin: 0;">Mengetahui,</p>
            <p style="margin: 2px 0 55px 0;">Kepala Sekolah,</p>
            <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                {{ $setting->headmaster_name ?? 'H. Jajang Sutisna, M.Pd.' }}
            </p>
            <p style="margin: 2px 0 0 0;">
                NIP. {{ $setting->headmaster_nip ?? '196805141994121001' }}
            </p>
        </td>
        <!-- KANAN: KOORDINATOR -->
        <td style="width: 50%; vertical-align: top;">
            <p style="margin: 0;">{{ $setting->report_place ?? 'Lengkong' }}, {{ Carbon\Carbon::parse($setting->report_date)->translatedFormat('d F Y') }}</p>
            <p style="margin: 2px 0 55px 0;">Koordinator Projek,</p>
            <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                {{ $projects->first()?->coordinator->name ?? 'Tim Fasilitator P5' }}
            </p>
            <p style="margin: 2px 0 0 0;">
                NIP. {{ $projects->first()?->coordinator->nip ?? '-' }}
            </p>
        </td>
    </tr>
</table>

