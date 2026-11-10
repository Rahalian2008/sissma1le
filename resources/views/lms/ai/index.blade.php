@extends('layouts.app')

@section('title', 'Pusat Layanan AI Kurikulum - SMAN 1 Lengkong')

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
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-purple-950 border border-purple-900/40 p-6 sm:p-8 shadow-xl text-white">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>
        <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/20 border border-purple-400/30 text-purple-300 text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Modul G • Kecerdasan Buatan Terpadu & Etika Pedagogis
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white flex items-center gap-3">
                    <span>Pusat Layanan AI Kurikulum</span>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-indigo-500/30 text-indigo-200 border border-indigo-400/30 uppercase">SMA1LE AI Studio</span>
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Asisten cerdas pendidik untuk merumuskan draf Modul Ajar berdiferensiasi, butir asesmen HOTS berkonteks kearifan lokal, diagnosis remedial, serta perumusan indikator Tujuan Pembelajaran.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="bg-slate-800/80 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/60 text-xs space-y-1">
                    <div class="text-slate-400 font-medium">Provider Aktif:</div>
                    <div class="font-extrabold text-emerald-400 flex items-center gap-1.5 uppercase">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span id="providerNameDisplay">{{ $stats['primary_provider'] ?? 'Gemini AI' }}</span>
                        @if($stats['fallback_rate'] > 0)
                            <span class="text-[10px] text-amber-300 font-normal">({{ $stats['fallback_rate'] }}% Fallback)</span>
                        @endif
                    </div>
                </div>

                <button onclick="testAiConnection()" id="btnTestConn" class="px-4 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                    <span>⚡</span>
                    <span id="btnTestConnText">Uji Koneksi AI</span>
                </button>
            </div>
        </div>

        <!-- Quick Stats Matrix -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-slate-400 text-xs font-semibold">Total Permintaan AI</span>
                <span class="text-xl sm:text-2xl font-black text-white">{{ $stats['total_requests'] }} Draf</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-purple-400 text-xs font-semibold">Rata-rata Waktu Respon</span>
                <span class="text-xl sm:text-2xl font-black text-purple-300">{{ $stats['avg_response_time'] }} ms</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-emerald-400 text-xs font-semibold">Tingkat Ketahanan</span>
                <span class="text-xl sm:text-2xl font-black text-emerald-300">100% Siap</span>
            </div>
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-3 border border-slate-700/50">
                <span class="block text-amber-400 text-xs font-semibold">Proteksi Privasi PII</span>
                <span class="text-xl sm:text-2xl font-black text-amber-300">Aktif & Aman</span>
            </div>
        </div>
    </div>

    <!-- Alert Etika AI & Penegasan Status Draf -->
    <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/80 flex items-start gap-3">
        <span class="text-2xl shrink-0">🛡️</span>
        <div class="text-xs text-indigo-950 dark:text-indigo-200 leading-relaxed">
            <strong class="font-bold">Prinsip Etika & Keamanan AI SMA1LE:</strong>
            Seluruh luaran yang dihasilkan asisten kecerdasan buatan berstatus <strong>DRAF AWAL REKOMENDASI</strong>. Pendidik wajib meninjau, mengadaptasi, dan menyelaraskan konten dengan karakteristik siswa di kelas. Sistem secara otomatis menyaring NIK, NISN, dan nomor kontak siswa untuk menjamin privasi data pendidikan.
        </div>
    </div>

    <!-- Three Main AI Studio Tools -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Tool 1: HOTS Generator Lokal -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-xl">
                    🎯
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                    Generator Soal HOTS Kontekstual Lokal
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Menghasilkan butir soal penalaran (L3) dengan stimulus fenomena nyata di Sukabumi / Jawa Barat (Geopark Ciletuh, agrikultur, kearifan lokal).
                </p>

                <div class="space-y-2.5 pt-2">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Topik Materi *</label>
                        <input type="text" id="tool1_topic" placeholder="Contoh: Ekologi & Keseimbangan Lingkungan" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Kelas</label>
                            <select id="tool1_grade" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
                                <option value="X">Kelas X</option>
                                <option value="XI">Kelas XI</option>
                                <option value="XII">Kelas XII</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Konteks Daerah</label>
                            <input type="text" id="tool1_context" value="Sukabumi / Jawa Barat" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="button" onclick="executeTool1()" id="btnTool1" class="w-full py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                    <span>✨</span>
                    <span id="btnTool1Text">Generate Soal HOTS Lokal</span>
                </button>
            </div>
        </div>

        <!-- Tool 2: Modul Ajar Berdiferensiasi -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center text-xl">
                    📝
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                    Modul Ajar Berdiferensiasi
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Menyusun skema 3 dimensi diferensiasi: Konten (Visual/Auditori/Kinestetik), Proses (Tiering aktivitas), dan Produk hasil belajar siswa.
                </p>

                <div class="space-y-2.5 pt-2">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Topik Pembelajaran *</label>
                        <input type="text" id="tool2_topic" placeholder="Contoh: Dinamika Gerak dan Hukum Newton" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Model Pembelajaran</label>
                            <select id="tool2_model" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
                                <option value="Problem Based Learning (PBL)">PBL</option>
                                <option value="Project Based Learning (PjBL)">PjBL</option>
                                <option value="Discovery Learning">Discovery</option>
                                <option value="Inquiry Learning">Inquiry</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Tingkat Kelas</label>
                            <select id="tool2_grade" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
                                <option value="X">Kelas X</option>
                                <option value="XI">Kelas XI</option>
                                <option value="XII">Kelas XII</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="button" onclick="executeTool2()" id="btnTool2" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                    <span>✨</span>
                    <span id="btnTool2Text">Generate Draf Diferensiasi</span>
                </button>
            </div>
        </div>

        <!-- Tool 3: Diagnostik Remedial -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-xl">
                    🌱
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                    Analisis Diagnostik & Remedial
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Menganalisis akar penyebab kesulitan belajar pada Tujuan Pembelajaran (TP) tertentu dan merekomendasikan intervensi pedagogis terfokus.
                </p>

                <div class="space-y-2.5 pt-2">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Tujuan Pembelajaran (TP) *</label>
                        <input type="text" id="tool3_tp" placeholder="Contoh: Menganalisis Reaksi Redoks dan Penyetaraan" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Aspek yang Menjadi Hambatan Siswa</label>
                        <input type="text" id="tool3_aspects" placeholder="Penentuan bilangan oksidasi dan setengah reaksi" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="button" onclick="executeTool3()" id="btnTool3" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                    <span>✨</span>
                    <span id="btnTool3Text">Analisis Diagnostik Remedial</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Output Modal / Viewer Panel -->
    <div id="aiOutputModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[85vh] flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2" id="outputModalTitle">
                    <span>✨</span>
                    <span>Hasil Draf Asisten AI SMA1LE</span>
                </h3>
                <button onclick="closeModal('aiOutputModal')" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <div class="overflow-y-auto p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-xs text-slate-800 dark:text-slate-200 whitespace-pre-line leading-relaxed font-mono" id="outputModalContent">
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                <div class="text-[11px] text-slate-500">
                    💡 Anda dapat menyalin teks ini untuk dimasukkan ke Modul Ajar atau Bank Soal.
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="copyOutputContent()" id="btnCopyOutput" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 transition">
                        📋 Salin Teks
                    </button>
                    <button type="button" onclick="closeModal('aiOutputModal')" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition">
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Riwayat Audit Penggunaan AI (ai_generation_logs) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📜</span>
                    <span>Log Audit & Riwayat Penggunaan Layanan AI</span>
                </h3>
                <p class="text-xs text-slate-500">Transparansi, tata kelola token, dan akuntabilitas pemanfaatan AI di SMAN 1 Lengkong.</p>
            </div>

            <!-- Filter Task Type -->
            <form method="GET" action="{{ route('lms.ai.index') }}" class="flex items-center gap-2">
                <select name="task_type" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white">
                    <option value="">Semua Kategori Tugas AI</option>
                    <option value="tp" {{ $selectedTask === 'tp' ? 'selected' : '' }}>Tujuan Pembelajaran (TP)</option>
                    <option value="modul_ajar" {{ $selectedTask === 'modul_ajar' ? 'selected' : '' }}>Modul Ajar</option>
                    <option value="hots_question" {{ $selectedTask === 'hots_question' ? 'selected' : '' }}>Soal HOTS</option>
                    <option value="diferensiasi" {{ $selectedTask === 'diferensiasi' ? 'selected' : '' }}>Diferensiasi</option>
                    <option value="diagnostik" {{ $selectedTask === 'diagnostik' ? 'selected' : '' }}>Diagnostik Remedial</option>
                    <option value="refleksi" {{ $selectedTask === 'refleksi' ? 'selected' : '' }}>Refleksi KBM</option>
                </select>
                @if($selectedTask)
                    <a href="{{ route('lms.ai.index') }}" class="px-2.5 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/30">
                        <th class="py-3 px-3">Waktu</th>
                        <th class="py-3 px-3">Pendidik</th>
                        <th class="py-3 px-3">Kategori Tugas</th>
                        <th class="py-3 px-3">Provider / Model</th>
                        <th class="py-3 px-3">Ringkasan Konteks</th>
                        <th class="py-3 px-2 text-center">Durasi</th>
                        <th class="py-3 px-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3 px-3 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                {{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-800 dark:text-slate-200">
                                {{ $log->user->name ?? 'Sistem / Guru' }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                    {{ $log->task_type }}
                                </span>
                            </td>
                            <td class="py-3 px-3 font-mono text-[11px] text-slate-600 dark:text-slate-400">
                                {{ $log->provider }} ({{ $log->model }})
                                @if($log->is_fallback)
                                    <span class="text-amber-500 font-bold text-[10px]">[Fallback]</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-600 dark:text-slate-400 max-w-xs truncate" title="{{ $log->prompt_summary }}">
                                {{ $log->prompt_summary ?: '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono text-slate-700 dark:text-slate-300">
                                {{ $log->response_time_ms }} ms
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $log->status === 'SUCCESS' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">
                                Belum ada riwayat aktivitas log generasi AI.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
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

    function showOutputModal(title, content) {
        document.getElementById('outputModalTitle').innerHTML = `<span>✨</span> <span>${title}</span>`;
        document.getElementById('outputModalContent').innerText = content;
        openModal('aiOutputModal');
    }

    function copyOutputContent() {
        const text = document.getElementById('outputModalContent').innerText;
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById('btnCopyOutput');
            btn.innerText = '✅ Tersalin!';
            setTimeout(() => { btn.innerText = '📋 Salin Teks'; }, 2000);
        });
    }

    function testAiConnection() {
        const btn = document.getElementById('btnTestConn');
        const btnText = document.getElementById('btnTestConnText');
        btn.disabled = true;
        btnText.innerText = 'Menguji...';

        fetch("{{ route('lms.ai.test_connection') }}")
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btnText.innerText = `Terhubung (${data.latency_ms} ms)`;
            alert(`Koneksi AI Normal!\nProvider: ${data.provider}\nLatensi: ${data.latency_ms} ms\nStatus: Aktif`);
        })
        .catch(err => {
            btn.disabled = false;
            btnText.innerText = 'Uji Koneksi AI';
            alert('Gagal menguji koneksi AI.');
        });
    }

    function executeTool1() {
        const topic = document.getElementById('tool1_topic').value;
        const grade = document.getElementById('tool1_grade').value;
        const context = document.getElementById('tool1_context').value;

        if (!topic) {
            alert('Silakan masukkan topik materi.');
            return;
        }

        const btn = document.getElementById('btnTool1');
        const btnText = document.getElementById('btnTool1Text');
        btn.disabled = true;
        btnText.innerText = 'Menyusun soal HOTS...';

        fetch("{{ route('lms.ai.generate_hots') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                topic: topic,
                grade: grade,
                local_context: context
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btnText.innerText = 'Generate Soal HOTS Lokal';
            if (data.content) {
                showOutputModal(`Rekomendasi Soal HOTS — ${topic}`, data.content);
            } else {
                alert('Gagal menghasilkan soal.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btnText.innerText = 'Generate Soal HOTS Lokal';
            alert('Terjadi kesalahan jaringan.');
        });
    }

    function executeTool2() {
        const topic = document.getElementById('tool2_topic').value;
        const model = document.getElementById('tool2_model').value;
        const grade = document.getElementById('tool2_grade').value;

        if (!topic) {
            alert('Silakan masukkan topik pembelajaran.');
            return;
        }

        const btn = document.getElementById('btnTool2');
        const btnText = document.getElementById('btnTool2Text');
        btn.disabled = true;
        btnText.innerText = 'Menyusun diferensiasi...';

        fetch("{{ route('lms.ai.generate_differentiated') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                topic: topic,
                grade: grade,
                learning_model: model
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btnText.innerText = 'Generate Draf Diferensiasi';
            if (data.content) {
                showOutputModal(`Draf Modul Ajar Berdiferensiasi — ${topic}`, data.content);
            } else {
                alert('Gagal menghasilkan diferensiasi.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btnText.innerText = 'Generate Draf Diferensiasi';
            alert('Terjadi kesalahan jaringan.');
        });
    }

    function executeTool3() {
        const tp = document.getElementById('tool3_tp').value;
        const aspects = document.getElementById('tool3_aspects').value;

        if (!tp) {
            alert('Silakan masukkan Tujuan Pembelajaran (TP).');
            return;
        }

        const btn = document.getElementById('btnTool3');
        const btnText = document.getElementById('btnTool3Text');
        btn.disabled = true;
        btnText.innerText = 'Menganalisis kesulitan...';

        fetch("{{ route('lms.ai.analyze_diagnostic') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tp_title: tp,
                failed_aspects: aspects ? [aspects] : []
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btnText.innerText = 'Analisis Diagnostik Remedial';
            if (data.content) {
                showOutputModal(`Analisis Diagnostik & Intervensi — ${tp}`, data.content);
            } else {
                alert('Gagal menganalisis kesulitan belajar.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btnText.innerText = 'Analisis Diagnostik Remedial';
            alert('Terjadi kesalahan jaringan.');
        });
    }
</script>
@endsection

