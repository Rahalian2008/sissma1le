@props([
    'type',
    'title',
    'modalId' => null,
])

@php
    $mId = $modalId ?? ('modal-import-' . $type);
@endphp

<!-- Tombol Trigger Import CSV -->
<button type="button" onclick="document.getElementById('{{ $mId }}').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
    Import CSV
</button>

<!-- Modal Import CSV -->
<div id="{{ $mId }}" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Import CSV: {{ $title }}</h3>
                    <p class="text-[11px] text-slate-500">Unggah berkas spreadsheet CSV sesuai template</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('{{ $mId }}').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl mb-4 text-emerald-900 text-xs">
            <div class="font-bold flex items-center gap-1 mb-1">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Format Format Kolom
            </div>
            <p class="text-[11px] text-emerald-700 leading-relaxed mb-2.5">
                Pastikan nama kolom pada berkas CSV Anda sesuai dengan standar sistem. Unduh template resmi di bawah ini:
            </p>
            <a href="{{ route('import.template', $type) }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] rounded-lg shadow-xs transition">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Unduh Contoh Template CSV
            </a>
        </div>

        <form action="{{ route('import.process', $type) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Pilih Berkas CSV (.csv) <span class="text-rose-500">*</span></label>
                <input type="file" name="csv_file" accept=".csv, text/csv, application/vnd.ms-excel" required class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-300 rounded-xl cursor-pointer">
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('{{ $mId }}').classList.add('hidden')" class="px-4 py-2 border border-slate-300 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm transition cursor-pointer">
                    Mulai Import
                </button>
            </div>
        </form>
    </div>
</div>
