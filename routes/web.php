<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Helpers\Classes\CustomRouter;
use App\Models\BaseModel;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

$customRouter = function (string $as = '') use ($router) {
    $custom = new CustomRouter($router);
    return $custom->as($as);
};


$router->group(['prefix' => 'api/v1', 'as' => 'api.v1'], function () use ($router, $customRouter) {
    $router->get('/', ['as' => 'api-info', 'uses' => 'ApiInfoController@apiInfo']);

    $router->post('/file-upload', ['as' => 'api-info.upload', 'uses' => 'ApiInfoController@fileUpload']);

    $router->post('/auth-idp', function () {

        $url = BaseModel::INSTITUTE_USER_REGISTRATION_ENDPOINT_LOCAL . 'register-users';
        $userPostField = [
            'permission_sub_group_id' => 2,
            'user_type' => 'institute',
            'username' => 'Piyal_Hasan-' . time(),
            'institute_id' => '1',
            'name_en' => 'testing',
            'name_bn' => 'testing_en',
            'email' => 'piyalemail@gmail.com',
            'mobile' => '01767111434',
            'loc_division_id' => 1,
            'loc_district_id' => 1,
            'loc_upazila_id' => 1
        ];

        return Http::retry(3, 100, function ($exception) {
            return $exception instanceof ConnectionException;
        })->post($url, $userPostField)->throw(function ($response, $e) {
            return $e;
        })->json();

    });

    $customRouter()->resourceRoute('institutes', 'InstituteController')->render();
    $customRouter()->resourceRoute('programs', 'ProgramController')->render();
    $customRouter()->resourceRoute('training-centers', 'TrainingCenterController')->render();
    $customRouter()->resourceRoute('batches', 'BatchController')->render();
    $customRouter()->resourceRoute('courses', 'CourseController')->render();
    $customRouter()->resourceRoute('branches', 'BranchController')->render();
    $customRouter()->resourceRoute('trainers', 'TrainerController')->render();

    /** Assign Trainers to Batch */
    $router->post('batches/{id}/assign-trainer-to-batch', ['as' => 'batches.assign-trainer-to-batch', 'uses' => 'BatchController@assignTrainerToBatch']);

    /** institute registration */
    $router->post("institute-registration", ["as" => "register.organization", "uses" => "InstituteController@instituteRegistration"]);


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
