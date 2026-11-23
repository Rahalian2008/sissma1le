/**
 * SIS-SMA1LE Universal Searchable Select Component
 * Mengubah elemen <select> (terutama untuk pemilihan Siswa dan Guru yang panjang)
 * menjadi dropdown interaktif dengan kotak pencarian instan (live search),
 * keyboard navigation, styling modern Tailwind, dan dukungan mode gelap (dark mode).
 */
(function () {
    'use strict';

    function initSearchableSelect(select) {
        if (!select || select.dataset.searchableInit === 'true') return;
        if (select.dataset.searchable === 'false' || select.classList.contains('no-searchable')) return;

        // Cek kriteria: select khusus siswa/guru, memiliki class searchable-select, atau jumlah option > 15
        const name = (select.getAttribute('name') || '').toLowerCase();
        const id = (select.getAttribute('id') || '').toLowerCase();
        const isTargetName = name.includes('student') || name.includes('siswa') ||
                             name.includes('teacher') || name.includes('guru') ||
                             name.includes('employee') || name.includes('pegawai') ||
                             name.includes('user_id');
        const hasClass = select.classList.contains('searchable-select');
        const hasDataAttr = select.dataset.searchable === 'true';
        const hasManyOptions = select.options.length >= 15;

        if (!isTargetName && !hasClass && !hasDataAttr && !hasManyOptions) {
            return;
        }

        select.dataset.searchableInit = 'true';

        // Sembunyikan select asli namun tetap ada di DOM agar form submission, validation & serialize bekerja
        select.style.position = 'absolute';
        select.style.opacity = '0';
        select.style.pointerEvents = 'none';
        select.style.height = '0';
        select.style.width = '0';
        select.style.margin = '0';
        select.style.padding = '0';
        select.tabIndex = -1;

        // Container wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'searchable-select-wrapper relative w-full min-w-0 select-none text-left';
        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);

        // Tombol Trigger
        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'searchable-select-trigger w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs font-medium shadow-2xs hover:border-indigo-400 dark:hover:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition cursor-pointer';

        const label = document.createElement('span');
        label.className = 'searchable-select-label truncate flex-1';

        const arrow = document.createElement('div');
        arrow.className = 'searchable-select-arrow shrink-0 text-slate-400 transition-transform duration-200';
        arrow.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        `;

        trigger.appendChild(label);
        trigger.appendChild(arrow);
        wrapper.appendChild(trigger);

        // Panel Dropdown
        const dropdown = document.createElement('div');
        dropdown.className = 'searchable-select-dropdown hidden absolute left-0 right-0 top-full mt-1.5 z-[160] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl p-2.5 max-w-full';

        // Search Input Header
        const searchBox = document.createElement('div');
        searchBox.className = 'relative mb-2';
        searchBox.innerHTML = `
            <input type="text" class="searchable-select-input w-full pl-9 pr-8 py-2 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition" placeholder="🔍 Cari nama, NISN, NIP, atau kelas...">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <button type="button" class="searchable-select-clear hidden absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-0.5 text-xs font-bold">✕</button>
        `;
        dropdown.appendChild(searchBox);

        const searchInput = searchBox.querySelector('.searchable-select-input');
        const clearBtn = searchBox.querySelector('.searchable-select-clear');

        // Daftar Options
        const optionsList = document.createElement('div');
        optionsList.className = 'searchable-select-options max-h-56 sm:max-h-64 overflow-y-auto space-y-0.5 rounded-xl pr-1 scrollbar-thin';
        dropdown.appendChild(optionsList);

        // Status Kosong
        const emptyState = document.createElement('div');
        emptyState.className = 'searchable-select-empty hidden py-5 text-center text-xs text-slate-400 dark:text-slate-500';
        emptyState.innerText = 'Tidak ada pilihan yang cocok.';
        dropdown.appendChild(emptyState);

        // Footer info count
        const footer = document.createElement('div');
        footer.className = 'mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-[10px] text-slate-400 flex items-center justify-between px-1';
        footer.innerHTML = `
            <span class="count-text">0 data</span>
            <span class="text-slate-400/80">Ketik untuk menyaring</span>
        `;
        dropdown.appendChild(footer);
        const countText = footer.querySelector('.count-text');

        wrapper.appendChild(dropdown);

        // Render options dari select asli
        function renderOptions(filterKeyword = '') {
            optionsList.innerHTML = '';
            const keyword = filterKeyword.toLowerCase().trim();
            let matchCount = 0;

            Array.from(select.options).forEach((opt, idx) => {
                const text = opt.text;
                const value = opt.value;
                const isSelected = opt.selected;

                if (keyword && !text.toLowerCase().includes(keyword) && !value.toLowerCase().includes(keyword)) {
                    return;
                }

                matchCount++;
                const item = document.createElement('div');
                item.className = `searchable-select-item px-3 py-2 rounded-xl text-xs font-medium cursor-pointer transition flex items-center justify-between gap-2 ${
                    isSelected 
                        ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold' 
                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800'
                }`;

                // Highlight keyword
                let displayText = text;
                if (keyword && displayText.toLowerCase().includes(keyword)) {
                    const regex = new RegExp(`(${keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                    displayText = displayText.replace(regex, '<mark class="bg-amber-200 dark:bg-amber-800/80 text-inherit rounded-xs px-0.5">$1</mark>');
                }

                item.innerHTML = `
                    <span class="truncate flex-1">${displayText}</span>
                    ${isSelected ? '<span class="text-indigo-600 dark:text-indigo-400 shrink-0 font-bold">✓</span>' : ''}
                `;

                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectOption(value);
                    closeDropdown();
                });

                optionsList.appendChild(item);
            });

            if (matchCount === 0) {
                emptyState.classList.remove('hidden');
                optionsList.classList.add('hidden');
            } else {
                emptyState.classList.add('hidden');
                optionsList.classList.remove('hidden');
            }

            if (countText) {
                countText.innerText = `${matchCount} pilihan ditampilkan`;
            }
        }

        function updateTriggerLabel() {
            const selectedOpt = select.options[select.selectedIndex];
            if (selectedOpt && selectedOpt.value !== '') {
                label.innerText = selectedOpt.text;
                label.className = 'searchable-select-label truncate flex-1 font-semibold text-slate-900 dark:text-slate-100';
            } else {
                label.innerText = (selectedOpt && selectedOpt.text) ? selectedOpt.text : '-- Pilih --';
                label.className = 'searchable-select-label truncate flex-1 text-slate-400 dark:text-slate-500';
            }
        }

        function selectOption(val) {
            select.value = val;
            updateTriggerLabel();
            // Trigger native change and input events
            select.dispatchEvent(new Event('change', { bubbles: true }));
            select.dispatchEvent(new Event('input', { bubbles: true }));
        }

        function openDropdown() {
            // Close any other open dropdowns first
            document.querySelectorAll('.searchable-select-dropdown').forEach(d => {
                if (d !== dropdown) d.classList.add('hidden');
            });
            dropdown.classList.remove('hidden');
            arrow.style.transform = 'rotate(180deg)';
            renderOptions(searchInput.value);
            setTimeout(() => searchInput.focus(), 50);
        }

        function closeDropdown() {
            dropdown.classList.add('hidden');
            arrow.style.transform = 'none';
        }

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (dropdown.classList.contains('hidden')) {
                openDropdown();
            } else {
                closeDropdown();
            }
        });

        searchInput.addEventListener('input', (e) => {
            const val = e.target.value;
            if (val) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
            renderOptions(val);
        });

        clearBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            searchInput.value = '';
            clearBtn.classList.add('hidden');
            renderOptions('');
            searchInput.focus();
        });

        // Close on escape
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDropdown();
                trigger.focus();
            }
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                closeDropdown();
            }
        });

        // Sync when native select changes programmatically
        select.addEventListener('change', () => {
            updateTriggerLabel();
        });

        // Listen for option mutations
        const observer = new MutationObserver(() => {
            updateTriggerLabel();
            if (!dropdown.classList.contains('hidden')) {
                renderOptions(searchInput.value);
            }
        });
        observer.observe(select, { childList: true, subtree: true, attributes: true });

        // Initial setup
        updateTriggerLabel();
    }

    // Expose global initializer
    window.initSearchableSelects = function (container = document) {
        if (!container) return;
        const selects = container.querySelectorAll('select:not([data-searchable-init="true"])');
        selects.forEach(sel => {
            initSearchableSelect(sel);
        });
    };

    // Auto-init on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => window.initSearchableSelects());
    } else {
        window.initSearchableSelects();
    }

    // Auto-observe DOM insertions (for dynamically opened modals)
    const domObserver = new MutationObserver((mutations) => {
        let hasNewSelects = false;
        mutations.forEach(m => {
            m.addedNodes.forEach(node => {
                if (node.nodeType === 1) {
                    if (node.tagName === 'SELECT' || node.querySelector('select')) {
                        hasNewSelects = true;
                    }
                }
            });
        });
        if (hasNewSelects) {
            window.initSearchableSelects();
        }
    });

    domObserver.observe(document.body, { childList: true, subtree: true });
})();
