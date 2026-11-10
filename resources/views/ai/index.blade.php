@extends('layouts.app')

@section('title', 'SIKATAS AI Executive Analytics')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <span class="p-1.5 rounded-xl bg-linear-to-tr from-indigo-500 to-purple-600 text-white shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </span>
                SIKATAS AI Executive Analytics
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Asisten kecerdasan buatan terpadu untuk analisis tata kelola manajerial sekolah berbasis data riil.</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('modal-ai-settings').classList.remove('hidden')" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                Pengaturan Provider AI
            </button>
        </div>
    </div>

    <!-- Active Engine Status Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-purple-900 rounded-3xl p-6 sm:p-8 text-white shadow-md relative overflow-hidden border border-indigo-700/50">
        <div class="relative z-10 max-w-2xl space-y-3">
            <div class="flex flex-wrap items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-semibold text-indigo-200 border border-white/15">
                    <span class="w-2.5 h-2.5 rounded-full {{ !empty($setting->getEffectiveApiKey()) ? 'bg-emerald-400' : 'bg-amber-400' }} animate-pulse"></span>
                    <span>Engine: Google Gemini ({{ $setting->model ?: 'gemini-3.5-flash-lite' }})</span>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ !empty($setting->getEffectiveApiKey()) ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                    {{ !empty($setting->getEffectiveApiKey()) ? '● Mode Pembacaan Selalu Online Aktif' : '○ Siap Online (Menunggu API Key)' }}
                </span>
            </div>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight">Kajian Analitik Eksekutif Google Gemini</h2>
            <p class="text-xs text-indigo-100 leading-relaxed">
                SIKATAS AI membaca data riil secara online langsung dari database operasional sekolah (kepegawaian, kesiswaan, keuangan, sarpras, persuratan). Setiap kajian memuat 7 pilar: Ringkasan Eksekutif, Temuan Kunci, Kesimpulan, Permasalahan Kritis, Rekomendasi Solutif, Rencana Tindak Lanjut, dan Metrik Data Pendukung.
            </p>
        </div>
    </div>

    <!-- Generator Module Picker -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-4">
        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            Pilih Modul Analisis
        </h2>

        <form action="{{ route('ai.generate') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <label class="relative flex flex-col p-4 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-indigo-500 transition has-checked:border-indigo-600 has-checked:bg-indigo-50/50">
                    <input type="radio" name="module" value="executive_headmaster" checked class="sr-only">
                    <span class="text-xl mb-1">👑</span>
                    <span class="font-bold text-slate-900 text-sm">Eksekutif Kepala Sekolah</span>
                    <p class="text-[11px] text-slate-500 mt-1">Sintesis menyeluruh seluruh pilar tata usaha sekolah untuk rapat pimpinan.</p>
                </label>

                <label class="relative flex flex-col p-4 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-indigo-500 transition has-checked:border-indigo-600 has-checked:bg-indigo-50/50">
                    <input type="radio" name="module" value="kesiswaan" class="sr-only">
                    <span class="text-xl mb-1">🎓</span>
                    <span class="font-bold text-slate-900 text-sm">Kesiswaan & Kedisiplinan</span>
                    <p class="text-[11px] text-slate-500 mt-1">Evaluasi tren poin pelanggaran tata tertib, capaian prestasi lomba, dan beasiswa.</p>
                </label>

                <label class="relative flex flex-col p-4 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-indigo-500 transition has-checked:border-indigo-600 has-checked:bg-indigo-50/50">
                    <input type="radio" name="module" value="kepegawaian" class="sr-only">
                    <span class="text-xl mb-1">👔</span>
                    <span class="font-bold text-slate-900 text-sm">Kepegawaian & PTK</span>
                    <p class="text-[11px] text-slate-500 mt-1">Analisis rasio guru PNS vs Honorer, pemenuhan sertifikasi, dan kenaikan pangkat.</p>
                </label>

                <label class="relative flex flex-col p-4 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-indigo-500 transition has-checked:border-indigo-600 has-checked:bg-indigo-50/50">
                    <input type="radio" name="module" value="keuangan" class="sr-only">
                    <span class="text-xl mb-1">💰</span>
                    <span class="font-bold text-slate-900 text-sm">Keuangan & BKU BOSP</span>
                    <p class="text-[11px] text-slate-500 mt-1">Laju penyerapan anggaran per triwulan, efisiensi belanja, dan kepatuhan pajak.</p>
                </label>

                <label class="relative flex flex-col p-4 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-indigo-500 transition has-checked:border-indigo-600 has-checked:bg-indigo-50/50">
                    <input type="radio" name="module" value="sarpras" class="sr-only">
                    <span class="text-xl mb-1">🏢</span>
                    <span class="font-bold text-slate-900 text-sm">Sarpras & KIB-KIR (SIPD e-BMD)</span>
                    <p class="text-[11px] text-slate-500 mt-1">Kelaikan aset KIB A-F, penataan ruangan KIR, dan kepatuhan kode SIPD e-BMD.</p>
                </label>

                <label class="relative flex flex-col p-4 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-indigo-500 transition has-checked:border-indigo-600 has-checked:bg-indigo-50/50">
                    <input type="radio" name="module" value="tamu" class="sr-only">
                    <span class="text-xl mb-1">🤝</span>
                    <span class="font-bold text-slate-900 text-sm">Buku Tamu & Pelayanan Publik</span>
                    <p class="text-[11px] text-slate-500 mt-1">Lalu lintas kunjungan, verifikasi selfie resepsionis, dan kepatuhan check-out.</p>
                </label>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-slate-500 font-semibold">Periode Data:</span>
                    <input type="text" name="period" value="Tahun Ajaran {{ date('Y') }}/{{ date('Y')+1 }}" class="px-3 py-1.5 border border-slate-300 rounded-xl bg-slate-50">
                </div>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm transition cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Jalankan Analisis Cerdas
                </button>
            </div>
        </form>
    </div>

    <!-- History of AI Reports -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h2 class="text-sm font-bold text-slate-900">Riwayat Dokumen Kajian AI</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">ID & Tanggal Laporan</th>
                        <th class="px-5 py-3.5">Modul Analisis</th>
                        <th class="px-5 py-3.5">Periode Data</th>
                        <th class="px-5 py-3.5">Ringkasan Eksekutif</th>
                        <th class="px-5 py-3.5">Petugas Pembuat</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reports as $rep)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5">
                                <span class="font-mono font-bold text-indigo-700">#AI-{{ str_pad($rep->id, 4, '0', STR_PAD_LEFT) }}</span>
                                <div class="text-[11px] text-slate-400">{{ $rep->created_at->format('d M Y H:i') }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800">
                                    {{ ucwords(str_replace('_', ' ', $rep->module)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">
                                {{ $rep->period }}
                            </td>
                            <td class="px-5 py-3.5 max-w-sm">
                                <p class="text-slate-600 line-clamp-2 leading-relaxed">{{ $rep->executive_summary }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $rep->creator?->name ?? 'Sistem AI' }}
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('ai.show', $rep) }}" class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg transition border border-blue-200">
                                    Buka Kajian
                                </a>
                                <a href="{{ route('ai.print', $rep) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition border border-slate-200">
                                    Cetak
                                </a>
                                <form action="{{ route('ai.destroy', $rep) }}" method="POST" onsubmit="return confirm('Hapus dokumen kajian AI ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg transition border border-rose-200 cursor-pointer" title="Hapus Laporan">
                                        <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                Belum ada riwayat dokumen kajian AI. Pilih modul di atas untuk menjalankan analisis pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($reports->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Pengaturan Provider AI -->
<div id="modal-ai-settings" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-xl w-full shadow-2xl border border-slate-200 my-8">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Pengaturan Mesin AI Google Gemini</h2>
                    <p class="text-[11px] text-slate-500">Konfigurasi AI berbasis cloud untuk analisis online real-time.</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('modal-ai-settings').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition cursor-pointer">
                ✕
            </button>
        </div>

        <form action="{{ route('ai.settings.update') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-semibold text-slate-700 mb-1">AI Provider Engine <span class="text-rose-500">*</span></label>
                <select name="provider" id="ai-provider-select" onchange="toggleAiProviderFields(this.value)" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl font-bold bg-white text-slate-800 focus:ring-2 focus:ring-blue-500">
                    <option value="gemini" {{ $setting->provider === 'gemini' ? 'selected' : '' }}>Google Gemini (Rekomendasi - Online & Akurat)</option>
                    <option value="openai" {{ $setting->provider === 'openai' ? 'selected' : '' }}>OpenAI (GPT-4o / GPT-4o-mini)</option>
                    <option value="local_heuristic" {{ $setting->provider === 'local_heuristic' ? 'selected' : '' }}>Internal Heuristic Grounding (Offline Fallback)</option>
                    <option value="custom" {{ $setting->provider === 'custom' ? 'selected' : '' }}>Custom Self-Hosted LLM (Ollama / LocalAI)</option>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Model Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="model" id="ai-model-input" value="{{ $setting->model ?: 'gemini-3.5-flash-lite' }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono text-xs focus:ring-2 focus:ring-blue-500">
                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                        <button type="button" onclick="document.getElementById('ai-model-input').value='gemini-3.5-flash-lite'" class="px-2 py-0.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded text-[10px] font-semibold border border-blue-200">3.5 Flash Lite (Cepat)</button>
                        <button type="button" onclick="document.getElementById('ai-model-input').value='gemini-3.5-flash'" class="px-2 py-0.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded text-[10px] font-semibold border border-indigo-200">3.5 Flash</button>
                        <button type="button" onclick="document.getElementById('ai-model-input').value='gemini-flash-latest'" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[10px] font-semibold border border-slate-200">Flash Latest</button>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Temperature (Kreativitas) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" name="temperature" value="{{ $setting->temperature }}" min="0" max="2" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                    <p class="text-[10px] text-slate-400 mt-1">Nilai 0.3 - 0.7 sangat dianjurkan untuk analisis faktual.</p>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="font-semibold text-slate-700">Google Gemini API Key</label>
                    <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-[11px] font-semibold text-blue-600 hover:underline inline-flex items-center gap-1">
                        Dapatkan API Key Gratis ↗
                    </a>
                </div>
                <div class="relative">
                    <input type="password" id="ai-api-key" name="api_key" value="{{ $setting->api_key }}" placeholder="AIzaSy... (Atau kosongkan jika sudah ada di .env)" class="w-full pl-3 pr-10 py-2 border border-slate-300 rounded-xl font-mono text-xs focus:ring-2 focus:ring-blue-500">
                    <button type="button" onclick="toggleApiKeyVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                        👁
                    </button>
                </div>
                @if(!empty(config('services.gemini.api_key')) || !empty(env('GEMINI_API_KEY')))
                    <p class="text-[10px] text-emerald-600 mt-1 font-medium">✓ Terdeteksi GEMINI_API_KEY pada server environment (.env). Kolom ini dapat dikosongkan.</p>
                @else
                    <p class="text-[10px] text-slate-500 mt-1">Dapatkan API Key dari Google AI Studio secara gratis tanpa kartu kredit.</p>
                @endif
            </div>

            <!-- Uji Koneksi Live Button -->
            <div class="p-3 bg-blue-50/70 border border-blue-200/80 rounded-2xl space-y-2">
                <div class="flex items-center justify-between">
                    <div class="font-bold text-blue-900 text-xs flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>Uji Konektivitas Online</span>
                    </div>
                    <button type="button" id="btn-test-connection" onclick="testGeminiOnlineConnection()" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-lg text-[11px] font-bold shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span>Uji Koneksi Online</span>
                    </button>
                </div>
                <div id="test-connection-result" class="hidden text-[11px] p-2.5 rounded-xl"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Max Output Tokens <span class="text-rose-500">*</span></label>
                    <input type="number" name="max_tokens" value="{{ $setting->max_tokens }}" min="256" max="8192" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">API Endpoint Khusus (Opsional)</label>
                    <input type="text" name="api_endpoint" value="{{ $setting->api_endpoint }}" placeholder="Default: Google Gemini API resmi" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono text-xs">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">System Instruction (Instruksi Dasar AI)</label>
                <textarea name="system_prompt" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">{{ $setting->system_prompt }}</textarea>
            </div>

            <div class="pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                    <input type="checkbox" name="is_active" value="1" {{ $setting->is_active ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                    <span>Aktifkan Fitur Analisis AI SIKATAS</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-ai-settings').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer shadow-xs">Simpan Konfigurasi</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleApiKeyVisibility() {
    const input = document.getElementById('ai-api-key');
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}

function toggleAiProviderFields(provider) {
    const modelInput = document.getElementById('ai-model-input');
    if (provider === 'gemini') {
        if (!modelInput.value || modelInput.value.includes('gpt') || modelInput.value.includes('heuristic') || modelInput.value.includes('1.5')) {
            modelInput.value = 'gemini-3.5-flash-lite';
        }
    } else if (provider === 'openai') {
        if (!modelInput.value || modelInput.value.includes('gemini')) {
            modelInput.value = 'gpt-4o-mini';
        }
    }
}

function testGeminiOnlineConnection() {
    const btn = document.getElementById('btn-test-connection');
    const resultBox = document.getElementById('test-connection-result');
    const apiKey = document.getElementById('ai-api-key').value;
    const model = document.getElementById('ai-model-input').value;

    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Menguji Koneksi...</span>
    `;

    resultBox.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-800', 'border-emerald-200', 'bg-rose-50', 'text-rose-800', 'border-rose-200');
    resultBox.classList.add('bg-slate-100', 'text-slate-600', 'border', 'border-slate-200');
    resultBox.innerHTML = 'Menghubungi endpoint resmi Google Gemini online...';

    fetch('{{ route('ai.test-connection') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            api_key: apiKey,
            model: model
        })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = `
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <span>Uji Koneksi Ulang</span>
        `;
        resultBox.classList.remove('bg-slate-100', 'text-slate-600', 'border-slate-200');

        if (data.success) {
            resultBox.classList.add('bg-emerald-50', 'text-emerald-800', 'border', 'border-emerald-200');
            resultBox.innerHTML = `<strong>✓ Online Berhasil:</strong> ${data.message}`;
        } else {
            resultBox.classList.add('bg-rose-50', 'text-rose-800', 'border', 'border-rose-200');
            resultBox.innerHTML = `<strong>✕ Koneksi Gagal:</strong> ${data.message}`;
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = `
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <span>Uji Koneksi Ulang</span>
        `;
        resultBox.classList.remove('bg-slate-100', 'text-slate-600', 'border-slate-200');
        resultBox.classList.add('bg-rose-50', 'text-rose-800', 'border', 'border-rose-200');
        resultBox.innerHTML = `<strong>✕ Error:</strong> Gagal terhubung ke server (${err.message})`;
    });
}
</script>
@endpush
@endsection
