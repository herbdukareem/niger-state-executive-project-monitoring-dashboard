<?php

namespace App\Policies;

use App\Models\ProjectUpdate;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectUpdatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProjectUpdate $projectUpdate): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models with a project.
     */
    public function create(User $user, $project = null): bool
    {
        return true; // All authenticated users can create updates
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProjectUpdate $projectUpdate): bool
    {
        return $user->isAdmin() ||
               $projectUpdate->created_by === $user->id ||
               ($user->isProjectManager() && $projectUpdate->project->project_manager_id === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProjectUpdate $projectUpdate): bool
    {
        return $user->isAdmin() ||
               $projectUpdate->created_by === $user->id ||
               ($user->isProjectManager() && $projectUpdate->project->project_manager_id === $user->id);
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, ProjectUpdate $projectUpdate): bool
    {
        return $user->isAdmin() ||
               ($user->isProjectManager() && $projectUpdate->project->project_manager_id === $user->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProjectUpdate $projectUpdate): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProjectUpdate $projectUpdate): bool
    {
        return false;
    }
}
