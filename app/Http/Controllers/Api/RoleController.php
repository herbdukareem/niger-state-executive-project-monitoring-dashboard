<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Role::with(['permissions', 'users']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort by level (highest first) or name
        $sortBy = $request->get('sort_by', 'level');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $roles = $query->paginate($request->get('per_page', 15));

        return response()->json($roles);
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Sync permissions if provided
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        $role->load(['permissions', 'users']);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role
        ], 201);
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): JsonResponse
    {
        $role->load(['permissions', 'users']);
        return response()->json($role);
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        // Prevent modification of system roles
        if (in_array($role->name, ['governor', 'super_admin'])) {
            return response()->json([
                'message' => 'System roles cannot be modified'
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Sync permissions if provided
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        $role->load(['permissions', 'users']);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role
        ]);
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): JsonResponse
    {
        // Prevent deletion of system roles
        if (in_array($role->name, ['governor', 'super_admin', 'admin'])) {
            return response()->json([
                'message' => 'System roles cannot be deleted'
            ], 403);
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete role that has assigned users'
            ], 422);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }

    /**
     * Toggle role status.
     */
    public function toggleStatus(Role $role): JsonResponse
    {
        // Prevent deactivation of system roles
        if (in_array($role->name, ['governor', 'super_admin']) && $role->is_active) {
            return response()->json([
                'message' => 'System roles cannot be deactivated'
            ], 403);
        }

        $role->update(['is_active' => !$role->is_active]);

        return response()->json([
            'message' => 'Role status updated successfully',
            'role' => $role
        ]);
    }

    /**
     * Get all permissions grouped by category.
     */
    public function getPermissions(): JsonResponse
    {
        $permissions = Permission::where('is_active', true)
            ->orderBy('category')
            ->orderBy('display_name')
            ->get()
            ->groupBy('category');

        return response()->json($permissions);
    }

    /**
     * Assign permission to role.
     */
    public function assignPermission(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'permission_id' => 'required|exists:permissions,id'
        ]);

        $permission = Permission::find($validated['permission_id']);
        $role->givePermission($permission);

        return response()->json([
            'message' => 'Permission assigned successfully'
        ]);
    }

    /**
     * Remove permission from role.
     */
    public function revokePermission(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'permission_id' => 'required|exists:permissions,id'
        ]);

        $permission = Permission::find($validated['permission_id']);
        $role->revokePermission($permission);

        return response()->json([
            'message' => 'Permission revoked successfully'
        ]);
    }

    /**
     * Bulk assign permissions to role.
     */
    public function syncPermissions(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return response()->json([
            'message' => 'Permissions synchronized successfully'
        ]);
    }
}
