<?php

namespace Laradic\Idea\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Laradic\Generators\Completion\CompletionGenerator;

class IdeaCompletionCommand extends Command
{
    use DispatchesJobs;

    protected $signature = 'idea:completion {--array: Output the array docstring}';

    public function handle(CompletionGenerator $generator, Repository $repository)
    {
        $config = $repository->get('laradic.idea.completion');

        foreach ($config[ 'completions' ] as $class => $cfg) {
            $generator->append($this->laravel->make($class, ['config'=>$cfg]));
        }

        $result = $generator->generate();
        $result->writeToCompletionFile($config[ 'path' ]);

        $this->info('Generated idea completion file');
    }
}
