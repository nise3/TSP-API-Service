<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any exams.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_exam');
    }

    /**
     * Determine whether the user can view the exam.
     *
     * @param User $authUser
     * @return bool
     */
    public function view(User $authUser): bool
    {
        return $authUser->hasPermission('view_single_exam');
    }

    /**
     * Determine whether the user can create exams.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_exam');
    }

    /**
     * Determine whether the user can update the exam.
     *
     * @param User $authUser
     * @return bool
     */
    public function update(User $authUser): bool
    {
        return $authUser->hasPermission('update_exam');
    }

    /**
     * Determine whether the user can delete the exam.
     *
     * @param User $authUser
     * @return bool
     */
    public function delete(User $authUser): bool
    {
        return $authUser->hasPermission('delete_exam');
    }

    /**
     * Determine whether the user can view any youth exams.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAnyYouthExam(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_youth_exam');
    }

    /**
     * Determine whether the user can view any youth exams.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewYouthExam(User $authUser): bool
    {
        return $authUser->hasPermission('view_youth_exam');
    }
    /**
     * Determine whether the user can update youth exam.
     *
     * @param User $authUser
     * @return bool
     */
    public function updateYouthExam(User $authUser): bool
    {
        return $authUser->hasPermission('update_youth_exam');
    }
}
