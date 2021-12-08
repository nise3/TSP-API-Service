<?php

namespace App\Policies;

use App\Models\Institute;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InstitutePolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any institutes.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser)
    {
        return $authUser->hasPermission('view_any_institute');
    }

    /**
     * Determine whether the user can view the institute.
     *
     * @param User $authUser
     * @param Institute $institute
     * @return mixed
     */
    public function view(User $authUser, Institute $institute)
    {
        return $authUser->hasPermission('view_single_institute');
    }

    /**
     * Determine whether the user can create institutes.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('create_institute');
    }

    /**
     * Determine whether the user can update the institute.
     *
     * @param User $authUser
     * @param Institute $institute
     * @return mixed
     */
    public function update(User $authUser, Institute $institute)
    {
        return $authUser->hasPermission('update_institute');
    }

    /**
     * Determine whether the user can delete the institute.
     *
     * @param User $authUser
     * @param Institute $institute
     * @return mixed
     */
    public function delete(User $authUser, Institute $institute)
    {
        return $authUser->hasPermission('delete_institute');
    }
}
