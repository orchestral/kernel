<?php

namespace Orchestra\TestCase\Integration;

use Orchestra\Config\Repository;
use Orchestra\TestCase\TestCase;

class ConfigTest extends TestCase
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
    function instance_is_loaded_properly()
    {
        tap($this->app->make('config'), function ($config) {
            $this->assertInstanceOf(Repository::class, $config);
            $this->assertSame('Laravel', $config->get('app.name'));
            $this->assertSame('testing', $config->get('app.env'));
        });
    }
}
