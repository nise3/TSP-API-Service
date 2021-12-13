<?php

namespace App\Policies;

use App\Models\TrainingCenter;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrainingCenterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any trainingCenterPolicies.
     *
     * @param User  $authUser
     * @return mixed
     */
    public function viewAny(User $authUser)
    {
        return $authUser->hasPermission('view_any_training_center');
    }


    /**
     * Determine whether the user can view the trainingCenterPolicy.
     *
     * @param  User  $authUser
     * @param  TrainingCenterPolicy  $trainingCenterPolicy
     * @return mixed
     */
    public function view(User $authUser, TrainingCenter $trainingCenter)
    {
        return $authUser->hasPermission('view_single_training_center');
    }

    /**
     * Determine whether the user can create trainingCenterPolicies.
     *
     * @param  User  $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('create_training_center');
    }

    /**
     * Determine whether the user can update the trainingCenterPolicy.
     *
     * @param  User  $authUser
     * @param  TrainingCenterPolicy  $trainingCenterPolicy
     * @return mixed
     */
    public function update(User $authUser, TrainingCenter $trainingCenter)
    {
        return $authUser->hasPermission('update_training_center');
    }

    /**
     * Determine whether the user can delete the trainingCenterPolicy.
     *
     * @param  User  $authUser
     * @param  TrainingCenterPolicy  $trainingCenterPolicy
     * @return mixed
     */
    public function delete(User $authUser, TrainingCenter $trainingCenter)
    {
        return $authUser->hasPermission('delete_training_center');
    }
}
