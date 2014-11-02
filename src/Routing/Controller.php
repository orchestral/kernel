<?php namespace Orchestra\Routing;

use Orchestra\Contracts\Routing\CallableController;
use Orchestra\Contracts\Routing\StackableController;
use Illuminate\Routing\Controller as BaseController;
use Orchestra\Contracts\Routing\FilterableController;

abstract class Controller extends BaseController implements CallableController, FilterableController, StackableController
{

}
