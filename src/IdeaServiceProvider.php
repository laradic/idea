<?php
namespace Laradic\Idea;

use Laradic\ServiceProvider\ServiceProvider;

class IdeaServiceProvider extends ServiceProvider
{
    protected $defer = true;

    protected $configFiles = [ 'laradic.idea' ];

    protected $viewDirs = [ 'views' => 'idea' ];

    protected $singletons = [
        'idea.meta' => Metadata\MetaRepository::class,
    ];

    protected $aliases = [
        'idea.meta' => Metadata\MetaRepositoryInterface::class,
    ];

    protected $findCommands = [ 'Console' ];

    // automatic generated provides()
    public function boot()
    {
        return parent::boot();
    }

    public function register()
    {
        return parent::register();
    }


}