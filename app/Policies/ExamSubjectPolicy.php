<?php

namespace App\Policies;

use App\Models\ExamSubject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamSubjectPolicy extends  BasePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_exam_subject');
    }


    /**
     * @param User $authUser
     * @param ExamSubject $examSubject
     * @return bool
     */
    public function view(User $authUser, ExamSubject $examSubject): bool
    {
        return $authUser->hasPermission('view_single_exam_subject');
    }

    /**
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_exam_subject');
    }

    /**
     * @param User $authUser
     * @param ExamSubject $examSubject
     * @return bool
     */
    public function update(User $authUser, ExamSubject $examSubject): bool
    {
        return $authUser->hasPermission('update_exam_subject');
    }

    /**
     * @param User $authUser
     * @param ExamSubject $examSubject
     * @return bool
     */
    public function delete(User $authUser, ExamSubject $examSubject): bool
    {
        return $authUser->hasPermission('delete_exam_subject');
    }
}
