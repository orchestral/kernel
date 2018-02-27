<?php

namespace Orchestra\Config\Concerns;

use Orchestra\Config\LoaderInterface;

trait Cascading
{
    /**
     * The loader implementation.
     *
     * @var \Orchestra\Config\LoaderInterface
     */
    protected $loader;

    /**
     * The current environment.
     *
     * @var string
     */
    protected $environment;

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string  $hint
     *
     * @return void
     */
    public function addNamespace(string $namespace, string $hint): void
    {
        $this->loader->addNamespace($namespace, $hint);
    }

    /**
     * Returns all registered namespaces with the config
     * loader.
     *
     * @return array
     */
    public function getNamespaces(): array
    {
        return $this->loader->getNamespaces();
    }

    /**
     * Get the loader implementation.
     *
     * @return \Orchestra\Config\LoaderInterface
     */
    public function getLoader(): LoaderInterface
    {
        return $this->loader;
    }

    /**
     * Set the loader implementation.
     *
     * @param  \Orchestra\Config\LoaderInterface  $loader
     *
     * @return void
     */
    public function setLoader(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Get the current configuration environment.
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }
}
