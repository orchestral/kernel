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
     * @return $this
     */
    public function addVersion(string $code, string $namespace, bool $default = false)
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
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setDefaultVersion(string $code)
    {
        if (! \array_key_exists($code, $this->supportedVersions)) {
            throw new InvalidArgumentException("Unable to set [{$code}] as default version!");
        }

        $this->defaultVersion = $code;

        return $this;
    }

    /**
     * Resolve version for requested class.
     */
    public function resolve(string $namespace, string $version, string $name): string
    {
        $class = \str_replace('.', '\\', $name);

        if (! \array_key_exists($version, $this->supportedVersions)) {
            $version = $this->defaultVersion;
        }

        return \sprintf('%s\%s\%s\%s', $namespace, $this->supportedVersions[$version], $class);
    }
}
