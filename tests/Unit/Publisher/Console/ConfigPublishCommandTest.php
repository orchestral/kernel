<?php

namespace Orchestra\TestCase\Unit\Publisher\Console;

use Mockery as m;
use Orchestra\Publisher\Console\ConfigPublishCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class ConfigPublishCommandTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    public function testCommandCallsPublisherWithProperPackageName()
    {
        $laravel = m::mock('\Illuminate\Foundation\Application[call]');
        $laravel->shouldReceive('call')->once()->andReturnUsing(function ($method, $parameters = []) {
            return call_user_func_array($method, $parameters);
        });

        $command = new ConfigPublishCommand($pub = m::mock('\Orchestra\Publisher\Publishing\Config'));
        $command->setLaravel($laravel);
        $pub->shouldReceive('alreadyPublished')->andReturn(false);
        $pub->shouldReceive('publishPackage')->once()->with('foo');
        $command->run(new ArrayInput(['package' => 'foo']), new NullOutput());
    }
}
