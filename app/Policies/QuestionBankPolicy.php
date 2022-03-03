<?php

namespace App\Policies;

use App\Models\QuestionBank;
use App\Models\User;

class QuestionBankPolicy
{
    /**
     * Determine whether the user can view any Question Bank.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_question_bank');
    }

    /**
     * Determine whether the user can view the Question Bank.
     *
     * @param User $authUser
     * @param QuestionBank $questionBank
     * @return mixed
     */
    public function view(User $authUser, QuestionBank $questionBank): bool
    {
        return $authUser->hasPermission('view_single_question_bank');
    }

    /**
     * Determine whether the user can create Question Bank.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_question_bank');
    }

    /**
     * Determine whether the user can update the Question Bank.
     *
     * @param User $authUser
     * @param QuestionBank $questionBank
     * @return mixed
     */
    public function update(User $authUser, QuestionBank $questionBank): bool
    {
        return $authUser->hasPermission('update_question_bank');
    }

    /**
     * Determine whether the user can delete the Question Bank.
     *
     * @param User $authUser
     * @param QuestionBank $questionBank
     * @return mixed
     */
    public function delete(User $authUser, QuestionBank $questionBank): bool
    {
        return $authUser->hasPermission('delete_question_bank');
    }
}
