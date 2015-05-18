<?php namespace Orchestra\Config\Console;

use Symfony\Component\Finder\Finder;
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
            $this->getConfigurationFiles()
        );

        foreach ($files as $file) {
            $app['config'][$file];
        }

        return $app['config']->all();
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @return array
     */
    protected function getConfigurationFiles()
    {
        $files = [];
        $path  = $this->laravel->configPath();
        $found = Finder::create()->files()->name('*.php')->depth('== 0')->in($path);

        foreach ($found as $file) {
            $files[] = basename($file->getRealPath(), '.php');
        }

        return $files;
    }
}
