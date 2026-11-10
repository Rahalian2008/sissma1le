<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Room;
use App\Models\Teacher;
use App\Models\User;
use App\Services\AuditTrailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    /**
     * Memastikan ruangan default sekolah tersedia jika data ruangan masih kosong
     */
    public static function ensureDefaultRooms(): void
    {
        if (Room::count() > 0) {
            return;
        }

        $defaultRooms = [
            ['code' => 'R-LAB-KOM', 'name' => 'Laboratorium Komputer & Multimedia', 'person_in_charge' => 'Budi Santoso, S.Kom.', 'capacity' => 36, 'condition' => 'Baik'],
            ['code' => 'R-LAB-IPA', 'name' => 'Laboratorium IPA & Sains Terpadu', 'person_in_charge' => 'Dra. Siti Rahmah, M.Pd.', 'capacity' => 36, 'condition' => 'Baik'],
            ['code' => 'R-PERPUS', 'name' => 'Perpustakaan Ki Hajar Dewantara', 'person_in_charge' => 'Hj. Nurhayati, S.Pd.', 'capacity' => 50, 'condition' => 'Baik'],
            ['code' => 'R-GURU', 'name' => 'Ruang Guru & Tenaga Pendidik', 'person_in_charge' => 'Wakasek Kurikulum', 'capacity' => 45, 'condition' => 'Baik'],
            ['code' => 'R-TU', 'name' => 'Ruang Tata Usaha & Pelayanan', 'person_in_charge' => 'Kepala Tata Usaha', 'capacity' => 15, 'condition' => 'Baik'],
            ['code' => 'R-KS', 'name' => 'Ruang Kepala Sekolah', 'person_in_charge' => 'Drs. H. Ahmad Sudrajat, M.Pd.', 'capacity' => 10, 'condition' => 'Baik'],
            ['code' => 'R-BK', 'name' => 'Ruang Bimbingan & Konseling', 'person_in_charge' => 'Koordinator Guru BK', 'capacity' => 15, 'condition' => 'Baik'],
            ['code' => 'R-UKS', 'name' => 'Ruang Unit Kesehatan Sekolah (UKS)', 'person_in_charge' => 'Pembina UKS', 'capacity' => 10, 'condition' => 'Baik'],
            ['code' => 'R-GUDANG', 'name' => 'Gudang Sarpras & Logistik', 'person_in_charge' => 'Pengelola Logistik', 'capacity' => 10, 'condition' => 'Baik'],
            ['code' => 'R-TRANSIT', 'name' => 'Gudang Transit & Penerimaan Sarpras', 'person_in_charge' => 'Pengelola Barang', 'capacity' => 10, 'condition' => 'Baik'],
            ['code' => 'R-KELAS-X', 'name' => 'Ruang Belajar Kelas X', 'person_in_charge' => 'Koordinator Kelas X', 'capacity' => 36, 'condition' => 'Baik'],
            ['code' => 'R-KELAS-XI', 'name' => 'Ruang Belajar Kelas XI', 'person_in_charge' => 'Koordinator Kelas XI', 'capacity' => 36, 'condition' => 'Baik'],
            ['code' => 'R-KELAS-XII', 'name' => 'Ruang Belajar Kelas XII', 'person_in_charge' => 'Koordinator Kelas XII', 'capacity' => 36, 'condition' => 'Baik'],
            ['code' => 'R-IBADAH', 'name' => 'Masjid / Sarana Ibadah Sekolah', 'person_in_charge' => 'Pembina Keagamaan', 'capacity' => 200, 'condition' => 'Baik'],
            ['code' => 'R-OSIS', 'name' => 'Ruang Sekretariat OSIS & MPK', 'person_in_charge' => 'Pembina OSIS', 'capacity' => 25, 'condition' => 'Baik'],
        ];

        foreach ($defaultRooms as $dr) {
            Room::firstOrCreate(['code' => $dr['code']], $dr);
        }
    }

    public function index(): View
    {
        self::ensureDefaultRooms();

        $rooms = Room::withCount('assets')->orderBy('code')->paginate(15);

        // 1. Ambil data Guru (Tenaga Pendidik)
        $teachers = Teacher::active()->teachers()->orderBy('name')->get();
        if ($teachers->isEmpty()) {
            $teachers = Employee::active()->teachers()->orderBy('name')->get();
        }

        // 2. Ambil data Tenaga Kependidikan (Tendik / Tata Usaha)
        $staff = Teacher::active()->staff()->orderBy('name')->get();
        if ($staff->isEmpty()) {
            $staff = Employee::active()->staff()->orderBy('name')->get();
        }
        if ($staff->isEmpty()) {
            $staff = User::whereIn('role', ['admin', 'super_admin', 'tata_usaha', 'tendik', 'kepala_sekolah'])
                ->orderBy('name')
                ->get()
                ->map(function ($u) {
                    $roleLabel = match ($u->role) {
                        'super_admin' => 'Super Administrator',
                        'admin' => 'Administrator TU',
                        'kepala_sekolah' => 'Kepala Sekolah',
                        default => ucfirst(str_replace('_', ' ', $u->role)),
                    };

                    return (object) [
                        'id' => $u->id,
                        'name' => $u->name,
                        'formatted_name' => $u->name,
                        'nip' => $u->nip,
                        'position' => $roleLabel,
                        'employment_type' => 'Tenaga Kependidikan',
                    ];
                });
        }

        // 3. Gabungkan seluruh PTK untuk datalist autocomplete
        $allEmployees = $teachers->concat($staff)->sortBy('name')->values();

        return view('sarpras.rooms.index', compact('rooms', 'teachers', 'staff', 'allEmployees'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:rooms'],
            'name' => ['required', 'string', 'max:255'],
            'person_in_charge' => ['nullable', 'string', 'max:150'],
            'capacity' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'string'],
        ]);

        $room = Room::create($validated);

        AuditTrailService::log('create', 'sarpras', "Menambahkan data ruang: {$room->name} ({$room->code})");

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data ruang berhasil ditambahkan.',
                'room' => $room,
            ]);
        }

        return back()->with('success', 'Data ruang berhasil ditambahkan.');
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:rooms,code,'.$room->id],
            'name' => ['required', 'string', 'max:255'],
            'person_in_charge' => ['nullable', 'string', 'max:150'],
            'capacity' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'string'],
        ]);

        $room->update($validated);

        AuditTrailService::log('update', 'sarpras', "Memperbarui data ruang: {$room->name} ({$room->code})");

        return back()->with('success', 'Data ruang berhasil diperbarui.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $name = $room->name;
        $room->delete();

        AuditTrailService::log('delete', 'sarpras', "Menghapus data ruang: {$name}");

        return back()->with('success', 'Data ruang berhasil dihapus.');
    }
}
