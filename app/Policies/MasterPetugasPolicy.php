<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MasterPetugas;
use Illuminate\Auth\Access\HandlesAuthorization;

class MasterPetugasPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_master::petugas');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MasterPetugas $masterPetugas): bool
    {
        return $user->can('view_master::petugas');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_master::petugas');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MasterPetugas $masterPetugas): bool
    {
        return $user->can('update_master::petugas');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MasterPetugas $masterPetugas): bool
    {
        return $user->can('delete_master::petugas');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_master::petugas');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, MasterPetugas $masterPetugas): bool
    {
        return $user->can('force_delete_master::petugas');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_master::petugas');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, MasterPetugas $masterPetugas): bool
    {
        return $user->can('restore_master::petugas');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_master::petugas');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, MasterPetugas $masterPetugas): bool
    {
        return $user->can('replicate_master::petugas');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_master::petugas');
    }
}
