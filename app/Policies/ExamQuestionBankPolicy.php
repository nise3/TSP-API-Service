<?php

namespace App\Policies;

use App\Models\ExamQuestionBank;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamQuestionBankPolicy extends  BasePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_exam_question_bank');
    }


    /**
     * @param User $authUser
     * @param ExamQuestionBank $examQuestionBank
     * @return bool
     */
    public function view(User $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $authUser->hasPermission('view_single_exam_question_bank');
    }

    /**
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_exam_question_bank');
    }

    /**
     * @param User $authUser
     * @param ExamQuestionBank $examQuestionBank
     * @return bool
     */
    public function update(User $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $authUser->hasPermission('update_exam_question_bank');
    }

    /**
     * @param User $authUser
     * @param ExamQuestionBank $examQuestionBank
     * @return bool
     */
    public function delete(User $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $authUser->hasPermission('delete_exam_question_bank');
    }
}
