<?php

namespace Orchestra\Publisher\Publishing;

use InvalidArgumentException;

class Config extends Publisher
{
    /**
     * Get the source configuration directory to publish.
     *
     * @param  string  $package
     * @param  string  $packagePath
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function getSource($package, $packagePath)
    {
        $sources = [
            "{$packagePath}/{$package}/resources/config",
            "{$packagePath}/{$package}/config",
        ];

        foreach ($sources as $source) {
            if ($this->files->isDirectory($source)) {
                return $source;
            }
        }

        throw new InvalidArgumentException('Configuration not found.');
    }
}
