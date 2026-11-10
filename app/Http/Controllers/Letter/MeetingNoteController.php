<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Models\MeetingNote;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeetingNoteController extends Controller
{
    public function index(Request $request): View
    {
        $query = MeetingNote::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('meeting_title', 'like', "%{$search}%")
                    ->orWhere('meeting_number', 'like', "%{$search}%")
                    ->orWhere('chairperson_name', 'like', "%{$search}%")
                    ->orWhere('agenda_topics', 'like', "%{$search}%");
            });
        }

        if ($type = $request->query('meeting_type')) {
            $query->where('meeting_type', $type);
        }

        $meetings = $query->latest('meeting_date')->latest('id')->paginate(15)->withQueryString();

        $totalMeetings = MeetingNote::count();
        $thisYearCount = MeetingNote::whereYear('meeting_date', date('Y'))->count();
        $teacherMeetingCount = MeetingNote::where('meeting_type', 'Rapat Dewan Guru')->count();
        $tuMeetingCount = MeetingNote::where('meeting_type', 'Rapat Koordinasi TU')->count();

        $school = SchoolIdentity::current();

        return view('letters.meeting_notes.index', compact(
            'meetings',
            'totalMeetings',
            'thisYearCount',
            'teacherMeetingCount',
            'tuMeetingCount',
            'school'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'meeting_title' => ['required', 'string', 'max:255'],
            'meeting_type' => ['required', 'string', 'max:50'],
            'meeting_date' => ['required', 'date'],
            'start_time' => ['nullable', 'string', 'max:20'],
            'end_time' => ['nullable', 'string', 'max:20'],
            'location' => ['required', 'string', 'max:150'],
            'chairperson_name' => ['required', 'string', 'max:150'],
            'secretary_name' => ['required', 'string', 'max:150'],
            'attendees_count' => ['nullable', 'integer', 'min:0'],
            'agenda_topics' => ['nullable', 'string'],
            'discussion_summary' => ['nullable', 'string'],
            'decisions_and_follow_up' => ['nullable', 'string'],
            'attendance_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['meeting_number'] = MeetingNote::generateMeetingNumber();
        $validated['status'] = 'Final';

        if ($request->hasFile('attendance_file')) {
            $validated['attendance_file_path'] = $request->file('attendance_file')->store('meeting_attendances', 'public');
        }

        $meeting = MeetingNote::create($validated);

        AuditTrailService::log('create', 'persuratan', "Mencatat Notula Rapat: {$meeting->meeting_title} (No: {$meeting->meeting_number})");

        return redirect()->route('letters.meeting-notes.index')->with('success', "Notula rapat berhasil dicatat dengan No. {$meeting->meeting_number}");
    }

    public function update(Request $request, MeetingNote $meetingNote): RedirectResponse
    {
        $validated = $request->validate([
            'meeting_title' => ['required', 'string', 'max:255'],
            'meeting_date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['nullable'],
            'location' => ['required', 'string', 'max:150'],
            'chairperson_name' => ['required', 'string', 'max:150'],
            'secretary_name' => ['required', 'string', 'max:150'],
            'attendees_count' => ['nullable', 'integer', 'min:0'],
            'agenda_topics' => ['nullable', 'string'],
            'discussion_summary' => ['nullable', 'string'],
            'decisions_and_follow_up' => ['nullable', 'string'],
            'attendance_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('attendance_file')) {
            $validated['attendance_file_path'] = $request->file('attendance_file')->store('meeting_attendances', 'public');
        }

        $meetingNote->update($validated);

        AuditTrailService::log('update', 'persuratan', "Memperbarui notula rapat: {$meetingNote->meeting_number}");

        return redirect()->route('letters.meeting-notes.index')->with('success', "Notula rapat No. {$meetingNote->meeting_number} berhasil diperbarui.");
    }

    public function show(MeetingNote $meetingNote): View
    {
        $school = SchoolIdentity::current();

        return view('letters.meeting_notes.show', compact('meetingNote', 'school'));
    }

    public function print(MeetingNote $meetingNote): View
    {
        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'persuratan', "Mencetak Notula Rapat: {$meetingNote->meeting_number}");

        return view('letters.meeting_notes.print', compact('meetingNote', 'school'));
    }

    public function destroy(MeetingNote $meetingNote): RedirectResponse
    {
        $title = $meetingNote->meeting_title;
        $meetingNote->delete();

        AuditTrailService::log('delete', 'persuratan', "Menghapus notula rapat: {$title}");

        return back()->with('success', 'Notula rapat dinas berhasil dihapus.');
    }
}
