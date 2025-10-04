<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User;

class ProjectUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'created_by',
        'update_type',
        'title',
        'description',
        'progress_percentage',
        'activities_completed',
        'challenges_faced',
        'next_steps',
        'budget_spent_period',
        'cumulative_budget_spent',
        'financial_comments',
        'quality_assessment',
        'quality_observations',
        'deliverables_status',
        'stakeholder_feedback',
        'risk_assessment',
        'mitigation_measures',
        'location_visited',
        'visit_date',
        'weather_conditions',
        'site_conditions',
        'safety_observations',
        'recommendations',
        'status',
        'submitted_at',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'budget_spent_period' => 'decimal:2',
        'cumulative_budget_spent' => 'decimal:2',
        'visit_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'activities_completed' => 'array',
        'challenges_faced' => 'array',
        'next_steps' => 'array',
        'deliverables_status' => 'array',
        'stakeholder_feedback' => 'array',
        'risk_assessment' => 'array',
        'mitigation_measures' => 'array',
        'safety_observations' => 'array',
        'recommendations' => 'array',
    ];

    /**
     * Get the project this update belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who created this update.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this update.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all attachments for this update.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(ProjectAttachment::class, 'project_update_id');
    }

    /**
     * Get photos attached to this update.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(ProjectAttachment::class, 'project_update_id')
                    ->where('type', 'photo');
    }

    /**
     * Get documents attached to this update.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ProjectAttachment::class, 'project_update_id')
                    ->where('type', 'document');
    }

    /**
     * Check if update is pending approval.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if update is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if update is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Approve the update.
     */
    public function approve(User $approver): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approver->id,
        ]);
    }

    /**
     * Reject the update.
     */
    public function reject(): void
    {
        $this->update([
            'status' => 'rejected',
        ]);
    }

    /**
     * Submit the update for approval.
     */
    public function submit(): void
    {
        $this->update([
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }
}
