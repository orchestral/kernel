<?php

namespace Orchestra\Config;

use Illuminate\Filesystem\Filesystem;

class FileLoader implements LoaderInterface
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The default configuration path.
     *
     * @var string
     */
    protected $defaultPath;

    /**
     * All of the named path hints.
     *
     * @var array
     */
    protected $hints = [];

    /**
     * A cache of whether namespaces and groups exists.
     *
     * @var array
     */
    protected $exists = [];

    /**
     * Create a new file configuration loader.
     */
    public function __construct(Filesystem $files, string $defaultPath)
    {
        $this->files = $files;
        $this->defaultPath = $defaultPath;
    }

    /**
     * Load the given configuration group.
     */
    public function load(string $environment, string $group, ?string $namespace = null): array
    {
        $items = [];

        // First we'll get the root configuration path for the environment which is
        // where all of the configuration files live for that namespace, as well
        // as any environment folders with their specific configuration items.
        $path = $this->getPath($namespace);

        if (\is_null($path)) {
            return $items;
        }

        // First we'll get the main configuration file for the groups. Once we have
        // that we can check for any environment specific files, which will get
        // merged on top of the main arrays to make the environments cascade.
        $file = "{$path}/{$group}.php";

        if ($this->files->exists($file)) {
            $items = $this->files->getRequire($file);
        }

        // Finally we're ready to check for the environment specific configuration
        // file which will be merged on top of the main arrays so that they get
        // precedence over them if we are currently in an environments setup.
        $file = "{$path}/{$environment}/{$group}.php";

        if ($this->files->exists($file)) {
            $items = $this->mergeEnvironment($items, $file);
        }

        return $items;
    }

    /**
     * Merge the items in the given file into the items.
     */
    protected function mergeEnvironment(array $items, string $file): array
    {
        return \array_replace_recursive($items, $this->files->getRequire($file));
    }

    /**
     * Determine if the given group exists.
     */
    public function exists(string $group, ?string $namespace = null): bool
    {
        $key = $group.$namespace;

        // We'll first check to see if we have determined if this namespace and
        // group combination have been checked before. If they have, we will
        // just return the cached result so we don't have to hit the disk.
        if (! isset($this->exists[$key])) {
            $path = $this->getPath($namespace);

            // To check if a group exists, we will simply get the path based on the
            // namespace, and then check to see if this files exists within that
            // namespace. False is returned if no path exists for a namespace.
            if (\is_null($path)) {
                return $this->exists[$key] = false;
            }

            $file = "{$path}/{$group}.php";

            // Finally, we can simply check if this file exists. We will also cache
            // the value in an array so we don't have to go through this process
            // again on subsequent checks for the existing of the config file.
            $this->exists[$key] = $this->files->exists($file);
        }

        return $this->exists[$key];
    }

    /**
     * Apply any cascades to an array of package options.
     */
    public function cascadePackage(string $env, string $package, string $group, array $items): array
    {
        // First we will look for a configuration file in the packages configuration
        // folder. If it exists, we will load it and merge it with these original
        // options so that we will easily "cascade" a package's configurations.
        $file = "packages/{$package}/{$group}.php";

        if ($this->files->exists($path = $this->defaultPath.'/'.$file)) {
            $items = \array_merge($items, $this->getRequire($path));
        }

        // Once we have merged the regular package configuration we need to look for
        // an environment specific configuration file. If one exists, we will get
        // the contents and merge them on top of this array of options we have.
        $path = $this->getPackagePath($env, $package, $group);

        if ($this->files->exists($path)) {
            $items = \array_merge($items, $this->getRequire($path));
        }

        return $items;
    }

    /**
     * Get the package path for an environment and group.
     */
    protected function getPackagePath(string $env, string $package, string $group): string
    {
        $file = "packages/{$package}/{$env}/{$group}.php";

        return $this->defaultPath.'/'.$file;
    }

    /**
     * Get the configuration path for a namespace.
     */
    protected function getPath(?string $namespace): ?string
    {
        if (\is_null($namespace)) {
            return $this->defaultPath;
        } elseif (isset($this->hints[$namespace])) {
            return $this->hints[$namespace];
        }

        return null;
    }

    /**
     * Add a new namespace to the loader.
     */
    public function addNamespace(string $namespace, string $hint): void
    {
        $this->hints[$namespace] = $hint;
    }

    /**
     * Returns all registered namespaces with the config
     * loader.
     */
    public function getNamespaces(): array
    {
        return $this->hints;
    }

    /**
     * Get a file's contents by requiring it.
     *
     * @return mixed
     */
    protected function getRequire(string $path)
    {
        return $this->files->getRequire($path);
    }

    /**
     * Get the Filesystem instance.
     */
    public function getFilesystem(): Filesystem
    {
        return $this->files;
    }
}
