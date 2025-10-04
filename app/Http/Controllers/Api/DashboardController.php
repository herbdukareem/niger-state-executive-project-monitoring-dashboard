<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\WorkPlanActivity;
use App\Models\Lga;
use App\Models\Ward;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics.
     */
    public function stats(): JsonResponse
    {
        // Basic project stats
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'in_progress')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $overdueProjects = Project::where('end_date', '<', now())
            ->where('status', '!=', 'completed')->count();

        // Budget stats
        $totalBudget = Project::sum('total_budget');
        $totalExpenditure = Project::sum('cumulative_expenditure');
        $budgetUtilization = $totalBudget > 0 ? ($totalExpenditure / $totalBudget) * 100 : 0;

        // Progress stats (calculated from work plan activities)
        $averageProgress = $this->calculateAverageProgressFromWorkPlan();

        // LGA stats
        $lgaStats = Lga::withCount('projects')
            ->having('projects_count', '>', 0)
            ->orderBy('projects_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($lga) {
                $projects = Project::where('lga_id', $lga->id)->get();
                $totalBudget = $projects->sum('total_budget');
                $averageProgress = $projects->avg('progress_percentage') ?? 0;
                
                return [
                    'id' => $lga->id,
                    'name' => $lga->name,
                    'zone' => $lga->zone,
                    'projects_count' => $lga->projects_count,
                    'total_budget' => $totalBudget,
                    'average_progress' => round($averageProgress, 1),
                ];
            });

        // Zone stats
        $zoneStats = DB::table('lgas')
            ->join('projects', 'lgas.id', '=', 'projects.lga_id')
            ->select('lgas.zone', 
                DB::raw('COUNT(projects.id) as projects_count'),
                DB::raw('SUM(projects.total_budget) as total_budget'),
                DB::raw('AVG(projects.progress_percentage) as average_progress'))
            ->groupBy('lgas.zone')
            ->orderBy('projects_count', 'desc')
            ->get()
            ->map(function ($zone) {
                return [
                    'name' => $zone->zone,
                    'projects_count' => $zone->projects_count,
                    'total_budget' => $zone->total_budget,
                    'average_progress' => round($zone->average_progress, 1),
                ];
            });

        // Status distribution
        $statusStats = Project::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($stat) {
                return [
                    'status' => $stat->status,
                    'count' => $stat->count,
                    'label' => $this->getStatusLabel($stat->status),
                    'color' => $this->getStatusColor($stat->status),
                ];
            });

        // Sector distribution
        $sectorStats = Project::select('sector', DB::raw('COUNT(*) as count'))
            ->groupBy('sector')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($stat) {
                return [
                    'sector' => $stat->sector,
                    'count' => $stat->count,
                ];
            });

        // Recent activity (last 10 projects updated)
        $recentActivity = Project::with(['projectManager:id,name', 'lga:id,name'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'id_code' => $project->id_code,
                    'status' => $project->status,
                    'progress_percentage' => $project->progress_percentage,
                    'lga_name' => $project->lga?->name,
                    'project_manager' => $project->projectManager?->name,
                    'updated_at' => $project->updated_at->format('Y-m-d H:i:s'),
                ];
            });

        // Performance metrics
        $performanceMetrics = [
            'on_time_projects' => Project::where('end_date', '>=', now())
                ->orWhere('status', 'completed')->count(),
            'behind_schedule' => $overdueProjects,
            'budget_variance' => $budgetUtilization - 100, // Positive means over budget
            'completion_rate' => $totalProjects > 0 ? ($completedProjects / $totalProjects) * 100 : 0,
        ];

        // Work plan based metrics
        $workPlanMetrics = $this->getWorkPlanMetrics();

        // Monthly completion trends
        $monthlyTrends = $this->getMonthlyCompletionTrends();

        // Department performance (by sector)
        $departmentPerformance = $this->getDepartmentPerformance();

        return response()->json([
            'overview' => [
                'total_projects' => $totalProjects,
                'active_projects' => $activeProjects,
                'completed_projects' => $completedProjects,
                'overdue_projects' => $overdueProjects,
                'total_budget' => $totalBudget,
                'total_expenditure' => $totalExpenditure,
                'budget_utilization' => round($budgetUtilization, 1),
                'average_progress' => round($averageProgress, 1),
            ],
            'work_plan_metrics' => $workPlanMetrics,
            'monthly_trends' => $monthlyTrends,
            'department_performance' => $departmentPerformance,
            'lga_stats' => $lgaStats,
            'zone_stats' => $zoneStats,
            'status_stats' => $statusStats,
            'sector_stats' => $sectorStats,
            'recent_activity' => $recentActivity,
            'performance_metrics' => $performanceMetrics,
            'alerts' => [
                'overdue_projects' => $overdueProjects,
                'low_progress_projects' => Project::where('progress_percentage', '<', 25)
                    ->where('status', 'in_progress')->count(),
                'high_budget_utilization' => Project::whereRaw('(cumulative_expenditure / total_budget) * 100 > 90')
                    ->where('status', '!=', 'completed')->count(),
            ]
        ]);
    }

    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'not_started' => 'Not Started',
            'in_progress' => 'In Progress',
            'on_hold' => 'On Hold',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    private function getStatusColor(string $status): string
    {
        return match($status) {
            'not_started' => 'gray',
            'in_progress' => 'blue',
            'on_hold' => 'yellow',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Calculate average progress from work plan activities
     */
    private function calculateAverageProgressFromWorkPlan(): float
    {
        $projects = Project::with('workPlanActivities')->get();
        $totalProgress = 0;
        $projectCount = 0;

        foreach ($projects as $project) {
            if ($project->workPlanActivities->count() > 0) {
                $activityProgress = $project->workPlanActivities->avg('progress_percentage') ?? 0;
                $totalProgress += $activityProgress;
                $projectCount++;
            }
        }

        return $projectCount > 0 ? $totalProgress / $projectCount : 0;
    }

    /**
     * Get work plan based metrics
     */
    private function getWorkPlanMetrics(): array
    {
        $totalActivities = WorkPlanActivity::count();
        $completedActivities = WorkPlanActivity::where('status', 'completed')->count();
        $inProgressActivities = WorkPlanActivity::whereIn('status', ['in_progress', 'on_track'])->count();
        $delayedActivities = WorkPlanActivity::where('status', 'delayed')->count();
        $overdueActivities = WorkPlanActivity::where('planned_end_date', '<', now())
            ->where('status', '!=', 'completed')->count();

        // Calculate overall progress by category
        $categoryProgress = WorkPlanActivity::select('category',
                DB::raw('AVG(progress_percentage) as avg_progress'),
                DB::raw('COUNT(*) as total_activities'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_activities'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category,
                    'progress_percentage' => round($item->avg_progress, 1),
                    'total_activities' => $item->total_activities,
                    'completed_activities' => $item->completed_activities,
                    'completion_rate' => $item->total_activities > 0 ?
                        round(($item->completed_activities / $item->total_activities) * 100, 1) : 0
                ];
            });

        return [
            'total_activities' => $totalActivities,
            'completed_activities' => $completedActivities,
            'in_progress_activities' => $inProgressActivities,
            'delayed_activities' => $delayedActivities,
            'overdue_activities' => $overdueActivities,
            'completion_rate' => $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100, 1) : 0,
            'category_progress' => $categoryProgress
        ];
    }

    /**
     * Get monthly completion trends
     */
    private function getMonthlyCompletionTrends(): array
    {
        $trends = [];

        // Get last 6 months of data
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $completedActivities = WorkPlanActivity::where('status', 'completed')
                ->whereBetween('actual_end_date', [$monthStart, $monthEnd])
                ->count();

            $totalActivities = WorkPlanActivity::where('planned_end_date', '<=', $monthEnd)
                ->count();

            $trends[] = [
                'month' => $date->format('M'),
                'year' => $date->format('Y'),
                'completed_activities' => $completedActivities,
                'total_activities' => $totalActivities,
                'completion_rate' => $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100, 1) : 0
            ];
        }

        return $trends;
    }

    /**
     * Get department performance by sector
     */
    private function getDepartmentPerformance(): array
    {
        return Project::select('sector',
                DB::raw('COUNT(*) as total_projects'),
                DB::raw('AVG(progress_percentage) as avg_progress'),
                DB::raw('SUM(total_budget) as total_budget'),
                DB::raw('SUM(cumulative_expenditure) as total_expenditure'))
            ->groupBy('sector')
            ->get()
            ->map(function ($item) {
                // Get work plan activities for this sector
                $sectorProjects = Project::where('sector', $item->sector)->pluck('id');
                $sectorActivities = WorkPlanActivity::whereIn('project_id', $sectorProjects);

                $totalActivities = $sectorActivities->count();
                $completedActivities = $sectorActivities->where('status', 'completed')->count();
                $avgActivityProgress = $sectorActivities->avg('progress_percentage') ?? 0;

                return [
                    'sector' => $item->sector,
                    'total_projects' => $item->total_projects,
                    'avg_progress' => round($item->avg_progress, 1),
                    'total_budget' => $item->total_budget,
                    'total_expenditure' => $item->total_expenditure,
                    'budget_utilization' => $item->total_budget > 0 ?
                        round(($item->total_expenditure / $item->total_budget) * 100, 1) : 0,
                    'total_activities' => $totalActivities,
                    'completed_activities' => $completedActivities,
                    'activity_completion_rate' => $totalActivities > 0 ?
                        round(($completedActivities / $totalActivities) * 100, 1) : 0,
                    'avg_activity_progress' => round($avgActivityProgress, 1)
                ];
            })
            ->sortByDesc('total_projects')
            ->values()
            ->toArray();
    }
}
