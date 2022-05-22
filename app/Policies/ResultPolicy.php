<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResultPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the result.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_result');
    }

    /**
     * Determine whether the user can create results.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_result');


    }

}
