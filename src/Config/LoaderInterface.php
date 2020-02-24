<?php

namespace Orchestra\Config;

interface LoaderInterface
{
    /**
     * Load the given configuration group.
     */
    public function load(string $environment, string $group, ?string $namespace = null): array;

    /**
     * Determine if the given configuration group exists.
     */
    public function exists(string $group, ?string $namespace = null): bool;

    /**
     * Add a new namespace to the loader.
     */
    public function addNamespace(string $namespace, string $hint): void;

    /**
     * Returns all registered namespaces with the config
     * loader.
     */
    public function getNamespaces(): array;

    /**
     * Apply any cascades to an array of package options.
     */
    public function cascadePackage(string $environment, string $package, string $group, array $items): array;
}
