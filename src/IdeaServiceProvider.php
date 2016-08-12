<?php
namespace Laradic\Idea;

use Laradic\ServiceProvider\ServiceProvider;

class IdeaServiceProvider extends ServiceProvider
{
    protected $configFiles = ['laradic.idea'];
    protected $viewDirs = ['views' => 'idea'];

    protected $singletons = [
        'idea.meta' => Metadata\MetaRepository::class
    ];

    protected $aliases = [
        'idea.meta' => Metadata\MetaRepositoryInterface::class
    ];

    protected $commands = [
        //'idea.meta.generate' => Metadata\Console\GenerateCommand::class
    ];
}