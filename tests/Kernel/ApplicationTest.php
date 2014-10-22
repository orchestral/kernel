<?php namespace Orchestra\Kernel\TestCase;

use Orchestra\Kernel\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Orchestra\Kernel\Application::registerBaseServiceProviders()
     * method.
     *
     * @test
     */
    public function testRegisterBaseServiceProviders()
    {
        $app = new Application(__DIR__);

        $this->assertInstanceOf('\Illuminate\Events\Dispatcher', $app['events']);
        $this->assertInstanceOf('\Orchestra\Routing\Router', $app['router']);
    }
}
