<?php namespace Orchestra\Config\Console;

use Symfony\Component\Finder\Finder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\ConfigCacheCommand as BaseCommand;

class ConfigCacheCommand extends BaseCommand
{
    /**
     * Boot a fresh copy of the application configuration.
     *
     * @return array
     */
    protected function getFreshConfiguration()
    {
        $app = require $this->laravel['path.base'].'/bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $files = array_merge(
            $app['config']->get('compile.config', []),
            $this->getConfigurationFiles($app)
        );

        foreach ($files as $file) {
            $app['config'][$file];
        }

        return $app['config']->all();
    }

    /**
     * Set the "real" session driver on the configuratoin array.
     *
     * Typically the SessionManager forces the driver to "array" in CLI environment.
     *
     * @param  array  $config
     * @return array
     */
    protected function setRealSessionDriver(array $config)
    {
        $environment = $this->laravel->environment();
        $configPath = $this->laravel->configPath();

        $session = $this->files->getRequire("{$configPath}/session.php");

        if ($this->files->exists($file = "{$configPath}/{$environment}/session.php")) {
            $session = array_replace_recursive($session, $this->files->getRequire($file));
        }

        $config['*::session']['driver'] = $session['driver'];

        return $config;
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return array
     */
    protected function getConfigurationFiles(Application $app)
    {
        $files = [];

        foreach (Finder::create()->files()->name('*.php')->depth('== 0')->in($app->configPath()) as $file) {
            $files[] = basename($file->getRealPath(), '.php');
        }

        return $files;
    }
}
