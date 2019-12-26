<?php

namespace Orchestra\Publisher\Console;

use Orchestra\Publisher\Publishing\View;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ViewPublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'publish:views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish a package's views to the application";

    /**
     * Execute the console command.
     *
     * @param  \Orchestra\Publisher\Publishing\View  $view
     *
     * @return int
     */
    public function handle(View $view)
    {
        $package = $this->input->getArgument('package');

        if (! \is_null($path = $this->getPath())) {
            $view->publish($package, $path);
        } else {
            $view->publishPackage($package);
        }

        $this->line('<info>Views published for package:</info> '.$package);

        return 0;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['package', InputArgument::REQUIRED, 'The name of the package being published.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path to the source view files.', null],
        ];
    }
}
