<?php namespace Orchestra\Routing;

use Illuminate\Http\Request;

class RoutingServiceProvider extends \Illuminate\Routing\RoutingServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRequestOnConsole();

        parent::register();
    }

    /**
     * Register the requst on console interface.
     *
     * @return void
     */
    protected function registerRequestOnConsole()
    {
        $app = $this->app;

        if ($app->runningInConsole()) {
            $url = $app['config']->get('app.url', 'http://localhost');

            $app->instance('request', Request::create($url, 'GET', [], [], [], $_SERVER));
        }
    }

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
