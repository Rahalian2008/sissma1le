@extends('layouts.app')

@section('title', 'Detail Pengaduan ' . $complaint->ticket_number)

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ \Illuminate\Support\Facades\Route::has('complaints.admin.index') ? route('complaints.admin.index') : url('/admin/pengaduan') }}" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-xs font-black text-indigo-600 dark:text-indigo-400">{{ $complaint->ticket_number }}</span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500">{{ $complaint->created_at->translatedFormat('d F Y - H:i') }} WIB</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-0.5">
                    {{ $complaint->title }}
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @php $badge = $complaint->status_badge; @endphp
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border {{ $badge['bg'] }}">
                <span class="w-2 h-2 rounded-full {{ $badge['dot'] }}"></span>
                {{ $badge['label'] }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Rincian Aduan & Bukti -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Profil Pelapor -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                    <span>Identitas Pelapor</span>
                    @if($complaint->is_anonymous)
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">Mode Anonim / Terlindungi</span>
                    @endif
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block">Nama Pelapor:</span>
                        <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $complaint->reporter_name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Status / Kategori:</span>
                        <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $complaint->reporter_type_label }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">No. WhatsApp / HP:</span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="font-bold text-slate-900 dark:text-white text-sm font-mono">{{ $complaint->reporter_phone }}</span>
                            @php
                                $cleanPhone = preg_replace('/[^0-9]/', '', $complaint->reporter_phone);
                                if (str_starts_with($cleanPhone, '0')) {
                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                }
                            @endphp
                            <a href="https://wa.me/{{ $cleanPhone }}?text=Halo%20{{ urlencode($complaint->reporter_name) }},%20terkait%20pengaduan%20Anda%20dengan%20nomor%20tiket%20{{ $complaint->ticket_number }}%20di%20SMAN%201%20Lengkong..." target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-[10px]">
                                <span>Chat WA</span>
                            </a>
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Alamat Email:</span>
                        <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $complaint->reporter_email ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Kronologi Aduan -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                    <span>Uraian & Kronologi Kejadian</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                        {{ $complaint->category_label }}
                    </span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700">
                    <div>
                        <span class="text-slate-400 block">Waktu Kejadian:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $complaint->incident_date ? $complaint->incident_date->translatedFormat('d F Y') : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Lokasi Kejadian:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $complaint->incident_location ?: '-' }}</span>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Deskripsi Lengkap:</span>
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs sm:text-sm text-slate-800 dark:text-slate-200 whitespace-pre-line leading-relaxed">
                        {{ $complaint->description }}
                    </div>
                </div>

                <!-- Lampiran Bukti -->
                @if($complaint->attachment_path)
                    <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Berkas / Foto Bukti Pendukung:</span>
                        @php
                            $ext = strtolower(pathinfo($complaint->attachment_path, PATHINFO_EXTENSION));
                            $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                        @endphp

                        @if($isImg)
                            <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 max-h-96 bg-black/5">
                                <a href="{{ Storage::url($complaint->attachment_path) }}" target="_blank">
                                    <img src="{{ Storage::url($complaint->attachment_path) }}" alt="Bukti Lampiran" class="w-full h-auto object-contain hover:opacity-95 transition">
                                </a>
                            </div>
                        @endif

                        <div class="flex items-center gap-2">
                            <a href="{{ Storage::url($complaint->attachment_path) }}" target="_blank" download class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-xs font-bold text-slate-700 dark:text-slate-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Unduh Berkas Lampiran ({{ $complaint->attachment_name ?: 'Dokumen' }})</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <!-- Kolom Kanan: Form Disposisi & Tanggapan Pimpinan -->
        <div class="space-y-6">

            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4 sticky top-6">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            Disposisi & Tindak Lanjut
                        </h2>
                        <p class="text-[11px] text-slate-400">Kepala Sekolah & Jajaran Wakasek</p>
                    </div>
                </div>

                <form action="{{ route('complaints.admin.disposition', $complaint) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Ubah Status -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Status Penanganan <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white font-bold focus:ring-2 focus:ring-indigo-500">
                            <option value="MENUNGGU" {{ old('status', $complaint->status) === 'MENUNGGU' ? 'selected' : '' }}>⏳ Menunggu Telaah</option>
                            <option value="DITELAAH" {{ old('status', $complaint->status) === 'DITELAAH' ? 'selected' : '' }}>🔍 Sedang Ditelaah Pimpinan</option>
                            <option value="DIPROSES" {{ old('status', $complaint->status) === 'DIPROSES' ? 'selected' : '' }}>⚙️ Dalam Tindak Lanjut / Disposisi</option>
                            <option value="SELESAI" {{ old('status', $complaint->status) === 'SELESAI' ? 'selected' : '' }}>✅ Tuntas / Selesai</option>
                            <option value="DITOLAK" {{ old('status', $complaint->status) === 'DITOLAK' ? 'selected' : '' }}>❌ Ditolak / Tidak Memenuhi Kriteria</option>
                        </select>
                    </div>

                    <!-- Tanggapan Resmi Publik -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                            <span>Tanggapan Resmi untuk Pelapor</span>
                            <span class="text-[10px] text-emerald-600 font-semibold">Tampil di Pelacakan Tiket</span>
                        </label>
                        <textarea name="disposition_notes" rows="4" placeholder="Tuliskan tanggapan resmi sekolah yang dapat dibaca oleh pelapor saat mengecek status tiket..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 leading-relaxed">{{ old('disposition_notes', $complaint->disposition_notes) }}</textarea>
                    </div>

                    <!-- Catatan Internal Pimpinan (Rahasia) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                            <span>Catatan Internal Pimpinan</span>
                            <span class="text-[10px] text-rose-500 font-semibold">Rahasia (Hanya Kepsek & Wakasek)</span>
                        </label>
                        <textarea name="internal_notes" rows="3" placeholder="Catatan teknis internal untuk pimpinan lain (tidak dapat dilihat oleh pelapor)..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 leading-relaxed">{{ old('internal_notes', $complaint->internal_notes) }}</textarea>
                    </div>

                    @if($complaint->handler)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-[11px] text-slate-500 space-y-0.5">
                            <div>Terakhir diproses oleh: <strong class="text-slate-800 dark:text-slate-200">{{ $complaint->handler->name }}</strong></div>
                            <div>Pada: {{ $complaint->handled_at?->translatedFormat('d F Y H:i') }} WIB</div>
                        </div>
                    @endif

                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition">
                        Simpan Disposisi & Update Status
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
