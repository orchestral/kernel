<?php

namespace Orchestra\Routing;

use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\RoutingServiceProvider as ServiceProvider;

class RoutingServiceProvider extends ServiceProvider
{
    /**
     * Register the router instance.
     *
     * @return void
     */
    protected function registerRouter()
    {
        $this->app->singleton('router', static function (Container $app) {
            return new Router($app->make('events'), $app);
        });
    }
}
