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

    /** Auth routes */
    $router->group(['middleware' => 'auth'], function () use ($customRouter, $router) {
        $customRouter()->resourceRoute('institutes', 'InstituteController')->render();
        $customRouter()->resourceRoute('programs', 'ProgramController')->render();
        $customRouter()->resourceRoute('training-centers', 'TrainingCenterController')->render();
        $customRouter()->resourceRoute('batches', 'BatchController')->render();
        $customRouter()->resourceRoute('courses', 'CourseController')->render();
        $customRouter()->resourceRoute('branches', 'BranchController')->render();
        $customRouter()->resourceRoute('trainers', 'TrainerController')->render();
        $customRouter()->resourceRoute('course-enrollments', 'CourseEnrollmentController')->render();

        /** Assign Trainers to Batch */
        $router->post('batches/{id}/assign-trainer-to-batch', ['as' => 'batches.assign-trainer-to-batch', 'uses' => 'BatchController@assignTrainerToBatch']);

        /** Institute Registration Approval */
        $router->put("institute-registration-approval/{instituteId}", ["as" => "Institute.institutes-registration-approval", "uses" => "InstituteController@instituteRegistrationApproval"]);

        /** Institute Admin Profile Update */
        $router->put("institute-admin-profile-update", ["as" => "Institute.admin-profile-update", "uses" => "InstituteController@updateInstituteAdminProfile"]);        /** Institute Admin Profile Update */

        /** Get Institute Admin Profile */
        $router->get("institute-admin-profile", ["as" => "Institute.admin-profile", "uses" => "InstituteController@getInstituteAdminProfile"]);

        /** Institute Registration Rejection */
        $router->put("institute-registration-rejection/{instituteId}", ["as" => "Institute.institutes-registration-rejection", "uses" => "InstituteController@instituteRegistrationRejection"]);

        /** Batch Assign*/
        $router->post("batch-assign", ["as" => "course-enroll.batch-assign", "uses" => "CourseEnrollmentController@assignBatch"]);

        /** Reject course enrollment application */
        $router->post("reject-course-enrollment", ["as" => "course-enroll.reject", "uses" => "CourseEnrollmentController@rejectCourseEnrollment"]);

        /** Course All batches / Active batches / Up-coming batches */
        $router->get('courses/{id}/training_centers/batches', ['as' => 'courses.get-batches', 'uses' => 'BatchController@getBatchesByCourseId']);

        $router->get('institute-dashboard-statistics', ["as" => "institute.dashboard-statistics", "uses" => "InstituteStatisticsController@dashboardStatistics"]);
        $router->get('demanded-courses', ["as" => "institute.demanding-courses", "uses" => "InstituteStatisticsController@demandingCourses"]);
    });

    $router->get('youth-feed-statistics/{youthId}', ["as" => "courses.youth-feed-statistics", "uses" => "CourseController@youthFeedStatistics"]);


    $router->group(['prefix' => 'public', 'as' => 'public'], function () use ($router) {
        /** Course Filter */
        $router->get('course-list[/{type}]', ["as" => "courses.filter", "uses" => "CourseController@getFilterCourseList"]);

        /** Course details with trainer */
        $router->get("courses/{id}", ["as" => "public.courses.course-details", "uses" => "CourseController@courseDetails"]);

        /** Training Centers Filter */
        $router->get('training-centers', ["as" => "training-centers.filter", "uses" => "TrainingCenterController@getTrainingCentersWithFilters"]);

        /** Program lists  */
        $router->get("programs", ["as" => "public.programs", "uses" => "ProgramController@getPublicProgramList"]);

        /** Single Institute Fetch  */
        $router->get("institutes/{id}", ["as" => "public.institute.details", "uses" => "InstituteController@instituteDetails"]);

        $router->get('institute-dashboard-statistics/{instituteId}', ["as" => "public.institute.dashboard-statistics", "uses" => "InstituteStatisticsController@dashboardStatistics"]);
        $router->get('demanded-courses/{instituteId}', ["as" => "public.institute.demanding-courses", "uses" => "InstituteStatisticsController@demandingCourses"]);
    });

    //Service to service direct call without any authorization and authentication
    $router->group(['prefix' => 'service-to-service-call', 'as' => 'service-to-service-call'], function () use ($router) {
        /** Single Institute Fetch  */
        $router->get("institutes/{id}", ["as" => "service-to-service-call.institute", "uses" => "InstituteController@instituteDetails"]);
    });

    /** institute registration */
    $router->post("institute-open-registration", ["as" => "register.organization", "uses" => "InstituteController@instituteRegistration"]);

    /** Course Enrollment */
    $router->post("course-enroll", ["as" => "course.enroll", "uses" => "CourseEnrollmentController@courseEnrollment"]);

    /** Course Enrollment Verification Code send */
    $router->post("course-enroll/{id}/verify-sms-code", ["as" => "course.verify-sms-code", "uses" => "CourseEnrollmentController@verifyCode"]);

    /** Course Enrollment Verification Code resend */
    $router->post("course-enroll/{id}/resend-verification-code", ["as" => "course.resend-verification-code", "uses" => "CourseEnrollmentController@reSendVerificationCode"]);

    /** Institute Title by Ids for Internal Api */
    $router->post("get-institute-title-by-ids", ["as" => "institutes.get-institute-title-by-ids", "uses" => "InstituteController@getInstituteTitleByIds"]);

    /** Batch and Program Title by Ids for Internal Api */
    $router->post("get-course-program-title-by-ids", ["as" => "institutes.get-course-program-title-by-ids", "uses" => "InstituteController@getCourseAndProgramTitleByIds"]);

    $router->post('payment/pay-now', ["as" => "payment.pay-now", "uses" => "PaymentController@payNow"]);
    $router->get('payment/success', ["as" => "payment.success", "uses" => "PaymentController@success"]);
    $router->get('payment/failed', ["as" => "payment.fail", "uses" => "PaymentController@fail"]);
    $router->get('payment/cancel', ["as" => "payment.cancel", "uses" => "PaymentController@cancel"]);
    $router->post('payment/ipn-handler/{secretToken}', ["as" => "payment.ipn-handler", "uses" => "PaymentController@ipnHandler"]);

});

$router->get("/idp-test", function () {
    Illuminate\Support\Facades\Log::info('Idp-Log');
    return "idp-User";
});
