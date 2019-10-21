<?php


namespace Laradic\Idea\Console;


use Illuminate\Console\Command;
use Laradic\Idea\CompletionGenerator;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;

class IdeaCompletionCommand extends Command
{
    use DispatchesJobs;

    protected $signature = 'idea:completion';

    public function handle(CompletionGenerator $generator, Repository $repository)
    {
        $config = $repository->get('laradic.idea.completion');

        $generator->append($config[ 'completions' ]);
        $result = $generator->generate();
        $result->writeToCompletionFile($config[ 'path' ]);

        $this->line('done');
    }
}