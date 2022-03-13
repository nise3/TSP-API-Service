<?php

namespace App\Policies;

use App\Models\RplApplication;
use App\Models\User;

class RplApplicationPolicy
{
    /**
     * Determine whether the user can view any Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_rpl_application');
    }

    /**
     * Determine whether the user can view the Rpl Sector.
     *
     * @param User $authUser
     * @param RplApplication $youthAssessment
     * @return mixed
     */
    public function view(User $authUser, RplApplication $youthAssessment): bool
    {
        return $authUser->hasPermission('view_single_rpl_application');
    }

    /**
     * Determine whether the user can create Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_rpl_application');
    }

    /**
     * Determine whether the user can update the Rpl Sector.
     *
     * @param User $authUser
     * @param RplApplication $youthAssessment
     * @return mixed
     */
    public function update(User $authUser, RplApplication $youthAssessment): bool
    {
        return $authUser->hasPermission('update_rpl_application');
    }

    /**
     * Determine whether the user can delete the Rpl Sector.
     *
     * @param User $authUser
     * @param RplApplication $youthAssessment
     * @return mixed
     */
    public function delete(User $authUser, RplApplication $youthAssessment): bool
    {
        return $authUser->hasPermission('delete_rpl_application');
    }
}
