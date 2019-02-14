<?php

namespace Orchestra\Hashing;

use Illuminate\Hashing\HashServiceProvider as ServiceProvider;

class HashServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->singleton('hash.password', function ($app) {
            return new PasswordHasher($app['hash']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return \array_merge(parent::provides(), ['hash.password']);
    }
}
