<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Helpers\Classes\CustomRouter;

$customRouter = function (string $as = '') use ($router) {
    $custom = new CustomRouter($router);
    return $custom->as($as);
};

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1', 'as' => 'api.v1'], function () use ($router, $customRouter) {

    $router->get('/', ['as' => 'api-info', 'uses' => 'ApiInfoController@apiInfo']);

    $router->post('/file-upload', ['as' => 'api-info.upload', 'uses' => 'ApiInfoController@fileUpload']);

    $customRouter()->resourceRoute('institutes', 'InstituteController')->render();
    $customRouter()->resourceRoute('programs', 'ProgramController')->render();
    $customRouter()->resourceRoute('training-centers', 'TrainingCenterController')->render();
    $customRouter()->resourceRoute('batches', 'BatchController')->render();
    $customRouter()->resourceRoute('courses', 'CourseController')->render();
    $customRouter()->resourceRoute('branches', 'BranchController')->render();
    $customRouter()->resourceRoute('trainers', 'TrainerController')->render();
    $customRouter()->resourceRoute('course-enrollments', 'CourseEnrollmentController')->render();

    $router->get('youth-enroll-courses', ["as" => "courses.filter", "uses" => "CourseEnrollmentController@getYouthEnrollCourses"]);


    $router->group(['prefix' => 'public', 'as' => 'public'], function () use ($router) {
        /** Course Filter */
        $router->get('course-list[/{type}]', ["as" => "courses.filter", "uses" => "CourseController@getFilterCourseList"]);

        /** Training Centers Filter */
        $router->get('training-centers', ["as" => "training-centers.filter", "uses" => "TrainingCenterController@getTrainingCentersWithFilters"]);

        /** Course details  */
        $router->get("courses/{id}", ["as" => "public.courses.course-details", "uses" => "CourseController@courseDetails"]);

        /** Program details  */
        $router->get("programs", ["as" => "public.programs", "uses" => "ProgramController@getPublicProgramList"]);
    });


    /** Assign Trainers to Batch */
    $router->post('batches/{id}/assign-trainer-to-batch', ['as' => 'batches.assign-trainer-to-batch', 'uses' => 'BatchController@assignTrainerToBatch']);


    //$router->get('courses', ['as' => 'institutes.get-trashed-data', 'uses' => 'InstituteController@getTrashedData']);

    /** institute registration */
    $router->post("institute-open-registration", ["as" => "register.organization", "uses" => "InstituteController@instituteRegistration"]);

    /* Course Enrollment */
    $router->post("course-enroll", ["as" => "course.enroll", "uses" => "CourseEnrollmentController@courseEnrollment"]);

    /* Batch Assign*/
    $router->post("batch-assign", ["as" => "course-enroll.batch-assign", "uses" => "CourseEnrollmentController@assignBatch"]);

    /* Reject course enrollment application*/
    $router->post("reject-course-enrollment", ["as" => "course-enroll.reject", "uses" => "CourseEnrollmentController@rejectCourseEnrollment"]);

    /** Course All batches / Active batches / Up-coming batches */
    $router->get('courses/{id}/training_centers/batches', ['as' => 'courses.get-batches', 'uses' => 'BatchController@getBatchesByCourseId']);

    //institutes trashed
    $router->get('institutes-trashed-data', ['as' => 'institutes.get-trashed-data', 'uses' => 'InstituteController@getTrashedData']);
    $router->patch('institutes-restore-data/{id}', ['as' => 'institutes.restore-data', 'uses' => 'InstituteController@restore']);
    $router->delete('institutes-force-delete/{id}', ['as' => 'institutes.restore-data', 'uses' => 'InstituteController@forceDelete']);

    //programs trashed
    $router->get('programs-trashed-data', ['as' => 'programs.get-trashed-data', 'uses' => 'ProgramController@getTrashedData']);
    $router->patch('programs-restore-data/{id}', ['as' => 'programs.restore-data', 'uses' => 'ProgramController@restore']);
    $router->delete('programs-force-delete/{id}', ['as' => 'programs.restore-data', 'uses' => 'ProgramController@forceDelete']);

    //training-centers trashed
    $router->get('training-centers-trashed-data', ['as' => 'training-centers.get-trashed-data', 'uses' => 'TrainingCenterController@getTrashedData']);
    $router->patch('training-centers-restore-data/{id}', ['as' => 'training-centers.restore-data', 'uses' => 'TrainingCenterController@restore']);
    $router->delete('training-centers-force-delete/{id}', ['as' => 'training-centers.restore-data', 'uses' => 'TrainingCenterController@forceDelete']);

    //batches trashed
    $router->get('batches-trashed-data', ['as' => 'batches.get-trashed-data', 'uses' => 'BatchController@getTrashedData']);
    $router->patch('batches-restore-data/{id}', ['as' => 'batches.restore-data', 'uses' => 'BatchController@restore']);
    $router->delete('batches-force-delete/{id}', ['as' => 'batches.restore-data', 'uses' => 'BatchController@forceDelete']);

    //courses trashed
    $router->get('courses-trashed-data', ['as' => 'courses.get-trashed-data', 'uses' => 'CourseController@getTrashedData']);
    $router->patch('courses-restore-data/{id}', ['as' => 'courses.restore-data', 'uses' => 'CourseController@restore']);
    $router->delete('courses-force-delete/{id}', ['as' => 'courses.restore-data', 'uses' => 'CourseController@forceDelete']);

    //branches trashed
    $router->get('branches-trashed-data', ['as' => 'branches.get-trashed-data', 'uses' => 'BranchController@getTrashedData']);
    $router->patch('branches-restore-data/{id}', ['as' => 'branches.restore-data', 'uses' => 'BranchController@restore']);
    $router->delete('branches-force-delete/{id}', ['as' => 'branches.restore-data', 'uses' => 'BranchController@forceDelete']);

    //trainers trash
    $router->get('trainers-trashed-data', ['as' => 'branches.get-trashed-data', 'uses' => 'TrainerController@getTrashedData']);
    $router->patch('trainers-restore-data/{id}', ['as' => 'branches.restore-data', 'uses' => 'TrainerController@restore']);
    $router->delete('trainers-force-delete/{id}', ['as' => 'branches.restore-data', 'uses' => 'TrainerController@forceDelete']);

});
