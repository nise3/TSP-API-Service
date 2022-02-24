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
        $customRouter()->resourceRoute('rto-countries', 'RtoCountryController')->render();
        $customRouter()->resourceRoute('registered-training-organizations', 'RegisteredTrainingOrganizationController')->render();

        /** Institute Registration Approval */
        $router->put("institute-registration-approval/{instituteId}", ["as" => "Institute.institutes-registration-approval", "uses" => "InstituteController@instituteRegistrationApproval"]);
        /** Institute Registration Rejection */
        $router->put("institute-registration-rejection/{instituteId}", ["as" => "Institute.institutes-registration-rejection", "uses" => "InstituteController@instituteRegistrationRejection"]);

        /** Get Institute Profile */
        $router->get("institute-profile", ["as" => "Institute.profile", "uses" => "InstituteController@getInstituteProfile"]);
        /** Institute Profile Update */
        $router->put("institute-profile-update", ["as" => "Institute.admin-profile-update", "uses" => "InstituteController@updateInstituteProfile"]);

        /** Batch Assign to youth */
        $router->post("batch-assign", ["as" => "course-enroll.batch-assign", "uses" => "CourseEnrollmentController@assignBatch"]);
        /** Assign Trainers to Batch */
        $router->post('batches/{id}/assign-trainer-to-batch', ['as' => 'batches.assign-trainer-to-batch', 'uses' => 'BatchController@assignTrainerToBatch']);

        /** Reject course enrollment application */
        $router->post("reject-course-enrollment", ["as" => "course-enroll.reject", "uses" => "CourseEnrollmentController@rejectCourseEnrollment"]);

        /** Course All batches / Active batches / Up-coming batches */
        $router->get('courses/{id}/training-centers/batches', ['as' => 'courses.get-batches', 'uses' => 'BatchController@getBatchesByCourseId']);

        /** Fetch youths who enrolled in courses of an Institute */
        $router->get('institute_trainee_youths', ['as' => 'institute.trainee.youths', 'uses' => 'CourseEnrollmentController@getInstituteTraineeYouths']);

        $router->get('institute-dashboard-statistics', ["as" => "institute.dashboard-statistics", "uses" => "InstituteStatisticsController@dashboardStatistics"]);
        $router->get('demanded-courses', ["as" => "institute.demanding-courses", "uses" => "InstituteStatisticsController@demandingCourses"]);
    });


    $router->get('youth-enroll-courses', ["as" => "courses.youth-enroll-courses", "uses" => "CourseEnrollmentController@getYouthEnrollCourses"]);

    /** Public Apis */
    $router->group(['prefix' => 'public', 'as' => 'public'], function () use ($router) {
        /** Course details with trainer */
        $router->get("courses/{id}", ["as" => "public.courses.course-details", "uses" => "CourseController@publicCourseDetails"]);
        $router->get("institutes", ["as" => "public.institutes", "uses" => "InstituteController@publicInstituteList"]);
        $router->get("registered-training-organizations", ["as" => "public.registered-training-organizations", "uses" => "RegisteredTrainingOrganizationController@getPublicList"]);
        /** Course All batches / Active batches / Up-coming batches */
        $router->get('courses/{id}/training-centers/batches', ['as' => 'courses.get-batches', 'uses' => 'BatchController@getPublicBatchesByCourseId']);

        /** nise-statistics */
        $router->get('nise-statistics', ["as" => "nise-statistics", "uses" => "InstituteStatisticsController@niseStatistics"]);

        $router->group(['middleware' => 'public-domain-handle'], function () use ($router) {
            $router->get('institute-dashboard-statistics', ["as" => "public.institute.dashboard-statistics", "uses" => "InstituteStatisticsController@publicDashboardStatistics"]);
            $router->get('demanded-courses', ["as" => "public.institute.demanding-courses", "uses" => "InstituteStatisticsController@publicDemandingCourses"]);

            /** Single Institute Fetch  */
            $router->get("institute-details", ["as" => "public.institute.details", "uses" => "InstituteController@institutePublicDetails"]);

            /** Program lists  */
            $router->get("programs", ["as" => "public.programs", "uses" => "ProgramController@getPublicProgramList"]);

            /** Training Centers Filter */
            $router->get('training-centers', ["as" => "training-centers.filter", "uses" => "TrainingCenterController@getTrainingCentersWithFilters"]);

            /** Course Filter */
            $router->get('course-list[/{type}]', ["as" => "courses.filter", "uses" => "CourseController@getFilterCourseList"]);
        });

    });

    //Service to service direct call without any authorization and authentication
    $router->group(['prefix' => 'service-to-service-call', 'as' => 'service-to-service-call'], function () use ($router) {
        /** Single Institute Fetch  */
        $router->get("institutes/{id}", ["as" => "service-to-service-call.institute", "uses" => "InstituteController@instituteDetails"]);

        /** Single RTO Fetch  */
        $router->get("registered-training-organizations/{id}", ["as" => "service-to-service-call.registered-training-organizations", "uses" => "RegisteredTrainingOrganizationController@rtoDetails"]);

        /** Youth feed statistics course data fetch */
        $router->get('youth-feed-statistics/{youthId}', ["as" => "courses.youth-feed-statistics", "uses" => "CourseController@youthFeedStatistics"]);

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

    $router->group(["prefix" => "course-enrollment", "as" => "course-enrollment"], function () use ($router) {
        $router->post('payment-by-ek-pay/pay-now', ["as" => "payment-by-ek-pay.pay-now", "uses" => "CourseEnrollmentPaymentController@payNowByEkPay"]);
        $router->get('payment-by-ek-pay/success', ["as" => "payment-by-ek-pay.success", "uses" => "CourseEnrollmentPaymentController@ekPayPaymentSuccess"]);
        $router->get('payment-by-ek-pay/failed', ["as" => "payment-by-ek-pay.fail", "uses" => "CourseEnrollmentPaymentController@ekPayPaymentFail"]);
        $router->get('payment-by-ek-pay/cancel', ["as" => "payment-by-ek-pay.cancel", "uses" => "CourseEnrollmentPaymentController@ekPayPaymentCancel"]);
        $router->post('payment-by-ek-pay/ipn-handler/{secretToken}', ["as" => "payment.ipn-handler", "uses" => "CourseEnrollmentPaymentController@ekPayPaymentIpnHandler"]);
    });
});

