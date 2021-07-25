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
    $customRouter()->resourceRoute('organizations', 'OrganizationController')->render();
    $customRouter()->resourceRoute('institutes', 'InstituteController')->render();
    $customRouter()->resourceRoute('course-configs', 'CourseConfigController')->render();
    $customRouter()->resourceRoute('branches', 'BranchController')->render();
});
