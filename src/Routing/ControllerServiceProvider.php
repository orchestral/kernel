<?php namespace Orchestra\Routing;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class ControllerServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('illuminate.route.dispatcher', function (Application $app) {
            return new ControllerDispatcher($app->make('router'), $app);
        });
    }
}
