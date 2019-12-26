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
     * The asset publisher instance.
     *
     * @var \Orchestra\Publisher\Publishing\Asset
     */
    protected $assets;

    /**
     * Create a new asset publish command instance.
     *
     * @param  \Orchestra\Publisher\Publishing\Asset  $assets
     */
    public function __construct(Asset $assets)
    {
        parent::__construct();

        $this->assets = $assets;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $package = $this->input->getArgument('package');

        $this->publishAssets($package);

        return 0;
    }

    /**
     * Publish the assets for a given package name.
     *
     * @param  string  $package
     *
     * @return void
     */
    protected function publishAssets(string $package): void
    {
        if (! is_null($path = $this->getPath())) {
            $this->assets->publish($package, $path);
        } else {
            $this->assets->publishPackage($package);
        }

        $this->output->writeln('<info>Assets published for package:</info> '.$package);
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
