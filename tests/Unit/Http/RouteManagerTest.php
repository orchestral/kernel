<?php

namespace Orchestra\Tests\Unit\Http;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Facade;
use Mockery as m;
use Orchestra\Http\RouteManager;
use PHPUnit\Framework\TestCase;

class RouteManagerTest extends TestCase
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $app;

    /**
     * Request instance.
     *
     * @var \Illuminate\Http\Requst
     */
    private $request;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->app = new Container();
        $this->request = m::mock('\Illuminate\Http\Request');

        $this->request->shouldReceive('root')->andReturn('http://localhost')
            ->shouldReceive('secure')->andReturn(false);

        $_SERVER['RouteManagerTest@callback'] = null;

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this->app);
    }

    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        unset($this->app);
        unset($this->request);
        unset($_SERVER['RouteManagerTest@callback']);

        m::close();
    }

    /**
     * Installed setup.
     */
    private function getApplicationContainer()
    {
        $app = $this->app;
        $app->instance('request', $this->request);

        return $app;
    }

    /** @test */
    public function router_with_group()
    {
        $app = $this->getApplicationContainer();
        $app['orchestra.extension'] = $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $app['url'] = $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $app['router'] = $router = m::mock('\Illuminate\Routing\Router');

        $appRoute = m::mock('\Orchestra\Contracts\Extension\UrlGenerator');

        $extension->shouldReceive('route')->once()
            ->with('admin', 'admin')->andReturn($appRoute);

        $appRoute->shouldReceive('group')->once()->andReturn(['prefix' => 'admin']);

        $stub = new StubRouteManager($app);

        $expected = [
            'before' => 'auth',
            'prefix' => 'admin',
        ];

        $this->assertEquals($expected, $stub->group('admin', 'admin', ['before' => 'auth']));
    }

    /** @test */
    public function router_with_group_using_closure()
    {
        $app = $this->getApplicationContainer();
        $app['orchestra.extension'] = $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $app['url'] = $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $app['router'] = $router = m::mock('\Illuminate\Routing\Router');

        $appRoute = m::mock('\Orchestra\Contracts\Extension\UrlGenerator');

        $extension->shouldReceive('route')->once()->with('admin', 'admin')->andReturn($appRoute);

        $appRoute->shouldReceive('group')->once()->andReturn(['prefix' => 'admin']);

        $group = [
            'before' => 'auth',
            'prefix' => 'admin',
        ];

        $callback = function () { };

        $router->shouldReceive('group')->once()->with($group, $callback)->andReturnNull();

        $stub = new StubRouteManager($app);

        $this->assertEquals($group, $stub->group('admin', 'admin', ['before' => 'auth'], $callback));
    }

    /** @test */
    public function router_with_group_using_closure_and_not_array()
    {
        $app = $this->getApplicationContainer();
        $app['orchestra.extension'] = $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $app['url'] = $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $app['router'] = $router = m::mock('\Illuminate\Routing\Router');

        $appRoute = m::mock('\Orchestra\Contracts\Extension\UrlGenerator');

        $extension->shouldReceive('route')->once()->with('admin', 'admin')->andReturn($appRoute);

        $appRoute->shouldReceive('group')->once()->andReturn(['prefix' => 'admin']);

        $group = [
            'prefix' => 'admin',
        ];

        $callback = function () { };

        $router->shouldReceive('group')->once()->with($group, $callback)->andReturnNull();

        $stub = new StubRouteManager($app);

        $this->assertEquals($group, $stub->group('admin', 'admin', $callback));
    }

    /** @test */
    public function router_with_handles()
    {
        $app = $this->getApplicationContainer();
        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['orchestra.extension'] = $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $app['url'] = $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $app['router'] = $router = m::mock('\Illuminate\Routing\Router');

        $appRoute = m::mock('\Orchestra\Contracts\Extension\UrlGenerator');

        $appRoute->shouldReceive('to')->once()->with('/')->andReturn('/')
            ->shouldReceive('to')->once()->with('info?foo=bar')->andReturn('info?foo=bar');
        $extension->shouldReceive('route')->once()->with('app', '/')->andReturn($appRoute);
        $url->shouldReceive('isValidUrl')->with('app::/')->andReturn(false)
            ->shouldReceive('isValidUrl')->once()->with('info?foo=bar')->andReturn(false)
            ->shouldReceive('isValidUrl')->once()->with('http://localhost/admin')->andReturn(true)
            ->shouldReceive('to')->once()->with('/')->andReturn('/')
            ->shouldReceive('to')->once()->with('info?foo=bar')->andReturn('info?foo=bar');

        $stub = new StubRouteManager($app);

        $this->assertEquals('/', $stub->handles('app::/'));
        $this->assertEquals('info?foo=bar', $stub->handles('info?foo=bar'));
        $this->assertEquals('http://localhost/admin', $stub->handles('http://localhost/admin'));
    }

    /** @test */
    public function router_with_handles_on_safe_mode()
    {
        $app = $this->getApplicationContainer();
        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['orchestra.extension'] = $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $app['orchestra.extension.status'] = $status = m::mock('\Orchestra\Contracts\Extension\StatusChecker');
        $app['url'] = $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $app['router'] = $router = m::mock('\Illuminate\Routing\Router');

        $appRoute = m::mock('\Orchestra\Contracts\Extension\UrlGenerator');

        $appRoute->shouldReceive('to')->once()->with('/?_mode=safe')->andReturn('/?_mode=safe')
            ->shouldReceive('to')->once()->with('info?foo=bar&_mode=safe')->andReturn('info?foo=bar&_mode=safe');
        $extension->shouldReceive('route')->once()->with('app', '/')->andReturn($appRoute);
        $status->shouldReceive('mode')->andReturn('safe');
        $url->shouldReceive('isValidUrl')->with('app::/')->andReturn(false)
            ->shouldReceive('isValidUrl')->once()->with('info?foo=bar')->andReturn(false)
            ->shouldReceive('isValidUrl')->once()->with('http://localhost/admin')->andReturn(true)
            ->shouldReceive('to')->once()->with('/?_mode=safe')->andReturn('/?_mode=safe')
            ->shouldReceive('to')->once()->with('info?foo=bar&_mode=safe')->andReturn('info?foo=bar&_mode=safe');

        $stub = new StubRouteManager($app);

        $this->assertEquals('/?_mode=safe', $stub->handles('app::/'));
        $this->assertEquals('info?foo=bar&_mode=safe', $stub->handles('info?foo=bar'));
        $this->assertEquals('http://localhost/admin', $stub->handles('http://localhost/admin'));
    }

    /** @test */
    public function router_with_handles_and_csrf_token()
    {
        $app = $this->getApplicationContainer();

        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['orchestra.extension'] = $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $app['session'] = $session = m::mock('\Illuminate\Session\Store');
        $app['url'] = $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $app['router'] = $router = m::mock('\Illuminate\Routing\Router');

        $appRoute = m::mock('\Orchestra\Contracts\Extension\UrlGenerator');

        $appRoute->shouldReceive('to')->once()->with('/?_token=StAGiQ')->andReturn('/?_token=StAGiQ')
            ->shouldReceive('to')->once()->with('info?foo=bar&_token=StAGiQ')->andReturn('info?foo=bar&_token=StAGiQ');
        $extension->shouldReceive('route')->once()->with('app', '/')->andReturn($appRoute);
        $session->shouldReceive('token')->once()->andReturn('StAGiQ');
        $url->shouldReceive('isValidUrl')->once()->with('app::/')->andReturn(false)
            ->shouldReceive('isValidUrl')->once()->with('info?foo=bar')->andReturn(false)
            ->shouldReceive('to')->once()->with('/?_token=StAGiQ')->andReturn('/?_token=StAGiQ')
            ->shouldReceive('to')->once()->with('info?foo=bar&_token=StAGiQ')->andReturn('info?foo=bar&_token=StAGiQ');

        $stub = new StubRouteManager($app);

        $options = ['csrf' => true];

        $this->assertEquals('/?_token=StAGiQ', $stub->handles('app::/', $options));
        $this->assertEquals('info?foo=bar&_token=StAGiQ', $stub->handles('info?foo=bar', $options));
    }

    /** @test */
    public function router_with_is()
    {
        $app = $this->getApplicationContainer();
        $request = $this->request;

        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['orchestra.extension'] = $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $app['url'] = $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $app['router'] = $router = m::mock('\Illuminate\Routing\Router');

        $appRoute = m::mock('\Orchestra\Contracts\Extension\UrlGenerator');

        $request->shouldReceive('path')->never()->andReturn('/');
        $appRoute->shouldReceive('is')->once()->with('/')->andReturn(true)
            ->shouldReceive('is')->once()->with('info?foo=bar')->andReturn(true);
        $extension->shouldReceive('route')->once()->with('app', '/')->andReturn($appRoute);

        $stub = new StubRouteManager($app);

        $this->assertTrue($stub->is('app::/'));
        $this->assertTrue($stub->is('info?foo=bar'));
    }

    /** @test */
    public function router_with_when()
    {
        $app = $this->getApplicationContainer();

        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['events'] = $events = new Dispatcher();
        $app['orchestra.extension'] = $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $app['url'] = $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $app['router'] = $router = m::mock('\Illuminate\Routing\Router');

        $appRoute = m::mock('\Orchestra\Contracts\Extension\UrlGenerator');

        $appRoute->shouldReceive('is')->once()->with('foo')->andReturn(true);
        $extension->shouldReceive('route')->once()->with('app', '/')->andReturn($appRoute);

        $stub = new StubRouteManager($app);

        $this->assertNull($_SERVER['RouteManagerTest@callback']);

        $stub->when('app::foo', function ($namespace, $method) {
            $_SERVER['RouteManagerTest@callback'] = "{$namespace}::{$method}";
        });

        $events->dispatch(RouteMatched::class, ['app', 'foo']);

        $this->assertEquals('app::foo', $_SERVER['RouteManagerTest@callback']);
    }
}

class StubRouteManager extends RouteManager
{
    public function installed(): bool
    {
        return true;
    }
}
