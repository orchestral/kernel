<?php namespace Orchestra\Routing;

use Illuminate\Routing\Router as BaseRouter;
use Illuminate\Routing\ResourceRegistrar as BaseResourceRegistrar;

class Router extends BaseRouter
{
    /**
     * Indicates if the router is running filters.
     *
     * @var bool
     */
    protected $filtering = true;

    /**
     * Call the given filter with the request and response.
     *
     * @param  string  $filter
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     *
     * @return mixed
     */
    protected function callFilter($filter, $request, $response = null)
    {
        if (! $this->filtering) {
            return;
        }

        return parent::callFilter($filter, $request, $response);
    }

    /**
     * Call the given route filter.
     *
     * @param  string  $filter
     * @param  array  $parameters
     * @param  \Illuminate\Routing\Route  $route
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response|null  $response
     *
     * @return mixed
     */
    public function callRouteFilter($filter, $parameters, $route, $request, $response = null)
    {
        if (! $this->filtering) {
            return;
        }

        return parent::callRouteFilter($filter, $parameters, $route, $request, $response);
    }

    /**
     * Enable route filtering on the router.
     *
     * @return void
     */
    public function enableFilters()
    {
        $this->filtering = true;
    }

    /**
     * Disable route filtering on the router.
     *
     * @return void
     */
    public function disableFilters()
    {
        $this->filtering = false;
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
}
