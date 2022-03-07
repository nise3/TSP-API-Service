<?php

namespace App\Policies;

use App\Models\RegisteredTrainingOrganization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegisteredTrainingOrganizationPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any RTO.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_registered_training_organization');
    }

    /**
     * Determine whether the user can view the RTO.
     *
     * @param User $authUser
     * @param RegisteredTrainingOrganization $rto
     * @return mixed
     */
    public function view(User $authUser, RegisteredTrainingOrganization $rto): bool
    {
        return $authUser->hasPermission('view_single_registered_training_organization');
    }

    /**
     * Determine whether the user can create RTO.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_registered_training_organization');
    }

    /**
     * Determine whether the user can update the RTO.
     *
     * @param User $authUser
     * @param RegisteredTrainingOrganization $rto
     * @return mixed
     */
    public function update(User $authUser, RegisteredTrainingOrganization $rto): bool
    {
        return $authUser->hasPermission('update_registered_training_organization');
    }

    /**
     * Determine whether the user can delete the RTO.
     *
     * @param User $authUser
     * @param RegisteredTrainingOrganization $rto
     * @return mixed
     */
    public function delete(User $authUser, RegisteredTrainingOrganization $rto): bool
    {
        return $authUser->hasPermission('delete_registered_training_organization');
    }
}
