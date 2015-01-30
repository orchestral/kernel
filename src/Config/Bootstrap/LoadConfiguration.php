<?php namespace Orchestra\Config\Bootstrap;

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
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $loader = new FileLoader(new Filesystem, $app['path.config']);

        $app->instance('config', $config = new Repository($loader, $app->environment()));

        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
        if (file_exists($cached = $app->getCachedConfigPath()) && ! $app->runningInConsole()) {
            $items = require $cached;

            $config->set($items);
        }

        date_default_timezone_set($config['app.timezone']);

        mb_internal_encoding('UTF-8');
    }
}
