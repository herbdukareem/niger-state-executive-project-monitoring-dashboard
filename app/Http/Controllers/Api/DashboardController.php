<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
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

        // Progress stats
        $averageProgress = Project::avg('progress_percentage') ?? 0;

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
}
