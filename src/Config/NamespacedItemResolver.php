<?php

namespace Orchestra\Config;

use Illuminate\Support\NamespacedItemResolver as Resolver;

abstract class NamespacedItemResolver extends Resolver
{
    /**
     * Parse a key into namespace, group, and item.
     *
     * @param  string  $key
     *
     * @return array
     */
    public function parseKey($key)
    {
        // If we've already parsed the given key, we'll return the cached version we
        // already have, as this will save us some processing. We cache off every
        // key we parse so we can quickly return it on all subsequent requests.
        if (isset($this->parsed[$key])) {
            return $this->parsed[$key];
        }

        // If the key does not contain a double colon, it means the key is not in a
        // namespace, and is just a regular configuration item. Namespaces are a
        // tool for organizing configuration items for things such as modules.
        if (\strpos($key, '::') === false) {
            $segments = \explode('.', $key);

            $parsed = $this->parseCustomSegments($segments);
        } else {
            $parsed = $this->parseNamespacedSegments($key);
        }

        // Once we have the parsed array of this key's elements, such as its groups
        // and namespace, we will cache each array inside a simple list that has
        // the key and the parsed array for quick look-ups for later requests.
        return $this->parsed[$key] = $parsed;
    }

    /**
     * Parse an array of basic segments.
     *
     * @param  array  $segments
     *
     * @return array
     */
    protected function parseCustomSegments(array $segments)
    {
        if (\count($segments) >= 2) {
            $group = "{$segments[0]}/{$segments[1]}";

            if ($this->getLoader()->exists($group)) {
                return [null, $group, \implode('.', \array_slice($segments, 2))];
            }
        }

        return $this->parseBasicSegments($segments);
    }

    /**
     * Parse an array of namespaced segments.
     *
     * @param  string  $key
     *
     * @return array
     */
    protected function parseNamespacedSegments($key)
    {
        list($namespace, $item) = \explode('::', $key);

        // If the namespace is registered as a package, we will just assume the group
        // is equal to the namespace since all packages cascade in this way having
        // a single file per package, otherwise we'll just parse them as normal.
        if (\in_array($namespace, $this->packages)) {
            return $this->parsePackageSegments($key, $namespace, $item);
        }

        return parent::parseNamespacedSegments($key);
    }

    /**
     * Parse the segments of a package namespace.
     *
     * @param  string  $key
     * @param  string  $namespace
     * @param  string  $item
     *
     * @return array
     */
    protected function parsePackageSegments($key, $namespace, $item)
    {
        $itemSegments = \explode('.', $item);

        // If the configuration file doesn't exist for the given package group we can
        // assume that we should implicitly use the config file matching the name
        // of the namespace. Generally packages should use one type or another.
        if (! $this->getLoader()->exists($itemSegments[0], $namespace)) {
            return [$namespace, 'config', $item];
        }

        return parent::parseNamespacedSegments($key);
    }

    /**
     * Get the loader implementation.
     *
     * @return \Orchestra\Config\LoaderInterface
     */
    abstract public function getLoader(): LoaderInterface;
}
