<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = [
        'lga_id',
        'name',
        'code',
        'latitude',
        'longitude',
        'population_estimate',
        'description',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'population_estimate' => 'integer',
    ];

    /**
     * Get the LGA this ward belongs to.
     */
    public function lga(): BelongsTo
    {
        return $this->belongsTo(Lga::class);
    }

    /**
     * Get all projects for this ward.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
