<?php

namespace App\Policies;

use App\Models\RtoBatch;
use App\Models\User;

class RtoBatchPolicy
{
    /**
     * Determine whether the user can view any Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_rto_batch') || $authUser->hasPermission('view_any_rpl_batch');
    }

    /**
     * Determine whether the user can view the Rpl Sector.
     *
     * @param User $authUser
     * @param RtoBatch $rtoBatch
     * @return mixed
     */
    public function view(User $authUser, RtoBatch $rtoBatch): bool
    {
        return $authUser->hasPermission('view_single_rto_batch');
    }

    /**
     * Determine whether the user can create Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_rto_batch');
    }

    /**
     * Determine whether the user can update the Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function update(User $authUser): bool
    {
        return $authUser->hasPermission('update_rto_batch');
    }

    /**
     * Determine whether the user can delete the Rpl Sector.
     *
     * @param User $authUser
     * @param RtoBatch $rtoBatch
     * @return mixed
     */
    public function delete(User $authUser, RtoBatch $rtoBatch): bool
    {
        return $authUser->hasPermission('delete_rto_batch');
    }
}
