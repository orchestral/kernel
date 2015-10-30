<?php namespace Orchestra\Routing;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router as BaseRouter;
use Illuminate\Routing\ResourceRegistrar as BaseResourceRegistrar;

class Router extends BaseRouter
{
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
     * @return array
     */
    public function gatherRouteMiddlewares(Route $route)
    {
        $middlewares = [];

        foreach ($route->middleware() as $name) {
            $middlewares[] = $this->resolveMiddlewareClassName($name);
        }

        return $middlewares;
    }
}
