<?php

namespace Orchestra\Tests\Integration\Reauthenticate;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Orchestra\Testbench\TestCase;

class ReauthenticateControllerTest extends TestCase
{
    /** @test */
    public function get_reauthenticate_shows_view()
    {
        $controller = new TestController();

        $this->expectException('InvalidArgumentException', 'View [auth.reauthenticate] not found.');

        $controller->getReauthenticate();
    }

    /** @test */
    public function test_post_reauthenticate_returns_error()
    {
        $user = new User();

        Auth::shouldReceive('guard')->with(null)->andReturnSelf()
            ->shouldReceive('user')->once()->andReturn($user);

        $request = Request::create('http://reauthenticate.app/auth/reauthenticate', 'POST', [
            'password' => 'test',
        ]);

        $this->setSession($request, app('session.store'));

        $controller = new TestController();

        $response = $controller->postReauthenticate($request);

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertEquals('http://localhost', $response->getTargetUrl());
    }

    /** @test */
    public function post_reauthenticate_returns_redirect()
    {
        $user = new User();
        $user->password = bcrypt('test');

        Auth::shouldReceive('guard')->with(null)->andReturnSelf()
            ->shouldReceive('user')->once()->andReturn($user);

        Session::put('url.intended', 'http://reauthenticate.app/auth/reauthenticate');

        $request = Request::create('http://reauthenticate.app/auth/reauthenticate', 'POST', [
            'password' => 'test',
        ]);

        $this->setSession($request, app('session.store'));

        $controller = new TestController();

        $response = $controller->postReauthenticate($request);
        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertEquals('http://reauthenticate.app/auth/reauthenticate', $response->getTargetUrl());

        $this->assertTrue(Session::has('reauthenticate.life'));
        $this->assertTrue(Session::has('reauthenticate.authenticated'));
        $this->assertTrue(Session::get('reauthenticate.authenticated'));
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.model', User::class);
    }

    /**
     * Set the session for tests in a backwards compatible way.
     *
     * @param \Illuminate\Http\Request $request
     * @param Illuminate\Session\Store $session
     *
     * @return void
     */
    protected function setSession($request, $session)
    {
        return $request->setLaravelSession($session);
    }
}

class TestController extends \Illuminate\Routing\Controller
{
    use \Illuminate\Foundation\Validation\ValidatesRequests,
        \Orchestra\Reauthenticate\Reauthenticates;
}
