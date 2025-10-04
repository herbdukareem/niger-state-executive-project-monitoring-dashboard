<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectUpdate;
use App\Services\FileUploadService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectUpdateController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Display a listing of project updates.
     */
    public function index(Project $project): Response
    {
        $updates = $project->updates()
            ->with(['creator', 'approver', 'attachments'])
            ->paginate(10);

        return Inertia::render('Projects/Updates/Index', [
            'project' => $project,
            'updates' => $updates,
            'canCreate' => request()->user()->can('create', [ProjectUpdate::class, $project]),
        ]);
    }

    /**
     * Show the form for creating a new project update.
     */
    public function create(Project $project): Response
    {
        $this->authorize('create', [ProjectUpdate::class, $project]);

        return Inertia::render('Projects/Updates/Create', [
            'project' => $project,
            'updateTypes' => $this->getUpdateTypes(),
        ]);
    }

    /**
     * Store a newly created project update.
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('create', [ProjectUpdate::class, $project]);

        $validated = $request->validate([
            'update_type' => 'required|in:progress,financial,quality,site_visit,milestone',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'progress_percentage' => 'nullable|numeric|min:0|max:100',
            'activities_completed' => 'nullable|array',
            'challenges_faced' => 'nullable|array',
            'next_steps' => 'nullable|array',
            'budget_spent_period' => 'nullable|numeric|min:0',
            'cumulative_budget_spent' => 'nullable|numeric|min:0',
            'financial_comments' => 'nullable|string',
            'quality_assessment' => 'nullable|string',
            'quality_observations' => 'nullable|string',
            'deliverables_status' => 'nullable|array',
            'stakeholder_feedback' => 'nullable|array',
            'risk_assessment' => 'nullable|array',
            'mitigation_measures' => 'nullable|array',
            'location_visited' => 'nullable|string|max:255',
            'visit_date' => 'nullable|date',
            'weather_conditions' => 'nullable|string|max:255',
            'site_conditions' => 'nullable|string',
            'safety_observations' => 'nullable|array',
            'recommendations' => 'nullable|array',
            'files' => 'nullable|array|max:10',
            'files.*' => 'file|max:102400|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,csv,mp4,avi,mov',
            'file_descriptions' => 'nullable|array',
            'file_descriptions.*' => 'nullable|string|max:500',
        ]);

        $update = $project->updates()->create([
            ...$validated,
            'created_by' => $request->user()->id,
            'status' => 'draft',
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            $descriptions = $request->get('file_descriptions', []);
            $this->fileUploadService->uploadMultipleFiles(
                $request->file('files'),
                $project,
                $update,
                $descriptions,
                'update_attachment',
                true
            );
        }

        return redirect()->route('projects.updates.show', [$project, $update])
            ->with('success', 'Project update created successfully.');
    }

    /**
     * Display the specified project update.
     */
    public function show(Project $project, ProjectUpdate $update): Response
    {
        $update->load(['creator', 'approver', 'attachments']);

        return Inertia::render('Projects/Updates/Show', [
            'project' => $project,
            'update' => $update,
            'canEdit' => request()->user()->can('update', $update),
            'canApprove' => request()->user()->can('approve', $update),
        ]);
    }

    /**
     * Show the form for editing the specified project update.
     */
    public function edit(Project $project, ProjectUpdate $update): Response
    {
        $this->authorize('update', $update);

        $update->load('attachments');

        return Inertia::render('Projects/Updates/Edit', [
            'project' => $project,
            'update' => $update,
            'updateTypes' => $this->getUpdateTypes(),
        ]);
    }

    /**
     * Update the specified project update.
     */
    public function update(Request $request, Project $project, ProjectUpdate $update)
    {
        $this->authorize('update', $update);

        $validated = $request->validate([
            'update_type' => 'required|in:progress,financial,quality,site_visit,milestone',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'progress_percentage' => 'nullable|numeric|min:0|max:100',
            'activities_completed' => 'nullable|array',
            'challenges_faced' => 'nullable|array',
            'next_steps' => 'nullable|array',
            'budget_spent_period' => 'nullable|numeric|min:0',
            'cumulative_budget_spent' => 'nullable|numeric|min:0',
            'financial_comments' => 'nullable|string',
            'quality_assessment' => 'nullable|string',
            'quality_observations' => 'nullable|string',
            'deliverables_status' => 'nullable|array',
            'stakeholder_feedback' => 'nullable|array',
            'risk_assessment' => 'nullable|array',
            'mitigation_measures' => 'nullable|array',
            'location_visited' => 'nullable|string|max:255',
            'visit_date' => 'nullable|date',
            'weather_conditions' => 'nullable|string|max:255',
            'site_conditions' => 'nullable|string',
            'safety_observations' => 'nullable|array',
            'recommendations' => 'nullable|array',
        ]);

        $update->update($validated);

        return redirect()->route('projects.updates.show', [$project, $update])
            ->with('success', 'Project update updated successfully.');
    }

    /**
     * Remove the specified project update.
     */
    public function destroy(Project $project, ProjectUpdate $update)
    {
        $this->authorize('delete', $update);

        $update->delete();

        return redirect()->route('projects.updates.index', $project)
            ->with('success', 'Project update deleted successfully.');
    }

    /**
     * Submit update for approval.
     */
    public function submit(Project $project, ProjectUpdate $update)
    {
        $this->authorize('update', $update);

        if ($update->status !== 'draft') {
            return back()->with('error', 'Only draft updates can be submitted.');
        }

        $update->submit();

        return back()->with('success', 'Update submitted for approval.');
    }

    /**
     * Approve a project update.
     */
    public function approve(Project $project, ProjectUpdate $update)
    {
        $this->authorize('approve', $update);

        if ($update->status !== 'pending') {
            return back()->with('error', 'Only pending updates can be approved.');
        }

        $update->approve(request()->user());

        return back()->with('success', 'Update approved successfully.');
    }

    /**
     * Reject a project update.
     */
    public function reject(Project $project, ProjectUpdate $update)
    {
        $this->authorize('approve', $update);

        if ($update->status !== 'pending') {
            return back()->with('error', 'Only pending updates can be rejected.');
        }

        $update->reject();

        return back()->with('success', 'Update rejected.');
    }

    /**
     * Get available update types.
     */
    private function getUpdateTypes(): array
    {
        return [
            'progress' => 'Progress Update',
            'financial' => 'Financial Update',
            'quality' => 'Quality Assessment',
            'site_visit' => 'Site Visit Report',
            'milestone' => 'Milestone Achievement',
        ];
    }
}
