<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GuestController extends Controller
{
    /**
     * Kiosk Mini Aplikasi Buku Tamu Publik (Akses Mandiri oleh Tamu / Layar Tablet Resepsionis)
     */
    public function kiosk(): View
    {
        $school = SchoolIdentity::current();
        $institutionTypes = [
            'Dinas Pendidikan Prov. Jawa Barat',
            'SPPG',
            'Kementerian / Instansi Pemerintah',
            'Pengawas Pembina Sekolah',
            'Komite Sekolah',
            'Orang Tua / Wali Siswa',
            'Perusahaan / Dunia Usaha / Vendor',
            'Lembaga Perguruan Tinggi / Kampus',
            'Media Massa / Pers',
            'Masyarakat Umum / Alumni',
        ];

        $departments = [
            'Kepala Sekolah',
            'Kepala Tata Usaha',
            'Sub. Bagian Kesiswaan',
            'Sub. Bagian Kepegawaian',
            'Sub. Bagian Humas',
            'Bendahara Sekolah',
            'Pengelola Sarpras',
            'Koord. BP/BK',
            'Koordinator MBG',
            'Pembina Ekstrakurikuler',
            'Guru / Wali Kelas',
            'Layanan Administrasi TU',
        ];

        $purposes = [
            'Kedinasan / Rapat / Koordinasi Resmi',
            'Penyerahan MBG',
            'Konsultasi Perkembangan / Masalah Siswa',
            'Legalisir Ijazah / Pengambilan Dokumen',
            'Penawaran Kerjasama / Barang & Jasa',
            'Pembayaran / Urusan Keuangan Sekolah',
            'Peliputan Berita / Kunjungan Jurnalistik',
            'Kunjungan Silaturahmi / Pribadi',
            'Keperluan Lainnya',
        ];

        return view('guests.kiosk', compact('school', 'institutionTypes', 'departments', 'purposes'));
    }

    /**
     * Simpan Kunjungan dari Mini Aplikasi Kiosk Tamu
     */
    public function storeKiosk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'gender' => ['required', 'string', 'in:Laki-laki,Perempuan'],
            'identity_type' => ['required', 'string', 'in:KTP,SIM,NIP,Paspor,Lainnya'],
            'identity_number' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'institution' => ['required', 'string', 'max:150'],
            'institution_type' => ['required', 'string', 'max:100'],
            'destination_department' => ['required', 'string', 'max:100'],
            'destination_person_name' => ['nullable', 'string', 'max:150'],
            'purpose' => ['required', 'string', 'max:100'],
            'purpose_detail' => ['required', 'string'],
            'number_of_guests' => ['required', 'integer', 'min:1', 'max:100'],
            'vehicle_type' => ['nullable', 'string', 'max:50'],
            'vehicle_license_plate' => ['nullable', 'string', 'max:30'],
            'selfie_image' => ['nullable', 'string'], // base64 dari kamera selfie
            'selfie_file' => ['nullable', 'image', 'max:5120'], // file upload alternatif
            'signature_data' => ['nullable', 'string'], // base64 digital signature
        ]);

        $ticketNumber = Guest::generateTicketNumber();
        $qrToken = Str::uuid()->toString();

        $selfiePath = null;

        // Simpan Foto Selfie (dari webcam/kamera tablet atau upload file)
        if ($request->hasFile('selfie_file')) {
            $path = $request->file('selfie_file')->store('guests/selfies', 'public');
            $selfiePath = Storage::url($path);
        } elseif (! empty($validated['selfie_image']) && str_starts_with($validated['selfie_image'], 'data:image')) {
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $validated['selfie_image']);
            $decoded = base64_decode($imageData);
            if ($decoded !== false) {
                $filename = 'guests/selfies/selfie_'.date('Ymd_His').'_'.Str::random(8).'.jpg';
                Storage::disk('public')->put($filename, $decoded);
                $selfiePath = Storage::url($filename);
            }
        }

        $signaturePath = null;
        if (! empty($validated['signature_data']) && str_starts_with($validated['signature_data'], 'data:image')) {
            $sigData = preg_replace('/^data:image\/\w+;base64,/', '', $validated['signature_data']);
            $decodedSig = base64_decode($sigData);
            if ($decodedSig !== false) {
                $sigFilename = 'guests/signatures/sig_'.date('Ymd_His').'_'.Str::random(8).'.png';
                Storage::disk('public')->put($sigFilename, $decodedSig);
                $signaturePath = Storage::url($sigFilename);
            }
        }

        $guest = Guest::create([
            'ticket_number' => $ticketNumber,
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'identity_type' => $validated['identity_type'],
            'identity_number' => $validated['identity_number'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'institution' => $validated['institution'],
            'institution_type' => $validated['institution_type'],
            'destination_department' => $validated['destination_department'],
            'destination_person_name' => $validated['destination_person_name'] ?? null,
            'purpose' => $validated['purpose'],
            'purpose_detail' => $validated['purpose_detail'],
            'number_of_guests' => $validated['number_of_guests'],
            'vehicle_type' => $validated['vehicle_type'] ?? null,
            'vehicle_license_plate' => $validated['vehicle_license_plate'] ?? null,
            'selfie_path' => $selfiePath,
            'signature_path' => $signaturePath,
            'status' => 'menunggu',
            'check_in_at' => Carbon::now(),
            'qr_code_token' => $qrToken,
        ]);

        return redirect()->route('guests.card', ['ticket' => $guest->ticket_number])
            ->with('success', "Terima kasih! Kunjungan Anda telah tercatat dengan Nomor Tiket {$guest->ticket_number}. Silakan tunjukkan kartu ini kepada petugas resepsionis/keamanan sekolah.");
    }

    /**
     * Tampilan Kartu Tamu Digital (Guest Pass) Publik
     */
    public function card(string $ticket): View
    {
        $guest = Guest::where('ticket_number', $ticket)->firstOrFail();
        $school = SchoolIdentity::current();

        $verificationUrl = route('guests.card', ['ticket' => $guest->ticket_number]);
        $qrCodeSvg = QrCodeService::svg($verificationUrl, 160);

        return view('guests.card', compact('guest', 'school', 'qrCodeSvg'));
    }

    /**
     * Cetak Kartu / ID Badge Tamu
     */
    public function printCard(string $ticket): View
    {
        $guest = Guest::where('ticket_number', $ticket)->firstOrFail();
        $school = SchoolIdentity::current();

        $verificationUrl = route('guests.card', ['ticket' => $guest->ticket_number]);
        $qrCodeSvg = QrCodeService::svg($verificationUrl, 130);

        return view('guests.card-print', compact('guest', 'school', 'qrCodeSvg'));
    }

    /**
     * Fitur Check-out Mandiri Tamu (Selesai Kunjungan)
     */
    public function checkout(Request $request): RedirectResponse
    {
        $identifier = trim((string) $request->input('ticket_or_phone'));

        $guest = Guest::where(function ($q) use ($identifier) {
            $q->where('ticket_number', $identifier)
                ->orWhere('phone', $identifier);
        })->whereNull('check_out_at')->latest()->first();

        if (! $guest) {
            return back()->with('error', 'Data kunjungan aktif tidak ditemukan atau tamu sudah melakukan check-out sebelumnya.');
        }

        $guest->update([
            'status' => 'selesai',
            'check_out_at' => Carbon::now(),
        ]);

        $school = SchoolIdentity::current();

        return redirect()->route('guests.card', ['ticket' => $guest->ticket_number])
            ->with('success', "Kunjungan tamu ({$guest->name}) telah selesai. Terima kasih atas kunjungan Anda di ".($school?->name ?? 'SMA Negeri 1 Lengkong').'.');
    }

    // ==============================================================
    // MANAJEMEN BUKU TAMU TERINTEGRASI DI SISI ADMIN DASHBOARD SIKATAS
    // ==============================================================

    public function index(Request $request): View
    {
        $query = Guest::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('ticket_number', 'like', "%{$search}%")
                    ->orWhere('institution', 'like', "%{$search}%")
                    ->orWhere('identity_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($date = $request->query('date')) {
            $query->whereDate('check_in_at', $date);
        } else {
            // Default filter hari ini jika parameter filter_all tidak diset
            if (! $request->has('filter_all')) {
                $query->whereDate('check_in_at', Carbon::today());
            }
        }

        if ($department = $request->query('department')) {
            $query->where('destination_department', $department);
        }

        if ($institutionType = $request->query('institution_type')) {
            $query->where('institution_type', $institutionType);
        }

        $guests = $query->latest('check_in_at')->paginate(15)->withQueryString();

        // Statistik Hari Ini
        $today = Carbon::today();
        $stats = [
            'today_total' => Guest::whereDate('check_in_at', $today)->count(),
            'active_in_school' => Guest::whereDate('check_in_at', $today)->whereNull('check_out_at')->count(),
            'dinas_jabar' => Guest::whereDate('check_in_at', $today)->where('institution_type', 'like', '%Dinas Pendidikan%')->count(),
            'orang_tua' => Guest::whereDate('check_in_at', $today)->where('institution_type', 'like', '%Orang Tua%')->count(),
        ];

        $departments = [
            'Kepala Sekolah',
            'Kepala Tata Usaha',
            'Sub. Bagian Kesiswaan',
            'Sub. Bagian Kepegawaian',
            'Sub. Bagian Humas',
            'Bendahara Sekolah',
            'Pengelola Sarpras',
            'Koord. BP/BK',
            'Koordinator MBG',
            'Pembina Ekstrakurikuler',
            'Guru / Wali Kelas',
            'Layanan Administrasi TU',
        ];

        $institutionTypes = [
            'Dinas Pendidikan Prov. Jawa Barat',
            'SPPG',
            'Kementerian / Instansi Pemerintah',
            'Pengawas Pembina Sekolah',
            'Komite Sekolah',
            'Orang Tua / Wali Siswa',
            'Perusahaan / Dunia Usaha / Vendor',
            'Lembaga Perguruan Tinggi / Kampus',
            'Media Massa / Pers',
            'Masyarakat Umum / Alumni',
        ];

        $purposes = [
            'Kedinasan / Rapat / Koordinasi Resmi',
            'Penyerahan MBG',
            'Konsultasi Perkembangan / Masalah Siswa',
            'Legalisir Ijazah / Pengambilan Dokumen',
            'Penawaran Kerjasama / Barang & Jasa',
            'Pembayaran / Urusan Keuangan Sekolah',
            'Peliputan Berita / Kunjungan Jurnalistik',
            'Kunjungan Silaturahmi / Pribadi',
            'Keperluan Lainnya',
        ];

        return view('guests.index', compact('guests', 'stats', 'departments', 'institutionTypes', 'purposes'));
    }

    public function update(Request $request, Guest $guest): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'gender' => ['required', 'string', 'in:Laki-laki,Perempuan'],
            'identity_type' => ['required', 'string', 'in:KTP,SIM,NIP,Paspor,Lainnya'],
            'identity_number' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'institution' => ['required', 'string', 'max:150'],
            'institution_type' => ['required', 'string', 'max:100'],
            'destination_department' => ['required', 'string', 'max:100'],
            'destination_person_name' => ['nullable', 'string', 'max:150'],
            'purpose' => ['required', 'string', 'max:100'],
            'purpose_detail' => ['required', 'string'],
            'number_of_guests' => ['required', 'integer', 'min:1', 'max:100'],
            'vehicle_type' => ['nullable', 'string', 'max:50'],
            'vehicle_license_plate' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:menunggu,bertemu,selesai,dibatalkan'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['status'] === 'selesai' && is_null($guest->check_out_at)) {
            $validated['check_out_at'] = Carbon::now();
        }

        $guest->update($validated);

        AuditTrailService::log('update', 'kesiswaan', "Memperbarui data kunjungan tamu: {$guest->name} ({$guest->ticket_number})");

        return back()->with('success', "Data tamu {$guest->ticket_number} ({$guest->name}) berhasil diperbarui.");
    }

    public function show(Guest $guest): View
    {
        $school = SchoolIdentity::current();
        $verificationUrl = route('guests.card', ['ticket' => $guest->ticket_number]);
        $qrCodeSvg = QrCodeService::svg($verificationUrl, 160);

        return view('guests.show', compact('guest', 'school', 'qrCodeSvg'));
    }

    public function updateStatus(Request $request, Guest $guest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:menunggu,bertemu,selesai,dibatalkan'],
            'notes' => ['nullable', 'string'],
        ]);

        $updateData = [
            'status' => $validated['status'],
            'handled_by_user_id' => Auth::id(),
            'notes' => $validated['notes'] ?? $guest->notes,
        ];

        if ($validated['status'] === 'selesai' && is_null($guest->check_out_at)) {
            $updateData['check_out_at'] = Carbon::now();
        }

        $guest->update($updateData);

        AuditTrailService::log('update', 'kesiswaan', "Memperbarui status kunjungan tamu: {$guest->name} ({$guest->ticket_number}) menjadi {$guest->status}");

        return back()->with('success', "Status kunjungan {$guest->ticket_number} berhasil diperbarui.");
    }

    public function destroy(Guest $guest): RedirectResponse
    {
        $name = $guest->name;
        $ticket = $guest->ticket_number;
        $guest->delete();

        AuditTrailService::log('delete', 'kesiswaan', "Menghapus catatan buku tamu: {$name} ({$ticket})");

        return redirect()->route('guests.index')->with('success', "Catatan buku tamu {$ticket} berhasil dihapus.");
    }

    /**
     * Cetak Laporan Rekapitulasi Buku Tamu Sekolah (Format Standar Disdik Jawa Barat)
     */
    public function printReport(Request $request): View
    {
        $school = SchoolIdentity::current();
        $dateFrom = $request->query('date_from', Carbon::today()->format('Y-m-d'));
        $dateTo = $request->query('date_to', Carbon::today()->format('Y-m-d'));

        $guests = Guest::whereBetween('check_in_at', [
            Carbon::parse($dateFrom)->startOfDay(),
            Carbon::parse($dateTo)->endOfDay(),
        ])
            ->orderBy('check_in_at')
            ->get();

        AuditTrailService::log('print', 'kesiswaan', "Mencetak Laporan Buku Tamu dari {$dateFrom} s.d. {$dateTo}");

        return view('guests.report-print', compact('school', 'guests', 'dateFrom', 'dateTo'));
    }
}
