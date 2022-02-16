<?php

namespace App\Policies;

use App\Models\Trainer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrainerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any trainers.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser)
    {
        return $authUser->hasPermission('view_any_trainer');
    }

    /**
     * Determine whether the user can view the trainer.
     *
     * @param User $authUser
     * @param Trainer $trainer
     * @return mixed
     */
    public function view(User $authUser, Trainer $trainer)
    {
        return $authUser->hasPermission('view_single_trainer');
    }

    /**
     * Determine whether the user can create trainers.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('create_trainer');
    }

    /**
     * Determine whether the user can update the trainer.
     *
     * @param User $authUser
     * @param Trainer $trainer
     * @return mixed
     */
    public function update(User $authUser, Trainer $trainer)
    {
        return $authUser->hasPermission('update_trainer');
    }

    /**
     * Determine whether the user can delete the trainer.
     *
     * @param User $authUser
     * @param Trainer $trainer
     * @return mixed
     */
    public function delete(User $authUser, Trainer $trainer)
    {
        return $authUser->hasPermission('delete_trainer');
    }
}
