<?php

namespace App\Policies;

use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionSet;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class AssessmentQuestionSetPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $authUser
     * @return bool
     */
    public function viewAny( User $authUser): bool
    {
        return $authUser->hasPermission('view_any_assessment_question_set');
    }

    /**
     * @param User $authUser
     * @param AssessmentQuestionSet $assessmentQuestionSet
     * @return bool
     */
    public function view(User $authUser, AssessmentQuestionSet $assessmentQuestionSet): bool
    {
        return $authUser->hasPermission('view_single_assessment_question_set');
    }


    /**
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_assessment_question_set');
    }

    /**
     * @param User $authUser
     * @param AssessmentQuestionSet $assessmentQuestionSet
     * @return bool
     */
    public function update(User $authUser, AssessmentQuestionSet $assessmentQuestionSet): bool
    {
        return $authUser->hasPermission('update_assessment_question_set');
    }


    /**
     * @param User $authUser
     * @param AssessmentQuestionSet $assessmentQuestionSet
     * @return bool
     */
    public function delete(User $authUser,  AssessmentQuestionSet $assessmentQuestionSet): bool
    {
        return $authUser->hasPermission('delete_assessment_question_set');
    }
}
