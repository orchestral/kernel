<?php

namespace Orchestra\Publisher;

use Orchestra\Publisher\Publishing\View;
use Orchestra\Publisher\Publishing\Asset;
use Orchestra\Publisher\Publishing\Config;
use Illuminate\Contracts\Foundation\Application;
use Orchestra\Publisher\Console\ViewPublishCommand;
use Orchestra\Publisher\Console\AssetPublishCommand;
use Orchestra\Publisher\Console\ConfigPublishCommand;
use Orchestra\Support\Providers\CommandServiceProvider as ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'AssetPublish' => 'command.asset.publish',
        'ConfigPublish' => 'command.config.publish',
        'ViewPublish' => 'command.view.publish',
    ];

    /**
     * Additional provides.
     *
     * @var array
     */
    protected $provides = [
        'asset.publisher',
        'config.publisher',
        'view.publisher',
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAssetPublisher();

        $this->registerConfigPublisher();

        $this->registerViewPublisher();

        parent::register();
    }

    /**
     * Register the asset publisher service and command.
     *
     * @return void
     */
    protected function registerAssetPublisher(): void
    {
        $this->app->singleton('asset.publisher', function (Application $app) {
            $publicPath = $app->publicPath();

            // The asset "publisher" is responsible for moving package's assets into the
            // web accessible public directory of an application so they can actually
            // be served to the browser. Otherwise, they would be locked in vendor.
            $publisher = new Asset($app->make('files'), $publicPath);

            $publisher->setPackagePath($app->basePath().'/vendor');

            return $publisher;
        });
    }

    /**
     * Register the configuration publisher class and command.
     *
     * @return void
     */
    protected function registerConfigPublisher(): void
    {
        $this->app->singleton('config.publisher', function (Application $app) {
            $path = $app->configPath();

            // Once we have created the configuration publisher, we will set the default
            // package path on the object so that it knows where to find the packages
            // that are installed for the application and can move them to the app.
            $publisher = new Config($app->make('files'), $path);

            $publisher->setPackagePath($app->basePath().'/vendor');

            return $publisher;
        });
    }

    /**
     * Register the view publisher class and command.
     *
     * @return void
     */
    protected function registerViewPublisher(): void
    {
        $this->app->singleton('view.publisher', function (Application $app) {
            $viewPath = $app->basePath().'/resources/views';

            // Once we have created the view publisher, we will set the default packages
            // path on this object so that it knows where to find all of the packages
            // that are installed for the application and can move them to the app.
            $publisher = new View($app->make('files'), $viewPath);

            $publisher->setPackagePath($app->basePath().'/vendor');

            return $publisher;
        });
    }

    /**
     * Register the asset publish console command.
     *
     * @return void
     */
    protected function registerAssetPublishCommand(): void
    {
        $this->app->singleton('command.asset.publish', function (Application $app) {
            return new AssetPublishCommand($app->make('asset.publisher'));
        });
    }

    /**
     * Register the configuration publish console command.
     *
     * @return void
     */
    protected function registerConfigPublishCommand(): void
    {
        $this->app->singleton('command.config.publish', function (Application $app) {
            return new ConfigPublishCommand($app->make('config.publisher'));
        });
    }

    /**
     * Register the view publish console command.
     *
     * @return void
     */
    protected function registerViewPublishCommand(): void
    {
        $this->app->singleton('command.view.publish', function (Application $app) {
            return new ViewPublishCommand($app->make('view.publisher'));
        });
    }
}
