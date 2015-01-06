<?php namespace Orchestra\Config\Console;

use Illuminate\Foundation\Console\ConfigCacheCommand as BaseCommand;

class ConfigCacheCommand extends BaseCommand
{
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
}
