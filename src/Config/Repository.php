<?php

namespace Orchestra\Config;

use ArrayAccess;
use Illuminate\Support\Arr;
use Orchestra\Contracts\Config\PackageRepository;
use Illuminate\Contracts\Config\Repository as ConfigContract;

class Repository extends NamespacedItemResolver implements ArrayAccess, ConfigContract, PackageRepository
{
    use Concerns\Cascading,
        Concerns\Loader;

    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * All of the registered packages.
     *
     * @var array
     */
    protected $packages = [];

    /**
     * Create a new configuration repository.
     *
     * @param  \Orchestra\Config\LoaderInterface  $loader
     * @param  string  $environment
     */
    public function __construct(LoaderInterface $loader, string $environment)
    {
        $this->setLoader($loader);

        $this->environment = $environment;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function has($key)
    {
        $default = \microtime(true);

        return $this->get($key, $default) !== $default;
    }

    /**
     * Determine if a configuration group exists.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function hasGroup($key)
    {
        list($namespace, $group) = $this->parseKey($key);

        return $this->loader->exists($group, $namespace);
    }

    /**
     * Get the specified configuration value.
     *
     * @param  string  $key
     * @param  mixed   $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        list($namespace, $group, $item) = $this->parseKey($key);

        // Configuration items are actually keyed by "collection", which is simply a
        // combination of each namespace and groups, which allows a unique way to
        // identify the arrays of configuration items for the particular files.
        $collection = $this->getCollection($group, $namespace);

        $this->load($group, $namespace, $collection);

        if (empty($item)) {
            return Arr::get($this->items, $collection, $default);
        }

        return Arr::get($this->items[$collection], $item, $default);
    }

    /**
     * Set a given configuration value.
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return void
     */
    public function set($key, $value = null)
    {
        if (\is_array($key)) {
            foreach ($key as $configKey => $configValue) {
                $this->setSingleItem($configKey, $configValue);
            }
        } else {
            $this->setSingleItem($key, $value);
        }
    }

    /**
     * Prepend a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function prepend($key, $value)
    {
        $config = $this->get($key);

        $this->setSingleItem($key, \array_unshift($config, $value));
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function push($key, $value)
    {
        $config = $this->get($key);

        $this->setSingleItem($key, \array_push($config, $value));
    }

    /**
     * Set a given collections of configuration value from cache.
     *
     * @param  array  $items
     *
     * @return $this
     */
    public function setFromCache(array $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Set a given configuration value.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  bool    $load
     *
     * @return void
     */
    protected function setSingleItem($key, $value = null, $load = true)
    {
        list($namespace, $group, $item) = $this->parseKey($key);

        $collection = $this->getCollection($group, $namespace);

        // We'll need to go ahead and lazy load each configuration groups even when
        // we're just setting a configuration item so that the set item does not
        // get overwritten if a different item in the group is requested later.
        if ($load) {
            $this->load($group, $namespace, $collection);
        }

        if (\is_null($item)) {
            $this->items[$collection] = $value;
        } else {
            Arr::set($this->items[$collection], $item, $value);
        }
    }

    /**
     * Load the configuration group for the key.
     *
     * @param  string  $group
     * @param  string  $namespace
     * @param  string  $collection
     *
     * @return void
     */
    protected function load($group, $namespace, $collection)
    {
        $env = $this->environment;

        // If we've already loaded this collection, we will just bail out since we do
        // not want to load it again. Once items are loaded a first time they will
        // stay kept in memory within this class and not loaded from disk again.
        if (isset($this->items[$collection])) {
            return;
        }

        $items = $this->loader->load($env, $group, $namespace);

        // If we've already loaded this collection, we will just bail out since we do
        // not want to load it again. Once items are loaded a first time they will
        // stay kept in memory within this class and not loaded from disk again.
        if (isset($this->afterLoad[$namespace])) {
            $items = $this->callAfterLoad($namespace, $group, $items);
        }

        $this->items[$collection] = $items;
    }

    /**
     * Register a package for cascading configuration.
     *
     * @param  string  $package
     * @param  string  $hint
     * @param  string|null  $namespace
     *
     * @return void
     */
    public function package(string $package, string $hint, ?string $namespace = null): void
    {
        $namespace = $this->getPackageNamespace($package, $namespace);

        $this->packages[] = $namespace;

        // First we will simply register the namespace with the repository so that it
        // can be loaded. Once we have done that we'll register an after namespace
        // callback so that we can cascade an application package configuration.
        $this->addNamespace($namespace, $hint);

        $this->afterLoading($namespace, function (Repository $me, $group, $items) use ($package) {
            $env = $me->getEnvironment();

            $loader = $me->getLoader();

            return $loader->cascadePackage($env, $package, $group, $items);
        });
    }

    /**
     * Get the configuration namespace for a package.
     *
     * @param  string  $package
     * @param  string|null  $namespace
     *
     * @return string
     */
    protected function getPackageNamespace(string $package, ?string $namespace): string
    {
        if (\is_null($namespace)) {
            list(, $namespace) = \explode('/', $package);
        }

        return $namespace;
    }

    /**
     * Get the collection identifier.
     *
     * @param  string  $group
     * @param  string|null  $namespace
     *
     * @return string
     */
    protected function getCollection(string $group, ?string $namespace = null): string
    {
        $namespace = $namespace ?: '*';

        return $namespace.'::'.$group;
    }

    /**
     * Get all of the configuration items.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string  $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string  $key
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}
