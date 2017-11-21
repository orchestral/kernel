<?php

namespace Orchestra\TestCase\Unit\Config;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Orchestra\Config\Repository;

class RepositoryTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    public function has_group_indicates_if_config_group_exists()
    {
        $config = $this->getRepository();
        $config->getLoader()->shouldReceive('exists')->once()->with('group', 'namespace')->andReturn(false);

        $this->assertFalse($config->hasGroup('namespace::group'));
    }

    /** @test */
    public function has_exist_when_item_exist()
    {
        $config = $this->getRepository();
        $options = $this->getDummyOptions();
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'app', null)->andReturn($options);
        $config->getLoader()->shouldReceive('exists')->once()->with('app/bing')->andReturn(false);

        $this->assertTrue($config->has('app.bing'));
        $this->assertTrue($config->get('app.bing'));
    }

    /** @test */
    public function get_returns_basic_items()
    {
        $config = $this->getRepository();
        $options = $this->getDummyOptions();
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'app', null)->andReturn($options);
        $config->getLoader()->shouldReceive('exists')->twice()->with('app/foo')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->once()->with('app/baz')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->once()->with('app/code')->andReturn(false);

        $this->assertEquals('bar', $config->get('app.foo'));
        $this->assertNull($config->get('app.foo.bar'));
        $this->assertEquals('breeze', $config->get('app.baz.boom'));
        $this->assertEquals('blah', $config->get('app.code', 'blah'));
        $this->assertEquals('blah', $config->get('app.code', function () { return 'blah'; }));
    }

    /** @test */
    public function entire_arrays_can_be_returned()
    {
        $config = $this->getRepository();
        $options = $this->getDummyOptions();
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'app', null)->andReturn($options);

        $this->assertEquals($options, $config->get('app'));
    }

    /** @test */
    public function loader_gets_called_correct_for_namespaces()
    {
        $config = $this->getRepository();
        $options = $this->getDummyOptions();
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'options', 'namespace')->andReturn($options);

        $this->assertEquals('bar', $config->get('namespace::options.foo'));
        $this->assertEquals('breeze', $config->get('namespace::options.baz.boom'));
        $this->assertEquals('blah', $config->get('namespace::options.code', 'blah'));
        $this->assertEquals('blah', $config->get('namespace::options.code', function () { return 'blah'; }));
    }

    /** @test */
    public function namespaced_accessed_and_post_namespace_run_the_events()
    {
        $config = $this->getRepository();
        $options = $this->getDummyOptions();
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'options', 'namespace')->andReturn($options);
        $config->afterLoading('namespace', function ($repository, $group, $items) {
            $items['dayle'] = 'rees';

            return $items;
        });

        $this->assertEquals('bar', $config->get('namespace::options.foo'));
        $this->assertEquals('breeze', $config->get('namespace::options.baz.boom'));
        $this->assertEquals('blah', $config->get('namespace::options.code', 'blah'));
        $this->assertEquals('blah', $config->get('namespace::options.code', function () { return 'blah'; }));
        $this->assertEquals('rees', $config->get('namespace::options.dayle'));
    }

    /** @test */
    public function loader_uses_namespace_as_group_when_using_packages_and_group_doesnt_exist()
    {
        $config = $this->getRepository();
        $options = $this->getDummyOptions();
        $config->getLoader()->shouldReceive('addNamespace')->with('namespace', __DIR__);
        $config->getLoader()->shouldReceive('cascadePackage')->andReturnUsing(function ($env, $package, $group, $items) { return $items; });
        $config->getLoader()->shouldReceive('exists')->once()->with('foo', 'namespace')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->once()->with('baz', 'namespace')->andReturn(false);
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'config', 'namespace')->andReturn($options);

        $config->package('foo/namespace', __DIR__);
        $this->assertEquals('bar', $config->get('namespace::foo'));
        $this->assertEquals('breeze', $config->get('namespace::baz.boom'));
    }

    /** @test */
    public function can_set_config()
    {
        $config = $this->getRepository();
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'foo', null)->andReturn(['name' => 'dayle']);
        $config->getLoader()->shouldReceive('exists')->once()->with('foo/name')->andReturn(false);

        $config->set('foo.name', 'taylor');
        $this->assertEquals('taylor', $config->get('foo.name'));

        $config = $this->getRepository();
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'foo', 'namespace')->andReturn(['name' => 'dayle']);

        $config->set('namespace::foo.name', 'taylor');
        $this->assertEquals('taylor', $config->get('namespace::foo.name'));
    }

    /** @test */
    public function package_register_namespace_and_setup_callback()
    {
        $config = m::mock('\Orchestra\Config\Repository[addNamespace]', [m::mock('\Orchestra\Config\LoaderInterface'), 'production']);
        $config->shouldReceive('addNamespace')->once()->with('rees', __DIR__)->andReturnNull();
        $config->getLoader()->shouldReceive('cascadePackage')->once()->with('production', 'dayle/rees', 'group', ['foo'])->andReturn(['bar']);
        $config->package('dayle/rees', __DIR__);
        $afterLoad = $config->getAfterLoadCallbacks();
        $results = call_user_func($afterLoad['rees'], $config, 'group', ['foo']);

        $this->assertEquals(['bar'], $results);
    }

    /**
     * Get mocked repository.
     *
     * @return \Orchestra\Config\Repository
     */
    protected function getRepository()
    {
        return new Repository(m::mock('\Orchestra\Config\LoaderInterface'), 'production');
    }

    /**
     * Get dummy options.
     *
     * @return array
     */
    protected function getDummyOptions()
    {
        return ['foo' => 'bar', 'baz' => ['boom' => 'breeze'], 'bing' => true];
    }
}
