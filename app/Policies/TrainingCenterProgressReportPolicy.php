<?php

namespace App\Policies;




use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrainingCenterProgressReportPolicy extends  BasePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_training_center_report');
    }

    /**
     * @param User $authUser
     * @return bool
     */
    public function view(User $authUser): bool
    {
        return $authUser->hasPermission('view_single_training_center_report');
    }

    /**
     * @param User $authUser
     * @return bool
     */

    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_training_center_report');
    }

}
