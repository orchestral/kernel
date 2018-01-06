<?php

namespace Orchestra\Config;

interface LoaderInterface
{
    /**
     * Load the given configuration group.
     *
     * @param  string  $environment
     * @param  string  $group
     * @param  string|null  $namespace
     *
     * @return array
     */
    public function load(string $environment, string $group, ?string $namespace = null): array;

    /**
     * Determine if the given configuration group exists.
     *
     * @param  string  $group
     * @param  string|null  $namespace
     *
     * @return bool
     */
    public function exists(string $group, ?string $namespace = null): bool;

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string  $hint
     *
     * @return void
     */
    public function addNamespace(string $namespace, string $hint): void;

    /**
     * Returns all registered namespaces with the config
     * loader.
     *
     * @return array
     */
    public function getNamespaces(): array;

    /**
     * Apply any cascades to an array of package options.
     *
     * @param  string  $environment
     * @param  string  $package
     * @param  string  $group
     * @param  array   $items
     *
     * @return array
     */
    public function cascadePackage(string $environment, string $package, string $group, array $items): array;
}
