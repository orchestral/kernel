<?php

namespace Orchestra\Http;

use Hashids\Hashids;
use Illuminate\Contracts\Container\Container;
use Orchestra\Support\Providers\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class HashIdServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('orchestra.hashid', static function (Container $app) {
            return new Hashids($app->make('config')->get('app.key'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['orchestra.hashid'];
    }
}
