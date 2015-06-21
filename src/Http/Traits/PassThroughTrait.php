<?php namespace Orchestra\Http\Traits; 

trait PassThroughTrait
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
    protected function shouldPassThrough($request)
    {
        foreach ($this->except as $except) {
            if ($this->foundation->is($except)) {
                return true;
            }
        }
        return false;
    }
}
