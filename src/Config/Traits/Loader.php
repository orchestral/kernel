<?php namespace Orchestra\Config\Traits;

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
    public function afterLoading($namespace, Closure $callback)
    {
        $this->afterLoad[$namespace] = $callback;
    }

    /**
     * Get the after load callback array.
     *
     * @return array
     */
    public function getAfterLoadCallbacks()
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
    protected function callAfterLoad($namespace, $group, $items)
    {
        $callback = $this->afterLoad[$namespace];

        return $callback($this, $group, $items);
    }
}
