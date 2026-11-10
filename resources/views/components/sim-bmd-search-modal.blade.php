{{-- Modal Pencarian Online Katalog SIPD e-BMD Jawa Barat (Permendagri No. 108/2016) --}}
@props([
    'triggerId' => 'btn-open-sim-bmd-modal',
    'targetNameId' => 'field-good-name',
    'targetCodeId' => 'field-sim-bmd-code',
    'targetKibId' => 'field-kib-type',
    'targetUnitId' => 'field-unit',
    'presetSelectId' => 'preset-sim-bmd',
])

<div id="sim-bmd-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeSimBmdModal()"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-4xl border border-slate-200">
            <!-- Modal Header -->
            <div class="p-5 bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-800 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold tracking-tight">Katalog Kode Barang SIPD e-BMD</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-400/20 text-emerald-200 border border-emerald-400/30">Permendagri No. 108/2016</span>
                        </div>
                        <p class="text-xs text-blue-100/90 mt-0.5">Basis Data Resmi Penggolongan & Kodefikasi BMD Satuan Pendidikan SMA Provinsi Jawa Barat</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="useManualSimBmdEntry()" class="px-3 py-1.5 bg-white/15 hover:bg-white/25 text-white text-xs font-semibold rounded-xl border border-white/20 transition cursor-pointer flex items-center gap-1.5" title="Ketik kode dan nama barang secara manual jika tidak ada di katalog">
                        <span>✍️ Isi Manual</span>
                    </button>
                    <button type="button" onclick="closeSimBmdModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer">
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
                    <input type="text" id="sim-bmd-search-input" placeholder="Cari nama barang atau kode SIPD e-BMD (cth: laptop, proyektor, meja siswa, chromebook, mikroskop, genset, ac)..." class="w-full pl-10 pr-32 py-2.5 bg-white border border-slate-300 rounded-2xl text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-2xs">
                    <div class="absolute inset-y-1.5 right-1.5 flex items-center gap-1.5">
                        <button type="button" id="btn-clear-sim-bmd" onclick="clearSimBmdSearch()" class="px-2 py-1 text-[11px] font-semibold text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 hidden">
                            ✕
                        </button>
                        <button type="button" id="btn-online-search-sim-bmd" onclick="performOnlineSearchSimBmd()" class="px-3 py-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer" title="Cari data kodefikasi secara online langsung dari Permendagri 108/2016">
                            <span id="spinner-online-sim-bmd" class="hidden animate-spin">⟳</span>
                            <span>🌐 Cari Online</span>
                        </button>
                    </div>
                </div>

                <!-- Tabs KIB & BHP -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs font-semibold custom-scroll">
                    <button type="button" class="tab-kib px-3 py-1.5 rounded-xl bg-blue-600 text-white shadow-2xs transition whitespace-nowrap cursor-pointer" data-kib="ALL" onclick="filterSimBmdKib('ALL', this)">
                        Semua KIB &amp; BHP
                    </button>
                    <button type="button" class="tab-kib px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-kib="B" onclick="filterSimBmdKib('B', this)">
                        KIB B: Peralatan &amp; Mesin / TIK
                    </button>
                    <button type="button" class="tab-kib px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-kib="C" onclick="filterSimBmdKib('C', this)">
                        KIB C: Gedung &amp; Bangunan
                    </button>
                    <button type="button" class="tab-kib px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-kib="E" onclick="filterSimBmdKib('E', this)">
                        KIB E: Buku Kurikulum &amp; Aset Lain
                    </button>
                    <button type="button" class="tab-kib px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-kib="D" onclick="filterSimBmdKib('D', this)">
                        KIB D: Jalan, Jaringan &amp; Irigasi
                    </button>
                    <button type="button" class="tab-kib px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-kib="A" onclick="filterSimBmdKib('A', this)">
                        KIB A: Tanah
                    </button>
                    <button type="button" class="tab-kib px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-kib="F" onclick="filterSimBmdKib('F', this)">
                        KIB F: KDP Fisik
                    </button>
                    <button type="button" class="tab-kib px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-kib="BHP" onclick="filterSimBmdKib('BHP', this)">
                        BHP: Bahan Habis Pakai (Akun 1.1.7)
                    </button>
                </div>
            </div>

            <!-- Results Counter & Info -->
            <div class="px-5 py-2 bg-slate-100/70 border-b border-slate-200 flex items-center justify-between text-[11px] text-slate-600">
                <div id="sim-bmd-count-info">
                    Ditemukan <span class="font-bold text-slate-900" id="sim-bmd-total-count">0</span> barang inventaris sekolah
                </div>
                <div class="text-slate-400">
                    Klik tombol <span class="text-blue-600 font-semibold">"Pilih Kode"</span> untuk langsung mengisi otomatis formulir
                </div>
            </div>

            <!-- Results List -->
            <div id="sim-bmd-results-container" class="max-h-[380px] overflow-y-auto divide-y divide-slate-100 p-2 text-xs">
                <!-- Dynamically populated via JS -->
            </div>

            <!-- Modal Footer -->
            <div class="p-3.5 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <span class="text-slate-400 text-[11px]">Sesuai standar Permendagri No. 108/2016 & Format Pelaporan SIPD e-BMD Jawa Barat</span>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" onclick="useManualSimBmdEntry()" class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-800 font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                        <span>✍️ Kode Tidak Ditemukan? Isi Manual</span>
                    </button>
                    <button type="button" onclick="closeSimBmdModal()" class="px-4 py-2 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 font-semibold rounded-xl transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.simBmdCatalogData = @json(\App\Services\SipdBmdService::searchCodes(null, null, 500));
window.activeSimBmdKib = 'ALL';
window.simBmdConfig = {
    targetNameId: '{{ $targetNameId }}',
    targetCodeId: '{{ $targetCodeId }}',
    targetKibId: '{{ $targetKibId }}',
    targetUnitId: '{{ $targetUnitId }}',
    presetSelectId: '{{ $presetSelectId }}'
};

function openSimBmdModal(customConfig = null) {
    if (customConfig) {
        window.simBmdConfig = Object.assign({}, window.simBmdConfig, customConfig);
    }
    const modal = document.getElementById('sim-bmd-modal');
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    const input = document.getElementById('sim-bmd-search-input');
    input.value = '';
    renderSimBmdResults();
    setTimeout(() => input.focus(), 80);
}
// Alias for SIPD e-BMD
window.openSipdBmdModal = openSimBmdModal;

function closeSimBmdModal() {
    const modal = document.getElementById('sim-bmd-modal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}
window.closeSipdBmdModal = closeSimBmdModal;

function clearSimBmdSearch() {
    const input = document.getElementById('sim-bmd-search-input');
    input.value = '';
    document.getElementById('btn-clear-sim-bmd').classList.add('hidden');
    renderSimBmdResults();
    input.focus();
}

function filterSimBmdKib(kib, btn) {
    window.activeSimBmdKib = kib;
    document.querySelectorAll('.tab-kib').forEach(b => {
        b.className = 'tab-kib px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer';
    });
    btn.className = 'tab-kib px-3 py-1.5 rounded-xl bg-blue-600 text-white shadow-2xs transition whitespace-nowrap cursor-pointer';
    renderSimBmdResults();
}

function getKibBadge(kib) {
    const map = {
        'A': { bg: 'bg-emerald-50 text-emerald-700 border-emerald-200', label: 'KIB-A (Tanah)' },
        'B': { bg: 'bg-blue-50 text-blue-700 border-blue-200', label: 'KIB-B (Peralatan & Mesin)' },
        'C': { bg: 'bg-amber-50 text-amber-700 border-amber-200', label: 'KIB-C (Gedung)' },
        'D': { bg: 'bg-purple-50 text-purple-700 border-purple-200', label: 'KIB-D (Jaringan)' },
        'E': { bg: 'bg-pink-50 text-pink-700 border-pink-200', label: 'KIB-E (Aset Lain/Buku)' },
        'F': { bg: 'bg-rose-50 text-rose-700 border-rose-200', label: 'KIB-F (Konstruksi KDP)' },
        'BHP': { bg: 'bg-teal-50 text-teal-700 border-teal-200', label: 'BHP (Persediaan)' },
    };
    const c = map[kib] || { bg: 'bg-slate-50 text-slate-700 border-slate-200', label: 'KIB-' + kib };
    return `<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border ${c.bg}">${c.label}</span>`;
}

function renderSimBmdResults() {
    const query = (document.getElementById('sim-bmd-search-input').value || '').toLowerCase().trim();
    const clearBtn = document.getElementById('btn-clear-sim-bmd');
    if (clearBtn) {
        if (query.length > 0) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }

    const filtered = window.simBmdCatalogData.filter(item => {
        if (window.activeSimBmdKib !== 'ALL' && item.kib !== window.activeSimBmdKib) {
            return false;
        }
        if (query.length > 0) {
            const haystack = (item.code + ' ' + item.name + ' ' + (item.category || '') + ' ' + (item.unit || '') + ' ' + (item.specification || '')).toLowerCase();
            return haystack.includes(query);
        }
        return true;
    });

    document.getElementById('sim-bmd-total-count').textContent = filtered.length;

    const container = document.getElementById('sim-bmd-results-container');
    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="py-10 px-4 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <div class="font-bold text-slate-800 text-sm">Tidak Ditemukan di Katalog Lokal</div>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    Kombinasi kata kunci "${query}" belum ada di database lokal. Klik tombol di bawah untuk mencari secara online langsung dari taksonomi Permendagri No. 108/2016.
                </p>
                <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                    <button type="button" onclick="performOnlineSearchSimBmd()" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm inline-flex items-center gap-2 cursor-pointer transition">
                        <span>🌐 Cari Online Permendagri 108/2016 Sekarang</span>
                    </button>
                    <button type="button" onclick="useManualSimBmdEntry()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 inline-flex items-center gap-1.5 cursor-pointer transition">
                        <span>✍️ Ketik Manual</span>
                    </button>
                </div>
            </div>
        `;
        return;
    }

    let html = '';
    filtered.forEach((item, index) => {
        html += `
            <div class="p-3 hover:bg-blue-50/60 rounded-xl transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 group">
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center text-[11px] font-bold shrink-0 transition">
                        ${index + 1}
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200 text-xs">${item.code}</span>
                            ${getKibBadge(item.kib)}
                            ${item.is_online ? '<span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 border border-indigo-200 text-indigo-700">🌐 Online Permendagri</span>' : ''}
                            <span class="text-[11px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded">Satuan: ${item.unit}</span>
                            ${item.category ? `<span class="text-[10px] text-slate-500 font-semibold">(${item.category})</span>` : ''}
                        </div>
                        <div class="font-bold text-slate-900 mt-1 text-xs sm:text-sm">
                            ${item.name}
                        </div>
                        ${item.specification ? `<div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1 italic">${item.specification}</div>` : ''}
                    </div>
                </div>
                <div class="shrink-0 w-full sm:w-auto">
                    <button type="button" onclick="selectSimBmdItem('${item.code}')" class="w-full sm:w-auto px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-2xs hover:shadow transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>Pilih Kode</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function performOnlineSearchSimBmd() {
    const input = document.getElementById('sim-bmd-search-input');
    const query = (input ? input.value : '').trim();
    if (!query) {
        showToastNotification('Silakan ketik nama barang atau kata kunci yang ingin dicari secara online.');
        if (input) input.focus();
        return;
    }

    const spinner = document.getElementById('spinner-online-sim-bmd');
    if (spinner) spinner.classList.remove('hidden');

    const kib = window.activeSimBmdKib || 'ALL';
    const url = '{{ route('sarpras.sipd-bmd.online-search') }}?q=' + encodeURIComponent(query) + '&kib=' + encodeURIComponent(kib);

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data && data.data && data.data.length > 0) {
                let addedCount = 0;
                data.data.forEach(onlineItem => {
                    const exists = window.simBmdCatalogData.some(i => i.code === onlineItem.code);
                    if (!exists) {
                        window.simBmdCatalogData.unshift(onlineItem);
                        addedCount++;
                    }
                });
                renderSimBmdResults();
                showToastNotification(`Berhasil memuat ${data.data.length} hasil klasifikasi Permendagri 108/2016 secara online.`);
            } else {
                showToastNotification('Pencarian online selesai, tidak ada item tambahan yang ditemukan.');
            }
        })
        .catch(err => {
            console.error('Online search error:', err);
            showToastNotification('Gagal terhubung ke modul pencarian online. Silakan ketik kode secara manual.');
        })
        .finally(() => {
            if (spinner) spinner.classList.add('hidden');
        });
}

function selectSimBmdItem(code) {
    const item = window.simBmdCatalogData.find(i => i.code === code);
    if (!item) return;

    const cfg = window.simBmdConfig;

    // Fill Good Name / Asset Name
    const targetName = document.getElementById(cfg.targetNameId);
    if (targetName) {
        targetName.value = item.name;
    }

    // Fill SIPD e-BMD Code
    const targetCode = document.getElementById(cfg.targetCodeId);
    if (targetCode) {
        targetCode.value = item.code;
    }

    // Fill KIB Type
    const targetKib = document.getElementById(cfg.targetKibId);
    if (targetKib) {
        targetKib.value = item.kib;
        targetKib.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // Fill Unit
    const targetUnit = document.getElementById(cfg.targetUnitId);
    if (targetUnit && item.unit) {
        targetUnit.value = item.unit;
    }

    // If there is a preset dropdown, sync its value
    const presetSelect = document.getElementById(cfg.presetSelectId);
    if (presetSelect) {
        presetSelect.value = item.code;
        presetSelect.dispatchEvent(new Event('change', { bubbles: true }));

        const parentWrapper = presetSelect.closest('.custom-searchable-select');
        if (parentWrapper) {
            const selectedTextSpan = parentWrapper.querySelector('.selected-text');
            if (selectedTextSpan) {
                selectedTextSpan.textContent = `[${item.code}] ${item.name} (KIB-${item.kib})`;
            }
        }
    }

    closeSimBmdModal();

    showToastNotification(`Kode SIPD e-BMD [${item.code}] berhasil dipilih untuk ${item.name}`);
}

function useManualSimBmdEntry() {
    closeSimBmdModal();
    const cfg = window.simBmdConfig;
    const targetCode = document.getElementById(cfg.targetCodeId);
    if (targetCode) {
        targetCode.focus();
    }
    showToastNotification('Silakan ketik Kode Barang dan Nama Aset secara manual.');
}

function showToastNotification(message) {
    let toast = document.getElementById('sim-bmd-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'sim-bmd-toast';
        toast.className = 'fixed bottom-5 right-5 z-50 bg-slate-900 text-white text-xs font-semibold px-4 py-3 rounded-2xl shadow-xl border border-slate-700 flex items-center gap-2 transform translate-y-10 opacity-0 transition-all duration-300';
        toast.innerHTML = `
            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span id="sim-bmd-toast-msg"></span>
        `;
        document.body.appendChild(toast);
    }
    document.getElementById('sim-bmd-toast-msg').textContent = message;
    toast.classList.remove('translate-y-10', 'opacity-0');
    setTimeout(() => {
        toast.classList.add('translate-y-10', 'opacity-0');
    }, 3500);
}

document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('sim-bmd-search-input');
    if (input) {
        input.addEventListener('input', function () {
            renderSimBmdResults();
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeSimBmdModal();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                performOnlineSearchSimBmd();
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('sim-bmd-modal');
            if (modal && !modal.classList.contains('hidden')) {
                closeSimBmdModal();
            }
        }
    });
});
</script>
