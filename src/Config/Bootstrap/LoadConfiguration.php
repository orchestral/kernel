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

        date_default_timezone_set($config['app.timezone']);
    }
}
