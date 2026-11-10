@php
    $currentUser = Auth::user();
    $isStudentLearner = $currentUser && ($currentUser->isStudent() || $currentUser->student !== null);
    $isSecurityEnabledOnClass = isset($courseClass) && $courseClass->isSecurityEnabled();
    $shouldTrackSecurity = $isStudentLearner && $isSecurityEnabledOnClass;

    // Untuk tugas: Jika siswa telah mengumpulkan (submitted), nonaktifkan pelacak keamanan
    if (isset($mySubmission) && $mySubmission && $mySubmission->submitted_at) {
        $shouldTrackSecurity = false;
    }
@endphp

@if($shouldTrackSecurity)
<!-- ======================================================== -->
<!-- LMS ANTI-CHEAT & TAB SWITCH / MINIMIZE SECURITY MONITOR -->
<!-- ======================================================== -->
<div id="lms-security-violation-overlay" class="fixed inset-0 z-[99999] bg-slate-950/95 backdrop-blur-md hidden flex items-center justify-center p-4 select-none">
    <div class="max-w-md w-full bg-white dark:bg-slate-900 border-2 border-rose-500 rounded-3xl p-6 text-center space-y-4 shadow-2xl">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-rose-500/15 text-rose-500 border border-rose-500/30 flex items-center justify-center text-3xl animate-bounce">
            🔒
        </div>
        <div class="space-y-1">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30">
                Peringatan Keamanan LMS
            </span>
            <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">
                Akses Otomatis Dikunci!
            </h3>
        </div>
        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
            Terdeteksi aktivitas yang dilarang (<strong>membuka tab baru, meminimalkan browser, atau beralih aplikasi</strong>). Akses Anda ke <span class="font-bold text-rose-600 dark:text-rose-400">{{ $itemTitle ?? 'pembelajaran' }}</span> telah otomatis diblokir oleh sistem.
        </p>
        <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/30 text-left text-[11px] text-amber-800 dark:text-amber-300 space-y-1">
            <p class="font-bold">⚠️ Cara Membuka Blokir:</p>
            <p>Silakan hubungi <strong>Guru Mata Pelajaran</strong> atau <strong>Admin LMS</strong> untuk membuka blokir Anda.</p>
        </div>
        <button type="button" onclick="window.location.reload()" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition cursor-pointer">
            Muat Ulang Halaman
        </button>
    </div>
</div>

<script>
(function() {
    window.__isNormalSubmission = false;
    window.__isLmsSecurityBlocked = false;

    const violationUrl = '{{ route("lms.security.violation", $courseClass->id) }}';
    const csrfToken = '{{ csrf_token() }}';
    const payloadData = {
        blockable_type: '{{ $blockableType ?? "material" }}',
        blockable_id: {{ $blockableId ?? 0 }},
        _token: csrfToken
    };

    function triggerViolation(type, reason) {
        if (window.__isNormalSubmission || window.__isLmsSecurityBlocked) return;
        window.__isLmsSecurityBlocked = true;

        const overlay = document.getElementById('lms-security-violation-overlay');
        if (overlay) {
            overlay.classList.remove('hidden');
        }

        const dataToSend = Object.assign({}, payloadData, {
            violation_type: type,
            reason: reason
        });

        const jsonStr = JSON.stringify(dataToSend);

        // Prioritas kirim via sendBeacon saat tab ditutup/berpindah
        let sent = false;
        if (navigator.sendBeacon) {
            try {
                const blob = new Blob([jsonStr], { type: 'application/json' });
                sent = navigator.sendBeacon(violationUrl, blob);
            } catch(e) {
                sent = false;
            }
        }

        if (!sent) {
            fetch(violationUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: jsonStr,
                keepalive: true
            }).catch(function(err) {
                console.error("Gagal mencatat pelanggaran keamanan:", err);
            });
        }
    }

    // 1. Deteksi Perpindahan Tab / Buka Tab Baru / Minimize Browser
    document.addEventListener('visibilitychange', function() {
        if (document.hidden || document.visibilityState === 'hidden') {
            triggerViolation('tab_switch', 'Terdeteksi membuka tab baru atau meminimalkan jendela web browser.');
        }
    });

    // 2. Deteksi Window Blur (Aplikasi Kehilangan Fokus / Beralih ke Aplikasi Lain)
    window.addEventListener('blur', function() {
        // Hindari false-positive jika yang diklik adalah iframe lokal
        if (document.activeElement && document.activeElement.tagName === 'IFRAME') {
            return;
        }
        triggerViolation('minimize', 'Terdeteksi meminimalkan jendela browser atau beralih fokus ke aplikasi lain.');
    });

    // 3. Deteksi Penutupan Jendela / Unload Sebelum Selesai
    window.addEventListener('pagehide', function() {
        if (!window.__isNormalSubmission && !window.__isLmsSecurityBlocked) {
            triggerViolation('app_close', 'Terdeteksi menutup halaman aplikasi pembelajaran sebelum selesai.');
        }
    });

    // Pasang listener pada seluruh form submission sah agar tidak dianggap pelanggaran
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(function(f) {
            f.addEventListener('submit', function() {
                window.__isNormalSubmission = true;
            });
        });
    });
})();
</script>
@endif
