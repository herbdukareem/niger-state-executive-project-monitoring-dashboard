<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lga extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'headquarters',
        'zone',
        'latitude',
        'longitude',
        'population_estimate',
        'area_km2',
        'description',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'population_estimate' => 'integer',
        'area_km2' => 'decimal:2',
    ];

    /**
     * Get all wards for this LGA.
     */
    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class);
    }

    /**
     * Get all projects for this LGA.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
