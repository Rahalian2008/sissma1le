<!-- KOP SEKOLAH DINAS RESMI -->
<table class="kop-table" style="width: 100%; border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 12px;">
    <tr>
        <td style="width: 75px; text-align: center;">
            <img src="{{ asset('images/logo-jabar.png') }}" onerror="this.src='{{ asset('images/logo-sma1.png') }}'" style="width: 65px; height: auto;">
        </td>
        <td style="text-align: center;">
            <h4 style="margin: 0; font-size: 11pt; font-weight: bold;">PEMERINTAH DAERAH PROVINSI JAWA BARAT</h4>
            <h3 style="margin: 1px 0; font-size: 12pt; font-weight: bold;">DINAS PENDIDIKAN</h3>
            <h4 style="margin: 0; font-size: 11pt; font-weight: bold;">CABANG DINAS PENDIDIKAN WILAYAH V</h4>
            <h2 style="margin: 2px 0; font-size: 14pt; font-weight: bold;">SMA NEGERI 1 LENGKONG</h2>
            <p style="margin: 0; font-size: 8.5pt;">Jl. Raya Tegallega Kec. Lengkong Kabupaten Sukabumi</p>
            <p style="margin: 0; font-size: 8.5pt;">Email: sman1lengkong@gmail.com | Website: https://sman1lengkong.school</p>
        </td>
        <td style="width: 75px; text-align: center;">
            <img src="{{ asset('images/logo-sma1.png') }}" onerror="this.src='{{ asset('images/logo-jabar.png') }}'" style="width: 65px; height: auto;">
        </td>
    </tr>
</table>

<!-- IDENTITAS LAPORAN -->
<div style="text-align: center; margin-bottom: 10px;">
    <h3 style="margin: 0; font-size: 12pt; font-weight: bold; text-decoration: underline;">
        LAPORAN HASIL BELAJAR (RAPOR)
    </h3>
    <p style="margin: 2px 0 0 0; font-size: 9.5pt;">KURIKULUM NASIONAL / MERDEKA</p>
</div>

<table style="width: 100%; margin-bottom: 12px; font-size: 9.5pt;">
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
    <tr>
        <td>Nama Sekolah</td>
        <td>:</td>
        <td>SMA Negeri 1 Lengkong</td>
        <td>Tahun Pelajaran</td>
        <td>:</td>
        <td>{{ $setting->academicYear->name ?? '2025/2026' }}</td>
    </tr>
</table>

<!-- TABEL NILAI CAPAIAN HASIL BELAJAR -->
<table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9pt;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="border: 1px solid #000; padding: 5px; width: 6%; text-align: center;">No</th>
            <th style="border: 1px solid #000; padding: 5px; width: 32%; text-align: left;">Mata Pelajaran</th>
            <th style="border: 1px solid #000; padding: 5px; width: 12%; text-align: center;">Nilai Akhir</th>
            <th style="border: 1px solid #000; padding: 5px; width: 50%; text-align: left;">Capaian Kompetensi</th>
        </tr>
    </thead>
    <tbody>
        @php
            $groupA = $subjectGroups->where('group_type', 'A');
            $groupB = $subjectGroups->where('group_type', 'B');
            $groupC = $subjectGroups->where('group_type', 'C');
            $scoresBySubject = $scores->keyBy(function($item) {
                return $item->courseClass->subject_id ?? 0;
            });
            $no = 1;

            $hasCombine = isset($combinedSubjects) && $combinedSubjects->isNotEmpty();
            $combinedSourceIds = $hasCombine ? $combinedSubjects->pluck('source_subject_ids')->flatten()->toArray() : [];
        @endphp

        @if($groupA->isNotEmpty())
            <tr style="background-color: #e6e6e6; font-weight: bold;">
                <td colspan="4" style="border: 1px solid #000; padding: 4px 6px;">A. Kelompok Mata Pelajaran Umum</td>
            </tr>

            @if($hasCombine)
                {{-- Render Combined Subjects first (e.g. IPA, IPS) --}}
                @foreach($combinedSubjects as $cs)
                    @php
                        $subScores = [];
                        $subDescs = [];
                        foreach($cs->source_subject_ids ?? [] as $sid) {
                            $sc = $scoresBySubject->get($sid);
                            if ($sc && !is_null($sc->score)) {
                                $subScores[] = $sc->score;
                                if ($sc->full_description) {
                                    $subDescs[] = ($sc->courseClass->subject->name ?? 'Mapel') . ': ' . $sc->full_description;
                                }
                            }
                        }
                        $combScore = !empty($subScores) ? round(array_sum($subScores) / count($subScores)) : null;
                        $combDesc = !empty($subDescs) ? implode(' ', $subDescs) : 'Mencapai kompetensi pembelajaran yang dipersyaratkan dengan baik.';
                    @endphp
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $no++ }}</td>
                        <td style="border: 1px solid #000; padding: 4px; font-weight: bold;">{{ $cs->combined_name }}</td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">{{ $combScore ? number_format($combScore, 0) : '-' }}</td>
                        <td style="border: 1px solid #000; padding: 4px; font-size: 8.5pt; text-align: justify; line-height: 1.25;">
                            {{ $combDesc }}
                        </td>
                    </tr>
                @endforeach

                {{-- Render other Group A subjects not in combination --}}
                @foreach($groupA as $item)
                    @if(!in_array($item->subject_id, $combinedSourceIds))
                        @php $sc = $scoresBySubject->get($item->subject_id); @endphp
                        <tr>
                            <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $no++ }}</td>
                            <td style="border: 1px solid #000; padding: 4px;">{{ $item->display_name ?? $item->subject->name ?? '-' }}</td>
                            <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">{{ $sc ? number_format($sc->score, 0) : '-' }}</td>
                            <td style="border: 1px solid #000; padding: 4px; font-size: 8.5pt; text-align: justify; line-height: 1.25;">
                                {{ $sc?->full_description ?? 'Mencapai kompetensi pembelajaran yang dipersyaratkan dengan baik.' }}
                            </td>
                        </tr>
                    @endif
                @endforeach
            @else
                @foreach($groupA as $item)
                    @php $sc = $scoresBySubject->get($item->subject_id); @endphp
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $no++ }}</td>
                        <td style="border: 1px solid #000; padding: 4px;">{{ $item->display_name ?? $item->subject->name ?? '-' }}</td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">{{ $sc ? number_format($sc->score, 0) : '-' }}</td>
                        <td style="border: 1px solid #000; padding: 4px; font-size: 8.5pt; text-align: justify; line-height: 1.25;">
                            {{ $sc?->full_description ?? 'Mencapai kompetensi pembelajaran yang dipersyaratkan dengan baik.' }}
                        </td>
                    </tr>
                @endforeach
            @endif
        @endif

        @if($groupB->isNotEmpty())
            <tr style="background-color: #e6e6e6; font-weight: bold;">
                <td colspan="4" style="border: 1px solid #000; padding: 4px 6px;">B. Kelompok Mata Pelajaran Pilihan</td>
            </tr>
            @foreach($groupB as $item)
                @php $sc = $scoresBySubject->get($item->subject_id); @endphp
                <tr>
                    <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $no++ }}</td>
                    <td style="border: 1px solid #000; padding: 4px;">{{ $item->display_name ?? $item->subject->name ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">{{ $sc ? number_format($sc->score, 0) : '-' }}</td>
                    <td style="border: 1px solid #000; padding: 4px; font-size: 8.5pt; text-align: justify; line-height: 1.25;">
                        {{ $sc?->full_description ?? 'Mencapai kompetensi pembelajaran yang dipersyaratkan dengan baik.' }}
                    </td>
                </tr>
            @endforeach
        @endif

        @if($groupC->isNotEmpty())
            <tr style="background-color: #e6e6e6; font-weight: bold;">
                <td colspan="4" style="border: 1px solid #000; padding: 4px 6px;">C. Muatan Lokal</td>
            </tr>
            @foreach($groupC as $item)
                @php $sc = $scoresBySubject->get($item->subject_id); @endphp
                <tr>
                    <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $no++ }}</td>
                    <td style="border: 1px solid #000; padding: 4px;">{{ $item->display_name ?? $item->subject->name ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">{{ $sc ? number_format($sc->score, 0) : '-' }}</td>
                    <td style="border: 1px solid #000; padding: 4px; font-size: 8.5pt; text-align: justify; line-height: 1.25;">
                        {{ $sc?->full_description ?? 'Mencapai kompetensi pembelajaran yang dipersyaratkan dengan baik.' }}
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

<!-- EKSTRAKURIKULER & KETIDAKHADIRAN -->
<table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
    <tr>
        <td style="width: 58%; vertical-align: top; padding-right: 5px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 8.5pt;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="border: 1px solid #000; padding: 4px; width: 8%; text-align: center;">No</th>
                        <th style="border: 1px solid #000; padding: 4px; width: 32%; text-align: left;">Ekstrakurikuler</th>
                        <th style="border: 1px solid #000; padding: 4px; width: 20%; text-align: center;">Predikat</th>
                        <th style="border: 1px solid #000; padding: 4px; width: 40%; text-align: left;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($extracurriculars as $eIdx => $extra)
                        <tr>
                            <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $eIdx + 1 }}</td>
                            <td style="border: 1px solid #000; padding: 4px;">{{ $extra->extracurricular->name ?? '-' }}</td>
                            <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">{{ $extra->predicate }}</td>
                            <td style="border: 1px solid #000; padding: 4px; font-size: 8pt;">{{ $extra->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td style="border: 1px solid #000; padding: 4px; text-align: center;">1</td>
                            <td style="border: 1px solid #000; padding: 4px;">Pramuka Wajib</td>
                            <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">Baik</td>
                            <td style="border: 1px solid #000; padding: 4px; font-size: 8pt;">Aktif mengikuti kegiatan kepramukaan dan kepemimpinan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </td>

        <td style="width: 42%; vertical-align: top; padding-left: 5px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 8.5pt;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th colspan="2" style="border: 1px solid #000; padding: 4px; text-align: center;">Ketidakhadiran</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px; width: 70%;">Sakit (S)</td>
                        <td style="border: 1px solid #000; padding: 4px; width: 30%; text-align: center; font-weight: bold;">{{ $homeroom->sick ?? 0 }} hari</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px;">Izin (I)</td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">{{ $homeroom->permission ?? 0 }} hari</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px;">Tanpa Keterangan (A)</td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">{{ $homeroom->absent ?? 0 }} hari</td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>

<!-- CATATAN WALI KELAS -->
<div style="font-size: 8.5pt; font-weight: bold; margin-bottom: 2px;">Catatan Wali Kelas:</div>
<div style="border: 1px solid #000; padding: 6px 10px; margin-bottom: 8px; font-size: 8.5pt;">
    <em>{{ $homeroom->note ?? 'Pertahankan prestasi belajar dan tingkatkan kedisiplinan serta partisipasi aktif dalam kegiatan sekolah.' }}</em>
</div>

<!-- KEPUTUSAN KENAIKAN / KELULUSAN -->
@if($setting->semester == '2')
    <div style="border: 2px solid #000; padding: 6px 10px; margin-bottom: 10px; font-size: 9pt; background-color: #fbfbfb;">
        <strong>Keputusan:</strong><br>
        Berdasarkan pencapaian seluruh kompetensi pada Tahun Pelajaran {{ $setting->academicYear->name ?? '2025/2026' }}, peserta didik dinyatakan:<br>
        <div style="margin-top: 3px; font-size: 10.5pt; font-weight: bold; text-align: center;">
            @if(($schoolClass->grade ?? 'X') === 'XII')
                {{ ($homeroom->status_promotion ?? 'LULUS') === 'LULUS' ? 'LULUS DARI SATUAN PENDIDIKAN' : 'TIDAK LULUS' }}
            @else
                {{ ($homeroom->status_promotion ?? 'NAIK') === 'NAIK' ? 'NAIK KE KELAS ' . ($homeroom->promotion_target_grade ?? (($schoolClass->grade ?? 'X') === 'X' ? 'XI' : 'XII')) : 'TINGGAL DI KELAS' }}
            @endif
        </div>
    </div>
@endif

<!-- TANDA TANGAN (KEPALA SEKOLAH WAJIB DI KIRI SESUAI ATURAN RESMI DINAS!) -->
<table style="width: 100%; margin-top: 15px; font-size: 9.5pt; text-align: center;">
    <tr>
        <!-- KIRI: KEPALA SEKOLAH -->
        <td style="width: 38%; vertical-align: top; position: relative;">
            <p style="margin: 0;">Mengetahui,</p>
            <p style="margin: 2px 0 0 0;">Kepala Sekolah,</p>

            <div style="height: 55px; position: relative; margin: 2px 0;">
                @if($setting->show_signature_image)
                    @if($setting->school_stamp)
                        <img src="{{ asset('storage/'.$setting->school_stamp) }}" alt="Stempel" style="position: absolute; left: 15%; top: -6px; height: 65px; opacity: 0.85; z-index: 1;">
                    @endif
                    @if($setting->headmaster_signature)
                        <img src="{{ asset('storage/'.$setting->headmaster_signature) }}" alt="TTD Kepsek" style="position: absolute; left: 25%; top: 0; height: 55px; z-index: 2;">
                    @endif
                @endif
            </div>

            <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                {{ $setting->headmaster_name ?? 'H. Jajang Sutisna, M.Pd.' }}
            </p>
            <p style="margin: 2px 0 0 0;">
                NIP. {{ $setting->headmaster_nip ?? '196805141994121001' }}
            </p>
        </td>

        <!-- TENGAH: ORANG TUA / WALI -->
        <td style="width: 28%; vertical-align: top;">
            <p style="margin: 0;">Mengetahui,</p>
            <p style="margin: 2px 0 55px 0;">Orang Tua / Wali,</p>
            <p style="margin: 0; text-decoration: underline;">
                ..........................................
            </p>
        </td>

        <!-- KANAN: WALI KELAS -->
        <td style="width: 34%; vertical-align: top;">
            <p style="margin: 0;">{{ $setting->report_place ?? 'Lengkong' }}, {{ Carbon\Carbon::parse($setting->report_date)->translatedFormat('d F Y') }}</p>
            <p style="margin: 2px 0 55px 0;">Wali Kelas,</p>
            <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                {{ $schoolClass->homeroomTeacher->name ?? '..........................................' }}
            </p>
            <p style="margin: 2px 0 0 0;">
                NIP. {{ $schoolClass->homeroomTeacher->nip ?? '-' }}
            </p>
        </td>
    </tr>
</table>

