<?php

namespace Orchestra\Config\Bootstrap;

use Orchestra\Config\FileLoader;
use Orchestra\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;

class LoadConfiguration
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     *
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        if ($app->bound('config') && $app->environment('testing')) {
            return;
        }

        $items = [];
        $loader = new FileLoader(new Filesystem(), $app->configPath());

        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
        if (file_exists($cached = $app->getCachedConfigPath())) {
            $items = require $cached;
        }

        $this->setEnvironment($app, $items['*::app']['env'] ?? null);

        tap(new Repository($loader, $app->environment()), function ($config) use ($app, $items) {
            $app->instance('config', $config);
            $config->setFromCache($items);

            date_default_timezone_set($config->get('app.timezone', 'UTC'));
        });

        mb_internal_encoding('UTF-8');
    }

    /**
     * Set application environment.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  string|null  $env
     *
     * @return void
     */
    protected function setEnvironment(Application $app, ?string $env = null): void
    {
        $app->detectEnvironment(function () use ($env) {
            return $env ?: env('APP_ENV', 'production');
        });
    }
}
