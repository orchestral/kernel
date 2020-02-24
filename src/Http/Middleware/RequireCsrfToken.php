<?php

namespace Orchestra\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Session\TokenMismatchException;

class RequireCsrfToken
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The encrypter implementation.
     *
     * @var \Illuminate\Contracts\Encryption\Encrypter
     */
    protected $encrypter;

    /**
     * Create a new filter instance.
     */
    public function __construct(Application $app, Encrypter $encrypter)
    {
        $this->app = $app;
        $this->encrypter = $encrypter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->runningUnitTests() || $this->tokensMatch($request)) {
            return $next($request);
        }

        throw new TokenMismatchException();
    }

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return bool
     */
    protected function tokensMatch($request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if (! $token && $header = $request->header('X-XSRF-TOKEN')) {
            $token = $this->encrypter->decrypt($header);
        }

        return \hash_equals((string) $request->session()->token(), (string) $token);
    }

    /**
     * Determine if the application is running unit tests.
     *
     * @return bool
     */
    protected function runningUnitTests()
    {
        return $this->app->runningInConsole() && $this->app->runningUnitTests();
    }
}
