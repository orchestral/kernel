<?php

namespace Orchestra\Routing;

use Illuminate\Routing\ResourceRegistrar as BaseResourceRegistrar;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router as BaseRouter;

class Router extends BaseRouter
{
    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     *
     * @return void
     */
    public function resource($name, $controller, array $options = [])
    {
        if ($this->container && $this->container->bound(BaseResourceRegistrar::class)) {
            $registrar = $this->container->make(BaseResourceRegistrar::class);
        } else {
            $registrar = new ResourceRegistrar($this);
        }

        $registrar->register($name, $controller, $options);
    }
}
