<?php

namespace Orchestra\Http;

use InvalidArgumentException;

class VersionControl
{
    /**
     * List of supported versions.
     *
     * @var array
     */
    protected $supportedVersions = [];

    /**
     * Default version.
     *
     * @var string
     */
    protected $defaultVersion;

    /**
     * Add version.
     *
     * @param  string  $code
     * @param  string  $namespace
     * @param  bool  $default
     *
     * @return $this
     */
    public function addVersion(string $code, string $namespace, bool $default = false): self
    {
        $this->supportedVersions[$code] = $namespace;

        if (is_null($this->defaultVersion) || $default === true) {
            $this->setDefaultVersion($code);
        }

        return $this;
    }

    /**
     * Set default version.
     *
     * @param  string  $code
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setDefaultVersion(string $code): self
    {
        if (! array_key_exists($code, $this->supportedVersions)) {
            throw new InvalidArgumentException("Unable to set [{$code}] as default version!");
        }

        $this->defaultVersion = $code;

        return $this;
    }

    /**
     * Resolve version for requested class.
     *
     * @param  string  $namespace
     * @param  string  $version
     * @param  string  $group
     * @param  string  $name
     *
     * @return string
     */
    public function resolve(string $namespace, string $version, string $name): string
    {
        $class = str_replace('.', '\\', $name);

        if (! array_key_exists($version, $this->supportedVersions)) {
            $version = $this->defaultVersion;
        }

        return sprintf('%s\%s\%s\%s', $namespace, $this->supportedVersions[$version], $class);
    }
}
