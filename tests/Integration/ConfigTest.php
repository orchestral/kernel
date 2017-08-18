<?php

namespace Orchestra\TestCase\Integration;

use Orchestra\Config\Repository;
use Orchestra\TestCase\TestCase;

class ConfigTest extends TestCase
{
    /**
     * Resolve application implementation.
     *
     * @return \Illuminate\Foundation\Application
     */
    protected function resolveApplication()
    {
        $app = parent::resolveApplication();

        $app->bind('Illuminate\Foundation\Bootstrap\LoadConfiguration', 'Orchestra\Config\Bootstrap\LoadConfiguration');

        return $app;
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
