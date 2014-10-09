<?php namespace Orchestra\Routing;

use BadMethodCallException;
use Illuminate\Routing\Router;
use Illuminate\Contracts\Container\Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class Controller
{
    use FilterableTrait;

    /**
     * The container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * The route filterer implementation.
     *
     * @var \Illuminate\Routing\Router
     */
    protected static $filterer;

    /**
     * Get the route filterer implementation.
     *
     * @return \Illuminate\Routing\Router
     */
    public static function getFilterer()
    {
        return static::$filterer;
    }

    /**
     * Set the route filterer implementation.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public static function setFilterer(Router $router)
    {
        static::$filterer = $router;
    }

    /**
     * Execute an action on the controller.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, $parameters)
    {
        return call_user_func_array([$this, $method], $parameters);
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function missingMethod($parameters = [])
    {
        throw new NotFoundHttpException("Controller method not found.");
    }

    /**
     * Set the container instance on the controller.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        throw new BadMethodCallException("Method [$method] does not exist.");
    }
}
