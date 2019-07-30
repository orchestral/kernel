<?php

namespace Orchestra\Tests\Integration\Reauthenticate;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Orchestra\Reauthenticate\Middleware\Reauthenticate as ReauthenticateMiddleware;

class ReauthenticateTest extends TestCase
{
    /** @test */
    public function middleware_returns_redirect()
    {
        $middleware = new ReauthenticateMiddleware();

        $request = Request::create('http://reauthenticate.app/restricted', 'GET', [
            'password' => 'test',
        ]);

        $this->setSession($request, app('session.store'));

        $result = $middleware->handle($request, function () {
            //
        });

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $result);
        $this->assertEquals('http://localhost/auth/reauthenticate', $result->getTargetUrl());
        $this->assertEquals(Session::get('url.intended'), 'http://reauthenticate.app/restricted');
    }

    /** @test */
    public function middleware_returns_next_with_valid_data()
    {
        Session::put('reauthenticate.life', Carbon::now()->timestamp);
        Session::put('reauthenticate.authenticated', true);

        $middleware = new ReauthenticateMiddleware();

        $called = false;
        $closure = function () use (&$called) {
            $called = true;
        };

        $request = Request::create('http://reauthenticate.app/restricted', 'GET', [
            'password' => 'test',
        ]);

        $this->setSession($request, app('session.store'));

        $result = $middleware->handle($request, $closure);

        $this->assertNotInstanceOf('Illuminate\Http\RedirectResponse', $result);
        $this->assertNull($result);
        $this->assertTrue($called);
    }

    /** @test */
    public function middleware_returns_redirect_with_invalid_data()
    {
        Session::put('reauthenticate.life', Carbon::minValue()->timestamp);
        Session::put('reauthenticate.authenticated', true);

        $middleware = new ReauthenticateMiddleware();

        $request = Request::create('http://reauthenticate.app/restricted', 'GET', [
            'password' => 'test',
        ]);

        $this->setSession($request, app('session.store'));

        $result = $middleware->handle($request, function () {
            //
        });

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $result);
        $this->assertEquals('http://localhost/auth/reauthenticate', $result->getTargetUrl());
        $this->assertEquals(Session::get('url.intended'), 'http://reauthenticate.app/restricted');
    }

    /** @test */
    public function middleware_returns_customized_redirect_url()
    {
        $middleware = new ReauthenticateMiddleware();

        $request = Request::create('http://reauthenticate.app/restricted', 'GET', [
            'password' => 'test',
        ]);

        $this->setSession($request, app('session.store'));

        config()->set('app.reauthenticate_url', '/custom-url');

        $result = $middleware->handle($request, function () {
            //
        });

        $this->assertEquals('http://localhost/custom-url', $result->getTargetUrl());
    }

    /**
     * Set the session for tests in a backwards compatible way
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \IlluminateSession\Store  $session
     *
     * @return void
     */
    protected function setSession(Request $request, $session)
    {
        return $request->setLaravelSession($session);
    }
}
