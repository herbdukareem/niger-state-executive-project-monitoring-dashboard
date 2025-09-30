<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkPlanActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'activity_number',
        'activity_name',
        'description',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'status',
        'progress_percentage',
        'is_tracked',
        'is_completed',
        'percentage_complete',
        'variance_comments',
        'responsible_person',
        'budget_allocated',
        'budget_spent',
        'deliverables',
        'milestones',
        'dependencies',
        'priority',
        'category',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'progress_percentage' => 'decimal:2',
        'percentage_complete' => 'decimal:2',
        'is_tracked' => 'boolean',
        'is_completed' => 'boolean',
        'budget_allocated' => 'decimal:2',
        'budget_spent' => 'decimal:2',
        'deliverables' => 'array',
        'milestones' => 'array',
        'dependencies' => 'array',
    ];

    /**
     * Get the project this activity belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Check if activity is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->planned_end_date < now() && $this->status !== 'completed';
    }

    /**
     * Check if activity is on track.
     */
    public function isOnTrack(): bool
    {
        if ($this->status === 'completed') {
            return true;
        }

        $totalDays = $this->planned_start_date->diffInDays($this->planned_end_date);
        $elapsedDays = $this->planned_start_date->diffInDays(now());
        $expectedProgress = ($elapsedDays / $totalDays) * 100;

        return $this->progress_percentage >= $expectedProgress;
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'not_started' => 'gray',
            'in_progress' => $this->isOnTrack() ? 'blue' : 'yellow',
            'completed' => 'green',
            'on_hold' => 'orange',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get budget utilization percentage.
     */
    public function getBudgetUtilizationAttribute(): float
    {
        if ($this->budget_allocated == 0) {
            return 0;
        }
        return ($this->budget_spent / $this->budget_allocated) * 100;
    }
}
