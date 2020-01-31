<?php

namespace Laradic\Idea\Console;

use Illuminate\Console\Command;
use Laradic\Idea\PhpToolbox\GenerateViewsMeta;
use Laradic\Idea\PhpToolbox\GenerateConfigMeta;
use Laradic\Idea\PhpToolbox\GenerateRoutesMeta;

class IdeaToolboxCommand extends Command
{
    protected $signature = 'idea:toolbox';

    protected $description = 'config items, views etc';

    public function handle()
    {

        $this->line('  - Generating config completions...', null, 'v');
        dispatch_now(new GenerateConfigMeta());
        $this->line('  - Generating view completions...', null, 'v');
        dispatch_now(new GenerateViewsMeta());
        $this->line('  - Generating route completions...', null, 'v');
        dispatch_now(new GenerateRoutesMeta());
    }
}