@extends('layouts.app')

@section('title', 'Notula & Risalah Rapat Dinas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">Tata Naskah Dinas</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Dokumentasi Keputusan Kedinasan</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Buku Notula & Risalah Rapat Dinas</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pencatatan resmi jalannya rapat dinas, rapat dewan guru, rapat koordinasi TU, notulen hasil pembahasan, dan daftar hadir rapat.</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('modalAddMeeting').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Notula Rapat Baru
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Notula</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalMeetings) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Risalah Terarsip</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-blue-200 bg-blue-50/20">
            <div class="text-[11px] font-semibold text-blue-700 uppercase tracking-wider">Tahun {{ date('Y') }}</div>
            <div class="text-2xl font-bold text-blue-800 mt-1">{{ number_format($thisYearCount) }}</div>
            <div class="text-[10px] text-blue-600/80 mt-0.5">Rapat Terlaksana</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-emerald-200 bg-emerald-50/20">
            <div class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider">Rapat Dewan Guru</div>
            <div class="text-2xl font-bold text-emerald-800 mt-1">{{ number_format($teacherMeetingCount) }}</div>
            <div class="text-[10px] text-emerald-600/80 mt-0.5">Akademik & Kurikulum</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-amber-200 bg-amber-50/20">
            <div class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider">Koordinasi Tata Usaha</div>
            <div class="text-2xl font-bold text-amber-800 mt-1">{{ number_format($tuMeetingCount) }}</div>
            <div class="text-[10px] text-amber-600/80 mt-0.5">Administrasi & Layanan</div>
        </div>
    </div>

    <!-- Filter & Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row items-center justify-between gap-3">
            <form action="{{ route('letters.meeting-notes.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, pimpinan, notulis, agenda..." class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500 w-full sm:w-64">
                <select name="meeting_type" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="">Semua Jenis Rapat</option>
                    <option value="Rapat Dinas" {{ request('meeting_type') === 'Rapat Dinas' ? 'selected' : '' }}>Rapat Dinas</option>
                    <option value="Rapat Dewan Guru" {{ request('meeting_type') === 'Rapat Dewan Guru' ? 'selected' : '' }}>Rapat Dewan Guru</option>
                    <option value="Rapat Komite" {{ request('meeting_type') === 'Rapat Komite' ? 'selected' : '' }}>Rapat Komite</option>
                    <option value="Rapat Koordinasi TU" {{ request('meeting_type') === 'Rapat Koordinasi TU' ? 'selected' : '' }}>Rapat Koordinasi TU</option>
                    <option value="Rapat Pleno" {{ request('meeting_type') === 'Rapat Pleno' ? 'selected' : '' }}>Rapat Pleno Kelulusan / Kenaikan</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white rounded-xl text-xs font-semibold">Filter</button>
                @if(request()->hasAny(['search', 'meeting_type']))
                    <a href="{{ route('letters.meeting-notes.index') }}" class="px-3 py-1.5 text-slate-500 hover:text-slate-700 text-xs">Reset</a>
                @endif
            </form>
            <div class="text-xs text-slate-500">
                Menampilkan <strong>{{ $meetings->total() }}</strong> notula rapat dinas
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3 text-center" style="width: 40px;">No</th>
                        <th class="px-4 py-3">No. Notula & Tanggal</th>
                        <th class="px-4 py-3">Judul Rapat & Jenis</th>
                        <th class="px-4 py-3">Pimpinan & Notulis</th>
                        <th class="px-4 py-3">Tempat & Kehadiran</th>
                        <th class="px-4 py-3">Ringkasan Keputusan</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($meetings as $idx => $m)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3 text-center text-slate-400 font-mono">
                                {{ $meetings->firstItem() + $idx }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono font-bold text-blue-900 block">{{ $m->meeting_number }}</span>
                                <span class="text-[11px] text-slate-500">{{ $m->meeting_date?->format('d/m/Y') }}</span>
                                @if($m->start_time)
                                    <span class="text-[10px] text-slate-400 block">{{ $m->start_time }} - {{ $m->end_time ?: 'Selesai' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <strong class="text-slate-900 block text-xs">{{ $m->meeting_title }}</strong>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-700 mt-0.5">
                                    {{ $m->meeting_type }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-[11px]">
                                    <span class="text-slate-400">Pimpinan:</span> <strong>{{ $m->chairperson_name }}</strong>
                                </div>
                                <div class="text-[11px] mt-0.5">
                                    <span class="text-slate-400">Notulis:</span> <strong>{{ $m->secretary_name }}</strong>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-[11px] text-slate-800">{{ $m->location }}</div>
                                <div class="text-[10px] text-slate-500 font-semibold mt-0.5">
                                    👥 Hadir: {{ $m->attendees_count }} Orang
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-[11px] text-slate-700 line-clamp-2 max-w-xs">{{ $m->decisions_and_follow_up ?: ($m->discussion_summary ?: '-') }}</p>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap space-x-1.5">
                                <button type="button" 
                                        onclick="openEditMeetingModal({{ json_encode($m) }})"
                                        class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold rounded-lg border border-amber-200 text-xs shadow-2xs transition inline-flex items-center gap-1">
                                    <span>✏️ Edit</span>
                                </button>
                                <a href="{{ route('letters.meeting-notes.print', $m) }}" target="_blank" class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-lg border border-slate-200 text-xs shadow-2xs transition inline-flex items-center gap-1">
                                    <span>🖨️ Cetak</span>
                                </a>
                                <form action="{{ route('letters.meeting-notes.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Hapus notula rapat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition" title="Hapus">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                Belum ada notula rapat dinas yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $meetings->links() }}
        </div>
    </div>
</div>

<!-- Modal Catat Notula Rapat Baru -->
<div id="modalAddMeeting" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="px-5 py-4 bg-blue-900 text-white flex items-center justify-between sticky top-0 z-10">
            <div>
                <h3 class="font-bold text-sm">Catat Notula & Risalah Rapat Kedinasan</h3>
                <p class="text-[11px] text-blue-200">Format resmi dokumentasi rapat dinas sekolah</p>
            </div>
            <button onclick="document.getElementById('modalAddMeeting').classList.add('hidden')" class="text-blue-200 hover:text-white">&times;</button>
        </div>
        <form action="{{ route('letters.meeting-notes.store') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Judul / Agenda Utama Rapat *</label>
                <input type="text" name="meeting_title" required placeholder="Contoh: Rapat Pembagian Tugas Mengajar & Implementasi Kurikulum Merdeka" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-semibold text-slate-900">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Rapat *</label>
                    <select name="meeting_type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        <option value="Rapat Dinas">Rapat Dinas</option>
                        <option value="Rapat Dewan Guru">Rapat Dewan Guru</option>
                        <option value="Rapat Komite">Rapat Komite</option>
                        <option value="Rapat Koordinasi TU">Rapat Koordinasi Tata Usaha</option>
                        <option value="Rapat Pleno">Rapat Pleno Kelulusan / Kenaikan</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Rapat *</label>
                    <input type="date" name="meeting_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Waktu Mulai</label>
                    <input type="text" name="start_time" value="08:30" placeholder="08:30 WIB" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Waktu Selesai</label>
                    <input type="text" name="end_time" value="11:30" placeholder="11:30 WIB" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jumlah Hadir (Orang)</label>
                    <input type="number" name="attendees_count" value="35" min="0" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-bold">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tempat / Ruang Pelaksanaan *</label>
                <input type="text" name="location" value="Ruang Rapat SMA Negeri 1 Lengkong" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pimpinan Rapat *</label>
                    <input type="text" name="chairperson_name" value="{{ $school->headmaster_name }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg font-medium">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Notulis Rapat *</label>
                    <input type="text" name="secretary_name" value="{{ auth()->user()?->name ?? 'Kasubag TU' }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg font-medium">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Topik / Susunan Acara Rapat</label>
                <textarea name="agenda_topics" rows="2" placeholder="1. Pembukaan&#10;2. Pengarahan Kepala Sekolah&#10;3. Pembahasan Teknis&#10;4. Tanya Jawab & Penutup" class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Ringkasan Pembahasan & Jalannya Rapat</label>
                <textarea name="discussion_summary" rows="3" placeholder="Poin-poin penting yang disampaikan dalam rapat..." class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kesimpulan, Keputusan & Rencana Tindak Lanjut *</label>
                <textarea name="decisions_and_follow_up" rows="3" required placeholder="Keputusan yang disepakati bersama beserta penanggung jawab tindak lanjut..." class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Upload Berkas Fisik Daftar Hadir / Notula (Opsional)</label>
                <input type="file" name="attendance_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalAddMeeting').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-sm">Simpan Notula Rapat</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Notula Rapat -->
<div id="modalEditMeeting" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="px-5 py-4 bg-amber-600 text-white flex items-center justify-between sticky top-0 z-10">
            <div>
                <h3 class="font-bold text-sm">Edit Notula Rapat Kedinasan</h3>
                <p id="editMeetingNo" class="text-[11px] text-amber-100 font-mono"></p>
            </div>
            <button onclick="document.getElementById('modalEditMeeting').classList.add('hidden')" class="text-amber-200 hover:text-white">&times;</button>
        </div>
        <form id="formEditMeeting" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Judul / Agenda Utama Rapat *</label>
                <input type="text" id="editMeetingTitle" name="meeting_title" required class="w-full px-3 py-2 border border-slate-300 rounded-lg font-semibold text-slate-900">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Rapat *</label>
                    <input type="date" id="editMeetingDate" name="meeting_date" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jumlah Hadir (Orang)</label>
                    <input type="number" id="editAttendeesCount" name="attendees_count" min="0" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-bold">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Waktu Mulai</label>
                    <input type="text" id="editStartTime" name="start_time" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Waktu Selesai</label>
                    <input type="text" id="editEndTime" name="end_time" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tempat / Ruang Pelaksanaan *</label>
                <input type="text" id="editLocation" name="location" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pimpinan Rapat *</label>
                    <input type="text" id="editChairperson" name="chairperson_name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg font-medium">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Notulis Rapat *</label>
                    <input type="text" id="editSecretary" name="secretary_name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg font-medium">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Topik / Susunan Acara Rapat</label>
                <textarea id="editAgendaTopics" name="agenda_topics" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Ringkasan Pembahasan & Jalannya Rapat</label>
                <textarea id="editDiscussionSummary" name="discussion_summary" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kesimpulan, Keputusan & Rencana Tindak Lanjut *</label>
                <textarea id="editDecisions" name="decisions_and_follow_up" rows="3" required class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Ganti Berkas Daftar Hadir / Notula (Opsional)</label>
                <input type="file" name="attendance_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700">
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalEditMeeting').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-bold shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditMeetingModal(m) {
    document.getElementById('formEditMeeting').action = '/letters/meeting-notes/' + m.id;
    document.getElementById('editMeetingNo').innerText = 'No: ' + m.meeting_number;
    document.getElementById('editMeetingTitle').value = m.meeting_title || '';
    document.getElementById('editMeetingDate').value = m.meeting_date ? m.meeting_date.substring(0, 10) : '';
    document.getElementById('editAttendeesCount').value = m.attendees_count || 0;
    document.getElementById('editStartTime').value = m.start_time || '';
    document.getElementById('editEndTime').value = m.end_time || '';
    document.getElementById('editLocation').value = m.location || '';
    document.getElementById('editChairperson').value = m.chairperson_name || '';
    document.getElementById('editSecretary').value = m.secretary_name || '';
    document.getElementById('editAgendaTopics').value = m.agenda_topics || '';
    document.getElementById('editDiscussionSummary').value = m.discussion_summary || '';
    document.getElementById('editDecisions').value = m.decisions_and_follow_up || '';
    document.getElementById('modalEditMeeting').classList.remove('hidden');
}
</script>
@endsection
