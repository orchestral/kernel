<?php namespace Orchestra\Routing;

use Orchestra\Contracts\Routing\CallableController;
use Orchestra\Contracts\Routing\StackableController;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController implements CallableController, StackableController
{
    //
}
