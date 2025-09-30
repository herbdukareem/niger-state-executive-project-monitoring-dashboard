<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'organization',
        'position',
        'phone',
        'address',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the role for this user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the projects assigned to this user.
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    /**
     * Get the project updates created by this user.
     */
    public function projectUpdates()
    {
        return $this->hasMany(ProjectUpdate::class, 'created_by');
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->role?->hasPermission($permission) ?? false;
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return $this->role?->hasAnyPermission($permissions) ?? false;
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        return $this->role?->hasAllPermissions($permissions) ?? false;
    }

    /**
     * Check if user is a governor.
     */
    public function isGovernor(): bool
    {
        return $this->role?->name === 'governor';
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role?->name === 'super_admin';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    /**
     * Check if user is a project manager.
     */
    public function isProjectManager(): bool
    {
        return $this->role?->name === 'project_manager';
    }

    /**
     * Check if user can manage users (create, edit, delete).
     */
    public function canManageUsers(): bool
    {
        return $this->hasPermission('manage_users');
    }

    /**
     * Check if user can manage projects.
     */
    public function canManageProjects(): bool
    {
        return $this->hasPermission('manage_projects');
    }

    /**
     * Check if user can manage settings.
     */
    public function canManageSettings(): bool
    {
        return $this->hasPermission('manage_settings');
    }

    /**
     * Get user's role name.
     */
    public function getRoleNameAttribute(): string
    {
        return $this->role?->display_name ?? 'No Role';
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}
