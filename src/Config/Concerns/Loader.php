<?php

namespace Orchestra\Config\Concerns;

use Closure;

trait Loader
{
    /**
     * The after load callbacks for namespaces.
     *
     * @var array
     */
    protected $afterLoad = [];

    /**
     * Register an after load callback for a given namespace.
     *
     * @param  string  $namespace
     * @param  \Closure  $callback
     *
     * @return void
     */
    public function afterLoading(string $namespace, Closure $callback): void
    {
        $this->afterLoad[$namespace] = $callback;
    }

    /**
     * Get the after load callback array.
     *
     * @return array
     */
    public function getAfterLoadCallbacks(): array
    {
        return $this->afterLoad;
    }

    /**
     * Call the after load callback for a namespace.
     *
     * @param  string  $namespace
     * @param  string  $group
     * @param  array   $items
     *
     * @return array
     */
    protected function callAfterLoad(string $namespace, string $group, array $items): array
    {
        $callback = $this->afterLoad[$namespace];

        return $callback($this, $group, $items);
    }
}
