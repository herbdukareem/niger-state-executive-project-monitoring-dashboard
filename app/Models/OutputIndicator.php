<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutputIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'indicator_name',
        'description',
        'unit_of_measurement',
        'baseline_value',
        'target_value',
        'current_value',
        'data_source',
        'collection_method',
        'frequency',
        'responsible_person',
        'last_updated',
        'status',
        'comments',
        'verification_method',
        'assumptions',
        'risks',
    ];

    protected $casts = [
        'baseline_value' => 'decimal:2',
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'last_updated' => 'datetime',
        'assumptions' => 'array',
        'risks' => 'array',
    ];

    /**
     * Get the project this indicator belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Calculate achievement percentage.
     */
    public function getAchievementPercentageAttribute(): float
    {
        if ($this->target_value == 0) {
            return 0;
        }
        
        $progress = $this->current_value - $this->baseline_value;
        $target = $this->target_value - $this->baseline_value;
        
        if ($target == 0) {
            return 100;
        }
        
        return min(($progress / $target) * 100, 100);
    }

    /**
     * Check if indicator is on track.
     */
    public function isOnTrack(): bool
    {
        return $this->achievement_percentage >= 80;
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        $achievement = $this->achievement_percentage;
        
        if ($achievement >= 100) {
            return 'green';
        } elseif ($achievement >= 80) {
            return 'blue';
        } elseif ($achievement >= 60) {
            return 'yellow';
        } else {
            return 'red';
        }
    }

    /**
     * Get variance from target.
     */
    public function getVarianceAttribute(): float
    {
        return $this->current_value - $this->target_value;
    }

    /**
     * Check if indicator has positive variance.
     */
    public function hasPositiveVariance(): bool
    {
        return $this->variance > 0;
    }
}
