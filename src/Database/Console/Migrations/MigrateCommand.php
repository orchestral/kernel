<?php namespace Orchestra\Database\Console\Migrations;

use Symfony\Component\Console\Input\InputOption;
use Illuminate\Database\Console\Migrations\MigrateCommand as BaseCommand;

class MigrateCommand extends BaseCommand
{
    /**
     * The path to the packages directory (vendor).
     *
     * @var string
     */
    protected $packagePath;

    /**
     * Set package path.
     *
     * @param  string  $packagePath
     * @return $this
     */
    public function setPackagePath($packagePath)
    {
        $this->packagePath = $packagePath;

        return $this;
    }

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        $package = $this->input->getOption('package');
        $path = $this->input->getOption('path');

        // If the package is in the list of migration paths we received we will put
        // the migrations in that path. Otherwise, we will assume the package is
        // is in the package directories and will place them in that location.
        if (! is_null($package)) {
            is_null($path) && $path = 'resources/migrations';

            return $this->packagePath.'/'.$package.'/'.$path;
        }

        // First, we will check to see if a path option has been defined. If it has
        // we will use the path relative to the root of this installation folder
        // so that migrations may be run for any path within the applications.
        if (! is_null($path)) {
            return $this->laravel['path.base'].'/'.$path;
        }

        return $this->laravel['path.database'].'/migrations';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path to migration files.', null],
            ['package', null, InputOption::VALUE_OPTIONAL, 'The package to migrate.', null],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
        ];
    }
}