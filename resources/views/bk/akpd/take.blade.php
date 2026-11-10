@extends('layouts.app')

@section('title', 'Isi Angket Kebutuhan Peserta Didik (AKPD)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-blue-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl">
        <span class="px-3 py-1 rounded-full bg-white/20 text-xs font-semibold backdrop-blur-sm">Instrumen Asesmen BK Mandiri</span>
        <h1 class="text-2xl font-black mt-2">{{ $akpd->title }}</h1>
        <p class="text-indigo-100 text-xs mt-1">{{ $akpd->description }}</p>

        <div class="mt-4 pt-4 border-t border-white/20 flex flex-wrap items-center justify-between text-xs">
            <div>
                <span class="text-indigo-200">Peserta Didik:</span>
                <span class="font-bold">{{ $student->name ?? 'Siswa' }} ({{ $student->schoolClass?->name ?? 'Kelas' }})</span>
            </div>
            <div>
                <span class="text-indigo-200">Total Butir Pertanyaan:</span>
                <span class="font-bold">{{ $akpd->questions->count() }} Butir</span>
            </div>
        </div>
    </div>

    <!-- Petunjuk Pengisian -->
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-xs text-amber-800 space-y-1">
        <div class="font-bold flex items-center gap-1.5">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Petunjuk Pengisian Angket:
        </div>
        <p>1. Bacalah setiap pernyataan di bawah ini dengan seksama dan jujur sesuai keadaan yang sebenarnya.</p>
        <p>2. Centang (✔) pada kotak di sebelah kiri apabila pernyataan tersebut <strong>pernah atau sedang Anda alami dan Anda membutuhkan bantuan/bimbingan</strong> dari Guru BK.</p>
        <p>3. Tidak ada jawaban benar atau salah. Jawaban Anda bersifat rahasia dan hanya digunakan untuk kepentingan bimbingan.</p>
    </div>

    @php
        $selectedIds = $existing ? ($existing->selected_question_ids ?? []) : [];
    @endphp

    <form action="{{ route('bk.akpd.submit', $akpd) }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="student_id" value="{{ $student->id }}">

        @foreach (['PRIBADI' => 'Bidang Layanan Pribadi', 'SOSIAL' => 'Bidang Layanan Sosial', 'BELAJAR' => 'Bidang Layanan Belajar', 'KARIER' => 'Bidang Layanan Karier'] as $fieldKey => $fieldTitle)
            @php
                $questions = $akpd->questions->where('field_of_service', $fieldKey);
            @endphp
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <span class="w-3 h-3 rounded-full {{ $fieldKey === 'PRIBADI' ? 'bg-rose-500' : ($fieldKey === 'SOSIAL' ? 'bg-amber-500' : ($fieldKey === 'BELAJAR' ? 'bg-blue-500' : 'bg-emerald-500')) }}"></span>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ $fieldTitle }}</h2>
                    <span class="text-xs text-slate-400">({{ $questions->count() }} Butir)</span>
                </div>

                <div class="space-y-2">
                    @foreach ($questions as $q)
                        <label class="flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition cursor-pointer">
                            <input type="checkbox" name="selected_questions[]" value="{{ $q->id }}" {{ in_array($q->id, $selectedIds) ? 'checked' : '' }} class="mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <div class="text-xs">
                                <span class="font-mono font-bold text-slate-400 text-[11px] mr-1">[{{ $q->code }}]</span>
                                <span class="text-slate-800">{{ $q->statement }}</span>
                                @if($q->profile_dimension)
                                    <span class="block text-[10px] text-slate-400 mt-0.5 font-medium">Aspek: {{ $q->profile_dimension }}</span>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('bk.akpd.show', $akpd) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                Batal / Kembali
            </a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer">
                Simpan & Analisis Kebutuhan
            </button>
        </div>
    </form>
</div>
@endsection

