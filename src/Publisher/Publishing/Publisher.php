<?php

namespace Orchestra\Publisher\Publishing;

use Illuminate\Filesystem\Filesystem;

abstract class Publisher
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The destination of the config files.
     *
     * @var string
     */
    protected $publishPath;

    /**
     * The path to the application's packages.
     *
     * @var string
     */
    protected $packagePath;

    /**
     * Create a new publisher instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $publishPath
     */
    public function __construct(Filesystem $files, string $publishPath)
    {
        $this->files = $files;
        $this->publishPath = $publishPath;
    }

    /**
     * Get the source directory to publish.
     *
     * @param  string  $package
     * @param  string  $packagePath
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    abstract protected function getSource(string $package, string $packagePath): string;

    /**
     * Publish files from a given path.
     *
     * @param  string  $package
     * @param  string  $source
     *
     * @return bool
     */
    public function publish(string $package, string $source): bool
    {
        $destination = $this->getDestinationPath($package);

        $this->makeDestination($destination);

        return $this->files->copyDirectory($source, $destination);
    }

    /**
     * Publish the files for a package.
     *
     * @param  string  $package
     * @param  string|null  $packagePath
     *
     * @return bool
     */
    public function publishPackage(string $package, ?string $packagePath = null): bool
    {
        $source = $this->getSource($package, $packagePath ?: $this->packagePath);

        return $this->publish($package, $source);
    }

    /**
     * Create the destination directory if it doesn't exist.
     *
     * @param  string  $destination
     *
     * @return void
     */
    protected function makeDestination(string $destination): void
    {
        if (! $this->files->isDirectory($destination)) {
            $this->files->makeDirectory($destination, 0777, true);
        }
    }

    /**
     * Determine if a given package has already been published.
     *
     * @param  string  $package
     *
     * @return bool
     */
    public function alreadyPublished(string $package): bool
    {
        return $this->files->isDirectory($this->getDestinationPath($package));
    }

    /**
     * Get the target destination path for the files.
     *
     * @param  string  $package
     *
     * @return string
     */
    public function getDestinationPath(string $package): string
    {
        return $this->publishPath."/packages/{$package}";
    }

    /**
     * Set the default package path.
     *
     * @param  string  $packagePath
     *
     * @return void
     */
    public function setPackagePath(string $packagePath): void
    {
        $this->packagePath = $packagePath;
    }
}
