<?php

namespace App\Policies;

use App\Models\RplAssessmentQuestion;
use App\Models\RplAssessmentQuestionSet;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class RplAssessmentQuestionSetPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $authUser
     * @return bool
     */
    public function viewAny( User $authUser): bool
    {
        return $authUser->hasPermission('view_any_rpl_assessment_question_set');
    }

    /**
     * @param User $authUser
     * @param RplAssessmentQuestionSet $assessmentQuestionSet
     * @return bool
     */
    public function view(User $authUser, RplAssessmentQuestionSet $assessmentQuestionSet): bool
    {
        return $authUser->hasPermission('view_single_rpl_assessment_question_set');
    }


    /**
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_rpl_assessment_question_set');
    }

    /**
     * @param User $authUser
     * @param RplAssessmentQuestionSet $assessmentQuestionSet
     * @return bool
     */
    public function update(User $authUser, RplAssessmentQuestionSet $assessmentQuestionSet): bool
    {
        return $authUser->hasPermission('update_rpl_assessment_question_set');
    }


    /**
     * @param User $authUser
     * @param RplAssessmentQuestionSet $assessmentQuestionSet
     * @return bool
     */
    public function delete(User $authUser, RplAssessmentQuestionSet $assessmentQuestionSet): bool
    {
        return $authUser->hasPermission('delete_rpl_assessment_question_set');
    }
}
