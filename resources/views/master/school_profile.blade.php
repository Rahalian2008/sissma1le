@extends('layouts.app')

@section('title', 'Identitas Lengkap Sekolah - SISWA SMA1LE')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-blue-500/20 text-blue-300 border border-blue-500/30 rounded-full text-xs font-bold">
                    MASTER DATA SEKOLAH
                </span>
                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-xs font-bold">
                    Akreditasi {{ $profile['school_accreditation'] }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white mt-2">Identitas Resmi SMA Negeri 1 Lengkong</h1>
            <p class="text-xs text-slate-300 mt-1">Data pokok sekolah, legalitas kelembagaan, nomor statistik nasional, dan pimpinan satuan pendidikan.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('master.classes') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Data Kelas
            </a>
            <a href="{{ route('master.settings') }}" class="px-4 py-2.5 bg-white text-slate-800 hover:bg-slate-100 font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan Sistem
            </a>
        </div>
    </div>

    <!-- Main Identity Card & Form -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <form action="{{ route('master.school_profile.update') }}" method="POST" class="p-6 sm:p-8 space-y-6 text-xs">
            @csrf

            <!-- Section 1: Data Pokok Sekolah -->
            <div>
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3 mb-4">
                    <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">1</span>
                    <h3 class="text-sm font-bold text-slate-800">Legalitas & Data Pokok Sekolah</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Nama Resmi Sekolah <span class="text-rose-500">*</span></label>
                        <input type="text" name="school_name" value="{{ old('school_name', $profile['school_name']) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500 font-bold text-sm">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status Akreditasi <span class="text-rose-500">*</span></label>
                        <input type="text" name="school_accreditation" value="{{ old('school_accreditation', $profile['school_accreditation']) }}" required
                            placeholder="Contoh: A (Unggul)"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500 font-bold text-emerald-700">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">NPSN (Nomor Pokok Sekolah Nasional) <span class="text-rose-500">*</span></label>
                        <input type="text" name="school_npsn" value="{{ old('school_npsn', $profile['school_npsn']) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">NSS (Nomor Statistik Sekolah)</label>
                        <input type="text" name="school_nss" value="{{ old('school_nss', $profile['school_nss']) }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Bentuk Pendidikan <span class="text-rose-500">*</span></label>
                        <input type="text" name="school_education_form" id="school_education_form" list="edu_form_list"
                            value="{{ old('school_education_form', $profile['school_education_form'] ?? 'Sekolah Menengah Atas (SMA)') }}" required
                            placeholder="Pilih atau ketik jenjang pendidikan..."
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500 font-medium text-slate-800">
                        <datalist id="edu_form_list">
                            <option value="Sekolah Menengah Atas (SMA)">
                            <option value="Sekolah Menengah Kejuruan (SMK)">
                            <option value="Sekolah Menengah Pertama (SMP)">
                            <option value="Sekolah Dasar (SD)">
                            <option value="Madrasah Aliyah (MA)">
                            <option value="Madrasah Tsanawiyah (MTs)">
                            <option value="Madrasah Ibtidaiyah (MI)">
                        </datalist>
                        <p class="text-[11px] text-slate-400 mt-1">Pilih jenjang (SMA, SMK, SMP, SD) dari daftar atau ketik bentuk pendidikan secara bebas.</p>
                    </div>
                </div>
            </div>

            <!-- Section 2: Pimpinan Satuan Pendidikan -->
            <div>
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3 mb-4">
                    <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">2</span>
                    <h3 class="text-sm font-bold text-slate-800">Kepala Sekolah & Penanggung Jawab</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Kepala Sekolah (beserta Gelar) <span class="text-rose-500">*</span></label>
                        <input type="text" name="headmaster_name" value="{{ old('headmaster_name', $profile['headmaster_name']) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500 font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">NIP Kepala Sekolah <span class="text-rose-500">*</span></label>
                        <input type="text" name="headmaster_nip" value="{{ old('headmaster_nip', $profile['headmaster_nip']) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500 font-mono">
                    </div>
                </div>
            </div>

            <!-- Section 3: Alamat & Kontak Resmi -->
            <div>
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3 mb-4">
                    <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">3</span>
                    <h3 class="text-sm font-bold text-slate-800">Alamat & Kontak Resmi Lembaga</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Alamat Lengkap <span class="text-rose-500">*</span></label>
                        <textarea name="school_address" rows="2" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500">{{ old('school_address', $profile['school_address']) }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor Telepon / Fax</label>
                            <input type="text" name="school_phone" value="{{ old('school_phone', $profile['school_phone']) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Email Resmi Sekolah <span class="text-rose-500">*</span></label>
                            <input type="email" name="school_email" value="{{ old('school_email', $profile['school_email']) }}" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Situs Web Resmi</label>
                            <input type="url" name="school_website" value="{{ old('school_website', $profile['school_website']) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Action Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-slate-400 text-xs">Identitas ini dicetak pada berkas Dossier Siswa & Laporan Presensi resmi.</span>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-md transition">
                    Simpan Perubahan Identitas Sekolah
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
