<?php

namespace App\Policies;

use App\Models\AssessmentQuestion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssessmentQuestionPolicy extends BasePolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any Question Bank.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_assessment_question');
    }


    /**
     * Determine whether the user can create Question Bank.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_assessment_question');
    }


}
