<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'id_code',
        'project_manager_id',
        'implementing_organization',
        'project_location',
        'latitude',
        'longitude',
        'lga_id',
        'ward_id',
        'address',
        'location_description',
        'sector',
        'start_date',
        'end_date',
        'overall_goal',
        'description',
        'total_budget',
        'budget_allocated_current_period',
        'cumulative_expenditure',
        'status',
        'progress_percentage',
        'monitoring_period_start',
        'monitoring_period_end',
        'monitor_name',
        'monitor_title',
        'data_collection_methods',
        'work_plan_presentation',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'monitoring_period_start' => 'date',
        'monitoring_period_end' => 'date',
        'total_budget' => 'decimal:2',
        'budget_allocated_current_period' => 'decimal:2',
        'cumulative_expenditure' => 'decimal:2',
        'progress_percentage' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'data_collection_methods' => 'array',
        'work_plan_presentation' => 'boolean',
    ];

    /**
     * Get the project manager for this project.
     */
    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    /**
     * Get all updates for this project.
     */
    public function updates(): HasMany
    {
        return $this->hasMany(ProjectUpdate::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the LGA this project belongs to.
     */
    public function lga(): BelongsTo
    {
        return $this->belongsTo(Lga::class);
    }

    /**
     * Get the ward this project belongs to.
     */
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * Get all attachments for this project.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(ProjectAttachment::class);
    }

    /**
     * Get work plan activities for this project.
     */
    public function workPlanActivities(): HasMany
    {
        return $this->hasMany(WorkPlanActivity::class);
    }

    /**
     * Get output indicators for this project.
     */
    public function outputIndicators(): HasMany
    {
        return $this->hasMany(OutputIndicator::class);
    }

    /**
     * Get the latest project update.
     */
    public function latestUpdate()
    {
        return $this->hasOne(ProjectUpdate::class)->latestOfMany();
    }

    /**
     * Calculate budget utilization percentage.
     */
    public function getBudgetUtilizationAttribute(): float
    {
        if ($this->total_budget == 0) {
            return 0;
        }
        return ($this->cumulative_expenditure / $this->total_budget) * 100;
    }

    /**
     * Get project status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'not_started' => 'gray',
            'in_progress' => 'blue',
            'on_hold' => 'yellow',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if project is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->end_date < now() && $this->status !== 'completed';
    }

    /**
     * Get project duration in days.
     */
    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Get remaining days until project end.
     */
    public function getRemainingDays(): int
    {
        if ($this->status === 'completed') {
            return 0;
        }
        return now()->diffInDays($this->end_date, false);
    }
}
