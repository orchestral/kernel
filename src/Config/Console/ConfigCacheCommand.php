<?php

namespace Orchestra\Config\Console;

use Symfony\Component\Finder\Finder;
use Illuminate\Contracts\Console\Kernel;
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
        $app = require $this->laravel->bootstrapPath('app.php');

        $app->make(Kernel::class)->bootstrap();
        $config = $app->make('config');

        $files = \array_merge(
            $this->configToCache(), $this->getConfigurationFiles()
        );

        foreach ($files as $file) {
            $config[$file];
        }

        return $config->all();
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @return array
     */
    protected function getConfigurationFiles()
    {
        $files = [];
        $path = $this->laravel->configPath();
        $found = Finder::create()->files()->name('*.php')->depth('== 0')->in($path);

        foreach ($found as $file) {
            $files[] = \basename($file->getRealPath(), '.php');
        }

        return $files;
    }

    /**
     * Get all of the package names that should be ignored.
     *
     * @return array
     */
    protected function configToCache(): array
    {
        if (! \file_exists($this->laravel->basePath('composer.json'))) {
            return [];
        }

        return \json_decode(\file_get_contents(
            $this->laravel->basePath('composer.json')
        ), true)['extra']['config-cache'] ?? [];
    }
}
