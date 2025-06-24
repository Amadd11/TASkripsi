<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BenarCara;
use Illuminate\Auth\Access\HandlesAuthorization;

class BenarCaraPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_benar::cara');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BenarCara $benarCara): bool
    {
        return $user->can('view_benar::cara');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_benar::cara');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BenarCara $benarCara): bool
    {
        return $user->can('update_benar::cara');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BenarCara $benarCara): bool
    {
        return $user->can('delete_benar::cara');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_benar::cara');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, BenarCara $benarCara): bool
    {
        return $user->can('force_delete_benar::cara');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_benar::cara');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, BenarCara $benarCara): bool
    {
        return $user->can('restore_benar::cara');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_benar::cara');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, BenarCara $benarCara): bool
    {
        return $user->can('replicate_benar::cara');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_benar::cara');
    }
}
