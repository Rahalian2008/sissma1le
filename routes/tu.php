<?php

use App\Http\Controllers\Ai\AiAnalyticsController;
use App\Http\Controllers\Archive\ArchiveController;
use App\Http\Controllers\Common\DataImportController;
use App\Http\Controllers\Employee\DsoController;
use App\Http\Controllers\Employee\DukController;
use App\Http\Controllers\Employee\EmployeeBookController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Finance\BkuController;
use App\Http\Controllers\Finance\BudgetController;
use App\Http\Controllers\Finance\CashTransactionController;
use App\Http\Controllers\Finance\FinancialReportController;
use App\Http\Controllers\Finance\SubsidiaryBookController;
use App\Http\Controllers\Finance\TaxController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\Letter\DispositionController;
use App\Http\Controllers\Letter\IncomingLetterController;
use App\Http\Controllers\Letter\LetterExpeditionController;
use App\Http\Controllers\Letter\LetterGeneratorController;
use App\Http\Controllers\Letter\MeetingNoteController;
use App\Http\Controllers\Letter\OutgoingLetterController;
use App\Http\Controllers\Letter\SppdController;
use App\Http\Controllers\Master\ReferenceItemController;
use App\Http\Controllers\PublicVerifyController;
use App\Http\Controllers\Sarpras\AssetController;
use App\Http\Controllers\Sarpras\AssetDisposalController;
use App\Http\Controllers\Sarpras\AssetMaintenanceController;
use App\Http\Controllers\Sarpras\BukuIndukBhpController;
use App\Http\Controllers\Sarpras\RoomController;
use App\Http\Controllers\Sarpras\SipdBmdController;
use App\Http\Controllers\Student\AlumniController;
use App\Http\Controllers\Student\BukuIndukController;
use App\Http\Controllers\Student\ClassLearningAgendaController;
use App\Http\Controllers\Student\KlaperController;
use App\Http\Controllers\Student\SpmbController;
use App\Http\Controllers\Student\StudentCohortController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\StudentDiplomaController;
use App\Http\Controllers\Student\StudentDisciplineController;
use App\Http\Controllers\Student\StudentMutationController;
use App\Http\Controllers\Student\StudentRombelController;
use App\Http\Controllers\Student\StudentScholarshipController;
use App\Http\Controllers\TuDashboardController;
use App\Http\Controllers\TuKepegawaianController;
use App\Http\Controllers\TuKesiswaanController;
use App\Http\Controllers\TuKeuanganController;
use App\Http\Controllers\TuPersuratanController;
use App\Http\Controllers\TuSarprasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Warehouse\GoodController;
use App\Http\Controllers\Warehouse\GoodsBorrowingController;
use App\Http\Controllers\Warehouse\GoodsTransactionController;
use App\Http\Controllers\Warehouse\StockOpnameController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes for Buku Tamu Digital & Dokumen Verifikasi
|--------------------------------------------------------------------------
*/
Route::get('/buku-tamu', [GuestController::class, 'kiosk'])->name('guests.kiosk');
Route::get('/guestbook', [GuestController::class, 'kiosk']);
Route::post('/buku-tamu', [GuestController::class, 'storeKiosk'])->name('guests.kiosk.store');
Route::post('/buku-tamu/checkout', [GuestController::class, 'checkout'])->name('guests.kiosk.checkout');
Route::get('/buku-tamu/{ticket}', [GuestController::class, 'card'])->name('guests.card');
Route::get('/buku-tamu/{ticket}/print', [GuestController::class, 'printCard'])->name('guests.print-card');
Route::get('/verify/{type}/{token}', [PublicVerifyController::class, 'verify'])->name('verify.public');

/*
|--------------------------------------------------------------------------
| Authenticated Adm. Ketatausahaan Routes
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'role:super_admin,admin,kepala_sekolah,tu,staff,bendahara,kepala_administrasi,ta_kesiswaan,ta_kepegawaian,ta_persuratan,ta_sarpras,bendahara_bosp,bendahara_bopd,bendahara_gaji,operator_dapodik,operator,tu_piket,wakasek_kesiswaan,kesiswaan',
    'tendik_only',
])->group(function () {
    // Dashboard Utama Adm. Ketatausahaan
    Route::get('/tu', [TuDashboardController::class, 'index'])->name('tu.dashboard');
    Route::get('/adm-tu', [TuDashboardController::class, 'index'])->name('adm_tu.index');

    // 5 Sub-menu Utama Adm. Ketatausahaan
    Route::get('/tu/kesiswaan', [TuKesiswaanController::class, 'index'])->name('tu.kesiswaan.index');
    Route::post('/tu/kesiswaan/pull-from-master', [TuKesiswaanController::class, 'pullFromMaster'])->name('tu.kesiswaan.pull_from_master');
    Route::post('/tu/kesiswaan/clear', [TuKesiswaanController::class, 'clearData'])->name('tu.kesiswaan.clear');

    // Administrasi Legalisir Dokumen Siswa
    Route::prefix('tu/kesiswaan/legalisir')->name('tu.kesiswaan.legalisir.')->group(function () {
        Route::get('/', [TuKesiswaanController::class, 'legalisirIndex'])->name('index');
        Route::post('/', [TuKesiswaanController::class, 'legalisirStore'])->name('store');
        Route::post('/{legalization}/status', [TuKesiswaanController::class, 'legalisirUpdateStatus'])->name('update_status');
        Route::get('/{legalization}/receipt', [TuKesiswaanController::class, 'legalisirPrintReceipt'])->name('print_receipt');
        Route::get('/print-book', [TuKesiswaanController::class, 'legalisirPrintBook'])->name('print_book');
        Route::delete('/{legalization}', [TuKesiswaanController::class, 'legalisirDestroy'])->name('destroy');
    });

    // Cetak Absen Manual Perkelas (Model SIKATAS)
    Route::prefix('tu/kesiswaan/absen-manual')->name('tu.kesiswaan.absen_manual.')->group(function () {
        Route::get('/', [TuKesiswaanController::class, 'manualAttendanceIndex'])->name('index');
        Route::get('/print', [TuKesiswaanController::class, 'manualAttendancePrint'])->name('print');
    });

    Route::get('/tu/kepegawaian', [TuKepegawaianController::class, 'index'])->name('tu.kepegawaian.index');
    Route::post('/tu/kepegawaian/pull-from-master', [TuKepegawaianController::class, 'pullFromMaster'])->name('tu.kepegawaian.pull_from_master');
    Route::post('/tu/kepegawaian/clear', [TuKepegawaianController::class, 'clearData'])->name('tu.kepegawaian.clear');
    Route::get('/tu/persuratan', [TuPersuratanController::class, 'index'])->name('tu.persuratan.index');
    Route::get('/tu/sarpras', [TuSarprasController::class, 'index'])->name('tu.sarpras.index');
    Route::get('/tu/keuangan', [TuKeuanganController::class, 'index'])->name('tu.keuangan.index');

    // Manajemen Pengguna & Akun
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

    // 1. Persuratan & E-Office
    Route::prefix('letters')->name('letters.')->group(function () {
        // Surat Masuk
        Route::get('/incoming', [IncomingLetterController::class, 'index'])->name('incoming.index');
        Route::get('/incoming/create', [IncomingLetterController::class, 'create'])->name('incoming.create');
        Route::post('/incoming', [IncomingLetterController::class, 'store'])->name('incoming.store');
        Route::get('/incoming/{incomingLetter}', [IncomingLetterController::class, 'show'])->name('incoming.show');
        Route::get('/incoming/{incomingLetter}/edit', [IncomingLetterController::class, 'edit'])->name('incoming.edit');
        Route::put('/incoming/{incomingLetter}', [IncomingLetterController::class, 'update'])->name('incoming.update');
        Route::delete('/incoming/{incomingLetter}', [IncomingLetterController::class, 'destroy'])->name('incoming.destroy');
        Route::post('/incoming/{incomingLetter}/archive', [IncomingLetterController::class, 'archive'])->name('incoming.archive');
        Route::get('/incoming/{incomingLetter}/print-disposition', [IncomingLetterController::class, 'printDisposition'])->name('incoming.print-disposition');
        Route::get('/incoming/{letter}/dispositions/create', [DispositionController::class, 'create'])->name('incoming.dispositions.create');
        Route::post('/incoming/{letter}/dispositions', [DispositionController::class, 'store'])->name('incoming.dispositions.store');
        Route::get('/incoming-book/print', [IncomingLetterController::class, 'printBook'])->name('incoming.print-book');
        Route::get('/incoming-agenda/print', [IncomingLetterController::class, 'printBook'])->name('incoming.agenda.print');

        // Surat Keluar
        Route::get('/outgoing', [OutgoingLetterController::class, 'index'])->name('outgoing.index');
        Route::get('/outgoing/create', [OutgoingLetterController::class, 'create'])->name('outgoing.create');
        Route::post('/outgoing', [OutgoingLetterController::class, 'store'])->name('outgoing.store');
        Route::get('/outgoing/{outgoingLetter}', [OutgoingLetterController::class, 'show'])->name('outgoing.show');
        Route::get('/outgoing/{outgoingLetter}/edit', [OutgoingLetterController::class, 'edit'])->name('outgoing.edit');
        Route::put('/outgoing/{outgoingLetter}', [OutgoingLetterController::class, 'update'])->name('outgoing.update');
        Route::delete('/outgoing/{outgoingLetter}', [OutgoingLetterController::class, 'destroy'])->name('outgoing.destroy');
        Route::post('/outgoing/{outgoingLetter}/archive', [OutgoingLetterController::class, 'archive'])->name('outgoing.archive');
        Route::post('/outgoing/{outgoingLetter}/send-expedition', [OutgoingLetterController::class, 'sendToExpedition'])->name('outgoing.send-expedition');
        Route::get('/outgoing-book/print', [OutgoingLetterController::class, 'printBook'])->name('outgoing.print-book');
        Route::get('/outgoing-agenda/print', [OutgoingLetterController::class, 'printBook'])->name('outgoing.agenda.print');
        Route::get('/outgoing/{outgoingLetter}/print', [OutgoingLetterController::class, 'print'])->name('outgoing.print');

        // Disposisi Surat
        Route::get('/dispositions', [DispositionController::class, 'index'])->name('dispositions.index');
        Route::post('/dispositions', [DispositionController::class, 'store'])->name('dispositions.store');
        Route::patch('/dispositions/{disposition}/status', [DispositionController::class, 'updateStatus'])->name('dispositions.update-status');
        Route::delete('/dispositions/{disposition}', [DispositionController::class, 'destroy'])->name('dispositions.destroy');
        Route::get('/dispositions/{disposition}/print', [DispositionController::class, 'print'])->name('dispositions.print');

        // Ekspedisi Surat
        Route::get('/expeditions', [LetterExpeditionController::class, 'index'])->name('expeditions.index');
        Route::post('/expeditions', [LetterExpeditionController::class, 'store'])->name('expeditions.store');
        Route::put('/expeditions/{letterExpedition}', [LetterExpeditionController::class, 'update'])->name('expeditions.update');
        Route::delete('/expeditions/{letterExpedition}', [LetterExpeditionController::class, 'destroy'])->name('expeditions.destroy');
        Route::get('/expeditions/print', [LetterExpeditionController::class, 'print'])->name('expeditions.print');

        // Notula Rapat Dinas
        Route::get('/meeting-notes', [MeetingNoteController::class, 'index'])->name('meeting-notes.index');
        Route::post('/meeting-notes', [MeetingNoteController::class, 'store'])->name('meeting-notes.store');
        Route::put('/meeting-notes/{meetingNote}', [MeetingNoteController::class, 'update'])->name('meeting-notes.update');
        Route::delete('/meeting-notes/{meetingNote}', [MeetingNoteController::class, 'destroy'])->name('meeting-notes.destroy');
        Route::get('/meeting-notes/{meetingNote}/print', [MeetingNoteController::class, 'print'])->name('meeting-notes.print');

        // SPPD (Surat Perintah Perjalanan Dinas)
        Route::get('/sppds', [SppdController::class, 'index'])->name('sppds.index');
        Route::post('/sppds', [SppdController::class, 'store'])->name('sppds.store');
        Route::get('/sppds/{sppd}', [SppdController::class, 'show'])->name('sppds.show');
        Route::put('/sppds/{sppd}', [SppdController::class, 'update'])->name('sppds.update');
        Route::delete('/sppds/{sppd}', [SppdController::class, 'destroy'])->name('sppds.destroy');
        Route::get('/sppds/{sppd}/print', [SppdController::class, 'print'])->name('sppds.print');

        // Standalone letters.sppd aliases
        Route::get('/sppd', [SppdController::class, 'index'])->name('sppd.index');
        Route::get('/sppd/create', [SppdController::class, 'create'])->name('sppd.create');
        Route::post('/sppd', [SppdController::class, 'store'])->name('sppd.store');
        Route::get('/sppd/{sppd}', [SppdController::class, 'show'])->name('sppd.show');
        Route::get('/sppd/{sppd}/edit', [SppdController::class, 'edit'])->name('sppd.edit');
        Route::put('/sppd/{sppd}', [SppdController::class, 'update'])->name('sppd.update');
        Route::delete('/sppd/{sppd}', [SppdController::class, 'destroy'])->name('sppd.destroy');
        Route::get('/sppd/{sppd}/print', [SppdController::class, 'print'])->name('sppd.print');

        // Klasifikasi Surat
        Route::get('/classifications', [ReferenceItemController::class, 'index'])->name('classifications.index');

        // Pembuat Surat (Letter Generator & Templates)
        Route::get('/generator', [LetterGeneratorController::class, 'index'])->name('generator.index');
        Route::get('/generator/create/{template}', [LetterGeneratorController::class, 'create'])->name('generator.create');
        Route::match(['get', 'post'], '/generator/generate/{template?}', [LetterGeneratorController::class, 'generate'])->name('generator.generate');
    });

    // Disposisi Standalone Route
    Route::resource('dispositions', DispositionController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/dispositions/{disposition}/follow-up', [DispositionController::class, 'updateFollowUp'])->name('dispositions.follow-up');
    Route::get('/dispositions/{disposition}/print', [DispositionController::class, 'print'])->name('dispositions.print');

    // SPPD Standalone Route
    Route::resource('sppds', SppdController::class);
    Route::get('/sppds/{sppd}/print', [SppdController::class, 'print'])->name('sppds.print');

    // Arsip Dinamis & Digital
    Route::prefix('archives')->name('archives.')->group(function () {
        Route::get('/', [ArchiveController::class, 'index'])->name('index');
        Route::post('/', [ArchiveController::class, 'store'])->name('store');
        Route::get('/{archive}', [ArchiveController::class, 'show'])->name('show');
        Route::put('/{archive}', [ArchiveController::class, 'update'])->name('update');
        Route::delete('/{archive}', [ArchiveController::class, 'destroy'])->name('destroy');
        Route::get('/{archive}/download', [ArchiveController::class, 'download'])->name('download');
    });

    // 2. Sarana & Prasarana (Sarpras)
    Route::prefix('sarpras')->name('sarpras.')->group(function () {
        Route::get('/sipd-bmd', [SipdBmdController::class, 'index'])->name('sipd-bmd.index');
        Route::get('/sipd-bmd/search', [SipdBmdController::class, 'search'])->name('sipd-bmd.search');
        Route::get('/sipd-bmd/online-search', [SipdBmdController::class, 'onlineSearch'])->name('sipd-bmd.online-search');
        Route::get('/sim-bmd/search', [AssetController::class, 'searchSimBmd'])->name('sim-bmd.search');

        Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
        Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

        Route::get('/kir', [AssetController::class, 'kir'])->name('kir.index');
        Route::get('/kir/{room}/print', [AssetController::class, 'printKir'])->name('kir.print');
        Route::get('/kib/print', [AssetController::class, 'printKib'])->name('kib.print');

        Route::get('/assets/labels/custom', [AssetController::class, 'customLabelIndex'])->name('assets.labels.custom');
        Route::get('/assets/labels/print', [AssetController::class, 'printLabels'])->name('assets.labels.print');
        Route::get('/assets/{asset}/label', [AssetController::class, 'label'])->name('assets.label');
        Route::resource('assets', AssetController::class);

        // Buku Induk Inventaris (BII / BIBI)
        Route::get('/bibi', [AssetController::class, 'bibi'])->name('bibi.index');
        Route::get('/bii', [AssetController::class, 'bibi'])->name('bii.index');
        Route::get('/bibi/print', [AssetController::class, 'printBibi'])->name('bibi.print');
        Route::get('/bii/print', [AssetController::class, 'printBibi'])->name('bii.print');

        // Buku Induk Barang Habis Pakai / Non-Inventaris (BIBHP)
        Route::get('/bibhp', [BukuIndukBhpController::class, 'index'])->name('bibhp.index');
        Route::get('/bibhp/print', [BukuIndukBhpController::class, 'print'])->name('bibhp.print');
        Route::get('/bibhp/export-csv', [BukuIndukBhpController::class, 'exportCsv'])->name('bibhp.export-csv');

        // Pemeliharaan & Servis Aset
        Route::get('/maintenance', [AssetMaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('/maintenance', [AssetMaintenanceController::class, 'store'])->name('maintenance.store');
        Route::patch('/maintenance/{maintenance}/status', [AssetMaintenanceController::class, 'updateStatus'])->name('maintenance.update-status');

        // Usulan Penghapusan Barang Inventaris (Afkir / Disposal BMD)
        Route::get('/disposals', [AssetDisposalController::class, 'index'])->name('disposals.index');
        Route::post('/disposals', [AssetDisposalController::class, 'store'])->name('disposals.store');
        Route::patch('/disposals/{disposal}/status', [AssetDisposalController::class, 'updateStatus'])->name('disposals.update-status');
        Route::get('/disposals/{disposal}/print', [AssetDisposalController::class, 'print'])->name('disposals.print');
        Route::post('/disposals/{disposal}/archive', [AssetDisposalController::class, 'archive'])->name('disposals.archive');
        Route::delete('/disposals/{disposal}', [AssetDisposalController::class, 'destroy'])->name('disposals.destroy');
    });

    // 3. Gudang & Logistik ATK (Warehouse)
    Route::prefix('warehouse')->name('warehouse.')->group(function () {
        Route::get('/goods', [GoodController::class, 'index'])->name('goods.index');
        Route::post('/goods', [GoodController::class, 'store'])->name('goods.store');
        Route::put('/goods/{good}', [GoodController::class, 'update'])->name('goods.update');
        Route::get('/goods/{good}/card', [GoodController::class, 'card'])->name('goods.card');
        Route::delete('/goods/{good}', [GoodController::class, 'destroy'])->name('goods.destroy');

        Route::get('/receipts', [GoodsTransactionController::class, 'receipts'])->name('receipts.index');
        Route::post('/receipts', [GoodsTransactionController::class, 'storeReceipt'])->name('receipts.store');
        Route::put('/receipts/{receipt}', [GoodsTransactionController::class, 'updateReceipt'])->name('receipts.update');
        Route::delete('/receipts/{receipt}', [GoodsTransactionController::class, 'destroyReceipt'])->name('receipts.destroy');
        Route::get('/receipts/{receipt}/print', [GoodsTransactionController::class, 'printReceipt'])->name('receipts.print');
        Route::post('/receipts/{receipt}/archive', [GoodsTransactionController::class, 'archiveReceipt'])->name('receipts.archive');

        Route::get('/issuances', [GoodsTransactionController::class, 'issuances'])->name('issuances.index');
        Route::post('/issuances', [GoodsTransactionController::class, 'storeIssuance'])->name('issuances.store');
        Route::put('/issuances/{issuance}', [GoodsTransactionController::class, 'updateIssuance'])->name('issuances.update');
        Route::delete('/issuances/{issuance}', [GoodsTransactionController::class, 'destroyIssuance'])->name('issuances.destroy');
        Route::get('/issuances/{issuance}/print', [GoodsTransactionController::class, 'printIssuance'])->name('issuances.print');

        Route::get('/borrowings', [GoodsBorrowingController::class, 'index'])->name('borrowings.index');
        Route::post('/borrowings', [GoodsBorrowingController::class, 'store'])->name('borrowings.store');
        Route::patch('/borrowings/{goodsBorrowing}/return', [GoodsBorrowingController::class, 'returnGood'])->name('borrowings.return');
        Route::delete('/borrowings/{goodsBorrowing}', [GoodsBorrowingController::class, 'destroy'])->name('borrowings.destroy');

        Route::get('/stock-opnames', [StockOpnameController::class, 'index'])->name('stock-opnames.index');
        Route::post('/stock-opnames', [StockOpnameController::class, 'store'])->name('stock-opnames.store');
        Route::post('/opnames', [StockOpnameController::class, 'store'])->name('opnames.store');
        Route::get('/stock-opnames/print', [StockOpnameController::class, 'print'])->name('stock-opnames.print');
    });

    // 4. Kepegawaian (PTK)
    Route::prefix('employees')->name('employees.')->group(function () {
        // Buku Induk Pegawai
        Route::get('/buku-induk', [EmployeeBookController::class, 'bukuInduk'])->name('buku-induk.index');
        Route::get('/buku-induk/print', [EmployeeBookController::class, 'printBukuInduk'])->name('buku-induk.print');
        Route::get('/buku-induk/{employee}', [EmployeeBookController::class, 'showBukuInduk'])->name('buku-induk.show');
        Route::get('/buku-induk/{employee}/print', [EmployeeBookController::class, 'printEmployeeDetail'])->name('buku-induk.print-single');

        // DUK (Daftar Urut Kepangkatan)
        Route::get('/duk', [DukController::class, 'index'])->name('duk.index');
        Route::get('/duk/print', [DukController::class, 'print'])->name('duk.print');
        Route::get('/duk/export-csv', [DukController::class, 'exportCsv'])->name('duk.export-csv');

        // DSO (Daftar Susunan Organisasi)
        Route::get('/dso', [DsoController::class, 'index'])->name('dso.index');
        Route::post('/dso', [DsoController::class, 'store'])->name('dso.store');
        Route::put('/dso/{organizationStructure}', [DsoController::class, 'update'])->name('dso.update');
        Route::delete('/dso/{organizationStructure}', [DsoController::class, 'destroy'])->name('dso.destroy');
        Route::get('/dso/print', [DsoController::class, 'print'])->name('dso.print');

        // Buku Kontrol Kenaikan Pangkat (KP)
        Route::get('/control/kp', [EmployeeBookController::class, 'controlKp'])->name('control.kp');
        Route::get('/control/kp/print', [EmployeeBookController::class, 'printControlKp'])->name('control.kp.print');
        Route::get('/kontrol-pangkat', [EmployeeBookController::class, 'controlKp'])->name('kontrol-pangkat.index');
        Route::get('/kontrol-pangkat/print', [EmployeeBookController::class, 'printControlKp'])->name('kontrol-pangkat.print');
        Route::patch('/kontrol-pangkat/{employee}/status', [EmployeeBookController::class, 'updateStatusKp'])->name('kontrol-pangkat.update');
        Route::post('/kontrol-pangkat/{employee}/apply-sk', [EmployeeBookController::class, 'applySkPangkat'])->name('kontrol-pangkat.apply-sk');

        // Buku Kontrol Kenaikan Gaji Berkala (KGB)
        Route::get('/control/kgb', [EmployeeBookController::class, 'controlKgb'])->name('control.kgb');
        Route::get('/control/kgb/print', [EmployeeBookController::class, 'printControlKgb'])->name('control.kgb.print');
        Route::get('/kontrol-kgb', [EmployeeBookController::class, 'controlKgb'])->name('kontrol-kgb.index');
        Route::get('/kontrol-kgb/print', [EmployeeBookController::class, 'printControlKgb'])->name('kontrol-kgb.print');
        Route::patch('/kontrol-kgb/{employee}', [EmployeeBookController::class, 'updateKgb'])->name('kontrol-kgb.update');
        Route::get('/kontrol-kgb/{employee}/surat', [EmployeeBookController::class, 'printSuratKgb'])->name('kontrol-kgb.surat');

        // Buku Kontrol Masa Kontrak PPPK
        Route::get('/control/pppk', [EmployeeBookController::class, 'controlPppk'])->name('control.pppk');
        Route::get('/control/pppk/print', [EmployeeBookController::class, 'printControlPppk'])->name('control.pppk.print');
        Route::get('/kontrol-pppk', [EmployeeBookController::class, 'controlPppk'])->name('kontrol-pppk.index');
        Route::get('/kontrol-pppk/print', [EmployeeBookController::class, 'printControlPppk'])->name('kontrol-pppk.print');
        Route::patch('/kontrol-pppk/{employee}', [EmployeeBookController::class, 'updateKontrakPppk'])->name('kontrol-pppk.update');

        // Buku Kontrol Batas Pensiun (BUP)
        Route::get('/control/pension', [EmployeeBookController::class, 'controlPension'])->name('control.pension');
        Route::get('/control/pension/print', [EmployeeBookController::class, 'printControlPension'])->name('control.pension.print');
        Route::get('/kontrol-pensiun', [EmployeeBookController::class, 'controlPension'])->name('kontrol-pensiun.index');
        Route::get('/kontrol-pensiun/print', [EmployeeBookController::class, 'printControlPension'])->name('kontrol-pensiun.print');
        Route::patch('/kontrol-pensiun/{employee}', [EmployeeBookController::class, 'updateStatusPensiun'])->name('kontrol-pensiun.update');
        Route::get('/kontrol-pensiun/{employee}/surat', [EmployeeBookController::class, 'printSuratPensiun'])->name('kontrol-pensiun.surat');

        // Buku Kontrol Cuti Pegawai
        Route::get('/control/leaves', [EmployeeBookController::class, 'controlLeaves'])->name('control.leaves');
        Route::get('/control/leaves/print', [EmployeeBookController::class, 'printControlLeaves'])->name('control.leaves.print');
        Route::get('/kontrol-cuti', [EmployeeBookController::class, 'controlLeaves'])->name('kontrol-cuti.index');
        Route::get('/kontrol-cuti/print', [EmployeeBookController::class, 'printControlLeaves'])->name('kontrol-cuti.print');
        Route::post('/kontrol-cuti', [EmployeeBookController::class, 'storeCuti'])->name('kontrol-cuti.store');
        Route::get('/kontrol-cuti/{leave}/surat', [EmployeeBookController::class, 'printSuratCuti'])->name('kontrol-cuti.surat');

        // Buku Kontrol Diklat Pegawai
        Route::get('/kontrol-diklat', [EmployeeBookController::class, 'kontrolDiklat'])->name('kontrol-diklat.index');
        Route::post('/kontrol-diklat', [EmployeeBookController::class, 'storeDiklat'])->name('kontrol-diklat.store');
        Route::delete('/kontrol-diklat/{history}', [EmployeeBookController::class, 'destroyDiklat'])->name('kontrol-diklat.destroy');
        Route::get('/kontrol-diklat/print', [EmployeeBookController::class, 'printKontrolDiklat'])->name('kontrol-diklat.print');

        // Manajemen Cuti Pegawai
        Route::get('/leaves', [EmployeeController::class, 'leaves'])->name('leaves.index');
        Route::post('/leaves', [EmployeeController::class, 'storeLeave'])->name('leaves.store');
        Route::patch('/leaves/{employeeLeave}/status', [EmployeeController::class, 'updateLeaveStatus'])->name('leaves.update-status');
        Route::get('/leaves/{employeeLeave}/print', [EmployeeController::class, 'printLeave'])->name('leaves.print');

        // Riwayat Karir & Diklat
        Route::post('/{employee}/histories', [EmployeeController::class, 'storeHistory'])->name('histories.store');
        Route::delete('/histories/{employeeHistory}', [EmployeeController::class, 'destroyHistory'])->name('histories.destroy');

        // Cetak & Export
        Route::get('/print', [EmployeeController::class, 'print'])->name('print');
        Route::get('/export-csv', [EmployeeController::class, 'exportCsv'])->name('export-csv');
    });
    Route::resource('employees', EmployeeController::class);

    // Standalone DUK & DSO aliases & Master References
    Route::get('/duk', [DukController::class, 'index'])->name('duk.index');
    Route::get('/duk/print', [DukController::class, 'print'])->name('duk.print');
    Route::get('/duk/export-csv', [DukController::class, 'exportCsv'])->name('duk.export-csv');
    Route::get('/dso', [DsoController::class, 'index'])->name('dso.index');
    Route::post('/dso', [DsoController::class, 'store'])->name('dso.store');
    Route::put('/dso/{organizationStructure}', [DsoController::class, 'update'])->name('dso.update');
    Route::delete('/dso/{organizationStructure}', [DsoController::class, 'destroy'])->name('dso.destroy');
    Route::get('/dso/print', [DsoController::class, 'print'])->name('dso.print');
    Route::resource('master-references', ReferenceItemController::class)->names('master.references');

    // 5. Keuangan Sekolah (Finance / Bendahara BOSP)
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
        Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
        Route::put('/budgets/{budget}', [BudgetController::class, 'update'])->name('budgets.update');
        Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');
        Route::post('/budgets/{budget}/items', [BudgetController::class, 'storeItem'])->name('budgets.items.store');
        Route::put('/budgets/items/{budgetItem}', [BudgetController::class, 'updateItem'])->name('budgets.items.update');
        Route::delete('/budgets/items/{budgetItem}', [BudgetController::class, 'destroyItem'])->name('budgets.items.destroy');

        Route::get('/transactions', [CashTransactionController::class, 'index'])->name('transactions.index');
        Route::post('/transactions', [CashTransactionController::class, 'store'])->name('transactions.store');
        Route::put('/transactions/{cashTransaction}', [CashTransactionController::class, 'update'])->name('transactions.update');
        Route::get('/transactions/{cashTransaction}/print-receipt', [CashTransactionController::class, 'printReceipt'])->name('transactions.receipt.print');
        Route::post('/transactions/{cashTransaction}/archive-receipt', [CashTransactionController::class, 'archiveReceipt'])->name('transactions.receipt.archive');
        Route::delete('/transactions/{cashTransaction}', [CashTransactionController::class, 'destroy'])->name('transactions.destroy');

        Route::get('/bku', [BkuController::class, 'index'])->name('bku.index');
        Route::get('/bku/print', [BkuController::class, 'print'])->name('bku.print');
        Route::get('/bku/export-csv', [BkuController::class, 'exportCsv'])->name('bku.export-csv');

        // Buku Pembantu Kas (Tunai) & Buku Pembantu Bank
        Route::get('/subsidiary', [SubsidiaryBookController::class, 'index'])->name('subsidiary.index');
        Route::get('/subsidiary/print', [SubsidiaryBookController::class, 'print'])->name('subsidiary.print');

        Route::get('/taxes', [TaxController::class, 'index'])->name('taxes.index');
        Route::post('/taxes', [TaxController::class, 'store'])->name('taxes.store');
        Route::put('/taxes/{tax}', [TaxController::class, 'update'])->name('taxes.update');
        Route::delete('/taxes/{tax}', [TaxController::class, 'destroy'])->name('taxes.destroy');
        Route::get('/taxes/print', [TaxController::class, 'print'])->name('taxes.print');
        Route::patch('/taxes/{tax}/status', [TaxController::class, 'updateStatus'])->name('taxes.update-status');

        Route::get('/reports', [FinancialReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/print', [FinancialReportController::class, 'print'])->name('reports.print');
        Route::get('/reports/reconciliation', [FinancialReportController::class, 'reconciliation'])->name('reconciliation.index');
        Route::get('/reports/reconciliation/print', [FinancialReportController::class, 'printReconciliation'])->name('reconciliation.print');
        Route::post('/reports/reconciliation/archive', [FinancialReportController::class, 'archiveReconciliation'])->name('reconciliation.archive');
    });

    // 6. Manajemen Buku Tamu Terpadu
    Route::prefix('guests')->name('guests.')->group(function () {
        Route::get('/', [GuestController::class, 'index'])->name('index');
        Route::get('/report/print', [GuestController::class, 'printReport'])->name('report.print');
        Route::get('/{guest}', [GuestController::class, 'show'])->name('show');
        Route::put('/{guest}', [GuestController::class, 'update'])->name('update');
        Route::patch('/{guest}/status', [GuestController::class, 'updateStatus'])->name('status');
        Route::delete('/{guest}', [GuestController::class, 'destroy'])->name('destroy');
    });

    // 7. Administrasi Kesiswaan & Arsip Siswa (Buku Induk, Klaper, Mutasi, Ijazah, Alumni, PPDB)
    Route::get('/buku-induk', [BukuIndukController::class, 'index'])->name('buku-induk.index');
    Route::get('/buku-induk/{student}', [BukuIndukController::class, 'show'])->name('buku-induk.show');
    Route::get('/buku-induk/{student}/print', [BukuIndukController::class, 'print'])->name('buku-induk.print');

    Route::get('/klaper', [KlaperController::class, 'index'])->name('klaper.index');
    Route::get('/klaper/print', [KlaperController::class, 'print'])->name('klaper.print');
    Route::get('/klaper/export-csv', [KlaperController::class, 'exportCsv'])->name('klaper.export-csv');

    Route::get('/mutations', [StudentMutationController::class, 'index'])->name('mutations.index');
    Route::post('/mutations', [StudentMutationController::class, 'store'])->name('mutations.store');
    Route::put('/mutations/{mutation}', [StudentMutationController::class, 'update'])->name('mutations.update');
    Route::delete('/mutations/{mutation}', [StudentMutationController::class, 'destroy'])->name('mutations.destroy');
    Route::get('/mutations/print-book', [StudentMutationController::class, 'printBook'])->name('mutations.print-book');
    Route::get('/mutations/{mutation}/print-letter', [StudentMutationController::class, 'printLetter'])->name('mutations.print-letter');

    Route::get('/students/diplomas', [StudentDiplomaController::class, 'index'])->name('students.diplomas.index');
    Route::post('/students/diplomas', [StudentDiplomaController::class, 'store'])->name('students.diplomas.store');
    Route::put('/students/diplomas/{diploma}', [StudentDiplomaController::class, 'update'])->name('students.diplomas.update');
    Route::delete('/students/diplomas/{diploma}', [StudentDiplomaController::class, 'destroy'])->name('students.diplomas.destroy');
    Route::get('/students/diplomas/print-book', [StudentDiplomaController::class, 'printBook'])->name('students.diplomas.print-book');
    Route::get('/students/diplomas/{diploma}/print-receipt', [StudentDiplomaController::class, 'printReceipt'])->name('students.diplomas.print-receipt');

    Route::get('/alumni', [AlumniController::class, 'index'])->name('alumni.index');
    Route::post('/alumni', [AlumniController::class, 'store'])->name('alumni.store');

    // SPMB (Sistem Penerimaan Murid Baru SMA & Jalur Nilai TKA)
    Route::prefix('spmb')->name('spmb.')->group(function () {
        Route::get('/', [SpmbController::class, 'index'])->name('index');
        Route::post('/', [SpmbController::class, 'store'])->name('store');
        Route::get('/tka', [SpmbController::class, 'tkaIndex'])->name('tka.index');
        Route::post('/tka/calculate-rankings', [SpmbController::class, 'calculateTkaRankings'])->name('tka.calculate_rankings');
        Route::post('/tka/auto-admit', [SpmbController::class, 'autoAdmitTka'])->name('tka.auto_admit');
        Route::get('/tracks', [SpmbController::class, 'tracksIndex'])->name('tracks.index');
        Route::post('/tracks', [SpmbController::class, 'storeTrack'])->name('tracks.store');
        Route::put('/tracks/{track}', [SpmbController::class, 'updateTrack'])->name('tracks.update');
        Route::delete('/tracks/{track}', [SpmbController::class, 'destroyTrack'])->name('tracks.destroy');

        // Manajemen Jadwal Gelombang Pendaftaran SPMB
        Route::get('/waves', [SpmbController::class, 'wavesIndex'])->name('waves.index');
        Route::post('/waves', [SpmbController::class, 'storeWave'])->name('waves.store');
        Route::put('/waves/{wave}', [SpmbController::class, 'updateWave'])->name('waves.update');
        Route::patch('/waves/{wave}/toggle-status', [SpmbController::class, 'toggleWaveStatus'])->name('waves.toggle-status');
        Route::delete('/waves/{wave}', [SpmbController::class, 'destroyWave'])->name('waves.destroy');

        // Update Daya Tampung Total Sekolah & Kuota Jalur
        Route::post('/capacity', [SpmbController::class, 'updateCapacity'])->name('capacity.update');
        Route::get('/print-recap', [SpmbController::class, 'printRecap'])->name('print-recap');
        Route::get('/export-csv', [SpmbController::class, 'exportCsv'])->name('export-csv');
        Route::get('/{spmb}', [SpmbController::class, 'show'])->name('show');
        Route::put('/{spmb}', [SpmbController::class, 'update'])->name('update');
        Route::patch('/{spmb}/status', [SpmbController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{spmb}/tka-score', [SpmbController::class, 'updateTkaScore'])->name('tka.update_score');
        Route::post('/{spmb}/promote', [SpmbController::class, 'promoteToStudent'])->name('promote');
        Route::get('/{spmb}/print-card', [SpmbController::class, 'printCard'])->name('print-card');
        Route::get('/{spmb}/print-letter', [SpmbController::class, 'printLetter'])->name('print-letter');
        Route::delete('/{spmb}', [SpmbController::class, 'destroy'])->name('destroy');
    });

    // Alias & Backward Compatibility PPDB
    Route::get('/ppdb', [SpmbController::class, 'index'])->name('ppdb.index');
    Route::post('/ppdb', [SpmbController::class, 'store'])->name('ppdb.store');
    Route::put('/ppdb/{ppdbRegistration}', [SpmbController::class, 'update'])->name('ppdb.update');
    Route::patch('/ppdb/{ppdbRegistration}/status', [SpmbController::class, 'updateStatus'])->name('ppdb.update-status');
    Route::delete('/ppdb/{ppdbRegistration}', [SpmbController::class, 'destroy'])->name('ppdb.destroy');

    Route::get('/students/learning-agendas', [ClassLearningAgendaController::class, 'index'])->name('students.learning-agendas.index');
    Route::post('/students/learning-agendas', [ClassLearningAgendaController::class, 'store'])->name('students.learning-agendas.store');
    Route::put('/students/learning-agendas/{learningAgenda}', [ClassLearningAgendaController::class, 'update'])->name('students.learning-agendas.update');
    Route::delete('/students/learning-agendas/{learningAgenda}', [ClassLearningAgendaController::class, 'destroy'])->name('students.learning-agendas.destroy');
    Route::get('/students/learning-agendas/print', [ClassLearningAgendaController::class, 'print'])->name('students.learning-agendas.print');

    // Siswa Administrasi & Beasiswa
    Route::get('/tu-students/demographics', [StudentController::class, 'demographics'])->name('students.demographics');
    Route::get('/tu-students/demographics/print', [StudentController::class, 'printDemographics'])->name('students.demographics.print');
    Route::get('/tu-students/export-csv', [StudentController::class, 'exportCsv'])->name('students.export-csv');
    Route::resource('tu-students', StudentController::class)->parameters(['tu-students' => 'student'])->names('students');
    Route::get('/sis', [TuKesiswaanController::class, 'index'])->name('sis.index');
    Route::get('/scholarships', [StudentDisciplineController::class, 'scholarships'])->name('scholarships.index');

    // Analisis Kohort Siswa
    Route::get('/students/cohort', [StudentCohortController::class, 'index'])->name('students.cohort.index');
    Route::get('/students/cohort/print', [StudentCohortController::class, 'print'])->name('students.cohort.print');

    // Penataan Rombel & Kenaikan Kelas
    Route::get('/students/rombel', [StudentRombelController::class, 'index'])->name('students.rombel.index');
    Route::post('/students/rombel/move', [StudentRombelController::class, 'moveStudent'])->name('students.rombel.move');
    Route::post('/students/rombel/promote', [StudentRombelController::class, 'promoteRombel'])->name('students.rombel.promote');
    Route::get('/students/rombel/print', [StudentRombelController::class, 'printRombel'])->name('students.rombel.print');

    // Beasiswa PIP & KETM Siswa
    Route::get('/students/scholarships', [StudentScholarshipController::class, 'index'])->name('students.scholarships.index');
    Route::post('/students/scholarships', [StudentScholarshipController::class, 'store'])->name('students.scholarships.store');
    Route::put('/students/scholarships/{scholarship}', [StudentScholarshipController::class, 'update'])->name('students.scholarships.update');
    Route::delete('/students/scholarships/{scholarship}', [StudentScholarshipController::class, 'destroy'])->name('students.scholarships.destroy');
    Route::get('/students/scholarships/print', [StudentScholarshipController::class, 'print'])->name('students.scholarships.print');

    // 8. Asisten AI Tata Usaha
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/', [AiAnalyticsController::class, 'index'])->name('index');
        Route::post('/generate', [AiAnalyticsController::class, 'generate'])->name('generate');
        Route::post('/test-connection', [AiAnalyticsController::class, 'testConnection'])->name('test-connection');
        Route::get('/reports/{aiReport}', [AiAnalyticsController::class, 'show'])->name('show');
        Route::get('/reports/{aiReport}/print', [AiAnalyticsController::class, 'print'])->name('print');
        Route::post('/reports/{aiReport}/archive', [AiAnalyticsController::class, 'saveToArchive'])->name('archive');
        Route::delete('/reports/{aiReport}', [AiAnalyticsController::class, 'destroy'])->name('destroy');
        Route::put('/settings', [AiAnalyticsController::class, 'updateSettings'])->name('settings.update');
    });

    // Universal CSV Data Import for TU
    Route::get('/tu-import/template/{type}', [DataImportController::class, 'downloadTemplate'])->name('tu.import.template');
    Route::post('/tu-import/{type}', [DataImportController::class, 'import'])->name('tu.import.process');
    Route::get('/import/template/{type}', [DataImportController::class, 'downloadTemplate'])->name('import.template');
    Route::post('/import/{type}', [DataImportController::class, 'import'])->name('import.process');
});
