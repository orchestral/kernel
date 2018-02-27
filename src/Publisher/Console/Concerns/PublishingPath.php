<?php

namespace Orchestra\Publisher\Console\Concerns;

trait PublishingPath
{
    /**
     * Get the specified path to the files.
     *
     * @return string|null
     */
    protected function getPath(): ?string
    {
        $path = $this->input->getOption('path');

        // First we will check for an explicitly specified path from the user. If one
        // exists we will use that as the path to the assets. This allows the free
        // storage of assets wherever is best for this developer's web projects.
        if (is_null($path)) {
            return null;
        }

        return $this->laravel->basePath().'/'.$path;
    }
}
