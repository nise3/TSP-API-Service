<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Helpers\Classes\CustomRouter;
use Maatwebsite\Excel\Facades\Excel;


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
    $router->post('batches/{id}/assign-certificate-template-to-batch', ['as' => 'batches.assign-certificate-template-to-batch', 'uses' => 'BatchController@assignCertificateTemplateToBatch']);
    $router->get('batches/{id}/certificate-templates', ['as' => 'batches.assign-certificate-templates', 'uses' => 'BatchCertificateTemplateController@getListByBatchId']);

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
        // TODO: use 'certificates', 'certificate-types', ''certificate-issued'
        $customRouter()->resourceRoute('certificate-issued', 'CertificateIssuedController')->render();
//        $customRouter()->resourceRoute('certificate-templates', 'CertificateTemplateController')->render();

        $customRouter()->resourceRoute('course-result-configs', 'CourseResultConfigController')->render();

        /** Fetch all youth  who are a  participant of an exam */
        $router->get('exam-youth-list/{id}', ["as" => "exam-youth-list", "uses" => "ExamController@getExamYouthList"]);
        $router->get('preview-youth-exam/{examId}/{youthId}', ["as" => "preview-youth-exam", "uses" => "ExamController@previewYouthExam"]);

        /** youth individual exam answer sheet marking  */
        $router->put('youth-exam-mark-update', ["as" => "youth-exam-mark-update", "uses" => "ExamController@youthExamMarkUpdate"]);

        /** youth all exams by batch marking  */
        $router->post('youth-batch-exams-mark-update', ["as" => "youth-batch-exam-mark-update", "uses" => "ExamController@youthBatchExamsMarkUpdate"]);

        $router->put('exam-publish/{id}', ["as" => "exam-publish", "uses" => "ExamController@examPublish"]);

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

        /** Assign exams to batch */
        $router->post('batches/{id}/assign-exams-to-batch', ['as' => 'batches.assign-exams-to-batch', 'uses' => 'BatchController@assignExamToBatch']);
//        $router->post('batches/{id}/assign-certificate-template-to-batch', ['as' => 'batches.assign-certificate-template-to-batch', 'uses' => 'BatchController@assignCertificateTemplateToBatch']);

        /**  exam list by batch */
        $router->get('batches/{id}/exams', ["as" => "batches.exams-list-by-batch-id", "uses" => "BatchController@getExamsByBatchId"]);
        $router->get('batches/{id}/youth-exams', ["as" => "batches.youth-exams-list-by-batch-id", "uses" => "BatchController@getYouthExamListByBatch"]);

        /** Result Processing By Batch **/
        $router->post("batches/{id}/process-result", ["as" => "batches.process-result", "uses" => "BatchController@processBatchResult"]);
        $router->get("batches/{id}/results", ["as" => "batches.process-result", "uses" => "BatchController@getBatchExamResults"]);
        $router->put("batches/{id}/result-publish", ["as" => "batches.result-published", "uses" => "BatchController@publishExamResult"]);

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

        $router->post("course-enrollment-bulk-import", ["as" => "course-enrollment-bulk-import", "uses" => "CourseEnrollmentController@courseEnrollmentBulkImport"]);
    });

    $router->get("exam-result-summaries/{resultId}", ["as" => "batches.youth-exam-result-summaries", "uses" => "BatchController@getBatchExamResultSummaries"]);

    $router->get('youth-enroll-courses', ["as" => "courses.youth-enroll-courses", "uses" => "CourseEnrollmentController@getYouthEnrollCourses"]);


    /** Public Apis */
    $router->group(['prefix' => 'public', 'as' => 'public'], function () use ($router) {
        /** Course details with trainer */
        $router->get("courses/{id}", ["as" => "public.courses.course-details", "uses" => "CourseController@publicCourseDetails"]);
        $router->get("institutes", ["as" => "public.institutes", "uses" => "InstituteController@publicInstituteList"]);
        $router->get("registered-training-organizations", ["as" => "public.registered-training-organizations", "uses" => "RegisteredTrainingOrganizationController@getPublicList"]);
        /** Course All batches / Active batches / Up-coming batches */
        $router->get('courses/{id}/training-centers/batches', ['as' => 'courses.get-batches', 'uses' => 'BatchController@getPublicBatchesByCourseId']);
        $router->get('batches-by-four-ir-initiative-id/{fourIrInitiativeId}', ['as' => 'batches-by-four-ir-initiative-id', 'uses' => 'BatchController@getBatchesByFourIrInitiativeId']);
        /** nise-statistics */
        $router->get('nise-statistics', ["as" => "nise-statistics", "uses" => "InstituteStatisticsController@niseStatistics"]);

        $router->get("rto-countries", ["as" => "public.rto-countries", "uses" => "RtoCountryController@getPublicList"]);
        $router->get("rpl-sectors", ["as" => "public.rpl-sectors", "uses" => "RplSectorController@getPublicList"]);
        $router->get("rpl-occupations", ["as" => "public.rpl-occupations", "uses" => "RplOccupationController@getPublicList"]);
        $router->get("rpl-levels", ["as" => "public.rpl-levels", "uses" => "RplLevelController@getPublicList"]);
        $router->get("rpl-assessment-questions", ["as" => "public.rpl-assessment-questions", "uses" => "RplAssessmentQuestionController@getPublicList"]);

        /** youth batch exams  **/
        $router->get('batches/{id}/youth-exams', ["as" => "public.batches.youth-exams-list-by-batch-id", "uses" => "BatchController@getPublicYouthExamListByBatch"]);

        /** youth preview self exam answer paper */
        $router->get('preview-youth-exam/{examId}/{youthId}', ["as" => "preview-youth-exam", "uses" => "ExamController@previewPublicYouthExam"]);

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
        $router->get("course-enrollment-bulk-import-file-format", ["as" => "course-enrollment-bulk-import-file-format", "uses" => "CourseEnrollmentController@courseEnrollmentExcelFormat"]);
        /** Youth Certificate View */
        $router->get('youth-certificate-issued', ["as" => "certificarte-issued.youth-certificate-issued", "uses" => "CertificateIssuedController@getOneIssuedCertificate"]);

        $router->get('/batches/{id}/youth-exam-results', ["as" => "batches.public.exam-result", "uses" => "BatchController@getPublicYouthExamResultByBatch"]);

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

        /** Four IR */
        $router->get('get-four-ir-course-list', ["as" => "get-four-ir-course-list", "uses" => "CourseController@getFourIrCourses"]);
        $router->get('get-four-ir-course/{id}', ["as" => "get-four-ir-course", "uses" => "CourseController@getSingleFourIrCourse"]);
        $router->post('create-four-ir-course', ["as" => "create-four-ir-course", "uses" => "CourseController@createFourIrCourse"]);
        $router->put('update-four-ir-course/{id}', ["as" => "update-four-ir-course", "uses" => "CourseController@updateFourIrCourse"]);
        $router->put('approve-four-ir-course/{id}', ["as" => "approve-four-ir-course", "uses" => "CourseController@approveFourIrCourse"]);
        $router->get('get-four-ir-course-enrolled-youths', ["as" => "get-four-ir-course-enrolled-youths", "uses" => "CourseEnrollmentController@getEnrolledYouths"]);
        $router->get('get-four-ir-course-batches', ["as" => "get-four-ir-course-batches", "uses" => "BatchController@getCourseBatches"]);
        $router->get('get-four-ir-certificate-list/{fourIrInitiativeId}', ["as" => "get-four-ir-certificate-list", "uses" => "CertificateIssuedController@getCertificateList"]);

        /** Assessment List */
        $router->get('get-four-ir-youth-assessment-list/{fourIrInitiativeId}', ["as" => "get-four-ir-youth-assessment-list", "uses" => "ExamController@youthAssessmentList"]);

        /** Youth Certificate List */
        $router->get('get-youth-certificate-issued/{youthId}/course-id/{courseId}', ["as" => "get-youth-certificate-issued", "uses" => "CertificateIssuedController@getCertificateIssuedByYouthId"]);
    });

    /** Exam management */
    $router->get('exam-question-paper/{id}', ["as" => "exam-question-papers", "uses" => "ExamController@getExamQuestionPaper"]);
    $router->post('submit-exam-paper', ["as" => "submit exam-paper", "uses" => "ExamController@submitExamPaper"]);

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
//    $router->get("course-enrollment-bulk-import-file-format", ["as" => "course-enrollment-bulk-import-file-format", "uses" => "CourseEnrollmentController@courseEnrollmentExcelFormat"]);
});





