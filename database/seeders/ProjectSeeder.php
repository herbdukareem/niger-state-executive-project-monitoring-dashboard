<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectUpdate;
use App\Models\User;
use App\Models\Role;
use App\Models\WorkPlanActivity;
use App\Models\OutputIndicator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $projectManagerRole = Role::where('name', 'project_manager')->first();
        $officerRole = Role::where('name', 'monitoring_and_evaluation_officers')->first();

        // Get or create sample users with different roles
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role_id' => $adminRole?->id,
                'organization' => 'Government Agency',
                'position' => 'Administrator',
            ]
        );

        $projectManager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'John Manager',
                'password' => bcrypt('password'),
                'role_id' => $projectManagerRole?->id,
                'organization' => 'Development Agency',
                'position' => 'Project Manager',
            ]
        );

        $officer = User::firstOrCreate(
            ['email' => 'officer@example.com'],
            [
                'name' => 'Jane Officer',
                'password' => bcrypt('password'),
                'role_id' => $officerRole?->id,
                'organization' => 'Field Office',
                'position' => 'Field Officer',
            ]
        );

        // Create sample projects
        $projects = [
            [
                'name' => 'Water Infrastructure Development',
                'id_code' => 'WID-2024-001',
                'project_manager_id' => $projectManager->id,
                'implementing_organization' => 'Water Development Agency',
                'project_location' => 'Northern Region, District A',
                'sector' => 'Water & Sanitation',
                'start_date' => '2024-01-15',
                'end_date' => '2024-12-31',
                'overall_goal' => 'Improve access to clean water for rural communities',
                'description' => 'This project aims to construct water wells, install pumping systems, and establish water distribution networks in underserved rural areas.',
                'total_budget' => 500000.00,
                'budget_allocated_current_period' => 125000.00,
                'cumulative_expenditure' => 75000.00,
                'status' => 'in_progress',
                'progress_percentage' => 35.5,
                'monitoring_period_start' => '2024-01-01',
                'monitoring_period_end' => '2024-03-31',
                'monitor_name' => 'Jane Officer',
                'monitor_title' => 'Field Monitoring Officer',
                'data_collection_methods' => ['Site Visit', 'Beneficiary Survey', 'Photo Documentation'],
            ],
            [
                'name' => 'Education Quality Enhancement',
                'id_code' => 'EQE-2024-002',
                'project_manager_id' => $projectManager->id,
                'implementing_organization' => 'Education Ministry',
                'project_location' => 'Central Region, Districts B & C',
                'sector' => 'Education',
                'start_date' => '2024-02-01',
                'end_date' => '2025-01-31',
                'overall_goal' => 'Enhance quality of primary education through teacher training and infrastructure improvement',
                'description' => 'Comprehensive program to train teachers, renovate classrooms, and provide educational materials to improve learning outcomes.',
                'total_budget' => 750000.00,
                'budget_allocated_current_period' => 200000.00,
                'cumulative_expenditure' => 120000.00,
                'status' => 'in_progress',
                'progress_percentage' => 28.0,
                'monitoring_period_start' => '2024-02-01',
                'monitoring_period_end' => '2024-04-30',
                'monitor_name' => 'Jane Officer',
                'monitor_title' => 'Education Monitoring Specialist',
                'data_collection_methods' => ['School Visits', 'Teacher Interviews', 'Student Assessments'],
            ],
            [
                'name' => 'Healthcare Access Expansion',
                'id_code' => 'HAE-2024-003',
                'project_manager_id' => $projectManager->id,
                'implementing_organization' => 'Health Department',
                'project_location' => 'Southern Region, Multiple Districts',
                'sector' => 'Health',
                'start_date' => '2024-03-01',
                'end_date' => '2024-11-30',
                'overall_goal' => 'Expand healthcare access in remote areas through mobile clinics and health posts',
                'description' => 'Establish mobile health units and construct health posts to provide basic healthcare services to remote communities.',
                'total_budget' => 300000.00,
                'budget_allocated_current_period' => 100000.00,
                'cumulative_expenditure' => 45000.00,
                'status' => 'in_progress',
                'progress_percentage' => 15.0,
                'monitoring_period_start' => '2024-03-01',
                'monitoring_period_end' => '2024-05-31',
                'monitor_name' => 'Jane Officer',
                'monitor_title' => 'Health Program Monitor',
                'data_collection_methods' => ['Health Facility Visits', 'Patient Surveys', 'Staff Interviews'],
            ],
        ];

        foreach ($projects as $projectData) {
            $project = Project::firstOrCreate(
                ['id_code' => $projectData['id_code']],
                $projectData
            );

            // Create sample work plan activities
            $activities = [
                [
                    'project_id' => $project->id,
                    'activity_name' => 'Site Assessment and Planning',
                    'description' => 'Conduct detailed site assessment and develop implementation plans',
                    'planned_start_date' => $project->start_date,
                    'planned_end_date' => date('Y-m-d', strtotime($project->start_date . ' +30 days')),
                    'status' => 'completed',
                    'progress_percentage' => 100,
                    'responsible_person' => 'Technical Team Lead',
                    'budget_allocated' => 25000.00,
                    'budget_spent' => 23000.00,
                    'priority' => 'high',
                    'category' => 'Planning',
                ],
                [
                    'project_id' => $project->id,
                    'activity_name' => 'Procurement and Logistics',
                    'description' => 'Procure materials and equipment, arrange logistics',
                    'planned_start_date' => date('Y-m-d', strtotime($project->start_date . ' +15 days')),
                    'planned_end_date' => date('Y-m-d', strtotime($project->start_date . ' +60 days')),
                    'status' => 'in_progress',
                    'progress_percentage' => 65,
                    'responsible_person' => 'Procurement Officer',
                    'budget_allocated' => 150000.00,
                    'budget_spent' => 95000.00,
                    'priority' => 'high',
                    'category' => 'Procurement',
                ],
                [
                    'project_id' => $project->id,
                    'activity_name' => 'Implementation Phase 1',
                    'description' => 'Begin construction/implementation of first phase',
                    'planned_start_date' => date('Y-m-d', strtotime($project->start_date . ' +45 days')),
                    'planned_end_date' => date('Y-m-d', strtotime($project->start_date . ' +120 days')),
                    'status' => 'in_progress',
                    'progress_percentage' => 25,
                    'responsible_person' => 'Implementation Team',
                    'budget_allocated' => 200000.00,
                    'budget_spent' => 45000.00,
                    'priority' => 'medium',
                    'category' => 'Implementation',
                ],
            ];

            foreach ($activities as $activityData) {
                WorkPlanActivity::create($activityData);
            }

            // Create sample output indicators
            $indicators = [
                [
                    'project_id' => $project->id,
                    'indicator_name' => 'Number of beneficiaries reached',
                    'description' => 'Total number of direct beneficiaries served by the project',
                    'unit_of_measurement' => 'People',
                    'baseline_value' => 0,
                    'target_value' => 5000,
                    'current_value' => 1200,
                    'data_source' => 'Beneficiary registration records',
                    'collection_method' => 'Registration forms and surveys',
                    'frequency' => 'monthly',
                    'responsible_person' => 'Field Coordinator',
                    'status' => 'on_track',
                ],
                [
                    'project_id' => $project->id,
                    'indicator_name' => 'Infrastructure units completed',
                    'description' => 'Number of infrastructure units (wells, classrooms, clinics) completed',
                    'unit_of_measurement' => 'Units',
                    'baseline_value' => 0,
                    'target_value' => 10,
                    'current_value' => 3,
                    'data_source' => 'Construction progress reports',
                    'collection_method' => 'Site inspections and progress reports',
                    'frequency' => 'weekly',
                    'responsible_person' => 'Construction Supervisor',
                    'status' => 'on_track',
                ],
            ];

            foreach ($indicators as $indicatorData) {
                OutputIndicator::create($indicatorData);
            }

            // Create sample project updates
            $updates = [
                [
                    'project_id' => $project->id,
                    'created_by' => $officer->id,
                    'update_type' => 'progress',
                    'title' => 'Monthly Progress Report - ' . date('F Y'),
                    'description' => 'Regular monthly progress update covering all project activities and milestones achieved.',
                    'progress_percentage' => $project->progress_percentage,
                    'activities_completed' => [
                        'Site assessment completed for all target locations',
                        'Community mobilization meetings conducted',
                        'Procurement process initiated for major equipment',
                    ],
                    'challenges_faced' => [
                        'Delayed delivery of some materials due to supply chain issues',
                        'Weather conditions affecting field work schedule',
                    ],
                    'next_steps' => [
                        'Complete procurement of remaining materials',
                        'Begin construction activities at priority sites',
                        'Conduct training sessions for local staff',
                    ],
                    'status' => 'approved',
                    'submitted_at' => now()->subDays(5),
                    'approved_at' => now()->subDays(2),
                    'approved_by' => $projectManager->id,
                ],
                [
                    'project_id' => $project->id,
                    'created_by' => $officer->id,
                    'update_type' => 'site_visit',
                    'title' => 'Site Visit Report - Primary Implementation Site',
                    'description' => 'Detailed report from site visit to assess progress and conditions at the main implementation site.',
                    'location_visited' => 'Primary Project Site, Village A',
                    'visit_date' => now()->subDays(10),
                    'weather_conditions' => 'Clear and dry',
                    'site_conditions' => 'Good access roads, suitable working conditions. Local community very cooperative and engaged.',
                    'safety_observations' => [
                        'All safety protocols being followed',
                        'Workers using appropriate protective equipment',
                        'Site properly secured and marked',
                    ],
                    'recommendations' => [
                        'Increase frequency of community meetings',
                        'Consider additional safety measures during rainy season',
                        'Establish local maintenance committee',
                    ],
                    'status' => 'pending',
                    'submitted_at' => now()->subDays(3),
                ],
            ];

            foreach ($updates as $updateData) {
                ProjectUpdate::create($updateData);
            }
        }
    }
}
