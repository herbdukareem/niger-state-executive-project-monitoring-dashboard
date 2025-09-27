<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ward;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WardController extends Controller
{
    /**
     * Display a listing of wards.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Ward::with(['lga:id,name,code'])
            ->withCount('projects');

        // Filter by LGA
        if ($request->filled('lga_id')) {
            $query->where('lga_id', $request->lga_id);
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $wards = $query->orderBy('name')
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
                    'description' => $ward->description,
                    'projects_count' => $ward->projects_count,
                    'lga' => $ward->lga ? [
                        'id' => $ward->lga->id,
                        'name' => $ward->lga->name,
                        'code' => $ward->lga->code,
                    ] : null,
                ];
            });

        return response()->json([
            'data' => $wards,
            'meta' => [
                'total' => $wards->count(),
            ]
        ]);
    }

    /**
     * Display the specified ward.
     */
    public function show(Ward $ward): JsonResponse
    {
        $ward->load(['lga', 'projects' => function ($query) {
            $query->with('projectManager:id,name')->latest();
        }]);

        return response()->json([
            'id' => $ward->id,
            'lga_id' => $ward->lga_id,
            'name' => $ward->name,
            'code' => $ward->code,
            'latitude' => $ward->latitude,
            'longitude' => $ward->longitude,
            'population_estimate' => $ward->population_estimate,
            'description' => $ward->description,
            'lga' => $ward->lga ? [
                'id' => $ward->lga->id,
                'name' => $ward->lga->name,
                'code' => $ward->lga->code,
                'headquarters' => $ward->lga->headquarters,
                'zone' => $ward->lga->zone,
            ] : null,
            'projects' => $ward->projects->map(function ($project) {
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
}
