<?php

namespace Orchestra\Tests\Unit\Publisher\Publishing;

use Mockery as m;
use Orchestra\Publisher\Publishing\Asset;
use PHPUnit\Framework\TestCase;

class AssetTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    public function testBasicPathPublishing()
    {
        $pub = new Asset($files = m::mock('Illuminate\Filesystem\Filesystem'), __DIR__);
        $files->shouldReceive('isDirectory')->once()->with(__DIR__.'/packages/bar')->andReturn(true);
        $files->shouldReceive('copyDirectory')->once()->with('foo', __DIR__.'/packages/bar')->andReturn(true);

        $this->assertTrue($pub->publish('bar', 'foo'));
    }

    public function testPackageAssetPublishing()
    {
        $pub = new Asset($files = m::mock('Illuminate\Filesystem\Filesystem'), __DIR__);
        $pub->setPackagePath(__DIR__.'/vendor');
        $files->shouldReceive('isDirectory')->once()->with(__DIR__.'/vendor/foo/resources/public')->andReturn(true);
        $files->shouldReceive('isDirectory')->once()->with(__DIR__.'/packages/foo')->andReturn(true);
        $files->shouldReceive('copyDirectory')->once()->with(__DIR__.'/vendor/foo/resources/public', __DIR__.'/packages/foo')->andReturn(true);

        $this->assertTrue($pub->publishPackage('foo'));

        $pub = new Asset($files2 = m::mock('Illuminate\Filesystem\Filesystem'), __DIR__);
        $files2->shouldReceive('isDirectory')->once()->with(__DIR__.'/custom-packages/foo/resources/public')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with(__DIR__.'/custom-packages/foo/public')->andReturn(true);
        $files2->shouldReceive('isDirectory')->once()->with(__DIR__.'/packages/foo')->andReturn(true);
        $files2->shouldReceive('copyDirectory')->once()->with(__DIR__.'/custom-packages/foo/public', __DIR__.'/packages/foo')->andReturn(true);

        $this->assertTrue($pub->publishPackage('foo', __DIR__.'/custom-packages'));
    }
}
