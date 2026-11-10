@extends('layouts.app')

@section('title', 'Generator Template Surat Resmi')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Generator Template Surat Resmi</h1>
            <p class="text-xs text-slate-500">Penerbitan otomatis naskah dinas resmi sesuai standar Tata Naskah Dinas & Master Referensi.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('master.references.index', ['category' => 'jenis_surat']) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Kelola Referensi Jenis Surat
            </a>
            <a href="{{ route('letters.sppd.index') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Arsip SPPD
            </a>
        </div>
    </div>

    <!-- Generator Form Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <form action="{{ route('letters.generator.generate') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- Step 1: Select Template with Master Referensi Filter -->
            <div class="space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <label class="block font-bold text-slate-900 text-sm">1. Pilih Jenis Template Surat <span class="text-rose-500">*</span></label>
                    <span class="text-[11px] text-slate-500">Pilihan terhubung langsung dengan Master Referensi (Jenis Surat Dinas)</span>
                </div>

                <!-- Category Filter Pills -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1.5 scrollbar-thin">
                    <a href="{{ route('letters.generator.index', ['category' => 'all']) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition {{ $selectedCategory === 'all' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                        Semua Kategori ({{ \App\Models\LetterTemplate::count() }})
                    </a>
                    @foreach ($categories as $cat)
                        @php
                            $count = \App\Models\LetterTemplate::where('category', $cat->name)->count();
                        @endphp
                        <a href="{{ route('letters.generator.index', ['category' => $cat->name]) }}"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition {{ $selectedCategory === $cat->name ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                            {{ $cat->name }} ({{ $count }})
                        </a>
                    @endforeach
                </div>

                <!-- Template Cards Grid -->
                @if ($templates->isEmpty())
                    <div class="p-8 text-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl">
                        <p class="text-sm font-semibold text-slate-600">Belum ada template pada kategori ini.</p>
                        <p class="text-xs text-slate-400 mt-1">Silakan pilih kategori lain atau reset ke "Semua Kategori".</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach ($templates as $t)
                            <label class="relative flex flex-col justify-between p-4 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-blue-500 transition has-checked:border-blue-600 has-checked:bg-blue-50/50 has-checked:shadow-xs">
                                <input type="radio" name="template_id" value="{{ $t->id }}" required class="sr-only" {{ $loop->first ? 'checked' : '' }}>
                                <div>
                                    <div class="flex items-center justify-between gap-1 mb-1.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 truncate">
                                            {{ $t->category ?? 'Surat Dinas' }}
                                        </span>
                                        <span class="text-[10px] font-mono text-slate-400 shrink-0 font-semibold">{{ $t->code }}</span>
                                    </div>
                                    <span class="font-bold text-slate-900 text-xs block leading-snug">{{ $t->title }}</span>
                                    <p class="text-[11px] text-slate-500 mt-1.5 line-clamp-2 leading-normal">{{ $t->description ?? 'Template standar naskah dinas resmi satuan pendidikan.' }}</p>
                                </div>
                                <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                                    <span>Klik untuk memilih</span>
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Step 2: Target Entity (Student or Employee) -->
            <div class="pt-4 border-t border-slate-200">
                <label class="block font-bold text-slate-900 text-sm mb-3">2. Pihak Terkait (Peserta Didik / Pendidik & Tenaga Kependidikan)</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1.5">
                        <label class="block font-bold text-slate-900">Pilih Siswa (Jika Surat Siswa / Keterangan Aktif)</label>
                        <select name="student_id" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs">
                            <option value="">-- Tidak Terkait Siswa / Lewati --</option>
                            @foreach ($students as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} (NIS: {{ $s->nis }} &bull; Kelas {{ $s->current_class_level }} {{ $s->current_rombel }})</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400">Variabel nama_siswa, nis, nisn, kelas, dan alamat akan diinjeksi otomatis.</p>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1.5">
                        <label class="block font-bold text-slate-900">Pilih Guru / Tendik (Jika Surat Tugas / Kepegawaian / SPPD)</label>
                        <select name="employee_id" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs">
                            <option value="">-- Tidak Terkait Pegawai / Lewati --</option>
                            @foreach ($employees as $e)
                                <option value="{{ $e->id }}">{{ $e->formatted_name }} (NIP: {{ $e->nip ?? '-' }} &bull; {{ $e->position }})</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400">Variabel nama_pegawai, nip, pangkat, jabatan akan diisi otomatis.</p>
                    </div>
                </div>
            </div>

            <!-- Step 3: Letter Details & Variables -->
            <div class="pt-4 border-t border-slate-200 space-y-4">
                <label class="block font-bold text-slate-900 text-sm">3. Detail & Konten Surat</label>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor Surat (Otomatis / Kustom)</label>
                        <input type="text" name="letter_number" placeholder="Format otomatis: [Kode]/[No]/SMA1LE/Cadisdik.WIL.V/{{ date('Y') }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-mono text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tempat / Lokasi Tujuan</label>
                        <input type="text" name="destination" value="Dinas Pendidikan Provinsi / Lokasi Kegiatan" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Pelaksanaan</label>
                        <input type="text" name="activity_date" value="{{ now()->translatedFormat('d F Y') }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Keperluan / Pokok Surat <span class="text-rose-500">*</span></label>
                    <input type="text" name="purpose" value="Pengurusan Kelengkapan Administrasi & Rekomendasi Resmi" required placeholder="Contoh: Mengikuti Workshop Peningkatan Kompetensi Guru Informatika / Pengurusan Beasiswa KIP Kuliah" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs">
                </div>
            </div>

            <div class="flex items-center justify-between pt-5 border-t border-slate-100">
                <div class="text-[11px] text-slate-400">
                    Dokumen siap cetak dan terintegrasi dengan tanda tangan Kepala Sekolah & Kop Satuan Pendidikan.
                </div>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Generate & Pratinjau Dokumen
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
