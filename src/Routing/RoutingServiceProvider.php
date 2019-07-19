<?php

namespace Orchestra\Routing;

use Illuminate\Contracts\Foundation\Application;
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
        $this->app->singleton('router', static function (Application $app) {
            return new Router($app->make('events'), $app);
        });
    }
}
