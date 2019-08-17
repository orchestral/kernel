<?php

namespace Orchestra\Http;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\NamespacedItemResolver;

class RouteResolver
{
    /**
     * Container implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * The extension instance.
     *
     * @var \Orchestra\Contracts\Extension\Factory
     */
    protected $extension;

    /**
     * Application status/mode implementation.
     *
     * @var \Orchestra\Contracts\Extension\StatusChecker
     */
    protected $status;

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
     * Session CSRF token value.
     *
     * @var string|null
     */
    protected $csrfToken;

    /**
     * Construct a new instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->urlGenerator = $app->make('url');

        $this->integrateWithExtension($app);
    }

    /**
     * Integrate with Orchestra Extension.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     *
     * @return void
     */
    protected function integrateWithExtension(Container $app): void
    {
        if ($app->bound('orchestra.extension')) {
            $this->extension = $app->make('orchestra.extension');
        }

        if ($app->bound('orchestra.extension.status')) {
            $this->status = $app->make('orchestra.extension.status');
        }
    }

    /**
     *  Return if handles URL match given string.
     *
     * @param  string  $path
     *
     * @return bool
     */
    public function is(string $path): bool
    {
        list($package, $route) = $this->locate($path);

        return $this->route($package)->is($route);
    }

    /**
     *  Return locate handles configuration for a package/app.
     *
     * @param  string  $path
     * @param  array   $options
     *
     * @return array
     */
    public function locate(string $path, array $options = []): array
    {
        $query = '';

        // split URI and query string, the route resolver should not worry
        // about provided query string.
        if (\strpos($path, '?') !== false) {
            list($path, $query) = \explode('?', $path, 2);
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
     * Get extension route.
     *
     * @param  string  $name
     * @param  string  $default
     *
     * @return \Orchestra\Contracts\Extension\UrlGenerator
     */
    public function route(string $name, string $default = '/')
    {
        if (! isset($this->routes[$name])) {
            $this->routes[$name] = $this->generateRouteByName($name, $default);
        }

        return $this->routes[$name];
    }

    /**
     * Get application mode.
     *
     * @return string
     */
    public function mode(): string
    {
        if (\is_null($this->status)) {
            return 'normal';
        }

        return $this->status->mode();
    }

    /**
     *  Return handles URL for a package/app.
     *
     * @param  string  $path
     * @param  array   $options
     *
     * @return string
     */
    public function to(string $path, array $options = []): string
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
     * Generate route by name.
     *
     * @param  string  $name
     * @param  string  $default
     *
     * @return \Orchestra\Contracts\Extension\UrlGenerator
     */
    protected function generateRouteByName(string $name, string $default)
    {
        if (\is_null($this->extension)) {
            return $default;
        }

        return $this->extension->route($name, $default);
    }

    /**
     * Prepare valid route, since we already extract package from route
     * we can re-append query string to route value.
     *
     * @param  string  $route
     * @param  string|null  $item
     * @param  string  $query
     * @param  array   $options
     *
     * @return string
     */
    protected function prepareValidRoute(string $route, ?string $item, string $query, array $options): string
    {
        $appends = [];
        $mode = $this->mode();

        if ((bool) ($options['csrf'] ?? false)) {
            $appends['_token'] = $this->getCsrfToken();
        }

        if (! \in_array($mode, ['normal'])) {
            $appends['_mode'] = $mode;
        }

        $query = $this->prepareHttpQueryString($query, $appends);

        ! empty($item) && $route = "{$route}.{$item}";
        empty($route) && $route = '';
        empty($query) || $route = "{$route}?{$query}";

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
    protected function prepareHttpQueryString(string $query, array $appends = []): string
    {
        if (! empty($appends)) {
            $query .= (! empty($query) ? '&' : '').\http_build_query($appends);
        }

        return $query;
    }

    /**
     * Get CSRF Token.
     *
     * @return string|null
     */
    protected function getCsrfToken(): ?string
    {
        if (\is_null($this->csrfToken)) {
            $this->csrfToken = $this->app->make('session')->token();
        }

        return $this->csrfToken;
    }
}
