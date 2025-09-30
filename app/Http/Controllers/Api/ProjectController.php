<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::with(['projectManager:id,name', 'lga:id,name,latitude,longitude', 'ward:id,name', 'latestUpdate'])
            ->withCount(['updates', 'attachments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by LGA
        if ($request->filled('lga_id')) {
            $query->where('lga_id', $request->lga_id);
        }

        // Filter by ward
        if ($request->filled('ward_id')) {
            $query->where('ward_id', $request->ward_id);
        }

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('id_code', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $projects = $query->get()->map(function ($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'id_code' => $project->id_code,
                'status' => $project->status,
                'progress_percentage' => $project->progress_percentage,
                'total_budget' => $project->total_budget,
                'cumulative_expenditure' => $project->cumulative_expenditure,
                'start_date' => $project->start_date->format('Y-m-d'),
                'end_date' => $project->end_date->format('Y-m-d'),
                'latitude' => $project->latitude,
                'longitude' => $project->longitude,
                'lga_id' => $project->lga_id,
                'lga_name' => $project->lga?->name,
                'lga_latitude' => $project->lga?->latitude,
                'lga_longitude' => $project->lga?->longitude,
                'ward_id' => $project->ward_id,
                'ward_name' => $project->ward?->name,
                'address' => $project->address,
                'location_description' => $project->location_description,
                'sector' => $project->sector,
                'implementing_organization' => $project->implementing_organization,
                'project_manager_id' => $project->project_manager_id,
                'work_plan_presentation' => $project->work_plan_presentation,
                'project_manager' => $project->projectManager ? [
                    'id' => $project->projectManager->id,
                    'name' => $project->projectManager->name,
                ] : null,
                'updates_count' => $project->updates_count,
                'attachments_count' => $project->attachments_count,
                'latest_update' => $project->latestUpdate ? [
                    'id' => $project->latestUpdate->id,
                    'title' => $project->latestUpdate->title,
                    'created_at' => $project->latestUpdate->created_at->format('Y-m-d'),
                ] : null,
                'budget_utilization' => $project->budget_utilization,
                'status_color' => $project->status_color,
                'is_overdue' => $project->isOverdue(),
                'remaining_days' => $project->getRemainingDays(),
            ];
        });

        return response()->json([
            'data' => $projects,
            'meta' => [
                'total' => $projects->count(),
                'stats' => [
                    'total' => Project::count(),
                    'in_progress' => Project::where('status', 'in_progress')->count(),
                    'completed' => Project::where('status', 'completed')->count(),
                    'overdue' => Project::where('end_date', '<', now())
                        ->where('status', '!=', 'completed')->count(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request): JsonResponse
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_code' => 'required|string|max:50|unique:projects',
            'project_manager_id' => 'required|exists:users,id',
            'implementing_organization' => 'required|string|max:255',
            'project_location' => 'required|string',
            'sector' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'overall_goal' => 'required|string',
            'description' => 'required|string',
            'total_budget' => 'required|numeric|min:0',
            'budget_allocated_current_period' => 'nullable|numeric|min:0',
            'lga_id' => 'nullable|exists:lgas,id',
            'ward_id' => 'nullable|exists:wards,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
            'location_description' => 'nullable|string|max:1000',
            'monitoring_period_start' => 'nullable|date',
            'monitoring_period_end' => 'nullable|date|after:monitoring_period_start',
            'monitor_name' => 'nullable|string|max:255',
            'monitor_title' => 'nullable|string|max:255',
            'data_collection_methods' => 'nullable|array',
            'work_plan_presentation' => 'nullable|boolean',
        ]);

        $project = Project::create($validated);

        return response()->json([
            'message' => 'Project created successfully',
            'data' => $project->load(['projectManager:id,name', 'lga:id,name', 'ward:id,name'])
        ], 201);
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project): JsonResponse
    {
        $project->load(['projectManager:id,name', 'lga:id,name', 'ward:id,name', 'latestUpdate', 'updates' => function ($query) {
            $query->latest()->limit(5);
        }]);

        return response()->json([
            'id' => $project->id,
            'name' => $project->name,
            'id_code' => $project->id_code,
            'status' => $project->status,
            'progress_percentage' => $project->progress_percentage,
            'total_budget' => $project->total_budget,
            'cumulative_expenditure' => $project->cumulative_expenditure,
            'start_date' => $project->start_date->format('Y-m-d'),
            'end_date' => $project->end_date->format('Y-m-d'),
            'latitude' => $project->latitude,
            'longitude' => $project->longitude,
            'lga_id' => $project->lga_id,
            'lga_name' => $project->lga?->name,
            'ward_id' => $project->ward_id,
            'ward_name' => $project->ward?->name,
            'address' => $project->address,
            'location_description' => $project->location_description,
            'sector' => $project->sector,
            'implementing_organization' => $project->implementing_organization,
            'overall_goal' => $project->overall_goal,
            'description' => $project->description,
            'project_manager_id' => $project->project_manager_id,
            'work_plan_presentation' => $project->work_plan_presentation,
            'project_manager' => $project->projectManager ? [
                'id' => $project->projectManager->id,
                'name' => $project->projectManager->name,
            ] : null,
            'latest_update' => $project->latestUpdate ? [
                'id' => $project->latestUpdate->id,
                'title' => $project->latestUpdate->title,
                'created_at' => $project->latestUpdate->created_at->format('Y-m-d'),
            ] : null,
            'recent_updates' => $project->updates->map(function ($update) {
                return [
                    'id' => $update->id,
                    'title' => $update->title,
                    'created_at' => $update->created_at->format('Y-m-d'),
                ];
            }),
            'budget_utilization' => $project->budget_utilization,
            'status_color' => $project->status_color,
            'is_overdue' => $project->isOverdue(),
            'remaining_days' => $project->getRemainingDays(),
        ]);
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, Project $project): JsonResponse
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_code' => 'required|string|max:50|unique:projects,id_code,' . $project->id,
            'project_manager_id' => 'required|exists:users,id',
            'implementing_organization' => 'required|string|max:255',
            'project_location' => 'required|string',
            'sector' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'overall_goal' => 'required|string',
            'description' => 'required|string',
            'total_budget' => 'required|numeric|min:0',
            'budget_allocated_current_period' => 'nullable|numeric|min:0',
            'cumulative_expenditure' => 'nullable|numeric|min:0',
            'status' => 'required|in:not_started,in_progress,on_hold,completed,cancelled',
            'progress_percentage' => 'required|numeric|min:0|max:100',
            'lga_id' => 'nullable|exists:lgas,id',
            'ward_id' => 'nullable|exists:wards,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
            'location_description' => 'nullable|string|max:1000',
            'monitoring_period_start' => 'nullable|date',
            'monitoring_period_end' => 'nullable|date|after:monitoring_period_start',
            'monitor_name' => 'nullable|string|max:255',
            'monitor_title' => 'nullable|string|max:255',
            'data_collection_methods' => 'nullable|array',
            'work_plan_presentation' => 'nullable|boolean',
        ]);

        $project->update($validated);

        return response()->json([
            'message' => 'Project updated successfully',
            'data' => $project->load(['projectManager:id,name', 'lga:id,name', 'ward:id,name'])
        ]);
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully'
        ]);
    }
}
