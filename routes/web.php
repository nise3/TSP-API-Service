<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Helpers\Classes\CustomRouter;

$customRouter = function (string $as = '') use ($router) {
    $custom = new CustomRouter($router);
    return $custom->as($as);
};


$router->get('/hello', 'ExampleController@hateoasResponse');

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix' => 'api/v1', 'as' => 'api.v1'], function () use ($router, $customRouter) {

    //ranks crud operation
    $customRouter()->resourceRoute('ranks', 'RankController')->render();

    //ranktypes crud operation
    $customRouter()->resourceRoute('rank-types', 'RankTypeController')->render();

    //jobsectors crud operation
    $customRouter()->resourceRoute('job-sectors', 'JobSectorController')->render();

    //skills crud operation
    $customRouter()->resourceRoute('skills', 'SkillController')->render();

    //occupation crud api
    $customRouter()->resourceRoute('occupations', 'OccupationController')->render();


    //organizationsTypes crud operation
    $customRouter()->resourceRoute('organization-types', 'OrganizationTypeController')->render();

    //organization crud operation
    $customRouter()->resourceRoute('organizations', 'OrganizationController')->render();


});
