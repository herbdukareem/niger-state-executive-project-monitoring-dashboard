<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Project::with(['projectManager', 'latestUpdate'])
            ->withCount(['updates', 'attachments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by project manager
        if ($request->filled('manager')) {
            $query->where('project_manager_id', $request->manager);
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

        $projects = $query->paginate(12)->withQueryString();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters' => $request->only(['status', 'manager', 'search', 'sort_by', 'sort_order']),
            'managers' => User::where('role', 'project_manager')->get(['id', 'name']),
            'stats' => [
                'total' => Project::count(),
                'in_progress' => Project::where('status', 'in_progress')->count(),
                'completed' => Project::where('status', 'completed')->count(),
                'overdue' => Project::where('end_date', '<', now())
                    ->where('status', '!=', 'completed')->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Project::class);

        return Inertia::render('Projects/Create', [
            'managers' => User::where('role', 'project_manager')->get(['id', 'name']),
            'sectors' => $this->getSectors(),
            'dataCollectionMethods' => $this->getDataCollectionMethods(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

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
            'monitoring_period_start' => 'nullable|date',
            'monitoring_period_end' => 'nullable|date|after:monitoring_period_start',
            'monitor_name' => 'nullable|string|max:255',
            'monitor_title' => 'nullable|string|max:255',
            'data_collection_methods' => 'nullable|array',
        ]);

        $project = Project::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): Response
    {
        $project->load([
            'projectManager',
            'updates' => function ($query) {
                $query->with(['creator', 'attachments'])->latest()->limit(10);
            },
            'attachments' => function ($query) {
                $query->latest();
            },
            'workPlanActivities' => function ($query) {
                $query->orderBy('planned_start_date');
            },
            'outputIndicators'
        ]);

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'canEdit' => request()->user()->can('update', $project),
            'canDelete' => request()->user()->can('delete', $project),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): Response
    {
        $this->authorize('update', $project);

        return Inertia::render('Projects/Edit', [
            'project' => $project,
            'managers' => User::where('role', 'project_manager')->get(['id', 'name']),
            'sectors' => $this->getSectors(),
            'dataCollectionMethods' => $this->getDataCollectionMethods(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

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
            'progress_percentage' => 'nullable|numeric|min:0|max:100',
            'monitoring_period_start' => 'nullable|date',
            'monitoring_period_end' => 'nullable|date|after:monitoring_period_start',
            'monitor_name' => 'nullable|string|max:255',
            'monitor_title' => 'nullable|string|max:255',
            'data_collection_methods' => 'nullable|array',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    /**
     * Get available sectors.
     */
    private function getSectors(): array
    {
        return [
            'Agriculture',
            'Education',
            'Health',
            'Infrastructure',
            'Water & Sanitation',
            'Energy',
            'Environment',
            'Social Protection',
            'Governance',
            'Economic Development',
            'Technology',
            'Other',
        ];
    }

    /**
     * Get data collection methods.
     */
    private function getDataCollectionMethods(): array
    {
        return [
            'Site Visit',
            'Beneficiary Survey',
            'Stakeholder Interview',
            'Document Review',
            'Technical Assessment',
            'Community Meeting',
            'Focus Group Discussion',
            'Key Informant Interview',
            'Observation',
            'Photo Documentation',
        ];
    }
}
