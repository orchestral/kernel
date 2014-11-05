<?php namespace Orchestra\Routing;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Orchestra\Contracts\Routing\CallableController;
use Orchestra\Contracts\Routing\StackableController;
use Orchestra\Contracts\Routing\FilterableController;
use Illuminate\Routing\Controller as IlluminateController;

class ControllerDispatcher extends \Illuminate\Routing\ControllerDispatcher
{
    /**
     * Dispatch a request to a given controller and method.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $controller
     * @param  string  $method
     * @return mixed
     */
    public function dispatch(Route $route, Request $request, $controller, $method)
    {
        $response = null;

        // First we will make an instance of this controller via the IoC container instance
        // so that we can call the methods on it. We will also apply any "after" filters
        // to the route so that they will be run by the routers after this processing.
        $instance = $this->makeController($controller);

        if ($instance instanceof FilterableController || $instance instanceof IlluminateController) {
            $this->assignAfter($instance, $route, $request, $method);

            $response = $this->before($instance, $route, $request, $method);
        }

        // If no before filters returned a response we'll call the method on the controller
        // to get the response to be returned to the router. We will then return it back
        // out for processing by this router and the after filters can be called then.
        if (is_null($response)) {
            $response = $this->callWithinStack($instance, $route, $request, $method);
        }

        return $response;
    }

    /**
     * Make a controller instance via the IoC container.
     *
     * @param  string  $controller
     * @return mixed
     */
    protected function makeController($controller)
    {
        Controller::setRouter($this->router);

        return $this->container->make($controller);
    }

    /**
     * Get the middleware for the controller instance.
     *
     * @param  object  $instance
     * @param  string  $method
     * @return array
     */
    protected function getMiddleware($instance, $method)
    {
        if (! ($instance instanceof StackableController || $instance instanceof IlluminateController)) {
            return [];
        }

        return parent::getMiddleware($instance, $method);
    }

    /**
     * Call the given controller instance method.
     *
     * @param  object  $instance
     * @param  \Illuminate\Routing\Route  $route
     * @param  string  $method
     * @return mixed
     */
    protected function call($instance, $route, $method)
    {
        if ($instance instanceof CallableController || $instance instanceof IlluminateController) {
            return parent::call($instance, $route, $method);
        }

        $parameters = $this->resolveClassMethodDependencies($route->parametersWithoutNulls(), $instance, $method);

        return call_user_func_array([$instance, $method], $parameters);
    }
}
