<?php namespace Orchestra\Routing;

use Illuminate\Support\Arr;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router as BaseRouter;
use Illuminate\Routing\ResourceRegistrar as BaseResourceRegistrar;

class Router extends BaseRouter
{
    /**
     * Register the typical authentication routes for an application.
     *
     * @return void
     */
    public function auth()
    {
        // Authentication Routes...
        $router->get('login', 'Auth\AuthenticateController@show');
        $router->post('login', 'Auth\AuthenticateController@attempt');
        $router->get('logout', 'Auth\DeauthenticateController@logout');

        $router->get('register', 'Auth\RegisterController@show');
        $router->post('register', 'Auth\RegisterController@store');
    }

     /**
     * Register the typical password reset routes for an application.
     *
     * @return void
     */
    public function password()
    {
        // Password Reset Routes...
        $this->get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
        $this->post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
        $this->post('password/reset', 'Auth\PasswordController@reset');
    }

    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     *
     * @return void
     */
    public function resource($name, $controller, array $options = [])
    {
        if ($this->container && $this->container->bound(BaseResourceRegistrar::class)) {
            $registrar = $this->container->make(BaseResourceRegistrar::class);
        } else {
            $registrar = new ResourceRegistrar($this);
        }

        $registrar->register($name, $controller, $options);
    }

    /**
     * Gather the middleware for the given route.
     *
     * @param  \Illuminate\Routing\Route  $route
     *
     * @return array
     */
    public function gatherRouteMiddlewares(Route $route)
    {
        $middlewares = [];

        foreach ($route->middleware() as $name) {
            $middlewares[] = $this->resolveMiddlewareClassName($name);
        }

        return Arr::flatten($middlewares);
    }
}
