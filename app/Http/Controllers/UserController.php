<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Extracurricular;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('roles', 'activeRole', 'employee');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::orderBy('level')->get();
        $employees = Employee::orderBy('name')->get();
        $extracurriculars = Extracurricular::orderBy('name')->get();

        return view('users.index', compact('users', 'roles', 'employees', 'extracurriculars'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Fallback for username from email if provided
        if (! $request->filled('username') && $request->filled('email')) {
            $request->merge(['username' => $request->input('email')]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'nip' => ['nullable', 'string', 'max:30', 'unique:users,nip'],
            'email' => ['nullable', 'string', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['nullable', 'string'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['exists:roles,id'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string'],
            'employee_id' => ['nullable', 'exists:employees,id'],
        ]);

        $roleIds = (array) $request->input('role_ids', []);
        $roleNamesInput = (array) $request->input('roles', []);
        if (! empty($roleNamesInput)) {
            $matchedRoles = Role::whereIn('name', $roleNamesInput)->get();
            $roleIds = array_unique(array_merge($roleIds, $matchedRoles->pluck('id')->toArray()));
        }
        if (empty($roleIds) && $request->filled('role_id')) {
            $roleIds = [(int) $request->input('role_id')];
        }

        $selectedRoles = ! empty($roleIds) ? Role::whereIn('id', $roleIds)->get() : collect();
        $roleNames = $selectedRoles->pluck('name')->toArray();
        $primaryRole = $request->input('role') ?: ($roleNames[0] ?? 'guru');
        $primaryRoleModel = Role::where('name', $primaryRole)->first();
        if ($primaryRoleModel && ! in_array($primaryRoleModel->id, $roleIds, true)) {
            $roleIds[] = $primaryRoleModel->id;
            $roleNames[] = $primaryRole;
        }
        $primaryRoleId = $primaryRoleModel?->id ?? ($selectedRoles->first()?->id ?? Role::first()?->id);

        $nip = $validated['nip'] ?? null;
        if (! empty($validated['employee_id'])) {
            $emp = Employee::find($validated['employee_id']);
            if ($emp && ! empty($emp->nip) && empty($nip)) {
                $nip = $emp->nip;
            }
        }

        $email = $validated['email'] ?? null;
        if (empty($email)) {
            $base = Str::slug($validated['username'], '.') ?: 'user';
            $candidate = $base.'@sma1le.sch.id';
            if (User::where('email', $candidate)->exists()) {
                $candidate = $base.'.'.time().'@sma1le.sch.id';
            }
            $email = $candidate;
        }

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'nip' => $nip,
            'email' => $email,
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => $primaryRole,
            'roles' => array_values(array_unique($roleNames ?: [$primaryRole])),
            'is_active' => true,
            'active_role_id' => $primaryRoleId,
        ]);

        if (! empty($roleIds)) {
            $user->roles()->sync($roleIds);
        }

        if (! empty($validated['employee_id'])) {
            Employee::where('id', $validated['employee_id'])->update(['user_id' => $user->id]);
        }

        AuditTrailService::log('create', 'user_management', "Membuat akun pengguna: {$user->name} (Akun: {$user->username})");

        return back()->with('success', 'Pengguna berhasil ditambahkan dengan akun login: '.$user->username);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if (! $request->filled('username') && $request->filled('email')) {
            $request->merge(['username' => $request->input('email')]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username,'.$user->id],
            'nip' => ['nullable', 'string', 'max:30', 'unique:users,nip,'.$user->id],
            'email' => ['nullable', 'string', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['nullable', 'string'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['exists:roles,id'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $roleIds = (array) $request->input('role_ids', []);
        $roleNamesInput = (array) $request->input('roles', []);
        if (! empty($roleNamesInput)) {
            $matchedRoles = Role::whereIn('name', $roleNamesInput)->get();
            $roleIds = array_unique(array_merge($roleIds, $matchedRoles->pluck('id')->toArray()));
        }
        if (empty($roleIds) && $request->filled('role_id')) {
            $roleIds = [(int) $request->input('role_id')];
        }

        if (empty($roleIds)) {
            $roleNames = $user->all_roles;
            $selectedRoles = Role::whereIn('name', $roleNames)->get();
            $roleIds = $selectedRoles->pluck('id')->toArray();
        } else {
            $selectedRoles = Role::whereIn('id', $roleIds)->get();
            $roleNames = $selectedRoles->pluck('name')->toArray();
        }

        $primaryRole = $request->input('role') ?: ($roleNames[0] ?? ($user->role ?? 'guru'));
        $primaryRoleModel = Role::where('name', $primaryRole)->first();
        if ($primaryRoleModel && ! in_array($primaryRoleModel->id, $roleIds, true)) {
            $roleIds[] = $primaryRoleModel->id;
            $roleNames[] = $primaryRole;
        }
        $primaryRoleId = $primaryRoleModel?->id ?? ($selectedRoles->firstWhere('name', $primaryRole)?->id ?? ($selectedRoles->first()?->id ?? null));

        $nip = $validated['nip'] ?? $user->nip;
        if (! empty($validated['employee_id'])) {
            $emp = Employee::find($validated['employee_id']);
            if ($emp && ! empty($emp->nip) && empty($nip)) {
                $nip = $emp->nip;
            }
        }

        $email = $validated['email'] ?? $user->email;
        if (empty($email)) {
            $base = Str::slug($validated['username'], '.') ?: 'user';
            $candidate = $base.'@sma1le.sch.id';
            if (User::where('email', $candidate)->where('id', '!=', $user->id)->exists()) {
                $candidate = $base.'.'.$user->id.'@sma1le.sch.id';
            }
            $email = $candidate;
        }

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'nip' => $nip,
            'email' => $email,
            'phone' => $validated['phone'] ?? null,
            'role' => $primaryRole,
            'roles' => array_values(array_unique($roleNames ?: [$primaryRole])),
            'is_active' => $request->boolean('is_active'),
            'active_role_id' => $primaryRoleId,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        if (! empty($roleIds)) {
            $user->roles()->sync($roleIds);
        }

        if (! empty($validated['employee_id'])) {
            Employee::where('id', $validated['employee_id'])->update(['user_id' => $user->id]);
        }

        AuditTrailService::log('update', 'user_management', "Memperbarui akun pengguna: {$user->name} ({$user->username})");

        return back()->with('success', 'Pengguna dan penugasan peran berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        AuditTrailService::log('delete', 'user_management', "Menghapus akun pengguna: {$userName}");

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
