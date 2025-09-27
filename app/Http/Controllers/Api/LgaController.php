<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lga;
use Illuminate\Http\JsonResponse;

class LgaController extends Controller
{
    /**
     * Display a listing of LGAs.
     */
    public function index(): JsonResponse
    {
        $lgas = Lga::with(['wards:id,lga_id,name,code', 'projects:id,lga_id,progress_percentage,total_budget'])
            ->withCount(['projects', 'wards'])
            ->orderBy('name')
            ->get()
            ->map(function ($lga) {
                // Calculate average progress and total budget for this LGA
                $averageProgress = 0;
                $totalBudget = 0;

                if ($lga->projects->count() > 0) {
                    $averageProgress = $lga->projects->avg('progress_percentage');
                    $totalBudget = $lga->projects->sum('total_budget');
                }

                return [
                    'id' => $lga->id,
                    'name' => $lga->name,
                    'code' => $lga->code,
                    'headquarters' => $lga->headquarters,
                    'zone' => $lga->zone,
                    'latitude' => $lga->latitude,
                    'longitude' => $lga->longitude,
                    'population_estimate' => $lga->population_estimate,
                    'area_km2' => $lga->area_km2,
                    'description' => $lga->description,
                    'projects_count' => $lga->projects_count,
                    'wards_count' => $lga->wards_count,
                    'average_progress' => round($averageProgress, 1),
                    'total_budget' => $totalBudget,
                    'wards' => $lga->wards->map(function ($ward) {
                        return [
                            'id' => $ward->id,
                            'name' => $ward->name,
                            'code' => $ward->code,
                        ];
                    }),
                ];
            });

        return response()->json([
            'data' => $lgas,
            'meta' => [
                'total' => $lgas->count(),
            ]
        ]);
    }

    /**
     * Display the specified LGA.
     */
    public function show(Lga $lga): JsonResponse
    {
        $lga->load(['wards', 'projects' => function ($query) {
            $query->with('projectManager:id,name')->latest();
        }]);

        return response()->json([
            'id' => $lga->id,
            'name' => $lga->name,
            'code' => $lga->code,
            'headquarters' => $lga->headquarters,
            'zone' => $lga->zone,
            'latitude' => $lga->latitude,
            'longitude' => $lga->longitude,
            'population_estimate' => $lga->population_estimate,
            'area_km2' => $lga->area_km2,
            'description' => $lga->description,
            'wards' => $lga->wards->map(function ($ward) {
                return [
                    'id' => $ward->id,
                    'name' => $ward->name,
                    'code' => $ward->code,
                    'latitude' => $ward->latitude,
                    'longitude' => $ward->longitude,
                    'population_estimate' => $ward->population_estimate,
                ];
            }),
            'projects' => $lga->projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'id_code' => $project->id_code,
                    'status' => $project->status,
                    'progress_percentage' => $project->progress_percentage,
                    'total_budget' => $project->total_budget,
                    'project_manager' => $project->projectManager ? [
                        'id' => $project->projectManager->id,
                        'name' => $project->projectManager->name,
                    ] : null,
                ];
            }),
        ]);
    }

    /**
     * Get wards for a specific LGA.
     */
    public function wards(Lga $lga): JsonResponse
    {
        $wards = $lga->wards()
            ->withCount('projects')
            ->orderBy('name')
            ->get()
            ->map(function ($ward) {
                return [
                    'id' => $ward->id,
                    'lga_id' => $ward->lga_id,
                    'name' => $ward->name,
                    'code' => $ward->code,
                    'latitude' => $ward->latitude,
                    'longitude' => $ward->longitude,
                    'population_estimate' => $ward->population_estimate,
                    'projects_count' => $ward->projects_count,
                ];
            });

        return response()->json([
            'data' => $wards,
            'meta' => [
                'total' => $wards->count(),
                'lga' => [
                    'id' => $lga->id,
                    'name' => $lga->name,
                    'code' => $lga->code,
                ]
            ]
        ]);
    }
}
