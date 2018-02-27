<?php

namespace Orchestra\Publisher\Console;

use Illuminate\Console\Command as BaseCommand;

class Command extends BaseCommand
{
    use Concerns\PublishingPath;
}
