{{-- Modal Pencarian & Pemilihan Katalog Kodefikasi Bahan Habis Pakai (BHP) & Persediaan (Akun 1.1.7) --}}
@props([
    'triggerId' => 'btn-open-consumable-modal',
    'targetNameId' => 'good-name',
    'targetCodeId' => 'good-code',
    'targetCategoryId' => 'good-category',
    'targetUnitId' => 'good-unit',
    'targetLocationId' => 'good-storage-location',
    'presetSelectId' => 'preset-consumable',
])

<div id="consumable-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeConsumableModal()"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-4xl border border-slate-200">
            <!-- Modal Header -->
            <div class="p-5 bg-gradient-to-r from-emerald-700 via-teal-700 to-emerald-800 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold tracking-tight">Katalog Kodefikasi Bahan Habis Pakai (BHP)</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-400/20 text-emerald-200 border border-emerald-400/30">Akun 1.1.7 Persediaan</span>
                        </div>
                        <p class="text-xs text-emerald-100/90 mt-0.5">Standar Kodefikasi Persediaan Satuan Pendidikan SMA Negeri (Permendagri 108/2016 &amp; BAS)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="useManualConsumableEntry()" class="px-3 py-1.5 bg-white/15 hover:bg-white/25 text-white text-xs font-semibold rounded-xl border border-white/20 transition cursor-pointer flex items-center gap-1.5" title="Ketik manual kode dan nama barang jika tidak ada di katalog">
                        <span>✍️ Ketik Manual</span>
                    </button>
                    <button type="button" onclick="closeConsumableModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer">
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
                    <input type="text" id="consumable-search-input" placeholder="Cari nama bahan habis pakai, kode 1.1.7, atau merk (cth: kertas HVS, spidol snowman, tinta epson, desinfektan, paracetamol, lugol)..." class="w-full pl-10 pr-32 py-2.5 bg-white border border-slate-300 rounded-2xl text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                    <div class="absolute inset-y-1.5 right-1.5 flex items-center gap-1.5">
                        <button type="button" id="btn-clear-consumable" onclick="clearConsumableSearch()" class="px-2 py-1 text-[11px] font-semibold text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 hidden">
                            ✕
                        </button>
                        <button type="button" id="btn-online-search-consumable" onclick="performOnlineSearchConsumable()" class="px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer" title="Cari data persediaan BHP secara online langsung dari Permendagri 108/2016 & BAS 1.1.7">
                            <span id="spinner-online-consumable" class="hidden animate-spin">⟳</span>
                            <span>🌐 Cari Online</span>
                        </button>
                    </div>
                </div>

                <!-- Tabs Subkategori BHP -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs font-semibold custom-scroll">
                    <button type="button" class="tab-bhp px-3 py-1.5 rounded-xl bg-emerald-600 text-white shadow-2xs transition whitespace-nowrap cursor-pointer" data-category="ALL" onclick="filterConsumableCategory('ALL', this)">
                        Semua BHP (70+)
                    </button>
                    <button type="button" class="tab-bhp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-category="Alat Tulis Kantor" onclick="filterConsumableCategory('Alat Tulis Kantor', this)">
                        1. ATK &amp; Kertas
                    </button>
                    <button type="button" class="tab-bhp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-category="Tinta & Komputer" onclick="filterConsumableCategory('Tinta & Komputer', this)">
                        2. Tinta &amp; Bahan Komputer
                    </button>
                    <button type="button" class="tab-bhp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-category="Barang Cetakan" onclick="filterConsumableCategory('Barang Cetakan', this)">
                        3. Barang Cetakan &amp; Formulir
                    </button>
                    <button type="button" class="tab-bhp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-category="Bahan Kebersihan" onclick="filterConsumableCategory('Bahan Kebersihan', this)">
                        4. Kebersihan &amp; Sanitasi
                    </button>
                    <button type="button" class="tab-bhp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-category="Bahan Praktik Lab" onclick="filterConsumableCategory('Bahan Praktik Lab', this)">
                        5. Praktikum Lab &amp; Seni
                    </button>
                    <button type="button" class="tab-bhp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer" data-category="Obat & Medis UKS" onclick="filterConsumableCategory('Obat & Medis UKS', this)">
                        6. Obat &amp; Medis UKS
                    </button>
                </div>
            </div>

            <!-- Results Counter & Info -->
            <div class="px-5 py-2 bg-slate-100/70 border-b border-slate-200 flex items-center justify-between text-[11px] text-slate-600">
                <div id="consumable-count-info">
                    Ditemukan <span class="font-bold text-slate-900" id="consumable-total-count">0</span> jenis bahan persediaan sekolah
                </div>
                <div class="text-slate-400">
                    Klik tombol <span class="text-emerald-700 font-semibold">"Pilih Bahan"</span> untuk otomatis mengisi kode, nama, kategori, dan satuan
                </div>
            </div>

            <!-- Results List -->
            <div id="consumable-results-container" class="max-h-[380px] overflow-y-auto divide-y divide-slate-100 p-2 text-xs">
                <!-- Dynamically populated via JS -->
            </div>

            <!-- Modal Footer -->
            <div class="p-3.5 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <span class="text-slate-400 text-[11px]">Sesuai Permendagri No. 108/2016, Permendagri No. 47/2021 &amp; Bagan Akun Standar (BAS) Persediaan</span>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" onclick="useManualConsumableEntry()" class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-800 font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                        <span>✍️ Barang Tidak Ada? Ketik Manual</span>
                    </button>
                    <button type="button" onclick="closeConsumableModal()" class="px-4 py-2 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 font-semibold rounded-xl transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.consumableCatalogData = @json(\App\Services\SipdBmdService::getConsumableCodes());
window.activeConsumableCategory = 'ALL';
window.consumableConfig = {
    targetNameId: '{{ $targetNameId }}',
    targetCodeId: '{{ $targetCodeId }}',
    targetCategoryId: '{{ $targetCategoryId }}',
    targetUnitId: '{{ $targetUnitId }}',
    targetLocationId: '{{ $targetLocationId }}',
    presetSelectId: '{{ $presetSelectId }}'
};

function openConsumableModal(customConfig = null) {
    if (customConfig) {
        window.consumableConfig = Object.assign({}, window.consumableConfig, customConfig);
    }
    const modal = document.getElementById('consumable-modal');
    if (!modal) return;
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    const input = document.getElementById('consumable-search-input');
    if (input) {
        input.value = '';
        setTimeout(() => input.focus(), 80);
    }
    renderConsumableResults();
}
window.openBhpModal = openConsumableModal;

function closeConsumableModal() {
    const modal = document.getElementById('consumable-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
    document.body.classList.remove('overflow-hidden');
}
window.closeBhpModal = closeConsumableModal;

function clearConsumableSearch() {
    const input = document.getElementById('consumable-search-input');
    if (input) {
        input.value = '';
        input.focus();
    }
    const clearBtn = document.getElementById('btn-clear-consumable');
    if (clearBtn) clearBtn.classList.add('hidden');
    renderConsumableResults();
}

function filterConsumableCategory(cat, btn) {
    window.activeConsumableCategory = cat;
    document.querySelectorAll('.tab-bhp').forEach(b => {
        b.className = 'tab-bhp px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition whitespace-nowrap cursor-pointer';
    });
    btn.className = 'tab-bhp px-3 py-1.5 rounded-xl bg-emerald-600 text-white shadow-2xs transition whitespace-nowrap cursor-pointer';
    renderConsumableResults();
}

function getBhpCategoryBadge(cat) {
    const map = {
        'Alat Tulis Kantor': { bg: 'bg-blue-50 text-blue-700 border-blue-200', label: 'ATK' },
        'Tinta & Komputer': { bg: 'bg-indigo-50 text-indigo-700 border-indigo-200', label: 'Tinta & IT' },
        'Barang Cetakan': { bg: 'bg-amber-50 text-amber-700 border-amber-200', label: 'Cetakan' },
        'Bahan Kebersihan': { bg: 'bg-teal-50 text-teal-700 border-teal-200', label: 'Kebersihan' },
        'Bahan Praktik Lab': { bg: 'bg-purple-50 text-purple-700 border-purple-200', label: 'Praktik Lab' },
        'Kesenian Habis Pakai': { bg: 'bg-pink-50 text-pink-700 border-pink-200', label: 'Kesenian' },
        'Obat & Medis UKS': { bg: 'bg-rose-50 text-rose-700 border-rose-200', label: 'UKS & Medis' },
    };
    const c = map[cat] || { bg: 'bg-slate-50 text-slate-700 border-slate-200', label: cat };
    return `<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border ${c.bg}">${c.label}</span>`;
}

function renderConsumableResults() {
    const input = document.getElementById('consumable-search-input');
    const query = input ? (input.value || '').toLowerCase().trim() : '';
    const clearBtn = document.getElementById('btn-clear-consumable');
    if (clearBtn) {
        if (query.length > 0) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }

    const items = Object.entries(window.consumableCatalogData || {}).map(([code, info]) => ({
        code: code,
        name: info.name,
        unit: info.unit,
        category: info.category,
        specification: info.specification || ''
    }));

    const filtered = items.filter(item => {
        if (window.activeConsumableCategory !== 'ALL' && item.category !== window.activeConsumableCategory) {
            return false;
        }
        if (query.length > 0) {
            const haystack = (item.code + ' ' + item.name + ' ' + item.category + ' ' + item.unit + ' ' + item.specification).toLowerCase();
            return haystack.includes(query);
        }
        return true;
    });

    const countElem = document.getElementById('consumable-total-count');
    if (countElem) countElem.textContent = filtered.length;

    const container = document.getElementById('consumable-results-container');
    if (!container) return;

    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="py-10 px-4 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="font-bold text-slate-800 text-sm">Bahan Habis Pakai Tidak Ditemukan di Katalog Lokal</div>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    Kombinasi kata kunci "${query}" belum tercatat di katalog lokal. Klik tombol di bawah untuk mencari secara online langsung dari taksonomi Permendagri No. 108/2016 &amp; BAS Akun 1.1.7.
                </p>
                <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                    <button type="button" onclick="performOnlineSearchConsumable()" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs rounded-xl shadow-sm inline-flex items-center gap-2 cursor-pointer transition">
                        <span>🌐 Cari Online Permendagri 108/2016 Sekarang</span>
                    </button>
                    <button type="button" onclick="useManualConsumableEntry()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 inline-flex items-center gap-1.5 cursor-pointer transition">
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
            <div class="p-3 hover:bg-emerald-50/60 rounded-xl transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 group">
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center text-[11px] font-bold shrink-0 transition">
                        ${index + 1}
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 text-xs">${item.code}</span>
                            ${getBhpCategoryBadge(item.category)}
                            ${item.is_online ? '<span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-teal-50 border border-teal-200 text-teal-700">🌐 Online Permendagri</span>' : ''}
                            <span class="text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">Satuan: ${item.unit}</span>
                        </div>
                        <div class="font-bold text-slate-900 mt-1 text-xs sm:text-sm">
                            ${item.name}
                        </div>
                        ${item.specification ? `<div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1 italic">${item.specification}</div>` : ''}
                    </div>
                </div>
                <div class="shrink-0 w-full sm:w-auto">
                    <button type="button" onclick="selectConsumableItem('${item.code}')" class="w-full sm:w-auto px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-2xs hover:shadow transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>Pilih Bahan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function performOnlineSearchConsumable() {
    const input = document.getElementById('consumable-search-input');
    const query = (input ? input.value : '').trim();
    if (!query) {
        showConsumableToast('Silakan ketik nama bahan habis pakai yang ingin dicari secara online.');
        if (input) input.focus();
        return;
    }

    const spinner = document.getElementById('spinner-online-consumable');
    if (spinner) spinner.classList.remove('hidden');

    const url = '{{ route('sarpras.sipd-bmd.online-search') }}?q=' + encodeURIComponent(query) + '&kib=BHP';

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data && data.data && data.data.length > 0) {
                let addedCount = 0;
                data.data.forEach(onlineItem => {
                    if (!window.consumableCatalogData[onlineItem.code]) {
                        window.consumableCatalogData[onlineItem.code] = {
                            name: onlineItem.name,
                            unit: onlineItem.unit,
                            category: onlineItem.category,
                            specification: onlineItem.specification || '',
                            is_online: true
                        };
                        addedCount++;
                    }
                });
                renderConsumableResults();
                showConsumableToast(`Ditemukan ${data.data.length} hasil klasifikasi persediaan/BHP Permendagri 108/2016 secara online.`);
            } else {
                showConsumableToast('Pencarian online selesai, tidak ada item persediaan tambahan yang ditemukan.');
            }
        })
        .catch(err => {
            console.error('Online BHP search error:', err);
            showConsumableToast('Gagal terhubung ke modul pencarian online. Silakan ketik kode secara manual.');
        })
        .finally(() => {
            if (spinner) spinner.classList.add('hidden');
        });
}

function selectConsumableItem(code) {
    const raw = window.consumableCatalogData[code];
    if (!raw) return;

    const item = {
        code: code,
        name: raw.name,
        unit: raw.unit,
        category: raw.category,
        specification: raw.specification || ''
    };

    const cfg = window.consumableConfig;

    // 1. Fill Good Name
    const targetName = document.getElementById(cfg.targetNameId);
    if (targetName) {
        targetName.value = item.name;
    }

    // 2. Fill Code
    const targetCode = document.getElementById(cfg.targetCodeId);
    if (targetCode) {
        targetCode.value = item.code;
    }

    // 3. Match & Select Category in select element
    const targetCat = document.getElementById(cfg.targetCategoryId);
    if (targetCat) {
        // Map catalog category to standard reference categories
        let matched = false;
        const options = Array.from(targetCat.options);
        
        // Exact match
        for (let opt of options) {
            if (opt.value.toLowerCase() === item.category.toLowerCase() || opt.text.toLowerCase().includes(item.category.toLowerCase())) {
                targetCat.value = opt.value;
                matched = true;
                break;
            }
        }
        
        // Keyword mapping if not exact
        if (!matched) {
            const catMap = {
                'Alat Tulis Kantor': ['atk', 'alat tulis', 'bhp-atk'],
                'Tinta & Komputer': ['elektronik', 'it', 'komputer', 'bhp-plh', 'bhp-atk'],
                'Barang Cetakan': ['cetakan', 'atk', 'bhp-atk'],
                'Bahan Kebersihan': ['pembersih', 'kebersihan', 'sanitasi', 'bhp-kbr'],
                'Bahan Praktik Lab': ['praktik', 'laboratorium', 'lab', 'bhp-prk'],
                'Kesenian Habis Pakai': ['kesenian', 'seni', 'olahraga', 'bhp-ola'],
                'Obat & Medis UKS': ['obat', 'uks', 'p3k', 'medis', 'bhp-uks']
            };
            const keywords = catMap[item.category] || [];
            for (let opt of options) {
                const optVal = opt.value.toLowerCase();
                const optText = opt.text.toLowerCase();
                if (keywords.some(kw => optVal.includes(kw) || optText.includes(kw))) {
                    targetCat.value = opt.value;
                    matched = true;
                    break;
                }
            }
        }
        targetCat.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // 4. Match & Select Unit
    const targetUnit = document.getElementById(cfg.targetUnitId);
    if (targetUnit) {
        if (targetUnit.tagName === 'SELECT') {
            let matchedUnit = false;
            for (let opt of Array.from(targetUnit.options)) {
                if (opt.value.toLowerCase() === item.unit.toLowerCase() || opt.text.toLowerCase().includes(item.unit.toLowerCase())) {
                    targetUnit.value = opt.value;
                    matchedUnit = true;
                    break;
                }
            }
            if (!matchedUnit) {
                // If not in options, append or select nearest
                const newOpt = new Option(item.unit, item.unit, true, true);
                targetUnit.add(newOpt);
            }
        } else {
            targetUnit.value = item.unit;
        }
        targetUnit.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // 5. Default location recommendation if empty
    const targetLoc = document.getElementById(cfg.targetLocationId);
    if (targetLoc && !targetLoc.value) {
        if (item.category.includes('UKS')) targetLoc.value = 'Lemari Obat Ruang UKS';
        else if (item.category.includes('Lab')) targetLoc.value = 'Ruang Preparasi Lab IPA';
        else if (item.category.includes('Kebersihan')) targetLoc.value = 'Gudang Alat Kebersihan';
        else targetLoc.value = 'Gudang Logistik & ATK';
    }

    // 6. Sync preset dropdown if available
    const presetSelect = document.getElementById(cfg.presetSelectId);
    if (presetSelect) {
        presetSelect.value = item.code;
        presetSelect.dispatchEvent(new Event('change', { bubbles: true }));
    }

    closeConsumableModal();

    showConsumableToast(`Kodefikasi BHP [${item.code}] berhasil dipilih untuk ${item.name}`);
}

function useManualConsumableEntry() {
    closeConsumableModal();
    const cfg = window.consumableConfig;
    const targetCode = document.getElementById(cfg.targetCodeId);
    if (targetCode) {
        targetCode.focus();
    }
    showConsumableToast('Silakan input kode barang dan nama bahan secara manual.');
}

function showConsumableToast(message) {
    let toast = document.getElementById('consumable-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'consumable-toast';
        toast.className = 'fixed bottom-5 right-5 z-50 bg-slate-900 text-white text-xs font-semibold px-4 py-3 rounded-2xl shadow-xl border border-slate-700 flex items-center gap-2 transform translate-y-10 opacity-0 transition-all duration-300';
        toast.innerHTML = `
            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span id="consumable-toast-msg"></span>
        `;
        document.body.appendChild(toast);
    }
    document.getElementById('consumable-toast-msg').textContent = message;
    toast.classList.remove('translate-y-10', 'opacity-0');
    setTimeout(() => {
        toast.classList.add('translate-y-10', 'opacity-0');
    }, 3500);
}

document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('consumable-search-input');
    if (input) {
        input.addEventListener('input', function () {
            renderConsumableResults();
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeConsumableModal();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                performOnlineSearchConsumable();
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('consumable-modal');
            if (modal && !modal.classList.contains('hidden')) {
                closeConsumableModal();
            }
        }
    });
});
</script>
