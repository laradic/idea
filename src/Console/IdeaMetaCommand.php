<?php


namespace Laradic\Idea\Console;


use Illuminate\Console\Command;
use Laradic\Idea\MetaGenerator;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;

class IdeaMetaCommand extends Command
{
    use DispatchesJobs;

    protected $signature = 'idea:meta';

    public function handle(MetaGenerator $generator, Repository $repository)
    {
        $config = $repository->get('laradic.idea.meta');
        $generator->setMetas($config[ 'metas' ]);
        $generator->setView($config[ 'view' ]);
        $generator->generate($config[ 'path' ]);
        $this->line('done');

    }
}