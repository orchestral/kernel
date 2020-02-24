<?php

namespace Orchestra\Reauthenticate\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Orchestra\Reauthenticate\ReauthLimiter;

class Reauthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $reauth = new ReauthLimiter($request);

        if (! $reauth->check()) {
            $request->session()->put('url.intended', $request->url());

            return $this->invalidated($request);
        }

        return $next($request);
    }

    /**
     * Handle invalidated auth.
     *
     * @param \Illuminate\Http\Request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function invalidated($request)
    {
        $url = Config::get('app.reauthenticate_url', 'auth/reauthenticate');

        return Redirect::to($url);
    }
}
