<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolIdentityController extends Controller
{
    public function index(): View
    {
        $school = SchoolIdentity::current();

        return view('master.school.index', compact('school'));
    }

    public function update(Request $request): RedirectResponse
    {
        $school = SchoolIdentity::current();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'nss' => ['nullable', 'string', 'max:30'],
            'status' => ['nullable', 'string', 'max:20'],
            'accreditation' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string'],
            'district' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'branch_office' => ['nullable', 'string', 'max:150'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'string', 'email', 'max:100'],
            'website' => ['nullable', 'string', 'max:150'],
            'headmaster_name' => ['nullable', 'string', 'max:150'],
            'headmaster_nip' => ['nullable', 'string', 'max:30'],
            'committee_head' => ['nullable', 'string', 'max:150'],
            'operator_name' => ['nullable', 'string', 'max:150'],
            'treasurer_name' => ['nullable', 'string', 'max:150'],
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'header_image' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'remove_logo' => ['nullable'],
            'remove_header_image' => ['nullable'],
        ]);

        if ($request->filled('remove_logo')) {
            if ($school->logo_path && Storage::disk('public')->exists($school->logo_path)) {
                Storage::disk('public')->delete($school->logo_path);
            }
            $validated['logo_path'] = null;
        }

        if ($request->filled('remove_header_image')) {
            if ($school->header_image_path && Storage::disk('public')->exists($school->header_image_path)) {
                Storage::disk('public')->delete($school->header_image_path);
            }
            $validated['header_image_path'] = null;
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('school', 'public');
            $validated['logo_path'] = $path;
        }

        if ($request->hasFile('header_image')) {
            $path = $request->file('header_image')->store('school', 'public');
            $validated['header_image_path'] = $path;
        }

        unset($validated['logo'], $validated['header_image'], $validated['remove_logo'], $validated['remove_header_image']);

        $oldValues = $school->toArray();
        $school->update($validated);

        AuditTrailService::log('update', 'master_sekolah', 'Memperbarui profil identitas sekolah beserta logo & kop surat', $oldValues, $validated);

        return back()->with('success', 'Identitas sekolah, logo aplikasi, dan kop surat berhasil diperbarui.');
    }
}
