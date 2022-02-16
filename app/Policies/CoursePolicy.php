<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any courses.
     *
     * @param  User  $authUser
     * @return mixed
     */
    public function viewAny(User $authUser)
    {
        return $authUser->hasPermission('view_any_course');
    }

    /**
     * Determine whether the user can view the course.
     *
     * @param  User  $authUser
     * @param  Course  $course
     * @return mixed
     */
    public function view(User $authUser, Course $course)
    {
        return $authUser->hasPermission('view_single_course');
    }

    /**
     * Determine whether the user can create courses.
     *
     * @param  User  $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('create_course');
    }

    /**
     * Determine whether the user can update the course.
     *
     * @param  User  $authUser
     * @param  Course  $course
     * @return mixed
     */
    public function update(User $authUser, Course $course)
    {
        return $authUser->hasPermission('update_course');
    }

    /**
     * Determine whether the user can delete the course.
     *
     * @param  User  $authUser
     * @param  Course  $course
     * @return mixed
     */
    public function delete(User $authUser, Course $course)
    {
        return $authUser->hasPermission('delete_course');
    }
}
