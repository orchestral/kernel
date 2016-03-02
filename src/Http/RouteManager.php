<?php namespace Orchestra\Http;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\NamespacedItemResolver;
use Illuminate\Contracts\Foundation\Application;
use Orchestra\Contracts\Http\RouteManager as RouteManagerContract;

abstract class RouteManager implements RouteManagerContract
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Session CSRF token value.
     *
     * @var string|null
     */
    protected $csrfToken;

    /**
     * The extension instance.
     *
     * @var \Orchestra\Contracts\Extension\Factory
     */
    protected $extension;

    /**
     * Application router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * List of routes.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * URL Generator instance.
     *
     * @var \Illuminate\Contracts\Routing\UrlGenerator
     */
    protected $urlGenerator;

    /**
     * Construct a new instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->extension    = $app->make('orchestra.extension');
        $this->router       = $app->make('router');
        $this->urlGenerator = $app->make('url');
    }

    /**
     *  Return locate handles configuration for a package/app.
     *
     * @param  string  $path
     * @param  array   $options
     *
     * @return array
     */
    public function locate($path, array $options = [])
    {
        $query = '';

        // split URI and query string, the route resolver should not worry
        // about provided query string.
        if (strpos($path, '?') !== false) {
            list($path, $query) = explode('?', $path, 2);
        }

        list($package, $route, $item) = with(new NamespacedItemResolver())->parseKey($path);

        $route = $this->prepareValidRoute($route, $item, $query, $options);

        // If package is empty, we should consider that the route is using
        // app (or root path), it doesn't matter at this stage if app is
        // an extension or simply handling root path.
        empty($package) && $package = 'app';

        return [$package, $route];
    }

    /**
     * Return route group dispatch for a package/app.
     *
     * @param  string  $name
     * @param  string  $default
     * @param  array|\Closure  $attributes
     * @param  \Closure|null  $callback
     *
     * @return array
     */
    public function group($name, $default, $attributes = [], Closure $callback = null)
    {
        $route = $this->route($name, $default);

        if ($attributes instanceof Closure) {
            $callback   = $attributes;
            $attributes = [];
        }

        $attributes = array_merge($attributes, $route->group());

        if (! is_null($callback)) {
            $this->router->group($attributes, $callback);
        }

        return $attributes;
    }

    /**
     *  Return handles URL for a package/app.
     *
     * @param  string  $path
     * @param  array   $options
     *
     * @return string
     */
    public function handles($path, array $options = [])
    {
        if ($this->urlGenerator->isValidUrl($path)) {
            return $path;
        }

        list($package, $route) = $this->locate($path, $options);

        // Get the path from route configuration, and append route.
        $locate = $this->route($package)->to($route);

        empty($locate) && $locate = '/';

        return $this->urlGenerator->to($locate);
    }

    /**
     *  Return if handles URL match given string.
     *
     * @param  string  $path
     *
     * @return bool
     */
    public function is($path)
    {
        list($package, $route) = $this->locate($path);

        return $this->route($package)->is($route);
    }

    /**
     * Get application mode.
     *
     * @return
     */
    abstract public function mode();

    /**
     * Get extension route.
     *
     * @param  string  $name
     * @param  string  $default
     *
     * @return \Orchestra\Contracts\Extension\RouteGenerator
     */
    public function route($name, $default = '/')
    {
        if (! isset($this->routes[$name])) {
            $this->routes[$name] = $this->generateRouteByName($name, $default);
        }

        return $this->routes[$name];
    }

    /**
     * Run the callback when route is matched.
     *
     * @param  string  $path
     * @param  mixed   $listener
     *
     * @return void
     */
    public function when($path, $listener)
    {
        return $this->whenOn($path, RouteMatched::class, $listener);
    }

    /**
     * Run the callback when route is matched.
     *
     * @param  string  $path
     * @param  string  $on
     * @param  mixed   $listener
     *
     * @return void
     */
    public function whenOn($path, $on, $listener)
    {
        $events   = $this->app->make('events');
        $listener = $events->makeListener($listener);

        $events->listen($on, function () use ($listener, $path) {
            if ($this->is($path)) {
                call_user_func($listener, ...func_get_args());
            }
        });
    }

    /**
     * Generate route by name.
     *
     * @param  string  $name
     * @param  string  $default
     *
     * @return \Orchestra\Contracts\Extension\RouteGenerator
     */
    protected function generateRouteByName($name, $default)
    {
        return $this->extension->route($name, $default);
    }

    /**
     * Prepare valid route, since we already extract package from route
     * we can re-append query string to route value.
     *
     * @param  string  $route
     * @param  string  $item
     * @param  string  $query
     * @param  array   $options
     *
     * @return string
     */
    protected function prepareValidRoute($route, $item, $query, array $options)
    {
        $appends = [];
        $mode    = $this->mode();

        if (!! Arr::get($options, 'csrf', false)) {
            $appends['_token'] = $this->getCsrfToken();
        }

        if (! in_array($mode, ['normal'])) {
            $appends['_mode'] = $mode;
        }

        $query = $this->prepareHttpQueryString($query, $appends);

        ! empty($item) && $route = "{$route}.{$item}";
        empty($route) && $route  = '';
        empty($query) || $route  = "{$route}?{$query}";

        return $route;
    }

    /**
     * Prepare HTTP query string.
     *
     * @param  string  $query
     * @param  array   $appends
     *
     * @return string
     */
    protected function prepareHttpQueryString($query, $appends = [])
    {
        if (! empty($appends)) {
            $query .= (! empty($query) ? '&' : '').http_build_query($appends);
        }

        return $query;
    }

    /**
     * Get CSRF Token.
     *
     * @return string|null
     */
    protected function getCsrfToken()
    {
        if (is_null($this->csrfToken)) {
            $this->csrfToken = $this->app->make('session')->getToken();
        }

        return $this->csrfToken;
    }
}
