<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Helpers\Classes\CustomRouter;
use App\Models\BaseModel;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

$customRouter = function (string $as = '') use ($router) {
    $custom = new CustomRouter($router);
    return $custom->as($as);
};


$router->group(['prefix' => 'api/v1', 'as' => 'api.v1'], function () use ($router, $customRouter) {
    $router->get('/', ['as' => 'api-info', 'uses' => 'ApiInfoController@apiInfo']);

    $router->post('/file-upload', ['as' => 'api-info.upload', 'uses' => 'ApiInfoController@fileUpload']);

    $router->post('/auth-idp',function (){
//        $curl =  Http::dd()->asForm()->contentType("application/json")
//            ->withoutVerifying()
//            ->post("https://admin:admin@identity.bus.softbd.xyz/scim2/user",
//                [
//                    "schemas" => [
//                    ],
//                    "name" => [
//                        "familyName" => "Test User Name",
//                        "givenName" => "Test User Name",
//                    ],
//                    "userName" =>  "piyal",
//                    "password" => "123456",
//                    "emails" => [
//                        0 => [
//                            "primary" => true,
//                            "value" => "testuser.work@gmail.com",
//                            "type" => "work",
//                        ],
//                        1 => [
//                            "value" => "testuser.home@gmail.com",
//                            "type" => "home"
//                        ],
//                    ],
//                ]
//            );

        $client   = new Client();
        $config=[
            'verify'=>false,
            'data'=>[
                'schemas' => [
                ],
                'name' => [
                    'familyName' => 'title_en',
                    'givenName' => 'title_en',
                ],
                'userName' =>  'title_en',
                'password' => "123456",
                'emails' => [
                    0 => [
                        'primary' => true,
                        'value' => 'email',
                        'type' => 'work',
                    ],
                    1 => [
                        'value' => 'email',
                        'type' => 'home',
                    ],
                ],
            ]
        ];
        $client=$client->post(BaseModel::INSTITUTE_IDP_REGISTRATION_ENDPOINT,$config);
        return json_decode($client->getBody());

    });

    $customRouter()->resourceRoute('institutes', 'InstituteController')->render();
    $customRouter()->resourceRoute('programmes', 'ProgrammeController')->render();
    $customRouter()->resourceRoute('training-centers', 'TrainingCenterController')->render();
    $customRouter()->resourceRoute('batches', 'BatchController')->render();
    $customRouter()->resourceRoute('courses', 'CourseController')->render();
    $customRouter()->resourceRoute('branches', 'BranchController')->render();
    $customRouter()->resourceRoute('trainers', 'TrainerController')->render();

    /** Assign Trainers to Batch */
    $router->post('batches/{id}/assign-trainer-to-batch', ['as' => 'batches.assign-trainer-to-batch', 'uses' => 'BatchController@assignTrainerToBatch']);


    //institutes trashed
    $router->get('institutes-trashed-data', ['as' => 'institutes.get-trashed-data', 'uses' => 'InstituteController@getTrashedData']);
    $router->patch('institutes-restore-data/{id}', ['as' => 'institutes.restore-data', 'uses' => 'InstituteController@restore']);
    $router->delete('institutes-force-delete/{id}', ['as' => 'institutes.restore-data', 'uses' => 'InstituteController@forceDelete']);

    //programmes trashed
    $router->get('programmes-trashed-data', ['as' => 'programmes.get-trashed-data', 'uses' => 'ProgrammeController@getTrashedData']);
    $router->patch('programmes-restore-data/{id}', ['as' => 'programmes.restore-data', 'uses' => 'ProgrammeController@restore']);
    $router->delete('programmes-force-delete/{id}', ['as' => 'programmes.restore-data', 'uses' => 'ProgrammeController@forceDelete']);


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
