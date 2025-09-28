<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User Management
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'description' => 'Create, edit, delete users', 'category' => 'users'],
            ['name' => 'view_users', 'display_name' => 'View Users', 'description' => 'View user list and details', 'category' => 'users'],
            ['name' => 'create_users', 'display_name' => 'Create Users', 'description' => 'Create new users', 'category' => 'users'],
            ['name' => 'edit_users', 'display_name' => 'Edit Users', 'description' => 'Edit user information', 'category' => 'users'],
            ['name' => 'delete_users', 'display_name' => 'Delete Users', 'description' => 'Delete users', 'category' => 'users'],

            // Project Management
            ['name' => 'manage_projects', 'display_name' => 'Manage Projects', 'description' => 'Full project management access', 'category' => 'projects'],
            ['name' => 'view_projects', 'display_name' => 'View Projects', 'description' => 'View project list and details', 'category' => 'projects'],
            ['name' => 'create_projects', 'display_name' => 'Create Projects', 'description' => 'Create new projects', 'category' => 'projects'],
            ['name' => 'edit_projects', 'display_name' => 'Edit Projects', 'description' => 'Edit project information', 'category' => 'projects'],
            ['name' => 'delete_projects', 'display_name' => 'Delete Projects', 'description' => 'Delete projects', 'category' => 'projects'],

            // Project Updates
            ['name' => 'manage_updates', 'display_name' => 'Manage Updates', 'description' => 'Full update management access', 'category' => 'updates'],
            ['name' => 'view_updates', 'display_name' => 'View Updates', 'description' => 'View project updates', 'category' => 'updates'],
            ['name' => 'create_updates', 'display_name' => 'Create Updates', 'description' => 'Create project updates', 'category' => 'updates'],
            ['name' => 'edit_updates', 'display_name' => 'Edit Updates', 'description' => 'Edit project updates', 'category' => 'updates'],
            ['name' => 'delete_updates', 'display_name' => 'Delete Updates', 'description' => 'Delete project updates', 'category' => 'updates'],
            ['name' => 'approve_updates', 'display_name' => 'Approve Updates', 'description' => 'Approve project updates', 'category' => 'updates'],

            // Settings
            ['name' => 'manage_settings', 'display_name' => 'Manage Settings', 'description' => 'Access system settings', 'category' => 'settings'],
            ['name' => 'view_settings', 'display_name' => 'View Settings', 'description' => 'View system settings', 'category' => 'settings'],

            // Reports
            ['name' => 'view_reports', 'display_name' => 'View Reports', 'description' => 'Access reports and analytics', 'category' => 'reports'],
            ['name' => 'export_reports', 'display_name' => 'Export Reports', 'description' => 'Export reports and data', 'category' => 'reports'],

            // Dashboard
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard', 'description' => 'Access main dashboard', 'category' => 'dashboard'],
            ['name' => 'view_analytics', 'display_name' => 'View Analytics', 'description' => 'View detailed analytics', 'category' => 'dashboard'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create roles
        $roles = [
            [
                'name' => 'governor',
                'display_name' => 'Governor',
                'description' => 'Governor of Niger State - Full system access',
                'level' => 100,
            ],
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Super Administrator - Full system access',
                'level' => 90,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Administrator - Manage users, projects, and settings',
                'level' => 80,
            ],
            [
                'name' => 'project_manager',
                'display_name' => 'Project Manager/Contractor',
                'description' => 'Project Manager/Contractor - Manage assigned projects and updates',
                'level' => 50,
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );

            // Assign permissions based on role
            $this->assignPermissionsToRole($role);
        }
    }

    private function assignPermissionsToRole(Role $role): void
    {
        $allPermissions = Permission::all();

        switch ($role->name) {
            case 'governor':
            case 'super_admin':
                // Full access to everything
                $role->permissions()->sync($allPermissions->pluck('id'));
                break;

            case 'admin':
                // Can manage users, projects, and settings but not super admin functions
                $adminPermissions = $allPermissions->whereIn('name', [
                    'manage_users', 'view_users', 'create_users', 'edit_users', 'delete_users',
                    'manage_projects', 'view_projects', 'create_projects', 'edit_projects', 'delete_projects',
                    'manage_updates', 'view_updates', 'create_updates', 'edit_updates', 'delete_updates', 'approve_updates',
                    'manage_settings', 'view_settings',
                    'view_reports', 'export_reports',
                    'view_dashboard', 'view_analytics',
                ]);
                $role->permissions()->sync($adminPermissions->pluck('id'));
                break;

            case 'project_manager':
                // Can only manage their own projects and create updates
                $pmPermissions = $allPermissions->whereIn('name', [
                    'view_projects', 'edit_projects', // Only their own projects
                    'view_updates', 'create_updates', 'edit_updates', // Only their own updates
                    'view_dashboard',
                ]);
                $role->permissions()->sync($pmPermissions->pluck('id'));
                break;
        }
    }
}
