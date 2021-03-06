<?php

namespace Orchestra\Tests\Feature\Config;

use Orchestra\Config\Repository;
use Orchestra\Testbench\TestCase;

class RepositoryTest extends TestCase
{
    /**
     * Override application bindings.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function overrideApplicationBindings($app)
    {
        return [
            'Illuminate\Foundation\Bootstrap\LoadConfiguration' => 'Orchestra\Config\Bootstrap\LoadConfiguration',
        ];
    }

    /** @test */
    public function it_loads_config_properly()
    {
        tap($this->app->make('config'), function ($config) {
            $this->assertInstanceOf(Repository::class, $config);
            $this->assertSame('Laravel', $config->get('app.name'));
            $this->assertSame('testing', $config->get('app.env'));
        });
    }
}
