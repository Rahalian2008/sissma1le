@extends('layouts.app')

@section('title', 'Kustomisasi Jalur Seleksi SPMB - SMA Negeri 1 Lengkong')

@section('content')
<div class="space-y-6">
    <!-- Header Hero Kustomisasi Jalur SPMB -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-700 via-purple-700 to-slate-900 p-6 sm:p-8 text-white shadow-xl shadow-indigo-500/10">
        <div class="absolute -right-10 -bottom-10 h-64 w-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('spmb.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md text-xs font-semibold text-sky-100 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Kembali ke SPMB Utama</span>
                    </a>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-400/30">
                        <span>✨ Fitur Kustomisasi Jalur</span>
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Kustomisasi & Kelola Jalur Seleksi SPMB</h1>
                <p class="text-indigo-100 text-xs sm:text-sm mt-1.5 max-w-2xl leading-relaxed">
                    Atur nama jalur pendaftaran, alokasi persentase kuota, syarat berkas, serta tetapkan apakah jalur seleksi mewajibkan <strong>Tes Kemampuan Akademik (TKA)</strong> atau non-tes.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('spmb.waves.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-amber-400 hover:bg-amber-300 text-amber-950 font-black text-xs shadow-md transition-all">
                    <span>🗓️ Jadwal Gelombang</span>
                </a>

                <a href="{{ route('spmb.public.register') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/15 hover:bg-white/25 text-white font-bold text-xs backdrop-blur-md border border-white/20 transition-all">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Lihat di Website</span>
                </a>

                <button onclick="document.getElementById('modal-track-create').classList.remove('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black text-xs shadow-lg transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Tambah Jalur Seleksi Baru</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Ringkasan Alokasi Kuota SPMB -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Daya Tampung SMA</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($totalCapacity) }} <span class="text-xs font-normal text-slate-400">Siswa</span></div>
            <div class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold mt-0.5">10 Rombel x 36 Kursi</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Jalur Terdaftar</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ count($tracks) }} <span class="text-xs font-normal text-slate-400">Jalur</span></div>
            <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-0.5">{{ $tracks->where('is_active', true)->count() }} Aktif / Dibuka</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Persentase Kuota</span>
            <div class="text-2xl font-black {{ $totalAllocatedPercentage == 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }} mt-1">
                {{ number_format($totalAllocatedPercentage, 1) }}%
            </div>
            <div class="text-[11px] text-slate-500 font-semibold mt-0.5">
                {{ $totalAllocatedPercentage == 100 ? '✓ Sesuai (Total 100%)' : 'Sisa kuota: ' . (100 - $totalAllocatedPercentage) . '%' }}
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Jalur Wajib Asesmen TKA</span>
            <div class="text-2xl font-black text-amber-500 mt-1">
                {{ $tracks->where('is_tka', true)->count() }} <span class="text-xs font-normal text-slate-400">Jalur</span>
            </div>
            <div class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-0.5">Ujian Literasi & Numerasi</div>
        </div>
    </div>

    <!-- Daftar Jalur Seleksi SPMB -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($tracks as $t)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border {{ $t->is_active ? 'border-slate-200 dark:border-slate-800 shadow-sm' : 'border-dashed border-slate-300 dark:border-slate-700 opacity-70' }} p-5 flex flex-col justify-between transition-all hover:shadow-md">
                <div>
                    <!-- Header Card -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl flex items-center justify-center text-base {{ $t->is_tka ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-600' : 'bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600' }}">
                                {{ $t->is_tka ? '🎯' : '📍' }}
                            </span>
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Kode: {{ $t->code }}</span>
                                <h3 class="font-black text-base text-slate-900 dark:text-white leading-snug">{{ $t->name }}</h3>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-1">
                            @if($t->is_active)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400">
                                    Nonaktif
                                </span>
                            @endif

                            @if($t->is_tka)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 dark:bg-amber-900/60 dark:text-amber-200 border border-amber-300 dark:border-amber-700">
                                    ⚡ Jalur TKA
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Kuota & Kapasitas -->
                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-3.5 mb-3.5 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-600 dark:text-slate-300">Alokasi Kuota</span>
                            <span class="font-black text-indigo-600 dark:text-indigo-400">
                                {{ $t->quota_percentage }}% ({{ $t->getCalculatedSeats($totalCapacity) }} Kursi)
                            </span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full" style="width: {{ min(100, $t->quota_percentage) }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
                            <span>Pendaftar Saat Ini:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $t->registrations_count }} Calon Siswa</span>
                        </div>
                    </div>

                    <!-- Deskripsi Jalur -->
                    @if($t->description)
                        <div class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-3">
                            <p class="line-clamp-2" title="{{ $t->description }}">{{ $t->description }}</p>
                        </div>
                    @endif

                    <!-- Persyaratan Berkas -->
                    @if($t->requirements)
                        <div class="border-t border-slate-100 dark:border-slate-800 pt-2.5 mb-3">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Ketentuan & Syarat Berkas:</span>
                            <pre class="text-[11px] font-sans text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed bg-slate-50/70 dark:bg-slate-800/40 p-2 rounded-xl">{{ $t->requirements }}</pre>
                        </div>
                    @endif
                </div>

                <!-- Action Footer -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2 text-xs">
                    <span class="text-[11px] text-slate-400">Urutan: #{{ $t->sort_order }}</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" 
                                onclick="openEditTrackModal({{ json_encode($t) }})"
                                class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/80 dark:hover:bg-indigo-900 dark:text-indigo-300 font-bold transition cursor-pointer">
                            ✏️ Edit Jalur
                        </button>

                        <form action="{{ route('spmb.tracks.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus atau menonaktifkan jalur {{ $t->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/80 dark:hover:bg-rose-900 dark:text-rose-300 font-bold transition cursor-pointer">
                                🗑️ Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal Tambah Jalur Seleksi Baru -->
<div id="modal-track-create" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white dark:bg-slate-900 w-full max-w-xl rounded-3xl p-6 shadow-2xl border border-slate-200 dark:border-slate-800 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Jalur Pendaftaran SPMB (Custom)</h3>
                <p class="text-xs text-slate-500">Tentukan nama jalur baru, persentase kuota, dan aturan asesmen TKA.</p>
            </div>
            <button onclick="document.getElementById('modal-track-create').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('spmb.tracks.store') }}" method="POST" class="space-y-4 mt-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Jalur Seleksi *</label>
                <input type="text" name="name" placeholder="Contoh: Jalur Tahfidz Al-Qur'an / Jalur Olahraga Berprestasi" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kode / Slug (Opsional)</label>
                    <input type="text" name="code" placeholder="Contoh: tahfidz / prestasi_olahraga" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                    <span class="text-[10px] text-slate-400">Kosongkan untuk pembuatan otomatis</span>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" value="{{ count($tracks) + 1 }}" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alokasi Kuota Persentase (%)</label>
                    <input type="number" step="0.01" name="quota_percentage" placeholder="Contoh: 10.00" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Atau Jumlah Kursi Pasti (Opsional)</label>
                    <input type="number" name="quota_seats" placeholder="Otomatis dihitung jika 0" value="0" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                </div>
            </div>

            <!-- Toggle Wajib TKA -->
            <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 flex items-start gap-3">
                <input type="checkbox" name="is_tka" id="create-is-tka" value="1" class="mt-0.5 rounded text-amber-600 focus:ring-amber-500">
                <label for="create-is-tka" class="cursor-pointer">
                    <span class="font-bold text-amber-900 dark:text-amber-200 block">Wajibkan Ujian TKA (Tes Kemampuan Akademik)</span>
                    <span class="text-[11px] text-amber-700 dark:text-amber-300 leading-tight block mt-0.5">
                        Centang opsi ini jika pendaftar di jalur ini wajib mengikuti asesmen TKA (Literasi, Numerasi, Sains) dan diperingkat melalui skor terbobot.
                    </span>
                </label>
            </div>

            <!-- Deskripsi & Persyaratan -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Singkat Jalur</label>
                <textarea name="description" rows="2" placeholder="Penjelasan umum jalur seleksi ini..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Persyaratan & Ketentuan Berkas</label>
                <textarea name="requirements" rows="3" placeholder="1. Fotokopi Kartu Keluarga&#10;2. Sertifikat/Piagam Asli&#10;3. Nilai Rapor SMP Semester 1-5" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs"></textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="create-is-active" value="1" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                <label for="create-is-active" class="font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Aktifkan dan Buka Pendaftaran untuk Jalur Ini</label>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                <button type="button" onclick="document.getElementById('modal-track-create').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black shadow-md transition cursor-pointer">Simpan Jalur Baru</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Jalur Seleksi -->
<div id="modal-track-edit" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white dark:bg-slate-900 w-full max-w-xl rounded-3xl p-6 shadow-2xl border border-slate-200 dark:border-slate-800 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit & Kustomisasi Jalur SPMB</h3>
                <p class="text-xs text-slate-500">Sesuaikan nama, kuota kursi, dan aturan TKA jalur ini.</p>
            </div>
            <button onclick="document.getElementById('modal-track-edit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-track-edit" method="POST" class="space-y-4 mt-4 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Jalur Seleksi *</label>
                <input type="text" name="name" id="edit-name" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Jalur</label>
                    <input type="text" name="code" id="edit-code" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" id="edit-sort-order" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alokasi Kuota Persentase (%)</label>
                    <input type="number" step="0.01" name="quota_percentage" id="edit-quota-percentage" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jumlah Kursi Pasti (Opsional)</label>
                    <input type="number" name="quota_seats" id="edit-quota-seats" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                </div>
            </div>

            <!-- Toggle Wajib TKA -->
            <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 flex items-start gap-3">
                <input type="checkbox" name="is_tka" id="edit-is-tka" value="1" class="mt-0.5 rounded text-amber-600 focus:ring-amber-500">
                <label for="edit-is-tka" class="cursor-pointer">
                    <span class="font-bold text-amber-900 dark:text-amber-200 block">Wajibkan Ujian TKA (Tes Kemampuan Akademik)</span>
                    <span class="text-[11px] text-amber-700 dark:text-amber-300 leading-tight block mt-0.5">
                        Centang opsi ini jika calon siswa di jalur ini wajib mengikuti asesmen TKA berbasis komputer di sekolah.
                    </span>
                </label>
            </div>

            <!-- Deskripsi & Persyaratan -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Singkat Jalur</label>
                <textarea name="description" id="edit-description" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Persyaratan & Ketentuan Berkas</label>
                <textarea name="requirements" id="edit-requirements" rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs"></textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="edit-is-active" value="1" class="rounded text-indigo-600 focus:ring-indigo-500">
                <label for="edit-is-active" class="font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Status Buka / Aktif</label>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                <button type="button" onclick="document.getElementById('modal-track-edit').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black shadow-md transition cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditTrackModal(track) {
        const form = document.getElementById('form-track-edit');
        form.action = `/spmb/tracks/${track.id}`;

        document.getElementById('edit-name').value = track.name || '';
        document.getElementById('edit-code').value = track.code || '';
        document.getElementById('edit-sort-order').value = track.sort_order || 0;
        document.getElementById('edit-quota-percentage').value = track.quota_percentage || 0;
        document.getElementById('edit-quota-seats').value = track.quota_seats || 0;
        document.getElementById('edit-is-tka').checked = Boolean(track.is_tka);
        document.getElementById('edit-description').value = track.description || '';
        document.getElementById('edit-requirements').value = track.requirements || '';
        document.getElementById('edit-is-active').checked = Boolean(track.is_active);

        document.getElementById('modal-track-edit').classList.remove('hidden');
    }
</script>
@endsection

