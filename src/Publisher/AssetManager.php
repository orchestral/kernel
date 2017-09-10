<?php

namespace Orchestra\Publisher;

use Exception;
use Orchestra\Publisher\Publishing\Asset;
use Orchestra\Contracts\Publisher\Publisher;
use Illuminate\Contracts\Container\Container;
use Orchestra\Contracts\Publisher\FilePermissionException;

class AssetManager implements Publisher
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Migrator instance.
     *
     * @var \Orchestra\Publisher\Publishing\Asset
     */
    protected $publisher;

    /**
     * Construct a new instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     * @param  \Orchestra\Publisher\Publishing\Asset  $publisher
     */
    public function __construct(Container $app, Asset $publisher)
    {
        $this->app       = $app;
        $this->publisher = $publisher;
    }

    /**
     * Run migration for an extension or application.
     *
     * @param  string  $name
     * @param  string  $destinationPath
     *
     * @return mixed
     */
    public function publish($name, $destinationPath)
    {
        return $this->publisher->publish($name, $destinationPath);
    }

    /**
     * Migrate extension.
     *
     * @param  string  $name
     *
     * @return mixed
     *
     * @throws \Orchestra\Contracts\Publisher\FilePermissionException
     */
    public function extension($name)
    {
        if (is_null($path = $this->getPathFromExtensionName($name))) {
            return false;
        }

        try {
            return $this->publish($name, $path);
        } catch (Exception $e) {
            throw new FilePermissionException("Unable to publish [{$path}].");
        }
    }

    /**
     * Migrate Orchestra Platform.
     *
     * @return mixed
     *
     * @throws \Orchestra\Contracts\Publisher\FilePermissionException
     */
    public function foundation()
    {
        $path = rtrim($this->app->basePath(), '/').'/vendor/orchestra/foundation/resources/public';

        if (! $this->app->make('files')->isDirectory($path)) {
            return false;
        }

        try {
            return $this->publish('orchestra/foundation', $path);
        } catch (Exception $e) {
            throw new FilePermissionException("Unable to publish [{$path}].");
        }
    }

    /**
     * Get path from extension name.
     *
     * @param  string  $name
     *
     * @return string|null
     */
    protected function getPathFromExtensionName($name)
    {
        $finder = $this->app->make('orchestra.extension.finder');
        $files  = $this->app->make('files');

        if ($name === 'app') {
            $basePath = $this->app->basePath();
        } else {
            $basePath = rtrim($this->app->make('orchestra.extension')->option($name, 'path'), '/');
            $basePath = $finder->resolveExtensionPath($basePath);
        }

        $paths = ["{$basePath}/resources/public", "{$basePath}/public"];

        foreach ($paths as $path) {
            if ($files->isDirectory($path)) {
                return $path;
            }
        }

        return;
    }
}
