<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Get permission icon for UI.
     */
    public function getIconAttribute(): string
    {
        return match($this->category) {
            'users' => 'mdi-account-group',
            'projects' => 'mdi-folder-multiple',
            'updates' => 'mdi-update',
            'settings' => 'mdi-cog',
            'reports' => 'mdi-chart-line',
            'dashboard' => 'mdi-view-dashboard',
            default => 'mdi-key',
        };
    }

    /**
     * Get permission color for UI.
     */
    public function getColorAttribute(): string
    {
        return match($this->category) {
            'users' => 'blue',
            'projects' => 'green',
            'updates' => 'orange',
            'settings' => 'purple',
            'reports' => 'red',
            'dashboard' => 'indigo',
            default => 'gray',
        };
    }
}
