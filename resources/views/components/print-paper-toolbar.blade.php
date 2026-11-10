@props([
    'title' => 'Pratinjau Cetak Dokumen Resmi',
    'defaultPaper' => 'a4',
    'isLandscape' => false,
])

@php
    $currentPaper = request('paper', $defaultPaper);
    $a4Label = $isLandscape ? 'A4 Landscape (297 × 210 mm)' : 'A4 Portrait (210 × 297 mm)';
    $f4Label = $isLandscape ? 'F4 / Folio Landscape (330 × 215 mm)' : 'F4 / Folio Portrait (215 × 330 mm)';
@endphp

<div class="no-print" style="background: #0f172a; color: #fff; padding: 12px 18px; border-radius: 12px; margin-bottom: 20px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
    <div style="display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 18px;">📄</span>
        <div>
            <h4 style="margin: 0; font-size: 13px; font-weight: 800; letter-spacing: 0.3px;">{{ $title }}</h4>
            <span style="font-size: 11px; color: #94a3b8;">Format Standar Dinas &bull; Kertas Aktif: <strong>{{ strtoupper($currentPaper) }}</strong></span>
        </div>
    </div>

    <div style="display: flex; align-items: center; gap: 8px;">
        <span style="font-size: 11.5px; font-weight: 700; color: #cbd5e1;">Pilih Kertas:</span>
        <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}"
           style="text-decoration: none; padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 700; transition: all 0.2s; {{ $currentPaper === 'a4' ? 'background: #6366f1; color: #ffffff;' : 'background: #1e293b; color: #94a3b8; border: 1px solid #334155;' }}">
            {{ $a4Label }}
        </a>
        <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}"
           style="text-decoration: none; padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 700; transition: all 0.2s; {{ $currentPaper === 'f4' ? 'background: #6366f1; color: #ffffff;' : 'background: #1e293b; color: #94a3b8; border: 1px solid #334155;' }}">
            {{ $f4Label }}
        </a>

        <button type="button" onclick="window.print()" style="padding: 7px 16px; background: #059669; color: #ffffff; border: none; border-radius: 8px; font-size: 11.5px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(5,150,105,0.4); margin-left: 6px;">
            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak Dokumen</span>
        </button>

        <button type="button" onclick="window.close()" style="padding: 7px 12px; background: #334155; color: #cbd5e1; border: none; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer;">
            Tutup
        </button>
    </div>
</div>

