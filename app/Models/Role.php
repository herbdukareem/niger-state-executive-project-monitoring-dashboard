<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
    ];

    /**
     * Get the permissions for this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Get the users with this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    /**
     * Check if role has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return $this->permissions()->whereIn('name', $permissions)->exists();
    }

    /**
     * Check if role has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        return $this->permissions()->whereIn('name', $permissions)->count() === count($permissions);
    }

    /**
     * Assign permission to role.
     */
    public function givePermission(Permission $permission): void
    {
        if (!$this->hasPermission($permission->name)) {
            $this->permissions()->attach($permission);
        }
    }

    /**
     * Remove permission from role.
     */
    public function revokePermission(Permission $permission): void
    {
        $this->permissions()->detach($permission);
    }

    /**
     * Sync permissions for role.
     */
    public function syncPermissions(array $permissions): void
    {
        $this->permissions()->sync($permissions);
    }

    /**
     * Check if this is a super admin role.
     */
    public function isSuperAdmin(): bool
    {
        return $this->name === 'super_admin';
    }

    /**
     * Check if this is a governor role.
     */
    public function isGovernor(): bool
    {
        return $this->name === 'governor';
    }

    /**
     * Check if this is an admin role.
     */
    public function isAdmin(): bool
    {
        return in_array($this->name, ['governor', 'super_admin', 'admin']);
    }

    /**
     * Get role color for UI.
     */
    public function getColorAttribute(): string
    {
        return match($this->name) {
            'governor' => 'purple',
            'super_admin' => 'red',
            'admin' => 'blue',
            'project_manager' => 'green',
            default => 'gray',
        };
    }

    /**
     * Get role icon for UI.
     */
    public function getIconAttribute(): string
    {
        return match($this->name) {
            'governor' => 'mdi-crown',
            'super_admin' => 'mdi-shield-crown',
            'admin' => 'mdi-shield-account',
            'project_manager' => 'mdi-account-hard-hat',
            default => 'mdi-account',
        };
    }
}
