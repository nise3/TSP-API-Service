<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProgramPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any programs.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser)
    {
        return $authUser->hasPermission('view_any_program');
    }

    /**
     * Determine whether the user can view the program.
     *
     * @param User $authUser
     * @param Program $program
     * @return mixed
     */
    public function view(User $authUser, Program $program)
    {
        return $authUser->hasPermission('view_single_program');
    }

    /**
     * Determine whether the user can create programs.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('create_program');
    }

    /**
     * Determine whether the user can update the program.
     *
     * @param User $authUser
     * @param Program $program
     * @return mixed
     */
    public function update(User $authUser, Program $program)
    {
        return $authUser->hasPermission('update_program');
    }

    /**
     * Determine whether the user can delete the program.
     *
     * @param User $authUser
     * @param Program $program
     * @return mixed
     */
    public function delete(User $authUser, Program $program)
    {
        return $authUser->hasPermission('delete_program');
    }
}
