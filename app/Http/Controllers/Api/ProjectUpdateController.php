<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectUpdate;
use App\Models\ProjectAttachment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProjectUpdateController extends Controller
{
    /**
     * Display a listing of project updates.
     */
    public function index(Project $project): JsonResponse
    {
        $updates = $project->updates()
            ->with(['creator:id,name', 'approver:id,name', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($update) {
                return [
                    'id' => $update->id,
                    'update_type' => $update->update_type,
                    'title' => $update->title,
                    'description' => $update->description,
                    'progress_percentage' => $update->progress_percentage,
                    'status' => $update->status,
                    'created_at' => $update->created_at->format('Y-m-d H:i:s'),
                    'creator' => $update->creator ? [
                        'id' => $update->creator->id,
                        'name' => $update->creator->name,
                    ] : null,
                    'attachments' => $update->attachments->map(function ($attachment) {
                        return [
                            'id' => $attachment->id,
                            'original_filename' => $attachment->original_filename,
                            'file_size' => $attachment->file_size,
                            'mime_type' => $attachment->mime_type,
                            'description' => $attachment->description,
                            'download_url' => $attachment->download_url,
                        ];
                    }),
                    'attachments_count' => $update->attachments->count(),
                ];
            });

        return response()->json([
            'data' => $updates,
            'meta' => [
                'total' => $updates->count(),
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'id_code' => $project->id_code,
                ]
            ]
        ]);
    }

    /**
     * Store a newly created project update.
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'update_type' => 'required|in:progress,financial,quality,site_visit,milestone,work_plan_activities',
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

        DB::beginTransaction();
        
        try {
            // Create the project update
            $update = $project->updates()->create([
                ...$validated,
                'created_by' => $request->user()?->id ?? 1, // Default to user ID 1 if no auth
                'status' => 'draft',
            ]);

            // Log the created update for debugging
            \Log::info('Project update created', [
                'update_id' => $update->id,
                'project_id' => $project->id,
                'title' => $update->title,
                'user_id' => $request->user()?->id ?? 1
            ]);

            // Update project progress if provided
            if (isset($validated['progress_percentage'])) {
                $project->update([
                    'progress_percentage' => $validated['progress_percentage']
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Project update created successfully',
                'data' => [
                    'id' => $update->id,
                    'update_type' => $update->update_type,
                    'title' => $update->title,
                    'description' => $update->description,
                    'progress_percentage' => $update->progress_percentage,
                    'status' => $update->status,
                    'created_at' => $update->created_at->format('Y-m-d H:i:s'),
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('Error creating project update', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error creating project update',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified project update.
     */
    public function show(Project $project, ProjectUpdate $update): JsonResponse
    {
        $update->load(['creator:id,name', 'approver:id,name', 'attachments']);

        return response()->json([
            'id' => $update->id,
            'project_id' => $update->project_id,
            'update_type' => $update->update_type,
            'title' => $update->title,
            'description' => $update->description,
            'progress_percentage' => $update->progress_percentage,
            'activities_completed' => $update->activities_completed,
            'challenges_faced' => $update->challenges_faced,
            'next_steps' => $update->next_steps,
            'budget_spent_period' => $update->budget_spent_period,
            'cumulative_budget_spent' => $update->cumulative_budget_spent,
            'financial_comments' => $update->financial_comments,
            'quality_assessment' => $update->quality_assessment,
            'quality_observations' => $update->quality_observations,
            'deliverables_status' => $update->deliverables_status,
            'stakeholder_feedback' => $update->stakeholder_feedback,
            'risk_assessment' => $update->risk_assessment,
            'mitigation_measures' => $update->mitigation_measures,
            'location_visited' => $update->location_visited,
            'visit_date' => $update->visit_date?->format('Y-m-d'),
            'weather_conditions' => $update->weather_conditions,
            'site_conditions' => $update->site_conditions,
            'safety_observations' => $update->safety_observations,
            'recommendations' => $update->recommendations,
            'status' => $update->status,
            'created_at' => $update->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $update->updated_at->format('Y-m-d H:i:s'),
            'creator' => $update->creator ? [
                'id' => $update->creator->id,
                'name' => $update->creator->name,
            ] : null,
            'attachments' => $update->attachments->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'filename' => $attachment->filename,
                    'original_filename' => $attachment->original_filename,
                    'file_size' => $attachment->file_size,
                    'mime_type' => $attachment->mime_type,
                    'description' => $attachment->description,
                    'download_url' => $attachment->url,
                    'human_file_size' => $attachment->human_file_size,
                    'is_image' => $attachment->isImage(),
                ];
            }),
        ]);
    }

    /**
     * Update the specified project update.
     */
    public function update(Request $request, Project $project, ProjectUpdate $update): JsonResponse
    {
        $validated = $request->validate([
            'update_type' => 'required|in:progress,financial,quality,site_visit,milestone,work_plan_activities',
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

        // Update project progress if provided
        if (isset($validated['progress_percentage'])) {
            $project->update([
                'progress_percentage' => $validated['progress_percentage']
            ]);
        }

        return response()->json([
            'message' => 'Project update updated successfully',
            'data' => $update->fresh()
        ]);
    }

    /**
     * Remove the specified project update.
     */
    public function destroy(Project $project, ProjectUpdate $update): JsonResponse
    {
        // Delete associated attachments from storage
        foreach ($update->attachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        }

        $update->delete();

        return response()->json([
            'message' => 'Project update deleted successfully'
        ]);
    }
}
