<?php

namespace Orchestra\TestCase\Unit\Config;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Orchestra\Config\FileLoader;

class FileLoaderTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function empty_array_is_returned_on_null_path()
    {
        $loader = $this->getLoader();
        $this->assertEquals([], $loader->load('local', 'group', 'namespace'));
    }

    /** @test */
    public function basic_array_is_returned()
    {
        $loader = $this->getLoader();
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__.'/app.php')->andReturn(true);
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__.'/local/app.php')->andReturn(false);
        $loader->getFilesystem()->shouldReceive('getRequire')->once()->with(__DIR__.'/app.php')->andReturn(['foo' => 'bar']);
        $array = $loader->load('local', 'app', null);

        $this->assertEquals(['foo' => 'bar'], $array);
    }

    /** @test */
    public function environment_arrays_is_merged()
    {
        $loader = $this->getLoader();
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__.'/app.php')->andReturn(true);
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__.'/local/app.php')->andReturn(true);
        $loader->getFilesystem()->shouldReceive('getRequire')->once()->with(__DIR__.'/app.php')->andReturn(['foo' => 'bar', 'providers' => ['AppProvider']]);
        $loader->getFilesystem()->shouldReceive('getRequire')->once()->with(__DIR__.'/local/app.php')->andReturn(['foo' => 'blah', 'baz' => 'boom', 'providers' => [1 => 'SomeProvider']]);
        $array = $loader->load('local', 'app', null);

        $this->assertEquals(['foo' => 'blah', 'baz' => 'boom', 'providers' => ['AppProvider', 'SomeProvider']], $array);
    }

    /** @test */
    public function group_exists_return_true_when_the_group_exists()
    {
        $loader = $this->getLoader();
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__.'/app.php')->andReturn(true);
        $this->assertTrue($loader->exists('app'));
    }

    /** @test */
    public function group_exists_return_true_when_namespace_group_exists()
    {
        $loader = $this->getLoader();
        $loader->addNamespace('namespace', __DIR__.'/namespace');
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__.'/namespace/app.php')->andReturn(true);
        $this->assertTrue($loader->exists('app', 'namespace'));
    }

    /** @test */
    public function group_exists_return_false_when_namespace_hint_doesnt_exists()
    {
        $loader = $this->getLoader();
        $this->assertFalse($loader->exists('app', 'namespace'));
    }

    /** @test */
    public function group_exists_return_false_when_namespace_group_doesnt_exists()
    {
        $loader = $this->getLoader();
        $loader->addNamespace('namespace', __DIR__.'/namespace');
        $loader->getFilesystem()->shouldReceive('exists')->with(__DIR__.'/namespace/app.php')->andReturn(false);
        $this->assertFalse($loader->exists('app', 'namespace'));
    }

    /** @test */
    public function cascading_packages_properly_load_files()
    {
        $loader = $this->getLoader();
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__.'/packages/dayle/rees/group.php')->andReturn(true);
        $loader->getFilesystem()->shouldReceive('getRequire')->once()->with(__DIR__.'/packages/dayle/rees/group.php')->andReturn(['bar' => 'baz']);
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__.'/packages/dayle/rees/local/group.php')->andReturn(true);
        $loader->getFilesystem()->shouldReceive('getRequire')->once()->with(__DIR__.'/packages/dayle/rees/local/group.php')->andReturn(['foo' => 'boom']);
        $items = $loader->cascadePackage('local', 'dayle/rees', 'group', ['foo' => 'bar']);

        $this->assertEquals(['foo' => 'boom', 'bar' => 'baz'], $items);
    }

    /**
     * Get the config loader.
     *
     * @return \Orchestra\Config\FileLoader
     */
    protected function getLoader()
    {
        return new FileLoader(m::mock('\Illuminate\Filesystem\Filesystem'), __DIR__);
    }
}
