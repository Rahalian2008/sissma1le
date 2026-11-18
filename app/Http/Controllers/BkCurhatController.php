<?php

namespace App\Http\Controllers;

use App\Models\BkConsultation;
use App\Models\BkConsultationMessage;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BkCurhatController extends Controller
{
    /**
     * Tampilkan halaman Curhat & Konseling Online BK (WhatsApp Web Style)
     */
    public function index(Request $request): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $isCounselor = $this->isCounselor($user);
        $school = SchoolIdentity::first();
        $bkCoordinator = Teacher::getBkCoordinator();
        $bkCounselors = Teacher::getBkCounselors();

        if (! $isCounselor) {
            // Mode Siswa: Dapatkan profil siswa
            $student = $user->student ?? Student::where('user_id', $user->id)->first();
            if (! $student && $user->username) {
                $student = Student::where('nisn', $user->username)->orWhere('nis', $user->username)->first();
            }

            if (! $student) {
                return redirect()->route('dashboard')->with('error', 'Akun Anda belum terhubung ke data siswa. Silakan hubungi operator.');
            }

            // Ambil semua sesi konsultasi siswa ini
            $consultations = BkConsultation::where('student_id', $student->id)
                ->with(['assignedCounselor', 'latestMessages'])
                ->orderByDesc('last_message_at')
                ->orderByDesc('created_at')
                ->get();

            // Sesi aktif
            $activeConsultation = null;
            if ($request->filled('c')) {
                $activeConsultation = $consultations->firstWhere('id', (int) $request->c);
            }

            if (! $activeConsultation && $consultations->isNotEmpty()) {
                $activeConsultation = $consultations->first();
            }

            if ($activeConsultation) {
                $activeConsultation->load(['messages.sender', 'assignedCounselor']);
                $activeConsultation->markAsReadFor($user, false);
            }

            return view('bk.curhat.index', [
                'mode' => 'student',
                'student' => $student,
                'consultations' => $consultations,
                'activeConsultation' => $activeConsultation,
                'school' => $school,
                'bkCoordinator' => $bkCoordinator,
                'bkCounselors' => $bkCounselors,
                'isCounselor' => false,
                'canDeleteChat' => $this->canDeleteConsultation($user),
            ]);
        }

        // Mode Konselor / Koordinator BK / Guru BK / Admin
        $query = BkConsultation::with(['student.schoolClass', 'assignedCounselor', 'latestMessages'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $status = strtoupper($request->status);
            if ($status === 'UNREAD') {
                $query->where('unread_counselor_count', '>', 0);
            } elseif (in_array($status, ['OPEN', 'ACTIVE', 'RESOLVED', 'CLOSED'], true)) {
                $query->where('status', $status);
            }
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('consultation_code', 'like', "%{$search}%")
                    ->orWhere('topic', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('nisn', 'like', "%{$search}%")
                            ->orWhereHas('schoolClass', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
                    });
            });
        }

        $consultations = $query->get();

        // Sesi aktif
        $activeConsultation = null;
        if ($request->filled('c')) {
            $activeConsultation = BkConsultation::with(['student.schoolClass', 'assignedCounselor', 'messages.sender'])
                ->find($request->c);
        }

        if (! $activeConsultation && $consultations->isNotEmpty()) {
            // Utamakan yang ada pesan belum dibaca, jika tidak ada ambil yang pertama
            $firstUnread = $consultations->firstWhere('unread_counselor_count', '>', 0);
            $activeConsultation = $firstUnread ?: $consultations->first();
            if ($activeConsultation) {
                $activeConsultation->load(['student.schoolClass', 'assignedCounselor', 'messages.sender']);
            }
        }

        if ($activeConsultation) {
            $activeConsultation->markAsReadFor($user, true);
        }

        $unreadTotal = BkConsultation::sum('unread_counselor_count');

        return view('bk.curhat.index', [
            'mode' => 'counselor',
            'consultations' => $consultations,
            'activeConsultation' => $activeConsultation,
            'school' => $school,
            'bkCoordinator' => $bkCoordinator,
            'bkCounselors' => $bkCounselors,
            'unreadTotal' => $unreadTotal,
            'isCounselor' => true,
            'canDeleteChat' => $this->canDeleteConsultation($user),
        ]);
    }

    /**
     * Buka Sesi Curhat Baru (Oleh Siswa)
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $isCounselor = $this->isCounselor($user);

        if ($isCounselor) {
            $request->validate([
                'student_id' => ['required', 'exists:students,id'],
                'topic' => ['required', 'string', 'max:150'],
                'category' => ['required', 'in:PRIBADI,BELAJAR,SOSIAL,KARIER,LAINNYA'],
                'assigned_counselor_id' => ['nullable', 'exists:users,id'],
                'message' => ['nullable', 'string', 'max:5000'],
                'attachment' => ['nullable', 'file', 'max:25600', 'mimes:jpg,jpeg,png,webp,gif,mp4,webm,weba,mov,m4v,mp3,wav,m4a,aac,ogg,oga,opus,flac,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip'],
            ]);
            $student = Student::findOrFail($request->student_id);
        } else {
            $request->validate([
                'topic' => ['required', 'string', 'max:150'],
                'category' => ['required', 'in:PRIBADI,BELAJAR,SOSIAL,KARIER,LAINNYA'],
                'assigned_counselor_id' => ['nullable', 'exists:users,id'],
                'message' => ['nullable', 'string', 'max:5000'],
                'is_anonymous' => ['nullable', 'boolean'],
                'attachment' => ['nullable', 'file', 'max:25600', 'mimes:jpg,jpeg,png,webp,gif,mp4,webm,weba,mov,m4v,mp3,wav,m4a,aac,ogg,oga,opus,flac,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip'],
            ]);

            $student = $user->student ?? Student::where('user_id', $user->id)->first();
            if (! $student && $user->username) {
                $student = Student::where('nisn', $user->username)->orWhere('nis', $user->username)->first();
            }

            if (! $student) {
                return back()->with('error', 'Data siswa tidak ditemukan.');
            }
        }

        // Pastikan ada pesan atau lampiran
        if (empty($request->message) && ! $request->hasFile('attachment')) {
            return back()->with('error', 'Tuliskan pesan curhat atau sertakan lampiran file.');
        }

        $code = 'CRH-'.date('Ym').'-'.strtoupper(Str::random(5));
        while (BkConsultation::where('consultation_code', $code)->exists()) {
            $code = 'CRH-'.date('Ym').'-'.strtoupper(Str::random(5));
        }

        $assignedCounselorId = $isCounselor ? $user->id : ($request->assigned_counselor_id ?: null);

        $consultation = BkConsultation::create([
            'consultation_code' => $code,
            'student_id' => $student->id,
            'user_id' => $user->id,
            'assigned_counselor_id' => $assignedCounselorId,
            'topic' => $request->topic,
            'category' => $request->category,
            'status' => 'OPEN',
            'is_anonymous' => false,
            'last_message_at' => now(),
            'last_message_preview' => $request->message ? Str::limit($request->message, 80) : '[Lampiran]',
            'unread_counselor_count' => $isCounselor ? 0 : 1,
            'unread_student_count' => $isCounselor ? 1 : 0,
        ]);

        $this->saveMessage($consultation, $user, $isCounselor ? 'COUNSELOR' : 'STUDENT', $request->message, $request->file('attachment'));

        return redirect()->route('bk.curhat.index', ['c' => $consultation->id])
            ->with('success', 'Sesi Curhat Berhasil Dimulai. Guru BK akan segera merespons pesanmu.');
    }

    /**
     * Kirim Pesan dalam Sesi Curhat (AJAX & Form Submit)
     */
    public function sendMessage(Request $request, BkConsultation $consultation): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $isCounselor = $this->isCounselor($user);

        // Verifikasi kepemilikan sesi jika siswa
        if (! $isCounselor && $consultation->student_id !== ($user->student?->id ?? null)) {
            $student = Student::where('user_id', $user->id)->first();
            if (! $student || $consultation->student_id !== $student->id) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
                }

                return back()->with('error', 'Akses ke sesi konsultasi ini ditolak.');
            }
        }

        $request->validate([
            'message' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:25600', 'mimes:jpg,jpeg,png,webp,gif,mp4,webm,weba,mov,m4v,mp3,wav,m4a,aac,ogg,oga,opus,flac,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip'],
        ]);

        if (empty($request->message) && ! $request->hasFile('attachment')) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Pesan atau lampiran tidak boleh kosong.'], 422);
            }

            return back()->with('error', 'Pesan atau lampiran tidak boleh kosong.');
        }

        $senderRole = $isCounselor ? 'COUNSELOR' : 'STUDENT';
        $message = $this->saveMessage($consultation, $user, $senderRole, $request->message, $request->file('attachment'));

        // Update status konsultasi
        $updates = [
            'last_message_at' => now(),
            'last_message_preview' => $message->message ? Str::limit($message->message, 80) : ('[Lampiran '.($message->attachment_type ?: 'File').']'),
        ];

        if ($isCounselor) {
            $updates['unread_student_count'] = $consultation->unread_student_count + 1;
            if (! $consultation->assigned_counselor_id) {
                $updates['assigned_counselor_id'] = $user->id;
            }
            if ($consultation->status === 'OPEN') {
                $updates['status'] = 'ACTIVE';
            }
        } else {
            $updates['unread_counselor_count'] = $consultation->unread_counselor_count + 1;
            if ($consultation->status === 'RESOLVED') {
                $updates['status'] = 'ACTIVE';
            }
        }

        $consultation->update($updates);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil terkirim.',
                'data' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'rendered_html' => $message->rendered_message_html,
                    'sender_role' => $message->sender_role,
                    'sender_name' => $isCounselor ? ($user->formatted_name ?? $user->name) : ($consultation->is_anonymous ? 'Kamu (Anonim)' : ($consultation->student?->name ?? 'Kamu')),
                    'created_at' => $message->created_at->format('H:i').' WIB',
                    'attachment_url' => $message->attachment_url,
                    'attachment_name' => $message->attachment_name,
                    'attachment_type' => $message->attachment_type,
                    'formatted_size' => $message->formatted_size,
                    'is_image' => $message->is_image,
                    'is_video' => $message->is_video,
                    'is_audio' => $message->is_audio,
                    'is_document' => $message->is_document,
                ],
            ]);
        }

        return redirect()->route('bk.curhat.index', ['c' => $consultation->id]);
    }

    /**
     * Endpoint Polling Real-time untuk mengambil pesan baru
     */
    public function fetchMessages(Request $request, BkConsultation $consultation): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $isCounselor = $this->isCounselor($user);

        // Validasi akses
        if (! $isCounselor) {
            $student = $user->student ?? Student::where('user_id', $user->id)->first();
            if (! $student || $consultation->student_id !== $student->id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
            }
        }

        $query = $consultation->messages()->with('sender');

        if ($request->filled('after_id')) {
            $query->where('id', '>', (int) $request->after_id);
        }

        $messages = $query->get();

        // Tandai pesan sebagai telah dibaca
        $consultation->markAsReadFor($user, $isCounselor);

        $payload = $messages->map(function (BkConsultationMessage $msg) use ($user, $consultation) {
            $isMine = (int) $msg->sender_id === (int) $user->id;

            return [
                'id' => $msg->id,
                'is_mine' => $isMine,
                'sender_role' => $msg->sender_role,
                'sender_name' => $msg->sender_role === 'COUNSELOR'
                    ? ($msg->sender?->formatted_name ?: ($msg->sender?->name ?: 'Guru BK'))
                    : ($consultation->is_anonymous && ! $isMine ? 'Siswa Rahasia' : ($consultation->student?->name ?: 'Siswa')),
                'message' => $msg->message,
                'rendered_html' => $msg->rendered_message_html,
                'created_at' => $msg->created_at->format('H:i').' WIB',
                'created_date' => $msg->created_at->translatedFormat('d M Y'),
                'is_read' => $msg->is_read,
                'attachment_url' => $msg->attachment_url,
                'attachment_name' => $msg->attachment_name,
                'attachment_type' => $msg->attachment_type,
                'formatted_size' => $msg->formatted_size,
                'is_image' => $msg->is_image,
                'is_video' => $msg->is_video,
                'is_audio' => $msg->is_audio,
                'is_document' => $msg->is_document,
            ];
        });

        return response()->json([
            'success' => true,
            'messages' => $payload,
            'consultation' => [
                'id' => $consultation->id,
                'status' => $consultation->status,
                'unread_counselor_count' => $consultation->unread_counselor_count,
                'unread_student_count' => $consultation->unread_student_count,
            ],
        ]);
    }

    /**
     * Tandai konsultasi telah selesai (Resolved)
     */
    public function resolve(Request $request, BkConsultation $consultation): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $isCounselor = $this->isCounselor($user);

        if (! $isCounselor && $consultation->student_id !== ($user->student?->id ?? null)) {
            $student = Student::where('user_id', $user->id)->first();
            if (! $student || $consultation->student_id !== $student->id) {
                return back()->with('error', 'Akses ditolak.');
            }
        }

        $consultation->update([
            'status' => 'RESOLVED',
            'conclusion_notes' => $request->conclusion_notes ?: 'Sesi curhat telah diselesaikan dengan baik.',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Sesi curhat telah ditandai selesai.']);
        }

        return redirect()->route('bk.curhat.index', ['c' => $consultation->id])
            ->with('success', 'Sesi Curhat telah ditandai selesai.');
    }

    /**
     * Hapus percakapan curhat secara permanen.
     * Hanya dapat dilakukan oleh Koordinator BK dan Guru BK (atau Admin),
     * dan hanya jika status percakapan sudah SELESAI (RESOLVED / CLOSED).
     * Jika dihapus, maka seluruh rekaman pesan, audio, dan berkas di server
     * maupun di tampilan akun siswa ikut terhapus permanen.
     */
    public function destroy(Request $request, BkConsultation $consultation): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // 1. Validasi hak akses: hanya Koordinator BK dan Guru BK (serta Admin)
        if (! $this->canDeleteConsultation($user)) {
            abort(403, 'Akses ditolak: Penghapusan percakapan curhat hanya berhak dilakukan oleh Koordinator BK dan Guru BK.');
        }

        // 2. Validasi status: hanya percakapan yang berstatus selesai yang boleh dihapus
        $isFinished = in_array(strtoupper((string) $consultation->status), ['RESOLVED', 'CLOSED', 'SELESAI'], true);
        if (! $isFinished) {
            return back()->with('error', 'Sesi curhat belum dapat dihapus. Sesi percakapan harus ditandai selesai terlebih dahulu.');
        }

        $code = $consultation->consultation_code;

        // 3. Hapus seluruh berkas lampiran dan rekaman audio dari penyimpanan publik
        try {
            foreach ($consultation->messages as $msg) {
                if ($msg->attachment_path && Storage::disk('public')->exists($msg->attachment_path)) {
                    Storage::disk('public')->delete($msg->attachment_path);
                }
            }
            Storage::disk('public')->deleteDirectory('bk/curhat/'.$consultation->id);
        } catch (\Throwable $e) {
            // Lanjutkan jika folder penyimpanan kosong atau tidak ditemukan
        }

        // 4. Hapus seluruh rekaman pesan dan sesi konsultasi dari database
        $consultation->messages()->delete();
        $consultation->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Percakapan curhat [{$code}] berhasil dihapus permanen.",
            ]);
        }

        return redirect()->route('bk.curhat.index')
            ->with('success', "Percakapan curhat [{$code}] berhasil dihapus permanen dari server dan akun siswa.");
    }

    /**
     * Helper penyimpanan file lampiran dan pesan
     */
    private function saveMessage(BkConsultation $consultation, User $user, string $senderRole, ?string $messageText, $attachmentFile): BkConsultationMessage
    {
        $attachmentType = null;
        $attachmentPath = null;
        $attachmentName = null;
        $attachmentMime = null;
        $attachmentSize = null;

        if ($attachmentFile) {
            $attachmentName = $attachmentFile->getClientOriginalName();
            $attachmentMime = $attachmentFile->getMimeType();
            $attachmentSize = $attachmentFile->getSize();
            $ext = strtolower($attachmentFile->getClientOriginalExtension());

            $isVoice = str_starts_with($attachmentName, 'voicenote_')
                || str_starts_with((string) $attachmentMime, 'audio/')
                || in_array($ext, ['mp3', 'wav', 'm4a', 'aac', 'ogg', 'oga', 'weba'], true)
                || ($ext === 'webm' && (str_contains((string) $messageText, 'Suara') || str_contains($attachmentName, 'audio') || str_contains($attachmentName, 'suara')));

            if ($isVoice) {
                $attachmentType = 'AUDIO';
                if (empty($attachmentMime) || str_starts_with($attachmentMime, 'video/') || $attachmentMime === 'application/octet-stream') {
                    $attachmentMime = ($ext === 'mp3') ? 'audio/mpeg' : 'audio/webm';
                }
            } elseif (str_starts_with($attachmentMime, 'image/') || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'], true)) {
                $attachmentType = 'IMAGE';
            } elseif (str_starts_with($attachmentMime, 'video/') || in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm'], true)) {
                $attachmentType = 'VIDEO';
            } else {
                $attachmentType = 'DOCUMENT';
            }

            // Simpan di disk public
            $folder = 'bk/curhat/'.$consultation->id;
            $attachmentPath = $attachmentFile->store($folder, 'public');
        }

        return BkConsultationMessage::create([
            'consultation_id' => $consultation->id,
            'sender_id' => $user->id,
            'sender_role' => $senderRole,
            'message' => $messageText,
            'attachment_type' => $attachmentType,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_mime' => $attachmentMime,
            'attachment_size' => $attachmentSize,
            'is_read' => false,
        ]);
    }

    /**
     * Cek apakah user adalah Staf BK (Koordinator BK, Guru BK, Kepala Sekolah, atau Admin)
     */
    private function isCounselor(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah'])
            || in_array($user->role, ['super_admin', 'admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah'], true)
            || ! $user->isStudent();
    }

    /**
     * Cek hak otorisasi penghapusan percakapan curhat.
     * Khusus untuk Koordinator BK dan Guru BK (serta Administrator sistem).
     */
    private function canDeleteConsultation(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $allowedRoles = ['koordinator_bk', 'guru_bk', 'bk', 'admin', 'super_admin'];

        return $user->hasAnyRole($allowedRoles)
            || in_array($user->role, $allowedRoles, true)
            || in_array(session('active_role'), $allowedRoles, true);
    }
}
