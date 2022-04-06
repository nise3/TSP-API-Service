<?php

namespace App\Policies;

use App\Models\RplSubject;
use App\Models\User;

class RplSubjectPolicy
{
    /**
     * Determine whether the user can view any RplSubject.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_rpl_subject');
    }

    /**
     * Determine whether the user can view the RplSubject.
     *
     * @param User $authUser
     * @param RplSubject $subject
     * @return mixed
     */
    public function view(User $authUser, RplSubject $subject): bool
    {
        return $authUser->hasPermission('view_single_rpl_subject');
    }

    /**
     * Determine whether the user can create RplSubject.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_rpl_subject');
    }

    /**
     * Determine whether the user can update the RplSubject.
     *
     * @param User $authUser
     * @param RplSubject $subject
     * @return mixed
     */
    public function update(User $authUser, RplSubject $subject): bool
    {
        return $authUser->hasPermission('update_rpl_subject');
    }

    /**
     * Determine whether the user can delete the RplSubject.
     *
     * @param User $authUser
     * @param RplSubject $subject
     * @return mixed
     */
    public function delete(User $authUser, RplSubject $subject): bool
    {
        return $authUser->hasPermission('delete_rpl_subject');
    }
}
