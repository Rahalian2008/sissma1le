<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsToMany<Permission, $this>
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_has_roles');
    }

    public function givePermission(string $permissionName): void
    {
        $permission = Permission::firstOrCreate([
            'name' => $permissionName,
        ], [
            'display_name' => ucwords(str_replace(['.', '_'], ' ', $permissionName)),
            'module' => explode('.', $permissionName)[0] ?? 'general',
        ]);

        if (! $this->permissions()->where('permission_id', $permission->id)->exists()) {
            $this->permissions()->attach($permission->id);
        }
    }
}
