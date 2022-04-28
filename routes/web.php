<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Facade\ServiceToServiceCall;
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
        $customRouter()->resourceRoute('rpl-sectors', 'RplSectorController')->render();
        $customRouter()->resourceRoute('exam-question-banks', 'ExamQuestionBankController')->render();
        $customRouter()->resourceRoute('rpl-occupations', 'RplOccupationController')->render();
        $customRouter()->resourceRoute('rpl-levels', 'RplLevelController')->render();
        $customRouter()->resourceRoute('rpl-subjects', 'RplSubjectController')->render();
        $customRouter()->resourceRoute('rpl-assessments', 'RplAssessmentController')->render();
        $customRouter()->resourceRoute('rpl-applications', 'RplApplicationController')->render();
        $customRouter()->resourceRoute('rto-batches', 'RtoBatchController')->render();
        $customRouter()->resourceRoute('rpl-question-banks', 'RplQuestionBankController')->render();
        $customRouter()->resourceRoute('rpl-assessment-questions', 'RplAssessmentQuestionController')->render();
        $customRouter()->resourceRoute('rpl-assessment-question-sets', 'RplAssessmentQuestionSetController')->render();
        $customRouter()->resourceRoute('exam-subjects', 'ExamSubjectController')->render();
        $customRouter()->resourceRoute('exams', 'ExamController')->render();
        $customRouter()->resourceRoute('exam_types', 'ExamTypeController')->render();
        $customRouter()->resourceRoute('certificates', 'CertificateController')->render();
        $customRouter()->resourceRoute('certificate-types', 'CertificateTypeController')->render();
        $customRouter()->resourceRoute('certificate-issued', 'CertificateIssuedController')->render();

        /** training center skill development reports */
        $router->group(['prefix' => 'training-centers/reporting', 'as' => 'training-centers-reporting'], function () use ($router) {
            $router->get("skill-development", ["as" => "training-centers.skill-development-reports", "uses" => "TrainingCenterSkillDevelopmentReportController@getList"]);
            $router->get("skill-development/{id}", ["as" => "training-centers.skill-development-report-get", "uses" => "TrainingCenterSkillDevelopmentReportController@read"]);
            $router->post("skill-development", ["as" => "training-centers.skill-development-report-store", "uses" => "TrainingCenterSkillDevelopmentReportController@store"]);

            $router->get("combined-progress", ["as" => "training-centers.combined-progress-reports", "uses" => "TrainingCenterCombinedProgressReportController@getList"]);
            $router->get("combined-progress/{id}", ["as" => "training-centers.combined-progress-report-get", "uses" => "TrainingCenterCombinedProgressReportController@read"]);
            $router->post("combined-progress", ["as" => "training-centers.combined-progress-report-store", "uses" => "TrainingCenterCombinedProgressReportController@store"]);

            $router->get("progress", ["as" => "training-centers.progress-reports", "uses" => "TrainingCenterProgressReportController@getList"]);
            $router->get("progress/{id}", ["as" => "training-centers.progress-report-get", "uses" => "TrainingCenterProgressReportController@read"]);
            $router->post("progress", ["as" => "training-centers.progress-report-store", "uses" => "TrainingCenterProgressReportController@store"]);

            $router->get("income-expenditure", ["as" => "training-centers.income-expenditure-reports", "uses" => "TrainingCenterIncomeExpenditureReportController@getList"]);
            $router->get("income-expenditure/{id}", ["as" => "training-centers.income-expenditure-report-get", "uses" => "TrainingCenterIncomeExpenditureReportController@read"]);
            $router->post("income-expenditure", ["as" => "training-centers.income-expenditure-report-store", "uses" => "TrainingCenterIncomeExpenditureReportController@store"]);

        });

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
        $router->get('certification-authority-dashboard-statistics', ["as" => "certification-authority-dashboard-statistics", "uses" => "InstituteStatisticsController@certificationAuthorityDashboardStatistics"]);
        $router->get('demanded-courses', ["as" => "institute.demanding-courses", "uses" => "InstituteStatisticsController@demandingCourses"]);

        $router->post('rpl-applications/{id}/assign-to-batch', ["as" => "institute.youth-assessment-assign-to-batch", "uses" => "RplApplicationController@assignToBatch"]);
        $router->post('rto-batches/{id}/assign-assessor', ["as" => "institute.rto-batches-assign-assessor", "uses" => "RtoBatchController@assignAssessor"]);

        /** RTO dashboard statistics */
        $router->get('rto-dashboard-statistics', ["as" => "rto.dashboard-statistics", "uses" => "InstituteStatisticsController@rtoDashboardStatistics"]);
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

        $router->get("rto-countries", ["as" => "public.rto-countries", "uses" => "RtoCountryController@getPublicList"]);
        $router->get("rpl-sectors", ["as" => "public.rpl-sectors", "uses" => "RplSectorController@getPublicList"]);
        $router->get("rpl-occupations", ["as" => "public.rpl-occupations", "uses" => "RplOccupationController@getPublicList"]);
        $router->get("rpl-levels", ["as" => "public.rpl-levels", "uses" => "RplLevelController@getPublicList"]);
        $router->get("rpl-assessment-questions", ["as" => "public.rpl-assessment-questions", "uses" => "RplAssessmentQuestionController@getPublicList"]);


        $router->get("rpl-applications/{id}", ["as" => "public.rpl-applications", "uses" => "RplApplicationController@getRplApplicationDetails"]);


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

        /** Fetch all recent courses for youth feed API */
        $router->get('youth-feed-courses', ["as" => "youth-feed-courses", "uses" => "CourseController@youthFeedCourses"]);
    });

    /** institute registration */
    $router->post("institute-open-registration", ["as" => "register.organization", "uses" => "InstituteController@instituteRegistration"]);

    /** rpl assessment */
    $router->post("rpl-self-assessment", ["as" => "rpl-self-assessment", "uses" => "RplApplicationController@createRplAssessment"]);

    /** rpl application */
    $router->post("rpl-application", ["as" => "rpl-application", "uses" => "RplApplicationController@createRplApplication"]);

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
        $router->post('payment-by-ek-pay/ipn-handler/{secretToken}', ["as" => "payment.ipn-handler", "uses" => "CourseEnrollmentPaymentController@ekPayPaymentIpnHandler"]);
    });

    $router->group(["prefix" => "rpl-applications/payment", "as" => "rpl-applications.payment"], function () use ($router) {
        $router->post('payment-via-ek-pay/pay-now', ["as" => "payment-via-ek-pay.pay-now", "uses" => "RplApplicationCertificationPaymentController@paymentViaEkPay"]);
        $router->post('payment-via-ek-pay/ipn-handler/{secretToken}', ["as" => "payment-via-ek-pay.ipn-handler", "uses" => "RplApplicationCertificationPaymentController@ipnHandler"]);
    });

});



