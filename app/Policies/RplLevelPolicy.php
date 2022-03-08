<?php

namespace App\Policies;

use App\Models\RplLevel;
use App\Models\User;

class RplLevelPolicy
{
    /**
     * Determine whether the user can view any Rpl Level.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_rpl_level');
    }

    /**
     * Determine whether the user can view the Rpl Level.
     *
     * @param User $authUser
     * @param RplLevel $rplLevel
     * @return mixed
     */
    public function view(User $authUser, RplLevel $rplLevel): bool
    {
        return $authUser->hasPermission('view_single_rpl_level');
    }

    /**
     * Determine whether the user can create Rpl Level.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_rpl_level');
    }

    /**
     * Determine whether the user can update the Rpl Level.
     *
     * @param User $authUser
     * @param RplLevel $rplLevel
     * @return mixed
     */
    public function update(User $authUser, RplLevel $rplLevel): bool
    {
        return $authUser->hasPermission('update_rpl_level');
    }

    /**
     * Determine whether the user can delete the Rpl Level.
     *
     * @param User $authUser
     * @param RplLevel $rplLevel
     * @return mixed
     */
    public function delete(User $authUser, RplLevel $rplLevel): bool
    {
        return $authUser->hasPermission('delete_rpl_level');
    }
}
