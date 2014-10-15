<?php namespace Orchestra\Routing;

class RoutingServiceProvider extends \Illuminate\Routing\RoutingServiceProvider
{
    /**
     * Register the router instance.
     *
     * @return void
     */
    protected function registerRouter()
    {
        $this->app['router'] = $this->app->share(function ($app) {
            return new Router($app['events'], $app);
        });
    }
}
