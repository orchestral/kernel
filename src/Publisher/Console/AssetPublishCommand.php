<?php

namespace Orchestra\Publisher\Console;

use Orchestra\Publisher\Publishing\Asset;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class AssetPublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'publish:assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish a package's assets to the public directory";

    /**
     * Execute the console command.
     *
     * @param  \Orchestra\Publisher\Publishing\Asset  $assets
     *
     * @return int
     */
    public function handle(Asset $assets)
    {
        $package = $this->input->getArgument('package');

        if (! \is_null($path = $this->getPath())) {
            $assets->publish($package, $path);
        } else {
            $assets->publishPackage($package);
        }

        $this->line('<info>Assets published for package:</info> '.$package);

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
            ['package', InputArgument::REQUIRED, 'The name of package being published.'],
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
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path to the asset files.', null],
        ];
    }
}
