<?php


namespace App\Helpers\Classes;


class CustomRouter
{
    private \Laravel\Lumen\Routing\Router $router;
    private string $uri;
    private string $controller;
    private ?string $as = null;

    public function __construct(\Laravel\Lumen\Routing\Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $uri
     * @param string $controller
     * @return $this
     */
    function resourceRoute(string $uri, string $controller): CustomRouter
    {
        $this->uri = $uri;
        $this->as = $this->uri;
        $this->controller = $controller;
        return $this;
    }

    /**
     * @param string $as
     * @return $this
     */
    function as(string $as): CustomRouter
    {
        $this->as = $as;
        return $this;
    }

    /**
     * @return void
     */
    function render()
    {
        $this->router->get($this->uri, ['as' => $this->as . '.get-list', 'uses' => $this->controller . '@getList']);
        $this->router->post($this->uri, ['as' => $this->as . '.store', 'uses' => $this->controller . '@store']);
        $this->router->get($this->uri . '/{id}', ['as' => $this->as . '.read', 'uses' => $this->controller . '@read']);
        $this->router->put($this->uri . '/{id}', ['as' => $this->as . '.update', 'uses' => $this->controller . '@update']);
        $this->router->delete($this->uri . '/{id}', ['as' => $this->as . '.destroy', 'uses' => $this->controller . '@destroy']);
    }
}
