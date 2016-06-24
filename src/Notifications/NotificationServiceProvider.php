<?php

namespace Orchestra\Notifications;

use Illuminate\Contracts\Notifications\Factory as FactoryContract;
use Illuminate\Notifications\NotificationServiceProvider as ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ChannelManager::class, function ($app) {
            return new ChannelManager($app);
        });

        $this->app->alias(
            ChannelManager::class, FactoryContract::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            ChannelManager::class,
            FactoryContract::class,
        ];
    }
}
