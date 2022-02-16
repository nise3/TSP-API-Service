<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BranchPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any branches.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser)
    {
        return $authUser->hasPermission('view_any_branch');
    }

    /**
     * Determine whether the user can view the branch.
     *
     * @param User $authUser
     * @param Branch $branch
     * @return mixed
     */
    public function view(User $authUser, Branch $branch)
    {
        return $authUser->hasPermission('view_single_branch');
    }

    /**
     * Determine whether the user can create branches.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('create_branch');
    }

    /**
     * Determine whether the user can update the branch.
     *
     * @param User $authUser
     * @param Branch $branch
     * @return mixed
     */
    public function update(User $authUser, Branch $branch)
    {
        return $authUser->hasPermission('update_branch');
    }

    /**
     * Determine whether the user can delete the branch.
     *
     * @param User $authUser
     * @param Branch $branch
     * @return mixed
     */
    public function delete(User $authUser, Branch $branch)
    {
        return $authUser->hasPermission('delete_branch');
    }
}
