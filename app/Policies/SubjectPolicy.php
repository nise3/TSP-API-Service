<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    /**
     * Determine whether the user can view any Subject.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_subject');
    }

    /**
     * Determine whether the user can view the Subject.
     *
     * @param User $authUser
     * @param Subject $subject
     * @return mixed
     */
    public function view(User $authUser, Subject $subject): bool
    {
        return $authUser->hasPermission('view_single_subject');
    }

    /**
     * Determine whether the user can create Subject.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_subject');
    }

    /**
     * Determine whether the user can update the Subject.
     *
     * @param User $authUser
     * @param Subject $subject
     * @return mixed
     */
    public function update(User $authUser, Subject $subject): bool
    {
        return $authUser->hasPermission('update_subject');
    }

    /**
     * Determine whether the user can delete the Subject.
     *
     * @param User $authUser
     * @param Subject $subject
     * @return mixed
     */
    public function delete(User $authUser, Subject $subject): bool
    {
        return $authUser->hasPermission('delete_subject');
    }
}
