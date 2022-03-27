<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\RplSector;
use App\Models\User;

class AssessmentPolicy
{
    /**
     * Determine whether the user can view any Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_assessment');
    }

    /**
     * Determine whether the user can view the Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function view(User $authUser, Assessment $assessment): bool
    {
        return $authUser->hasPermission('view_single_assessment');
    }

    /**
     * Determine whether the user can create Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_assessment');
    }


    /**
     *  Determine whether the user can update the Rpl Sector.
     * @param User $authUser
     * @param Assessment $assessment
     * @return bool
     */
    public function update(User $authUser, Assessment $assessment): bool
    {
        return $authUser->hasPermission('update_assessment');
    }

    /**
     * Determine whether the user can delete the Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function delete(User $authUser,  Assessment $assessment): bool
    {
        return $authUser->hasPermission('delete_assessment');
    }
}
