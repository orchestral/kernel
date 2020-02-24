<?php

namespace Orchestra\Publisher\Console;

use Illuminate\Console\ConfirmableTrait;
use Orchestra\Publisher\Publishing\Config;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ConfigPublishCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'publish:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish a package's configuration to the application";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Config $config)
    {
        $package = $this->input->getArgument('package');

        $proceed = $this->confirmToProceed('Config Already Published!', static function () use ($config, $package) {
            return $config->alreadyPublished($package);
        });

        if (! $proceed) {
            return 0;
        }

        if (! \is_null($path = $this->getPath())) {
            $config->publish($package, $path);
        } else {
            $config->publishPackage($package);
        }

        $this->line('<info>Configuration published for package:</info> '.$package);

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
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path to the configuration files.', null],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when the file already exists.'],
        ];
    }
}
