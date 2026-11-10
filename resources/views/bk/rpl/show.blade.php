@extends('layouts.app')

@section('title', 'Detail RPL BK 1 Lembar - ' . $rpl->topic)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('bk.rpl.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; Kembali ke Daftar RPL
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.rpl.edit', $rpl) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                Edit RPL
            </a>
            <a href="{{ route('bk.rpl.print', $rpl) }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Format 1 Lembar A4
            </a>
        </div>
    </div>

    <!-- RPL 1-Sheet Card -->
    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-md space-y-6 text-xs text-slate-700">
        <!-- Title & Metadata -->
        <div class="text-center border-b border-slate-200 pb-4">
            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-blue-100 text-blue-800">
                RENCANA PELAKSANAAN LAYANAN (RPL) BK - 1 LEMBAR
            </span>
            <h1 class="text-lg font-bold text-slate-900 mt-2">{{ $rpl->topic }}</h1>
            <p class="text-xs text-slate-500">Nomor Registrasi Perangkat: <span class="font-mono font-bold">{{ $rpl->rpl_code }}</span></p>
        </div>

        <!-- 1. Identitas Layanan -->
        <div class="grid grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
            <div>
                <p><strong class="text-slate-800">Komponen Layanan:</strong> {{ str_replace('_', ' ', $rpl->service_component) }}</p>
                <p class="mt-1"><strong class="text-slate-800">Jenis Layanan:</strong> Bimbingan {{ $rpl->service_type }}</p>
                <p class="mt-1"><strong class="text-slate-800">Bidang Layanan:</strong> Bidang {{ $rpl->field_of_service }}</p>
            </div>
            <div>
                <p><strong class="text-slate-800">Sasaran Layanan:</strong> {{ $rpl->grade_target }}</p>
                <p class="mt-1"><strong class="text-slate-800">Semester / Waktu:</strong> Semester {{ $rpl->semester }} ({{ $rpl->time_allocation }})</p>
                <p class="mt-1"><strong class="text-slate-800">Profil Pelajar Pancasila:</strong> {{ $rpl->profile_pelajar_pancasila ?? 'Mandiri & Bernalar Kritis' }}</p>
            </div>
        </div>

        <!-- 2. Tujuan & Materi -->
        <div class="space-y-4">
            <div>
                <h3 class="font-bold text-slate-900 uppercase text-[11px] mb-1">A. Capaian & Tujuan Layanan (Learning Objectives)</h3>
                <div class="p-3 bg-slate-50 rounded-xl whitespace-pre-line text-slate-800 leading-relaxed">{{ $rpl->learning_objectives }}</div>
            </div>

            <div>
                <h3 class="font-bold text-slate-900 uppercase text-[11px] mb-1">B. Ringkasan Materi & Metode / Media</h3>
                <div class="p-3 bg-slate-50 rounded-xl space-y-1">
                    <p><strong>Materi:</strong> {{ $rpl->materials_summary ?? '-' }}</p>
                    <p><strong>Metode & Media:</strong> {{ $rpl->method_and_media ?? '-' }}</p>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-slate-900 uppercase text-[11px] mb-1">C. Langkah-langkah Kegiatan Layanan</h3>
                <div class="p-3 bg-slate-50 rounded-xl space-y-2">
                    <p><strong>1. Tahap Pendahuluan (10 Menit):</strong><br>{{ $rpl->step_introduction ?? '-' }}</p>
                    <p><strong>2. Tahap Inti (25 Menit):</strong><br>{{ $rpl->step_core ?? '-' }}</p>
                    <p><strong>3. Tahap Penutup (10 Menit):</strong><br>{{ $rpl->step_closing ?? '-' }}</p>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-slate-900 uppercase text-[11px] mb-1">D. Evaluasi Layanan BK</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="font-bold text-slate-800">Evaluasi Proses:</span>
                        <p class="mt-1 text-slate-600">{{ $rpl->evaluation_process ?? '-' }}</p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="font-bold text-slate-800">Evaluasi Hasil:</span>
                        <p class="mt-1 text-slate-600">{{ $rpl->evaluation_result ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

