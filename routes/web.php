<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Helpers\Classes\CustomRouter;

$customRouter = function (string $as = '') use ($router) {
    $custom = new CustomRouter($router);
    return $custom->as($as);
};


$router->get('/', ['as' => 'api-info', 'uses' => 'ApiInfoController@apiInfo']);

$router->group(['prefix' => 'api/v1', 'as' => 'api.v1'], function () use ($router, $customRouter) {

    $customRouter()->resourceRoute('institutes', 'InstituteController')->render();
    $customRouter()->resourceRoute('programmes', 'ProgrammeController')->render();
    $customRouter()->resourceRoute('training-centers', 'TrainingCenterController')->render();
    $customRouter()->resourceRoute('batches', 'BatchController')->render();
    $customRouter()->resourceRoute('courses', 'CourseController')->render();
    $customRouter()->resourceRoute('branches', 'BranchController')->render();
    $customRouter()->resourceRoute('trainers', 'TrainerController')->render();


    $router->post('trainers/{id}/assign-trainer-to-batch', ['as' => 'trainers.assign-trainer-to-batch', 'uses' => 'TrainerController@assignTrainerToBatch']);



    //institutes trashed
    $router->get('institutes-trashed-data', ['as' => 'institutes.get-trashed-data', 'uses' => 'InstituteController@getTrashedData']);
    $router->get('institutes-restore-data/{id}', ['as' => 'institutes.restore-data', 'uses' => 'InstituteController@restore']);
    $router->get('institutes-force-delete/{id}', ['as' => 'institutes.restore-data', 'uses' => 'InstituteController@forceDelete']);

});
