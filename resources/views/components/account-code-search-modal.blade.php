{{-- Modal Pencarian Katalog Kode Rekening Keuangan Sekolah (BOSP / SIPD / BAS Kemendikbud) --}}
@props([
    'modalId' => 'account-code-modal',
    'targetCodeId' => 'field-account-code',
    'targetProgramId' => 'field-program-name',
    'targetActivityId' => 'field-activity-name',
])

<div id="{{ $modalId }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeAccountCodeModal('{{ $modalId }}')"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-4xl border border-slate-200">
            <!-- Modal Header -->
            <div class="p-5 bg-gradient-to-r from-emerald-700 via-teal-700 to-emerald-800 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold tracking-tight">Katalog Kode Rekening, Program & Kegiatan RKAS</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-400/20 text-emerald-200 border border-emerald-400/30">BOSP & SIPD Jabar</span>
                        </div>
                        <p class="text-xs text-emerald-100/90 mt-0.5">Klasifikasi 8 Standar Nasional Pendidikan (SNP) & Bagan Akun Standar (BAS) Keuangan Sekolah</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="useManualAccountCodeEntry('{{ $modalId }}')" class="px-3 py-1.5 bg-white/15 hover:bg-white/25 text-white text-xs font-semibold rounded-xl border border-white/20 transition cursor-pointer flex items-center gap-1.5" title="Ketik kode dan kegiatan manual jika tidak ada di katalog">
                        <span>✍️ Isi Manual</span>
                    </button>
                    <button type="button" onclick="closeAccountCodeModal('{{ $modalId }}')" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Modal Search & Filters -->
            <div class="p-4 bg-slate-50 border-b border-slate-200 space-y-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="{{ $modalId }}-search-input" placeholder="Cari Kode Rekening (cth: 5.1.02...), Nama Program, atau Uraian Kegiatan (cth: ATK, modul, lab, daya & jasa)..." class="w-full pl-10 pr-24 py-2.5 bg-white border border-slate-300 rounded-2xl text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                    <button type="button" id="{{ $modalId }}-btn-clear" onclick="clearAccountCodeSearch('{{ $modalId }}')" class="absolute inset-y-0 right-2 my-auto px-2 py-1 text-[11px] font-semibold text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 hidden">
                        Bersihkan (✕)
                    </button>
                </div>

                <!-- Tabs 8 Standar Nasional Pendidikan (SNP) -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs font-semibold custom-scroll">
                    <button type="button" class="tab-snp px-3 py-1.5 rounded-xl bg-emerald-700 text-white shadow-2xs transition whitespace-nowrap cursor-pointer" data-category="ALL" onclick="filterAccountCodeCategory('ALL', this, '{{ $modalId }}')">
                        Semua Standar (8 SNP)
                    </button>
                    <button type="button" class="tab-snp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-category="SKL" onclick="filterAccountCodeCategory('SKL', this, '{{ $modalId }}')">
                        1. SKL
                    </button>
                    <button type="button" class="tab-snp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-category="Standar Isi" onclick="filterAccountCodeCategory('Standar Isi', this, '{{ $modalId }}')">
                        2. Standar Isi
                    </button>
                    <button type="button" class="tab-snp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-category="Standar Proses" onclick="filterAccountCodeCategory('Standar Proses', this, '{{ $modalId }}')">
                        3. Standar Proses
                    </button>
                    <button type="button" class="tab-snp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-category="Standar Penilaian" onclick="filterAccountCodeCategory('Standar Penilaian', this, '{{ $modalId }}')">
                        4. Standar Penilaian
                    </button>
                    <button type="button" class="tab-snp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-category="Standar PTK" onclick="filterAccountCodeCategory('Standar PTK', this, '{{ $modalId }}')">
                        5. Standar PTK
                    </button>
                    <button type="button" class="tab-snp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-category="Standar Sarpras" onclick="filterAccountCodeCategory('Standar Sarpras', this, '{{ $modalId }}')">
                        6. Standar Sarpras
                    </button>
                    <button type="button" class="tab-snp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-category="Standar Pengelolaan" onclick="filterAccountCodeCategory('Standar Pengelolaan', this, '{{ $modalId }}')">
                        7. Standar Pengelolaan
                    </button>
                    <button type="button" class="tab-snp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-category="Standar Pembiayaan" onclick="filterAccountCodeCategory('Standar Pembiayaan', this, '{{ $modalId }}')">
                        8. Standar Pembiayaan
                    </button>
                </div>
            </div>

            <!-- Results Counter & Info -->
            <div class="px-5 py-2 bg-slate-100/70 border-b border-slate-200 flex items-center justify-between text-[11px] text-slate-600">
                <div>
                    Ditemukan <span class="font-bold text-slate-900" id="{{ $modalId }}-total-count">0</span> mata rekening kegiatan
                </div>
                <div class="text-slate-400">
                    Klik tombol <span class="text-emerald-700 font-semibold">"Pilih Kode"</span> untuk mengisi Kode Rekening, Nama Program & Kegiatan
                </div>
            </div>

            <!-- Results List -->
            <div id="{{ $modalId }}-results-container" class="max-h-[380px] overflow-y-auto divide-y divide-slate-100 p-2 text-xs">
                <!-- Dynamically populated via JS -->
            </div>

            <!-- Modal Footer -->
            <div class="p-3.5 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2 text-slate-500 text-[11px]">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Sesuai Permendikbudristek Petunjuk Teknis BOSP & Bagan Akun Standar (BAS)</span>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" onclick="useManualAccountCodeEntry('{{ $modalId }}')" class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-800 font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                        <span>✍️ Kode Tidak Ditemukan? Isi Manual</span>
                    </button>
                    <button type="button" onclick="closeAccountCodeModal('{{ $modalId }}')" class="px-4 py-2 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 font-semibold rounded-xl transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.accountCodeCatalogData = @json(\App\Services\AccountCodeService::all());
window.activeAccountCodeCategory = 'ALL';
window.accountCodeConfig = window.accountCodeConfig || {};
window.accountCodeConfig['{{ $modalId }}'] = {
    targetCodeId: '{{ $targetCodeId }}',
    targetProgramId: '{{ $targetProgramId }}',
    targetActivityId: '{{ $targetActivityId }}'
};

function openAccountCodeModal(modalId = '{{ $modalId }}', customConfig = null) {
    if (customConfig) {
        window.accountCodeConfig[modalId] = Object.assign({}, window.accountCodeConfig[modalId], customConfig);
    }
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    const input = document.getElementById(`${modalId}-search-input`);
    if (input) {
        input.value = '';
        setTimeout(() => input.focus(), 80);
    }
    renderAccountCodeResults(modalId);
}

function closeAccountCodeModal(modalId = '{{ $modalId }}') {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function clearAccountCodeSearch(modalId = '{{ $modalId }}') {
    const input = document.getElementById(`${modalId}-search-input`);
    if (input) {
        input.value = '';
        const clearBtn = document.getElementById(`${modalId}-btn-clear`);
        if (clearBtn) clearBtn.classList.add('hidden');
        renderAccountCodeResults(modalId);
        input.focus();
    }
}

function filterAccountCodeCategory(category, btn, modalId = '{{ $modalId }}') {
    window.activeAccountCodeCategory = category;
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.querySelectorAll('.tab-snp').forEach(b => {
            b.className = 'tab-snp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer';
        });
    }
    btn.className = 'tab-snp px-3 py-1.5 rounded-xl bg-emerald-700 text-white shadow-2xs transition whitespace-nowrap cursor-pointer';
    renderAccountCodeResults(modalId);
}

function renderAccountCodeResults(modalId = '{{ $modalId }}') {
    const searchInput = document.getElementById(`${modalId}-search-input`);
    const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
    const clearBtn = document.getElementById(`${modalId}-btn-clear`);
    if (clearBtn) {
        if (query.length > 0) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }

    const filtered = (window.accountCodeCatalogData || []).filter(item => {
        if (window.activeAccountCodeCategory !== 'ALL' && item.category !== window.activeAccountCodeCategory) {
            return false;
        }
        if (query.length > 0) {
            const haystack = (item.code + ' ' + item.program + ' ' + item.activity + ' ' + (item.description || '') + ' ' + (item.category || '')).toLowerCase();
            return haystack.includes(query);
        }
        return true;
    });

    const countEl = document.getElementById(`${modalId}-total-count`);
    if (countEl) countEl.textContent = filtered.length;

    const container = document.getElementById(`${modalId}-results-container`);
    if (!container) return;

    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="py-12 px-4 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="font-bold text-slate-800 text-sm">Kode Rekening / Kegiatan Tidak Ditemukan</div>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    Kombinasi kata kunci "${query}" tidak ditemukan dalam katalog resmi. Anda tetap dapat memasukkan Kode Rekening, Nama Program, dan Kegiatan secara bebas & manual.
                </p>
                <div class="mt-4">
                    <button type="button" onclick="useManualAccountCodeEntry('${modalId}')" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm inline-flex items-center gap-2 cursor-pointer transition">
                        <span>✍️ Ketik Kode & Kegiatan Secara Manual</span>
                    </button>
                </div>
            </div>
        `;
        return;
    }

    let html = '';
    filtered.forEach((item, index) => {
        const catBadge = `<span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">${item.category}</span>`;
        
        html += `
            <div class="p-3 hover:bg-emerald-50/50 rounded-xl transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 group">
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 group-hover:bg-emerald-700 group-hover:text-white flex items-center justify-center text-[11px] font-bold shrink-0 transition">
                        ${index + 1}
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 text-xs">${item.code}</span>
                            ${catBadge}
                            <span class="text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">${item.program}</span>
                        </div>
                        <div class="font-bold text-slate-900 mt-1 text-xs sm:text-sm">
                            ${item.activity}
                        </div>
                        ${item.description ? `<div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1 italic">${item.description}</div>` : ''}
                    </div>
                </div>
                <div class="shrink-0 w-full sm:w-auto">
                    <button type="button" onclick="selectAccountCodeItem('${item.code}', '${encodeURIComponent(item.program)}', '${encodeURIComponent(item.activity)}', '${modalId}')" class="w-full sm:w-auto px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-bold text-xs rounded-xl shadow-2xs hover:shadow transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>Pilih Kode</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function selectAccountCodeItem(code, encodedProgram, encodedActivity, modalId = '{{ $modalId }}') {
    const program = decodeURIComponent(encodedProgram);
    const activity = decodeURIComponent(encodedActivity);
    const cfg = window.accountCodeConfig[modalId] || {};

    if (cfg.targetCodeId) {
        const codeInput = document.getElementById(cfg.targetCodeId);
        if (codeInput) {
            codeInput.value = code;
            codeInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    if (cfg.targetProgramId) {
        const progInput = document.getElementById(cfg.targetProgramId);
        if (progInput) {
            progInput.value = program;
            progInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    if (cfg.targetActivityId) {
        const actInput = document.getElementById(cfg.targetActivityId);
        if (actInput) {
            actInput.value = activity;
            actInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    closeAccountCodeModal(modalId);
    showAccountCodeToast(`Kode [${code}] berhasil dipilih untuk ${activity}`);
}

function useManualAccountCodeEntry(modalId = '{{ $modalId }}') {
    closeAccountCodeModal(modalId);
    const cfg = window.accountCodeConfig[modalId] || {};
    if (cfg.targetCodeId) {
        const codeInput = document.getElementById(cfg.targetCodeId);
        if (codeInput) {
            codeInput.focus();
        }
    }
    showAccountCodeToast('Silakan isi Kode Rekening, Nama Program, dan Uraian Kegiatan secara manual.');
}

function showAccountCodeToast(message) {
    let toast = document.getElementById('account-code-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'account-code-toast';
        toast.className = 'fixed bottom-5 right-5 z-50 bg-slate-900 text-white text-xs font-semibold px-4 py-3 rounded-2xl shadow-xl border border-slate-700 flex items-center gap-2 transform translate-y-10 opacity-0 transition-all duration-300';
        toast.innerHTML = `
            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span id="account-code-toast-msg"></span>
        `;
        document.body.appendChild(toast);
    }
    document.getElementById('account-code-toast-msg').textContent = message;
    toast.classList.remove('translate-y-10', 'opacity-0');
    setTimeout(() => {
        toast.classList.add('translate-y-10', 'opacity-0');
    }, 3500);
}

document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('{{ $modalId }}-search-input');
    if (input) {
        input.addEventListener('input', function () {
            renderAccountCodeResults('{{ $modalId }}');
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAccountCodeModal('{{ $modalId }}');
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('{{ $modalId }}');
            if (modal && !modal.classList.contains('hidden')) {
                closeAccountCodeModal('{{ $modalId }}');
            }
        }
    });
});
</script>
