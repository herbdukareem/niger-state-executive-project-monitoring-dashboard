<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Permission::with('roles');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort by category then name
        $sortBy = $request->get('sort_by', 'category');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if ($sortBy === 'category') {
            $query->orderBy('category', $sortOrder)->orderBy('display_name', 'asc');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $permissions = $query->paginate($request->get('per_page', 15));

        return response()->json($permissions);
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $permission->load('roles');

        return response()->json([
            'message' => 'Permission created successfully',
            'permission' => $permission
        ], 201);
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission): JsonResponse
    {
        $permission->load('roles');
        return response()->json($permission);
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $permission->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $permission->load('roles');

        return response()->json([
            'message' => 'Permission updated successfully',
            'permission' => $permission
        ]);
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission): JsonResponse
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete permission that is assigned to roles'
            ], 422);
        }

        $permission->delete();

        return response()->json([
            'message' => 'Permission deleted successfully'
        ]);
    }

    /**
     * Toggle permission status.
     */
    public function toggleStatus(Permission $permission): JsonResponse
    {
        $permission->update(['is_active' => !$permission->is_active]);

        return response()->json([
            'message' => 'Permission status updated successfully',
            'permission' => $permission
        ]);
    }

    /**
     * Get all permission categories.
     */
    public function getCategories(): JsonResponse
    {
        $categories = Permission::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return response()->json($categories);
    }

    /**
     * Get permissions grouped by category.
     */
    public function getGroupedPermissions(): JsonResponse
    {
        $permissions = Permission::where('is_active', true)
            ->orderBy('category')
            ->orderBy('display_name')
            ->get()
            ->groupBy('category');

        return response()->json($permissions);
    }
}
