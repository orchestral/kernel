<?php namespace Orchestra\Contracts\Authorization;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

interface Authorizable extends Authenticatable
{
    /**
     * Get roles name as an array.
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function getRoles();
}
