<?php

namespace Orchestra\Tests\Unit\Publisher\Console;

use Mockery as m;
use Orchestra\Publisher\Console\AssetPublishCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class AssetPublishCommandTest extends TestCase
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
        $pub = m::mock('\Orchestra\Publisher\Publishing\Asset');
        $laravel->shouldReceive('call')->once()->andReturnUsing(function ($method, $parameters = []) use ($pub) {
            return call_user_func_array($method, [$pub]);
        });

        $command = new AssetPublishCommand();
        $command->setLaravel($laravel);
        $pub->shouldReceive('alreadyPublished')->andReturn(false);
        $pub->shouldReceive('publishPackage')->once()->with('foo');
        $command->run(new ArrayInput(['package' => 'foo']), new NullOutput());
    }
}
