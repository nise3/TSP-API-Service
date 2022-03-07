<?php

namespace App\Policies;

use App\Models\Institute;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InstitutePolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any institutes.
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_institute') || $authUser->hasPermission('view_any_certificate_authority');
    }

    /**
     * Determine whether the user can view the institute.
     *
     * @param User $authUser
     * @param Institute $institute
     * @return bool
     */
    public function view(User $authUser, Institute $institute): bool
    {
        return $authUser->hasPermission('view_single_institute') || $authUser->hasPermission('view_single_certificate_authority');
    }

    /**
     * Determine whether the user can create institutes.
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_institute') || $authUser->hasPermission('create_certificate_authority');
    }

    /**
     * Determine whether the user can update the institute.
     *
     * @param User $authUser
     * @param Institute $institute
     * @return bool
     */
    public function update(User $authUser, Institute $institute): bool
    {
        return $authUser->hasPermission('update_institute') || $authUser->hasPermission('update_certificate_authority');
    }

    /**
     * Determine whether the user can delete the institute.
     * @param User $authUser
     * @param Institute $institute
     * @return bool
     */
    public function delete(User $authUser, Institute $institute): bool
    {
        return $authUser->hasPermission('delete_institute') || $authUser->hasPermission('delete_certificate_authority');
    }


    /**
     * @param User $authUser
     * @param Institute $institute
     * @return bool
     */
    public function viewProfile(User $authUser, Institute $institute): bool
    {
        return $authUser->hasPermission('view_institute_profile') || $authUser->hasPermission('view_certificate_authority_profile');;
    }

    /**
     * @param User $authUser
     * @param Institute $institute
     * @return bool
     */
    public function updateProfile(User $authUser, Institute $institute): bool
    {
        return $authUser->hasPermission('update_institute_profile') || $authUser->hasPermission('update_certificate_authority_profile');

    }
}
