<?php

namespace Orchestra\Tests\Unit\Publisher;

use Illuminate\Container\Container;
use Mockery as m;
use Orchestra\Publisher\MigrateManager;
use PHPUnit\Framework\TestCase;

class MigrateManagerTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_run_migrations()
    {
        $app = new Container();
        $migrator = m::mock('\Illuminate\Database\Migrations\Migrator');
        $repository = m::mock('\Illuminate\Database\Migrations\DatabaseMigrationRepository');

        $migrator->shouldReceive('getRepository')->once()->andReturn($repository)
            ->shouldReceive('run')->once()->with('/var/www/laravel/migrations')->andReturn(null);
        $repository->shouldReceive('repositoryExists')->once()->andReturn(false)
            ->shouldReceive('createRepository')->once()->andReturn(null);

        $stub = new MigrateManager($app, $migrator);
        $stub->run('/var/www/laravel/migrations');
    }

    /** @test */
    public function it_can_run_migrations_for_extension()
    {
        $app = new Container();
        $app['migrator'] = $migrator = m::mock('\Illuminate\Database\Migrations\Migrator');
        $app['files'] = $files = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['orchestra.extension'] = $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $app['orchestra.extension.finder'] = $finder = m::mock('\Orchestra\Contracts\Extension\Finder');

        $repository = m::mock('\Illuminate\Database\Migrations\DatabaseMigrationRepository');

        $extension->shouldReceive('option')->once()->with('foo/bar', 'path')->andReturn('/var/www/laravel/vendor/foo/bar/')
            ->shouldReceive('option')->once()->with('foo/bar', 'source-path')->andReturn('/var/www/laravel/app/foo/bar/')
            ->shouldReceive('option')->once()->with('laravel/framework', 'path')->andReturn('/var/www/laravel/laravel/framework/')
            ->shouldReceive('option')->once()->with('laravel/framework', 'source-path')->andReturn('/var/www/laravel/laravel/framework/');
        $finder->shouldReceive('resolveExtensionPath')->once()->with('/var/www/laravel/vendor/foo/bar')->andReturn('/var/www/laravel/vendor/foo/bar')
            ->shouldReceive('resolveExtensionPath')->once()->with('/var/www/laravel/app/foo/bar')->andReturn('/var/www/laravel/app/foo/bar')
            ->shouldReceive('resolveExtensionPath')->twice()->with('/var/www/laravel/laravel/framework')->andReturn('/var/www/laravel/laravel/framework');
        $files->shouldReceive('isDirectory')->once()->with('/var/www/laravel/vendor/foo/bar/resources/database/migrations/')->andReturn(true)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/vendor/foo/bar/resources/migrations/')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/vendor/foo/bar/database/migrations/')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/app/foo/bar/resources/database/migrations/')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/app/foo/bar/resources/migrations/')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/app/foo/bar/database/migrations/')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/laravel/framework/resources/database/migrations/')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/laravel/framework/resources/migrations/')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/laravel/framework/database/migrations/')->andReturn(false);
        $migrator->shouldReceive('getRepository')->once()->andReturn($repository)
            ->shouldReceive('run')->once()->with('/var/www/laravel/vendor/foo/bar/resources/database/migrations/')->andReturn(null);
        $repository->shouldReceive('repositoryExists')->once()->andReturn(true)
            ->shouldReceive('createRepository')->never()->andReturn(null);

        $stub = new MigrateManager($app, $migrator);
        $stub->extension('foo/bar');
        $stub->extension('laravel/framework');
    }

    /** @test */
    public function it_can_run_migrations_for_app_as_extension()
    {
        $app = new class extends Container {
            public function basePath() {
                return '/var/www/laravel';
            }
        };

        $app['migrator'] = $migrator = m::mock('\Illuminate\Database\Migrations\Migrator');
        $app['files'] = $files = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['orchestra.extension'] = $extension = m::mock('\Orchestra\Contracts\Extension\Factory');
        $app['orchestra.extension.finder'] = $finder = m::mock('\Orchestra\Contracts\Extension\Finder');

        $repository = m::mock('\Illuminate\Database\Migrations\DatabaseMigrationRepository');

        $files->shouldReceive('isDirectory')->once()->with('/var/www/laravel/resources/database/migrations/')->andReturn(true)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/resources/migrations/')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/database/migrations/')->andReturn(false);
        $migrator->shouldReceive('getRepository')->once()->andReturn($repository)
            ->shouldReceive('run')->once()->with('/var/www/laravel/resources/database/migrations/')->andReturn(null);
        $repository->shouldReceive('repositoryExists')->once()->andReturn(true)
            ->shouldReceive('createRepository')->never()->andReturn(null);

        $stub = new MigrateManager($app, $migrator);
        $stub->extension('app');
    }

    /** @test */
    public function it_can_run_migrations_for_foundation()
    {
        $app = new class extends Container {
            public function basePath() {
                return '/var/www/laravel';
            }
        };

        $app['files'] = $files = m::mock('\Illuminate\Filesystem\Filesystem');
        $migrator = m::mock('\Illuminate\Database\Migrations\Migrator');

        $repository = m::mock('\Illuminate\Database\Migrations\DatabaseMigrationRepository');

        $files->shouldReceive('isDirectory')->once()->with('/var/www/laravel/vendor/orchestra/memory/resources/database/migrations/')->andReturn(true)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/vendor/orchestra/memory/database/migrations/')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/vendor/orchestra/memory/migrations/')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/vendor/orchestra/auth/resources/database/migrations/')->andReturn(true)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/vendor/orchestra/auth/database/migrations/')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with('/var/www/laravel/vendor/orchestra/auth/migrations/')->andReturn(false);
        $migrator->shouldReceive('getRepository')->twice()->andReturn($repository)
            ->shouldReceive('run')->once()->with('/var/www/laravel/vendor/orchestra/memory/resources/database/migrations/')->andReturn(null)
            ->shouldReceive('run')->once()->with('/var/www/laravel/vendor/orchestra/auth/resources/database/migrations/')->andReturn(null);
        $repository->shouldReceive('repositoryExists')->twice()->andReturn(true)
            ->shouldReceive('createRepository')->never()->andReturn(null);

        $stub = new MigrateManager($app, $migrator);
        $stub->foundation();
    }
}
