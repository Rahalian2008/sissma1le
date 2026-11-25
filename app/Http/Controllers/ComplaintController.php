<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /**
     * Role yang berhak mengakses & menindaklanjuti pengaduan
     */
    protected array $allowedAdminRoles = [
        'kepala_sekolah',
        'wakasek_kurikulum',
        'wakasek_kesiswaan',
        'wakasek_humas',
        'wakasek_sarpras',
        'super_admin',
    ];

    /**
     * Verifikasi otorisasi pimpinan sekolah (Kepala Sekolah & Wakil Kepala Sekolah)
     */
    protected function authorizeLeadership(): void
    {
        $user = auth()->user();

        if (! $user) {
            abort(403, 'Anda harus masuk terlebih dahulu.');
        }

        $hasAccess = $user->hasAnyRole($this->allowedAdminRoles)
            || in_array($user->role, $this->allowedAdminRoles, true);

        if (! $hasAccess) {
            abort(403, 'Akses terbatas! Kanal pengaduan hanya dapat diakses dan ditindaklanjuti oleh Kepala Sekolah dan jajaran Wakil Kepala Sekolah.');
        }
    }

    /**
     * 1. Halaman Formulir Pengaduan & Aspirasi Publik (Akses Terbuka)
     */
    public function create(): View
    {
        Complaint::ensureComplaintsTableExists();
        $school = SchoolIdentity::current();

        $categories = [
            'layanan_akademik' => [
                'title' => 'KBM, Guru & Layanan Akademik',
                'desc' => 'Terkait proses belajar mengajar, kurikulum, tugas, dan layanan guru.',
                'icon' => 'academic-cap',
            ],
            'kedisiplinan_tatib' => [
                'title' => 'Kedisiplinan & Tata Tertib Siswa',
                'desc' => 'Terkait kehadiran, seragam, ketertiban, dan perilaku siswa.',
                'icon' => 'shield-check',
            ],
            'bullying_perundungan' => [
                'title' => 'Pencegahan Perundungan (Bullying) & Kekerasan',
                'desc' => 'Kanal prioritas perlindungan siswa, kekerasan verbal/fisik, atau intimidasi.',
                'icon' => 'exclamation-triangle',
            ],
            'sarana_prasarana' => [
                'title' => 'Fasilitas & Sarana Prasarana',
                'desc' => 'Terkait kebersihan kelas, toilet, laboratorium, lapangan, dan sarana belajar.',
                'icon' => 'building-office-2',
            ],
            'administrasi_tu' => [
                'title' => 'Pelayanan Administrasi & Tata Usaha',
                'desc' => 'Terkait surat menyurat, legalisir ijazah, administrasi kesiswaan/keuangan.',
                'icon' => 'document-text',
            ],
            'integritas_pungli' => [
                'title' => 'Integritas Layanan & Dugaan Pungli',
                'desc' => 'Whistleblowing pungutan tidak resmi, gratifikasi, atau pelanggaran etika.',
                'icon' => 'scale',
            ],
            'aspirasi_kemajuan' => [
                'title' => 'Saran & Aspirasi Kemajuan Sekolah',
                'desc' => 'Ide inovasi, program ekstrakurikuler, dan usulan untuk kemajuan SMAN 1 Lengkong.',
                'icon' => 'light-bulb',
            ],
        ];

        return view('complaints.create', compact('school', 'categories'));
    }

    /**
     * 2. Simpan Pengaduan Publik & Buat Nomor Tiket Unik
     */
    public function store(Request $request): RedirectResponse
    {
        Complaint::ensureComplaintsTableExists();
        $isAnonymous = $request->boolean('is_anonymous');

        $validated = $request->validate([
            'is_anonymous' => 'nullable|boolean',
            'reporter_name' => $isAnonymous ? 'nullable|string|max:150' : 'required|string|max:150',
            'reporter_phone' => 'required|string|max:30',
            'reporter_email' => 'nullable|email|max:150',
            'reporter_type' => 'required|string|in:orang_tua,siswa,alumni,masyarakat,mitra,guru_staf',
            'category' => 'required|string|in:layanan_akademik,kedisiplinan_tatib,bullying_perundungan,sarana_prasarana,administrasi_tu,integritas_pungli,aspirasi_kemajuan',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:15',
            'incident_date' => 'nullable|date',
            'incident_location' => 'nullable|string|max:150',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf,doc,docx|max:10240',
        ], [
            'reporter_name.required' => 'Nama pelapor wajib diisi (atau aktifkan mode anonim jika ingin merahasiakan identitas).',
            'reporter_phone.required' => 'Nomor HP / WhatsApp wajib diisi agar pimpinan sekolah dapat mengabari tindak lanjut pengaduan.',
            'category.required' => 'Silakan pilih kategori pengaduan atau aspirasi.',
            'title.required' => 'Judul pengaduan/aspirasi wajib diisi.',
            'description.min' => 'Mohon jelaskan rincian atau kronologi kejadian secara lebih detail (minimal 15 karakter).',
            'attachment.max' => 'Ukuran berkas lampiran maksimal 10 MB.',
        ]);

        $ticketNumber = Complaint::generateTicketNumber();

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $cleanName = pathinfo($originalName, PATHINFO_FILENAME);
            $filename = time().'_'.substr(md5($cleanName), 0, 8).'.'.$extension;

            $attachmentPath = $file->storeAs('complaints', $filename, 'public');
            $attachmentName = $originalName;
        }

        $reporterName = $isAnonymous
            ? (! empty($validated['reporter_name']) ? $validated['reporter_name'].' (Identitas Dirahasiakan)' : 'Anonim / Terlindungi')
            : ($validated['reporter_name'] ?? 'Masyarakat Peduli');

        $complaint = Complaint::create([
            'ticket_number' => $ticketNumber,
            'is_anonymous' => $isAnonymous,
            'reporter_name' => $reporterName,
            'reporter_phone' => $validated['reporter_phone'],
            'reporter_email' => $validated['reporter_email'] ?? null,
            'reporter_type' => $validated['reporter_type'],
            'category' => $validated['category'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'incident_date' => $validated['incident_date'] ?? null,
            'incident_location' => $validated['incident_location'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'status' => 'MENUNGGU',
        ]);

        return redirect()->route('complaints.success', $complaint->ticket_number)
            ->with('success', 'Pengaduan Anda berhasil dikirim langsung ke Kepala Sekolah & Jajaran Wakil Kepala Sekolah!');
    }

    /**
     * 3. Halaman Sukses Pengiriman Tiket
     */
    public function success(string $ticketNumber): View
    {
        $complaint = Complaint::where('ticket_number', $ticketNumber)->firstOrFail();
        $school = SchoolIdentity::current();

        return view('complaints.success', compact('complaint', 'school'));
    }

    /**
     * 4. Halaman Pelacakan Status Pengaduan Publik
     */
    public function track(Request $request): View
    {
        Complaint::ensureComplaintsTableExists();
        $school = SchoolIdentity::current();
        $ticket = trim((string) $request->input('ticket'));
        $complaint = null;
        $searched = false;

        if ($ticket !== '') {
            $searched = true;
            $complaint = Complaint::where('ticket_number', $ticket)->first();
        }

        return view('complaints.track', compact('school', 'ticket', 'complaint', 'searched'));
    }

    /**
     * 5. Executive Backoffice - Daftar Pengaduan (Khusus Kepsek & Wakasek)
     */
    public function adminIndex(Request $request): View
    {
        $this->authorizeLeadership();
        Complaint::ensureComplaintsTableExists();

        $statusFilter = $request->query('status');
        $categoryFilter = $request->query('category');
        $search = trim((string) $request->query('search'));

        $query = Complaint::with('handler')->latest();

        if ($statusFilter && in_array($statusFilter, ['MENUNGGU', 'DITELAAH', 'DIPROSES', 'SELESAI', 'DITOLAK'], true)) {
            $query->where('status', $statusFilter);
        }

        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('reporter_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $complaints = $query->paginate(15)->withQueryString();

        // Statistik Cepat untuk Executive Summary
        $stats = [
            'total' => Complaint::count(),
            'menunggu' => Complaint::where('status', 'MENUNGGU')->count(),
            'ditelaah' => Complaint::where('status', 'DITELAAH')->count(),
            'diproses' => Complaint::where('status', 'DIPROSES')->count(),
            'selesai' => Complaint::where('status', 'SELESAI')->count(),
            'ditolak' => Complaint::where('status', 'DITOLAK')->count(),
            'prioritas_bullying' => Complaint::where('category', 'bullying_perundungan')->whereIn('status', ['MENUNGGU', 'DITELAAH', 'DIPROSES'])->count(),
        ];

        return view('complaints.admin.index', compact('complaints', 'stats', 'statusFilter', 'categoryFilter', 'search'));
    }

    /**
     * 6. Executive Backoffice - Detail & Telaah Pengaduan
     */
    public function adminShow(Complaint $complaint): View
    {
        $this->authorizeLeadership();
        $complaint->load('handler');

        return view('complaints.admin.show', compact('complaint'));
    }

    /**
     * 7. Executive Backoffice - Update Disposisi / Tanggapan & Status
     */
    public function adminUpdateDisposition(Request $request, Complaint $complaint): RedirectResponse
    {
        $this->authorizeLeadership();

        $validated = $request->validate([
            'status' => 'required|string|in:MENUNGGU,DITELAAH,DIPROSES,SELESAI,DITOLAK',
            'disposition_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $complaint->update([
            'status' => $validated['status'],
            'disposition_notes' => $validated['disposition_notes'],
            'internal_notes' => $validated['internal_notes'],
            'handled_by' => auth()->id(),
            'handled_at' => Carbon::now(),
        ]);

        AuditTrailService::log(
            'update',
            'executive_complaints',
            "Memperbarui status disposisi pengaduan {$complaint->ticket_number} menjadi {$validated['status']}"
        );

        return back()->with('success', "Status dan catatan disposisi tiket {$complaint->ticket_number} berhasil diperbarui.");
    }
}
