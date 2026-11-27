<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Induk Siswa - {{ $student->name }} (NIS: {{ $student->nis }})</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.35;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .printable-sheet {
            max-width: 215mm;
            margin: 0 auto;
            padding: 12mm 15mm;
            box-sizing: border-box;
            background: #fff;
        }
        .title-doc {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 14px;
        }
        .title-doc h3 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title-doc p {
            font-size: 9.5pt;
            margin: 2px 0 0 0;
            color: #222;
        }
        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            background-color: #f2f2f2;
            padding: 4px 8px;
            margin-top: 12px;
            margin-bottom: 6px;
            border: 1px solid #bbb;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 6px;
        }
        table.data-table td {
            padding: 2.5px 4px;
            vertical-align: top;
        }
        table.data-table td.label {
            width: 32%;
            color: #111;
        }
        table.data-table td.separator {
            width: 2%;
            text-align: center;
        }
        table.data-table td.value {
            width: 66%;
            color: #000;
        }
        .photo-box {
            width: 30mm;
            height: 40mm;
            border: 1px solid #333;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 8.5pt;
            float: right;
            margin-left: 12px;
            margin-bottom: 8px;
            background: #fafafa;
            overflow: hidden;
        }
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .table-grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-top: 4px;
            margin-bottom: 8px;
        }
        .table-grid th, .table-grid td {
            border: 1px solid #444;
            padding: 3.5px 6px;
        }
        .table-grid th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        .signature-section {
            margin-top: 24px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 10pt;
        }
        .signature-table td {
            vertical-align: top;
            padding: 4px;
        }
        .qr-section {
            text-align: center;
            font-size: 8pt;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .page-break {
            page-break-before: always;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: transparent;
            }
            .printable-sheet {
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Buku Induk Siswa - ' . $student->name" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet">
        <!-- KOP SURAT RESMI SEKOLAH -->
        <x-print-kop :school="$school" />

        <div class="title-doc">
            <h3>LEMBAR BUKU INDUK SISWA</h3>
            <p>Nomor Registrasi Induk: <strong>REG-{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }}-{{ $student->nis }}</strong> &bull; Dapodik &amp; SIS-SMA1LE Terpadu</p>
        </div>

        <!-- Pas Foto Siswa -->
        <div class="photo-box">
            @if(!empty($student->photo) || !empty($student->face_photo_path))
                <img src="{{ asset($student->photo ?: $student->face_photo_path) }}" alt="Foto Siswa">
            @else
                <span>PAS FOTO<br>3 x 4 cm</span>
            @endif
        </div>

        <!-- ================= BAGIAN A: KETERANGAN IDENTITAS SISWA ================= -->
        <div class="section-title">A. KETERANGAN TENTANG DIRI PESERTA DIDIK</div>
        <table class="data-table">
            <tr>
                <td class="label">1. Nama Lengkap Peserta Didik</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ strtoupper($student->name) }}</strong></td>
            </tr>
            <tr>
                <td class="label">2. Nomor Induk Siswa (NIS)</td>
                <td class="separator">:</td>
                <td class="value font-mono"><strong>{{ $student->nis }}</strong></td>
            </tr>
            <tr>
                <td class="label">3. NISN / NIK Siswa</td>
                <td class="separator">:</td>
                <td class="value font-mono">{{ $student->nisn ?? '-' }} / {{ $student->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">4. Nomor Kartu Keluarga (No. KK)</td>
                <td class="separator">:</td>
                <td class="value font-mono">{{ $student->family_card_number ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">5. Jenis Kelamin</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->gender === 'L' ? 'Laki-laki (L)' : 'Perempuan (P)' }}</td>
            </tr>
            <tr>
                <td class="label">6. Tempat, Tanggal Lahir</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->birth_place ?? '-' }}, {{ $student->birth_date ? $student->birth_date->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">7. Agama &amp; Kepercayaan</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->religion ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">8. Golongan Darah</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->blood_type ?: 'Belum Terdata' }}</td>
            </tr>
            <tr>
                <td class="label">9. Keadaan Jasmani (Tinggi / Berat)</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->height ? $student->height.' cm' : '-' }} / {{ $student->weight ? $student->weight.' kg' : '-' }}</td>
            </tr>
            <tr>
                <td class="label">10. Kebutuhan Khusus / Kelainan</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->special_needs ?: 'Tidak Ada' }}</td>
            </tr>
            <tr>
                <td class="label">11. Anak Ke- / Dari Bersaudara</td>
                <td class="separator">:</td>
                <td class="value">Anak Ke-{{ $student->child_order ?? 1 }} dari {{ $student->siblings_count ?? 1 }} bersaudara</td>
            </tr>
            <tr>
                <td class="label">12. Alamat Lengkap Siswa</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->address ?? '-' }} {{ $student->rt_rw ? '(RT/RW: '.$student->rt_rw.')' : '' }}, Kec. {{ $student->residence_district ?? 'Lengkong' }}, Kab. Sukabumi {{ $student->postal_code ? 'Kode Pos '.$student->postal_code : '' }}</td>
            </tr>
            <tr>
                <td class="label">13. Tinggal Bersama / Jarak ke Sekolah</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->living_with ?? 'Bersama Orang Tua' }} &bull; Jarak: {{ $student->distance_to_school ?? '-' }} &bull; Transportasi: {{ $student->transportation ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">14. Kontak WhatsApp / HP / Email</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->phone ?? '-' }} {{ $student->email ? '&bull; Email: '.$student->email : '' }}</td>
            </tr>
        </table>

        <!-- ================= BAGIAN B: KETERANGAN PENERIMAAN ================= -->
        <div class="section-title">B. KETERANGAN PENERIMAAN &amp; RIWAYAT PENDIDIKAN</div>
        <table class="data-table">
            <tr>
                <td class="label">1. Asal Sekolah (SMP / MTs)</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $student->school_origin ?: ($student->previous_school ?: '-') }}</strong></td>
            </tr>
            <tr>
                <td class="label">2. No. Ijazah / SKHUN SMP</td>
                <td class="separator">:</td>
                <td class="value font-mono">{{ $student->junior_diploma_number ?? '-' }} / {{ $student->junior_exam_number ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">3. Tanggal Masuk Sekolah</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->admission_date ? $student->admission_date->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">4. Jalur Masuk PPDB</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $student->entry_path ?? 'Zonasi' }}</strong></td>
            </tr>
            <tr>
                <td class="label">5. Diterima di Kelas Awal</td>
                <td class="separator">:</td>
                <td class="value">Kelas {{ $student->admission_class ?? $student->current_class_level ?? 'X' }}</td>
            </tr>
            <tr>
                <td class="label">6. Kelas &amp; Rombel Saat Ini</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $student->schoolClass?->name ?? ('Kelas ' . ($student->current_class_level ?? 'X') . ' ' . ($student->current_rombel ?? '')) }}</strong> (Wali Kelas: {{ $student->schoolClass?->homeroomTeacher?->name ?? 'Belum ditentukan' }})</td>
            </tr>
            <tr>
                <td class="label">7. Program Bantuan Pendidikan (PIP / KIP)</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->is_pip ? 'Penerima Program Indonesia Pintar (No. KIP: '.($student->pip_number ?? '-').')' : 'Bukan Penerima Bantuan PIP' }}</td>
            </tr>
            @if(!empty($student->graduation_year) || !empty($student->diploma_number))
            <tr>
                <td class="label">8. Kelulusan SMA / Nomor Ijazah</td>
                <td class="separator">:</td>
                <td class="value">Lulus Tahun {{ $student->graduation_year ?? '-' }} &bull; No. Ijazah SMA: {{ $student->diploma_number ?? '-' }}</td>
            </tr>
            @endif
        </table>

        <!-- ================= BAGIAN C: KETERANGAN ORANG TUA / WALI ================= -->
        <div class="section-title">C. KETERANGAN ORANG TUA KANDUNG &amp; WALI SISWA</div>
        <table class="data-table">
            <tr>
                <td colspan="3" style="background:#fafafa; font-weight:bold; padding-top:4px; padding-bottom:4px;">1. Data Ayah Kandung:</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:14px;">a. Nama Lengkap Ayah</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $student->family?->father_name ?: ($student->father_name ?: '-') }}</strong></td>
            </tr>
            <tr>
                <td class="label" style="padding-left:14px;">b. NIK Ayah Kandung</td>
                <td class="separator">:</td>
                <td class="value font-mono">{{ $student->family?->father_nik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:14px;">c. Pendidikan / Pekerjaan</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->family?->father_education ?? '-' }} &bull; {{ $student->family?->father_occupation ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:14px;">d. Penghasilan Bulanan / No. HP</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->family?->father_income ?? '-' }} &bull; Telp: {{ $student->family?->father_phone ?? '-' }}</td>
            </tr>

            <tr>
                <td colspan="3" style="background:#fafafa; font-weight:bold; padding-top:6px; padding-bottom:4px;">2. Data Ibu Kandung:</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:14px;">a. Nama Lengkap Ibu</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $student->family?->mother_name ?: ($student->mother_name ?: '-') }}</strong></td>
            </tr>
            <tr>
                <td class="label" style="padding-left:14px;">b. NIK Ibu Kandung</td>
                <td class="separator">:</td>
                <td class="value font-mono">{{ $student->family?->mother_nik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:14px;">c. Pendidikan / Pekerjaan</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->family?->mother_education ?? '-' }} &bull; {{ $student->family?->mother_occupation ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:14px;">d. Penghasilan Bulanan / No. HP</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->family?->mother_income ?? '-' }} &bull; Telp: {{ $student->family?->mother_phone ?? '-' }}</td>
            </tr>

            @if($student->family?->guardian_name)
            <tr>
                <td colspan="3" style="background:#fafafa; font-weight:bold; padding-top:6px; padding-bottom:4px;">3. Data Wali Murid:</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:14px;">a. Nama Lengkap Wali</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $student->family->guardian_name }}</strong></td>
            </tr>
            <tr>
                <td class="label" style="padding-left:14px;">b. NIK / Pekerjaan</td>
                <td class="separator">:</td>
                <td class="value font-mono">{{ $student->family->guardian_nik ?? '-' }} &bull; {{ $student->family->guardian_occupation ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:14px;">c. Alamat / No. HP Wali</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->family->guardian_address ?? '-' }} &bull; Telp: {{ $student->family->guardian_phone ?? '-' }}</td>
            </tr>
            @endif
        </table>

        <!-- ================= BAGIAN D: REKAPITULASI KEHADIRAN & KARAKTER ================= -->
        <div class="section-title">D. REKAPITULASI KEHADIRAN, TATA TERTIB &amp; KARAKTER</div>
        <table class="table-grid">
            <thead>
                <tr>
                    <th colspan="5">Statistik Kehadiran Siswa</th>
                    <th rowspan="2">Pembiasaan 7 KAIH</th>
                    <th rowspan="2">Tahfidz Juz 30</th>
                    <th rowspan="2">Poin Tata Tertib</th>
                </tr>
                <tr>
                    <th>Hadir</th>
                    <th>Izin</th>
                    <th>Sakit</th>
                    <th>Alpa</th>
                    <th>Terlambat</th>
                </tr>
            </thead>
            <tbody style="text-align:center;">
                <tr>
                    <td><strong>{{ $attendanceSummary->hadir ?? 0 }}</strong> Hari</td>
                    <td>{{ $attendanceSummary->izin ?? 0 }} Hari</td>
                    <td>{{ $attendanceSummary->sakit ?? 0 }} Hari</td>
                    <td>{{ $attendanceSummary->alpa ?? 0 }} Hari</td>
                    <td>{{ $attendanceSummary->terlambat ?? 0 }} Kali</td>
                    <td><strong>{{ $habitsCount ?? 0 }}</strong> Log Selesai</td>
                    <td>
                        @if($student->tahfidzRecords->isNotEmpty())
                            {{ $student->tahfidzRecords->count() }} Setoran
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @php
                            $vPoints = $student->violations->where('status', 'DIVERIFIKASI')->sum('points');
                        @endphp
                        @if($vPoints > 0)
                            <span style="color:#b91c1c; font-weight:bold;">{{ $vPoints }} Poin</span>
                        @else
                            <span style="color:#047857; font-weight:bold;">0 Poin (Disiplin)</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- ================= BAGIAN E: PRESTASI & PENGHARGAAN ================= -->
        <div class="section-title">E. CATATAN PRESTASI DAN PENGHARGAAN SISWA</div>
        @if($student->achievements->isEmpty())
            <p style="font-size:9.5pt; color:#666; font-style:italic; margin:4px 0 8px 10px;">Belum ada catatan kejuaraan / prestasi terdata di sistem.</p>
        @else
            <table class="table-grid">
                <thead>
                    <tr>
                        <th style="width:5%;">No</th>
                        <th style="width:40%;">Nama Kejuaraan / Prestasi</th>
                        <th style="width:20%;">Tingkat</th>
                        <th style="width:15%;">Peringkat</th>
                        <th style="width:20%;">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($student->achievements as $idx => $ach)
                        <tr>
                            <td style="text-align:center;">{{ $idx + 1 }}</td>
                            <td><strong>{{ $ach->title ?: ($ach->name ?: '-') }}</strong></td>
                            <td style="text-align:center;">{{ $ach->level ?? '-' }}</td>
                            <td style="text-align:center;">{{ $ach->rank ? 'Juara ' . $ach->rank : 'Penghargaan' }}</td>
                            <td style="text-align:center;">{{ $ach->date ? \Carbon\Carbon::parse($ach->date)->translatedFormat('d M Y') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- ================= BAGIAN F: REKAM LAYANAN BIMBINGAN & KONSELING ================= -->
        <div class="section-title">F. REKAM JEJAK BIMBINGAN KONSELING &amp; ASESMEN PSIKOTES (BK)</div>
        <table class="data-table">
            <tr>
                <td class="label">1. Sesi Bimbingan &amp; Konseling</td>
                <td class="separator">:</td>
                <td class="value">
                    Terdata <strong>{{ $student->counselingSessions->count() }} kali</strong> sesi konseling
                    (Panggilan Orang Tua: {{ $student->parentCalls->count() }} kali &bull; Kunjungan Rumah: {{ $student->homeVisits->count() }} kali)
                </td>
            </tr>
            <tr>
                <td class="label">2. Hasil Gaya Belajar (VAK)</td>
                <td class="separator">:</td>
                <td class="value">
                    @if($student->latestVakResult)
                        <strong>{{ $student->latestVakResult->profile_label }}</strong> &bull; {{ $student->latestVakResult->analysis_summary }}
                    @else
                        Belum melaksanakan Asesmen Diagnostik Gaya Belajar VAK
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">3. Minat Karier (Holland RIASEC)</td>
                <td class="separator">:</td>
                <td class="value">
                    @if($student->latestRiasecResult)
                        <strong>{{ $student->latestRiasecResult->profile_label }}</strong> &bull; {{ $student->latestRiasecResult->analysis_summary }}
                    @else
                        Belum melaksanakan Pemetaan Minat Karier RIASEC
                    @endif
                </td>
            </tr>
            @if($student->counselingSessions->whereNotNull('career_path')->isNotEmpty())
            @php
                $latestCareer = $student->counselingSessions->whereNotNull('career_path')->sortByDesc('date')->first();
            @endphp
            <tr>
                <td class="label">4. Peminatan Karir / Studi Lanjut</td>
                <td class="separator">:</td>
                <td class="value">
                    Rencana: <strong>{{ $latestCareer->career_path === 'KULIAH' ? 'Kuliah / Studi Lanjut' : 'Bekerja / Wirausaha' }}</strong>
                    @if($latestCareer->career_path === 'KULIAH')
                        &bull; Target PTN: <strong>{{ $latestCareer->career_target_university ?? '-' }}</strong>
                        &bull; Jurusan: <strong>{{ $latestCareer->career_target_major ?? '-' }}</strong>
                        &bull; Jalur: {{ $latestCareer->career_admission_track ?? '-' }}
                    @endif
                </td>
            </tr>
            @endif
        </table>

        <!-- ================= TANDA TANGAN & PENGESAHAN DOKUMEN ================= -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td style="width:33%;">
                        Mengetahui,<br>
                        Orang Tua / Wali Siswa<br>
                        <br><br><br><br>
                        <strong><u>{{ $student->family?->father_name ?: ($student->family?->mother_name ?: ($student->father_name ?: '( ...................................... )')) }}</u></strong>
                    </td>
                    <td style="width:34%;">
                        <div class="qr-section">
                            {!! $qrCodeSvg !!}
                            <div style="font-size:7.5pt; color:#444; margin-top:3px;">
                                Scan QR Code untuk Verifikasi Keaslian Lembar Induk Digital SIS-SMA1LE
                            </div>
                        </div>
                    </td>
                    <td style="width:33%;">
                        {{ $school->city ?? 'Sukabumi' }}, {{ now()->translatedFormat('d F Y') }}<br>
                        Kepala Sekolah,<br>
                        <br><br><br><br>
                        <strong><u>{{ $headmaster['name'] ?? ($school->headmaster_name ?? 'Pian Sopian, S.Pd., M.M.Pd.') }}</u></strong><br>
                        <span style="font-size:9pt;">NIP. {{ $headmaster['nip'] ?? ($school->headmaster_nip ?? '197604102000031003') }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-top:12px; font-size:9pt; color:#444;">
                        Dokumen Buku Induk Siswa ini dicetak secara sah melalui Sistem Informasi Sekolah Terpadu (SIS-SMA1LE) &bull; Diverifikasi oleh Tim Bimbingan Konseling (BK) &bull; Koordinator BK: <strong>{{ $bkCoordinator['name'] ?? 'Ida Marttin Maolidah SE, SP.,M.Pd.' }}</strong>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
