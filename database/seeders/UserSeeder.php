<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::all()->keyBy('name');

        // Create default users
        $users = [
            [
                'name' => 'Governor Mohammed Umar Bago',
                'email' => 'governor@nigerstate.gov.ng',
                'password' => Hash::make('governor2024'),
                'role_id' => $roles['governor']->id,
                'organization' => 'Niger State Government',
                'position' => 'Executive Governor',
                'phone' => '+234-803-000-0001',
                'address' => 'Government House, Minna, Niger State',
                'is_active' => true,
            ],
            [
                'name' => 'System Administrator',
                'email' => 'admin@nigerstate.gov.ng',
                'password' => Hash::make('admin2024'),
                'role_id' => $roles['super_admin']->id,
                'organization' => 'Niger State ICT Department',
                'position' => 'System Administrator',
                'phone' => '+234-803-000-0002',
                'address' => 'ICT Department, Minna, Niger State',
                'is_active' => true,
            ],
            [
                'name' => 'Project Coordinator',
                'email' => 'coordinator@nigerstate.gov.ng',
                'password' => Hash::make('admin2024'),
                'role_id' => $roles['admin']->id,
                'organization' => 'Niger State Project Management Office',
                'position' => 'Project Coordinator',
                'phone' => '+234-803-000-0003',
                'address' => 'PMO Office, Minna, Niger State',
                'is_active' => true,
            ],
            [
                'name' => 'John Contractor',
                'email' => 'contractor@example.com',
                'password' => Hash::make('contractor2024'),
                'role_id' => $roles['project_manager']->id,
                'organization' => 'ABC Construction Company',
                'position' => 'Project Manager',
                'phone' => '+234-803-000-0004',
                'address' => 'Minna, Niger State',
                'is_active' => true,
            ],
            [
                'name' => 'Sarah Project Manager',
                'email' => 'manager@example.com',
                'password' => Hash::make('manager2024'),
                'role_id' => $roles['project_manager']->id,
                'organization' => 'XYZ Engineering Ltd',
                'position' => 'Senior Project Manager',
                'phone' => '+234-803-000-0005',
                'address' => 'Suleja, Niger State',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Default users created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Governor: governor@nigerstate.gov.ng / governor2024');
        $this->command->info('Super Admin: admin@nigerstate.gov.ng / admin2024');
        $this->command->info('Admin: coordinator@nigerstate.gov.ng / admin2024');
        $this->command->info('Project Manager: contractor@example.com / contractor2024');
    }
}
