<?php

namespace App\Policies;

use App\Models\RplSector;
use App\Models\User;

class RplSectorPolicy
{
    /**
     * Determine whether the user can view any Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_rpl_sector');
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
        return $authUser->hasPermission('view_single_rpl_sector');
    }

    /**
     * Determine whether the user can create Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_rpl_sector');
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
        return $authUser->hasPermission('update_rpl_sector');
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
        return $authUser->hasPermission('delete_rpl_sector');
    }
}
