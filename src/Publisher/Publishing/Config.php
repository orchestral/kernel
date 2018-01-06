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
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function getSource(string $package, string $packagePath): string
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
