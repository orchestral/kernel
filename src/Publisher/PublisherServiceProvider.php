<?php

namespace Orchestra\Publisher;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;

class PublisherServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMigration();

        $this->registerAssetPublisher();
    }

    /**
     * Register the service provider for Orchestra Platform migrator.
     *
     * @return void
     */
    protected function registerMigration(): void
    {
        $this->app->singleton('orchestra.publisher.migrate', static function (Container $app) {
            // In order to use migration, we need to boot 'migration.repository' instance.
            $app->make('migration.repository');

            return new MigrateManager($app, $app->make('migrator'));
        });
    }

    /**
     * Register the service provider for Orchestra Platform asset publisher.
     *
     * @return void
     */
    protected function registerAssetPublisher(): void
    {
        $this->app->singleton('orchestra.publisher.asset', static function (Container $app) {
            return new AssetManager($app, $app->make('asset.publisher'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'orchestra.publisher.migrate',
            'orchestra.publisher.asset',
        ];
    }
}
