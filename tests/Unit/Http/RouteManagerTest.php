<?php

namespace Orchestra\TestCase\Unit\Http;

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
        $this->app = m::mock('\Illuminate\Contracts\Container\Container');
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
    private function getApplicationMocks()
    {
        $app = $this->app;
        $app->shouldReceive('make')->with('request')->andReturn($this->request);

        return $app;
    }

    /** @test */
    public function router_with_group()
    {
        $app = $this->getApplicationMocks();
        $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $router = m::mock('\Illuminate\Routing\Router');

        $app->shouldReceive('bound')->with('orchestra.extension')->andReturn(true)
            ->shouldReceive('make')->with('orchestra.extension')->andReturn($extension)
            ->shouldReceive('bound')->with('orchestra.extension.status')->andReturn(false)
            ->shouldReceive('make')->with('url')->andReturn($url)
            ->shouldReceive('make')->with('router')->andReturn($router);

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
        $app = $this->getApplicationMocks();
        $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $router = m::mock('\Illuminate\Routing\Router');

        $app->shouldReceive('bound')->with('orchestra.extension')->andReturn(true)
            ->shouldReceive('make')->with('orchestra.extension')->andReturn($extension)
            ->shouldReceive('bound')->with('orchestra.extension.status')->andReturn(false)
            ->shouldReceive('make')->with('url')->andReturn($url)
            ->shouldReceive('make')->with('router')->andReturn($router);

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
        $app = $this->getApplicationMocks();
        $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $router = m::mock('\Illuminate\Routing\Router');

        $app->shouldReceive('bound')->with('orchestra.extension')->andReturn(true)
            ->shouldReceive('make')->with('orchestra.extension')->andReturn($extension)
            ->shouldReceive('bound')->with('orchestra.extension.status')->andReturn(false)
            ->shouldReceive('make')->with('url')->andReturn($url)
            ->shouldReceive('make')->with('router')->andReturn($router);

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
        $app = $this->getApplicationMocks();
        $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $router = m::mock('\Illuminate\Routing\Router');

        $app->shouldReceive('make')->with('config')->andReturn($config)
            ->shouldReceive('bound')->with('orchestra.extension')->andReturn(true)
            ->shouldReceive('make')->with('orchestra.extension')->andReturn($extension)
            ->shouldReceive('bound')->with('orchestra.extension.status')->andReturn(false)
            ->shouldReceive('make')->with('url')->andReturn($url)
            ->shouldReceive('make')->with('router')->andReturn($router);

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
        $app = $this->getApplicationMocks();
        $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $status = m::mock('\Orchestra\Contracts\Extension\StatusChecker');
        $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $router = m::mock('\Illuminate\Routing\Router');

        $app->shouldReceive('make')->with('config')->andReturn($config)
            ->shouldReceive('bound')->with('orchestra.extension')->andReturn(true)
            ->shouldReceive('make')->with('orchestra.extension')->andReturn($extension)
            ->shouldReceive('bound')->with('orchestra.extension.status')->andReturn(true)
            ->shouldReceive('make')->with('orchestra.extension.status')->andReturn($status)
            ->shouldReceive('make')->with('url')->andReturn($url)
            ->shouldReceive('make')->with('router')->andReturn($router);

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
        $app = $this->getApplicationMocks();
        $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $session = m::mock('\Illuminate\Session\Store');
        $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $router = m::mock('\Illuminate\Routing\Router');

        $app->shouldReceive('make')->with('config')->andReturn($config)
            ->shouldReceive('bound')->with('orchestra.extension')->andReturn(true)
            ->shouldReceive('make')->with('orchestra.extension')->andReturn($extension)
            ->shouldReceive('bound')->with('orchestra.extension.status')->andReturn(false)
            ->shouldReceive('make')->with('session')->andReturn($session)
            ->shouldReceive('make')->with('url')->andReturn($url)
            ->shouldReceive('make')->with('router')->andReturn($router);

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
        $app = $this->getApplicationMocks();
        $request = $this->request;
        $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $router = m::mock('\Illuminate\Routing\Router');

        $app->shouldReceive('make')->with('config')->andReturn($config)
            ->shouldReceive('bound')->with('orchestra.extension')->andReturn(true)
            ->shouldReceive('make')->with('orchestra.extension')->andReturn($extension)
            ->shouldReceive('bound')->with('orchestra.extension.status')->andReturn(false)
            ->shouldReceive('make')->with('url')->andReturn($url)
            ->shouldReceive('make')->with('router')->andReturn($router);

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
        $app = $this->getApplicationMocks();
        $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $events = new Dispatcher();
        $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $url = m::mock('\Illuminate\Contracts\Routing\UrlGenerator');
        $router = m::mock('\Illuminate\Routing\Router');

        $app->shouldReceive('make')->with('config')->andReturn($config)
            ->shouldReceive('make')->with('events')->andReturn($events)
            ->shouldReceive('bound')->with('orchestra.extension')->andReturn(true)
            ->shouldReceive('make')->with('orchestra.extension')->andReturn($extension)
            ->shouldReceive('bound')->with('orchestra.extension.status')->andReturn(false)
            ->shouldReceive('make')->with('url')->andReturn($url)
            ->shouldReceive('make')->with('router')->andReturn($router);

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
