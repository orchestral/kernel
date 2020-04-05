<?php

namespace Orchestra\Http\Concerns;

trait PassThrough
{
    /**
     * The application implementation.
     *
     * @var \Orchestra\Contracts\Foundation\Foundation
     */
    protected $foundation;

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request, array $routesToExclude)
    {
        foreach ($routesToExclude as $except) {
            if ($this->foundation->is($except)) {
                return true;
            }
        }

        return false;
    }
}
