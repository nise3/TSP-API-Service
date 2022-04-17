<?php

namespace App\Policies;

use App\Models\RplSector;
use App\Models\User;

class RtoCountryPolicy
{
    /**
     * Determine whether the user can view any Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_rto_country');
    }

    /**
     * Determine whether the user can view the Rpl Sector.
     *
     * @param User $authUser
     * @param RplSector $rplSector
     * @return mixed
     */
    public function view(User $authUser, RplSector $rplSector): bool
    {
        return $authUser->hasPermission('view_single_rto_country');
    }

    /**
     * Determine whether the user can create Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_rto_country');
    }

    /**
     * Determine whether the user can update the Rpl Sector.
     *
     * @param User $authUser
     * @param RplSector $rplSector
     * @return mixed
     */
    public function update(User $authUser, RplSector $rplSector): bool
    {
        return $authUser->hasPermission('update_rto_country');
    }

    /**
     * Determine whether the user can delete the Rpl Sector.
     *
     * @param User $authUser
     * @param RplSector $rplSector
     * @return mixed
     */
    public function delete(User $authUser, RplSector $rplSector): bool
    {
        return $authUser->hasPermission('delete_rto_country');
    }
}
