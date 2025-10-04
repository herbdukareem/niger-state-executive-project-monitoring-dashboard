<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\WorkPlanActivity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class WorkPlanActivityController extends Controller
{
    /**
     * Store multiple work plan activities for a project.
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        try {
            $validated = $request->validate([
                'activities' => 'required|array',
                'activities.*.activity_number' => 'nullable|string|max:50',
                'activities.*.activity_name' => 'required|string|max:500',
                'activities.*.planned_start_date' => 'required|date',
                'activities.*.planned_end_date' => 'required|date|after_or_equal:activities.*.planned_start_date',
                'activities.*.actual_start_date' => 'nullable|date',
                'activities.*.actual_end_date' => 'nullable|date',
                'activities.*.status' => 'required|in:not_started,in_progress,on_track,delayed,completed,on_hold,cancelled',
                'activities.*.percentage_complete' => 'required|numeric|min:0|max:100',
                'activities.*.variance_comments' => 'nullable|string|max:1000',
                'activities.*.responsible_person' => 'nullable|string|max:255',
                'activities.*.is_tracked' => 'boolean',
                'activities.*.is_completed' => 'boolean',
            ]);

            $activities = [];
            foreach ($validated['activities'] as $activityData) {
                $activity = $project->workPlanActivities()->create([
                    'activity_number' => $activityData['activity_number'] ?? '',
                    'activity_name' => $activityData['activity_name'],
                    'planned_start_date' => $activityData['planned_start_date'],
                    'planned_end_date' => $activityData['planned_end_date'],
                    'actual_start_date' => $activityData['actual_start_date'] ?? null,
                    'actual_end_date' => $activityData['actual_end_date'] ?? null,
                    'status' => $activityData['status'],
                    'percentage_complete' => $activityData['percentage_complete'],
                    'variance_comments' => $activityData['variance_comments'] ?? '',
                    'is_tracked' => $activityData['is_tracked'] ?? true,
                    'is_completed' => $activityData['is_completed'] ?? false,
                    // Set default values for existing required fields
                    'description' => $activityData['activity_name'],
                    'progress_percentage' => $activityData['percentage_complete'],
                    'responsible_person' => $activityData['responsible_person'] ?? 'TBD',
                    'priority' => 'medium',
                ]);

                $activities[] = $activity;
            }

            return response()->json([
                'message' => 'Work plan activities created successfully',
                'data' => $activities
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating work plan activities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get work plan activities for a project.
     */
    public function index(Project $project): JsonResponse
    {
        $activities = $project->workPlanActivities()
            ->orderBy('activity_number')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'data' => $activities
        ]);
    }

    /**
     * Update a work plan activity.
     */
    public function update(Request $request, Project $project, WorkPlanActivity $activity): JsonResponse
    {
        // Ensure the activity belongs to the project
        if ($activity->project_id !== $project->id) {
            return response()->json([
                'message' => 'Activity not found for this project'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'activity_number' => 'nullable|string|max:50',
                'activity_name' => 'required|string|max:500',
                'planned_start_date' => 'required|date',
                'planned_end_date' => 'required|date|after_or_equal:planned_start_date',
                'actual_start_date' => 'nullable|date',
                'actual_end_date' => 'nullable|date',
                'status' => 'required|in:not_started,in_progress,on_track,delayed,completed,on_hold,cancelled',
                'percentage_complete' => 'required|numeric|min:0|max:100',
                'variance_comments' => 'nullable|string|max:1000',
                'is_tracked' => 'boolean',
                'is_completed' => 'boolean',
            ]);

            $activity->update([
                'activity_number' => $validated['activity_number'] ?? '',
                'activity_name' => $validated['activity_name'],
                'planned_start_date' => $validated['planned_start_date'],
                'planned_end_date' => $validated['planned_end_date'],
                'actual_start_date' => $validated['actual_start_date'] ?? null,
                'actual_end_date' => $validated['actual_end_date'] ?? null,
                'status' => $validated['status'],
                'percentage_complete' => $validated['percentage_complete'],
                'variance_comments' => $validated['variance_comments'] ?? '',
                'is_tracked' => $validated['is_tracked'] ?? true,
                'is_completed' => $validated['is_completed'] ?? false,
                // Update existing fields
                'description' => $validated['activity_name'],
                'progress_percentage' => $validated['percentage_complete'],
            ]);

            return response()->json([
                'message' => 'Work plan activity updated successfully',
                'data' => $activity->fresh()
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating work plan activity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a work plan activity.
     */
    public function destroy(Project $project, WorkPlanActivity $activity): JsonResponse
    {
        // Ensure the activity belongs to the project
        if ($activity->project_id !== $project->id) {
            return response()->json([
                'message' => 'Activity not found for this project'
            ], 404);
        }

        $activity->delete();

        return response()->json([
            'message' => 'Work plan activity deleted successfully'
        ]);
    }
}
