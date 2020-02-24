<?php

namespace Orchestra\Http;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Events\RouteMatched;
use Orchestra\Contracts\Http\RouteManager as RouteManagerContract;

abstract class RouteManager implements RouteManagerContract
{
    /**
     * Container implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Application router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Route handler implementation.
     *
     * @var \Orchestra\Http\RouteResolver
     */
    protected $resolver;

    /**
     * Construct a new instance.
     */
    public function __construct(Container $app, ?RouteResolver $resolver = null)
    {
        $this->app = $app;
        $this->router = $this->resolveApplicationRouter($app);
        $this->resolver = $resolver instanceof RouteResolver ? $resolver : new RouteResolver($app);
    }

    /**
     * Resolve application router.
     *
     * @return mixed
     */
    protected function resolveApplicationRouter(Container $app)
    {
        return $app->make('router');
    }

    /**
     * Return route group dispatch for a package/app.
     *
     * @param  array|\Closure  $attributes
     */
    public function group(string $name, string $default, $attributes = [], Closure $callback = null): array
    {
        $route = $this->route($name, $default);

        if ($attributes instanceof Closure) {
            $callback = $attributes;
            $attributes = [];
        }

        $attributes = \array_merge($attributes, $route->group());

        if (! \is_null($callback)) {
            $this->router->group($attributes, $callback);
        }

        return $attributes;
    }

    /**
     *  Return locate handles configuration for a package/app.
     */
    public function locate(string $path, array $options = []): array
    {
        return $this->resolver->locate($path, $options);
    }

    /**
     *  Return handles URL for a package/app.
     */
    public function handles(string $path, array $options = []): string
    {
        return $this->resolver->to($path, $options);
    }

    /**
     *  Return if handles URL match given string.
     */
    public function is(string $path): bool
    {
        return $this->resolver->is($path);
    }

    /**
     * Get installation status.
     */
    abstract public function installed(): bool;

    /**
     * Get application status.
     */
    public function mode(): string
    {
        return $this->resolver->mode();
    }

    /**
     * Get extension route.
     *
     * @return \Orchestra\Contracts\Extension\UrlGenerator
     */
    public function route(string $name, string $default = '/')
    {
        return $this->resolver->route($name, $default);
    }

    /**
     * Run the callback when route is matched.
     *
     * @param  mixed   $listener
     */
    public function when(string $path, $listener): void
    {
        $this->whenOn($path, RouteMatched::class, $listener);
    }

    /**
     * Run the callback when route is matched.
     *
     * @param  mixed   $listener
     */
    public function whenOn(string $path, string $on, $listener): void
    {
        $events = $this->app->make('events');
        $listener = $events->makeListener($listener);

        $events->listen($on, function (...$payloads) use ($events, $listener, $path) {
            if ($this->is($path) && $this->installed()) {
                $listener($events, $payloads);
            }
        });
    }
}
