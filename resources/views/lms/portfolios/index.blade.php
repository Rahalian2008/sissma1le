@extends('layouts.app')

@section('title', 'Portofolio Digital Siswa - SMAN 1 Lengkong')

@section('content')
<div class="space-y-6">
    @include('lms.partials.nav')

    <!-- Flash Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-purple-950 to-slate-900 border border-purple-900/40 p-6 sm:p-8 shadow-xl text-white">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>
        <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/20 border border-purple-400/30 text-purple-300 text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                    Modul G • Galeri Karya & Bukti Belajar Holistik
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">
                    Portofolio Digital Siswa
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Etalase karya autentik peserta didik, dokumentasi tugas terbaik, inovasi proyek penguatan profil pelajar Pancasila (P5), dan rekam jejak capaian kompetensi siswa.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button onclick="openModal('modalCreatePortfolio')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-purple-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Unggah Portofolio Karya</span>
                </button>
            </div>
        </div>

        <!-- Quick Stats Matrix -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-slate-400 text-xs font-semibold">Total Portofolio</span>
                <span class="text-xl sm:text-2xl font-black text-white">{{ $stats['total'] }}</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-amber-400 text-xs font-semibold">Karya Unggulan</span>
                <span class="text-xl sm:text-2xl font-black text-amber-300">{{ $stats['featured'] }}</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-emerald-400 text-xs font-semibold">Terverifikasi Guru</span>
                <span class="text-xl sm:text-2xl font-black text-emerald-300">{{ $stats['verified'] }}</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-purple-400 text-xs font-semibold">Proyek P5</span>
                <span class="text-xl sm:text-2xl font-black text-purple-300">{{ $stats['proyek_p5'] }}</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
        <form method="GET" action="{{ route('lms.portfolios.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Cari Karya / Nama Siswa</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kata kunci..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Kategori Karya</label>
                <select name="category" onchange="this.form.submit()" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Kelas & Mapel</label>
                <select name="course_class_id" onchange="this.form.submit()" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
                    <option value="">Semua Kelas</option>
                    @foreach($courseClasses as $cc)
                        <option value="{{ $cc->id }}" {{ $classId == $cc->id ? 'selected' : '' }}>
                            {{ $cc->subject->name ?? 'Mapel' }} — {{ $cc->schoolClass->name ?? 'Kelas' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 self-end pb-1">
                <label class="flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                    <input type="checkbox" name="featured" value="1" {{ $featuredOnly ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-purple-600">
                    <span>Hanya Karya Unggulan ⭐</span>
                </label>
            </div>
        </form>
    </div>

    <!-- Gallery Grid of Portfolios -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($portfolios as $p)
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-xs transition hover:shadow-md flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                            {{ $p->category }}
                        </span>
                        @if($p->is_featured)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 flex items-center gap-1">
                                ⭐ Unggulan
                            </span>
                        @endif
                    </div>

                    <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug line-clamp-2">
                        {{ $p->title }}
                    </h3>

                    <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">
                        {{ $p->description ?: 'Tidak ada deskripsi tambahan.' }}
                    </p>

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                            <span>Siswa:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $p->student->name ?? 'Siswa' }} ({{ $p->student->schoolClass->name ?? '-' }})</span>
                        </div>
                        @if($p->courseClass)
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                <span>Mapel:</span>
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $p->courseClass->subject->name ?? '-' }}</span>
                            </div>
                        @endif
                        @if($p->learningObjective)
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                <span>Tujuan Pembelajaran:</span>
                                <span class="px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 font-mono text-[11px]">
                                    TP {{ $p->learningObjective->tp_code }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        @if($p->file_path)
                            <a href="{{ asset('storage/'.$p->file_path) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition flex items-center gap-1">
                                <span>📎</span>
                                <span>Unduh Berkas</span>
                            </a>
                        @elseif($p->external_url)
                            <a href="{{ $p->external_url }}" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-xs font-bold transition flex items-center gap-1">
                                <span>🔗</span>
                                <span>Tautan Luar</span>
                            </a>
                        @endif
                        <a href="{{ route('lms.portfolios.print', $p) }}" target="_blank" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition" title="Cetak Lembar Portofolio">
                            🖨️
                        </a>
                    </div>

                    <div class="flex items-center gap-1">
                        @if($isTeacher)
                            <form method="POST" action="{{ route('lms.portfolios.verify', $p) }}">
                                @csrf
                                <input type="hidden" name="toggle_featured" value="1">
                                <button type="submit" class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ $p->is_featured ? 'bg-amber-100 text-amber-800 hover:bg-amber-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300' }}" title="Sematkan / Lepas dari Karya Unggulan">
                                    {{ $p->is_featured ? '★ Unggulan' : '☆ Sematkan' }}
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('lms.portfolios.destroy', $p) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus portofolio ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-slate-900 border border-dashed border-slate-300 dark:border-slate-800 rounded-3xl p-12 text-center">
                <span class="text-4xl block mb-2">🎨</span>
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">Belum Ada Portofolio Karya Terunggah</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 mb-4">
                    Etalase digital memuat artefak pembelajaran terbaik siswa, proyek penguatan profil pelajar Pancasila (P5), dan karya inovatif.
                </p>
                <button onclick="openModal('modalCreatePortfolio')" class="px-4 py-2 rounded-xl bg-purple-600 text-white text-xs font-bold shadow-md hover:bg-purple-500 transition">
                    + Unggah Portofolio Perdana
                </button>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $portfolios->links() }}
    </div>
</div>

<!-- Modal Tambah Portofolio -->
<div id="modalCreatePortfolio" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <span>🎨</span>
                <span>Unggah Portofolio Karya Siswa</span>
            </h3>
            <button onclick="closeModal('modalCreatePortfolio')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>

        <form method="POST" action="{{ route('lms.portfolios.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            @if($isTeacher)
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Siswa Pemilik Karya *</label>
                    <select name="student_id" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->schoolClass->name ?? '-' }}) — NISN: {{ $s->nisn }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Karya / Artefak *</label>
                <input type="text" name="title" required placeholder="Contoh: Infografis Gaya Hidup Berkelanjutan Proyek P5" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori Karya *</label>
                    <select name="category" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kaitkan Kelas & Mapel (Opsional)</label>
                    <select name="course_class_id" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                        <option value="">-- Lintas Mapel / Proyek Mandiri --</option>
                        @foreach($courseClasses as $cc)
                            <option value="{{ $cc->id }}">{{ $cc->subject->name ?? 'Mapel' }} — {{ $cc->schoolClass->name ?? 'Kelas' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kaitkan Tujuan Pembelajaran (TP)</label>
                    <select name="learning_objective_id" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                        <option value="">-- Pilih TP yang Relevan --</option>
                        @foreach($objectives as $obj)
                            <option value="{{ $obj->id }}">{{ $obj->tp_code }} : {{ \Illuminate\Support\Str::limit($obj->description, 50) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Karya</label>
                    <input type="date" name="artifact_date" value="{{ date('Y-m-d') }}" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi & Refleksi Karya Siswa</label>
                <textarea name="description" rows="3" placeholder="Ceritakan latar belakang karya, metodologi pembuatan, dan makna hasil belajar yang diperoleh..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Unggah Berkas (PDF, Gambar, Zip, Maks 20MB)</label>
                    <input type="file" name="artifact_file" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Atau Tautan Eksternal (Google Drive / YouTube / GitHub)</label>
                    <input type="url" name="external_url" placeholder="https://..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                </div>
            </div>

            @if($isTeacher)
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_featured" value="1" id="cb_featured" class="rounded text-purple-600">
                    <label for="cb_featured" class="text-xs font-bold text-purple-700 dark:text-purple-400">Sematkan Langsung Sebagai Karya Unggulan ⭐</label>
                </div>
            @endif

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('modalCreatePortfolio')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md transition">
                    Simpan Portofolio
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('hidden');
    }
    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    }
</script>
@endsection

