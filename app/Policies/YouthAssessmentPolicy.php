<?php

namespace App\Policies;

use App\Models\YouthAssessment;
use App\Models\User;

class YouthAssessmentPolicy
{
    /**
     * Determine whether the user can view any Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_youth_assessment');
    }

    /**
     * Determine whether the user can view the Rpl Sector.
     *
     * @param User $authUser
     * @param YouthAssessment $youthAssessment
     * @return mixed
     */
    public function view(User $authUser, YouthAssessment $youthAssessment): bool
    {
        return $authUser->hasPermission('view_single_youth_assessment');
    }

    /**
     * Determine whether the user can create Rpl Sector.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_youth_assessment');
    }

    /**
     * Determine whether the user can update the Rpl Sector.
     *
     * @param User $authUser
     * @param YouthAssessment $youthAssessment
     * @return mixed
     */
    public function update(User $authUser, YouthAssessment $youthAssessment): bool
    {
        return $authUser->hasPermission('update_youth_assessment');
    }

    /**
     * Determine whether the user can delete the Rpl Sector.
     *
     * @param User $authUser
     * @param YouthAssessment $youthAssessment
     * @return mixed
     */
    public function delete(User $authUser, YouthAssessment $youthAssessment): bool
    {
        return $authUser->hasPermission('delete_youth_assessment');
    }
}
