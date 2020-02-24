<?php

namespace Orchestra\Publisher\Publishing;

use InvalidArgumentException;

class Asset extends Publisher
{
    /**
     * Get the source assets directory to publish.
     *
     * @throws \InvalidArgumentException
     */
    protected function getSource(string $package, string $packagePath): string
    {
        $sources = [
            "{$packagePath}/{$package}/resources/public",
            "{$packagePath}/{$package}/public",
        ];

        foreach ($sources as $source) {
            if ($this->files->isDirectory($source)) {
                return $source;
            }
        }

        throw new InvalidArgumentException('Assets not found.');
    }
}
