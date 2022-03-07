<?php

namespace App\Policies;

use App\Models\RplOccupation;
use App\Models\User;

class RplOccupationPolicy
{
    /**
     * Determine whether the user can view any Rpl Occupation.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_rpl_occupation');
    }

    /**
     * Determine whether the user can view the Rpl Occupation.
     *
     * @param User $authUser
     * @param RplOccupation $rplOccupation
     * @return mixed
     */
    public function view(User $authUser, RplOccupation $rplOccupation): bool
    {
        return $authUser->hasPermission('view_single_rpl_occupation');
    }

    /**
     * Determine whether the user can create Rpl Occupation.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_rpl_occupation');
    }

    /**
     * Determine whether the user can update the Rpl Occupation.
     *
     * @param User $authUser
     * @param RplOccupation $rplOccupation
     * @return mixed
     */
    public function update(User $authUser, RplOccupation $rplOccupation): bool
    {
        return $authUser->hasPermission('update_rpl_occupation');
    }

    /**
     * Determine whether the user can delete the Rpl Occupation.
     *
     * @param User $authUser
     * @param RplOccupation $rplOccupation
     * @return mixed
     */
    public function delete(User $authUser, RplOccupation $rplOccupation): bool
    {
        return $authUser->hasPermission('delete_rpl_occupation');
    }
}
