<?php

namespace Laradic\Idea\Console;

use Illuminate\Console\Command;
use Laradic\Idea\Toolbox\ToolboxGeneratorRunner;

class IdeaToolboxCommand extends Command
{
    protected $signature = 'idea:toolbox';

    protected $description = 'config items, views etc';

    public function handle(ToolboxGeneratorRunner $runner)
    {
        $runner->before(function ($instance, $path) {
            $this->line('  - Running toolbox generator  ' . get_class($instance), null, 'v');
        });

        $runner->run();

        $this->info('Generated idea toolbox files');
    }
}