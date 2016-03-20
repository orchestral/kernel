<?php

namespace Orchestra\Routing;

use Illuminate\Routing\ResourceRegistrar as BaseResourceRegistrar;

class ResourceRegistrar extends BaseResourceRegistrar
{
    /**
     * The default actions for a resourceful controller.
     *
     * @var array
     */
    protected $resourceDefaults = ['index', 'create', 'store', 'show', 'edit', 'update', 'delete', 'destroy'];

    /**
     * Add the delete method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array   $options
     *
     * @return \Illuminate\Routing\Route
     */
    protected function addResourceDelete($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name).'/{'.$base.'}/delete';

        $action = $this->getResourceAction($name, $controller, 'delete', $options);

        return $this->router->get($uri, $action);
    }
}
