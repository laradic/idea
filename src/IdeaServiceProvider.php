<?php
namespace Laradic\Idea;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class IdeaServiceProvider extends ServiceProvider
{
    #protected $defer = true;

    protected $configFiles = [ 'laradic.idea' ];

    protected $viewDirs = [ 'views' => 'laradic-idea' ];

    protected $aliases = [
        'laradic.idea.meta' => Metadata\MetaRepositoryInterface::class,
    ];

    protected $findCommands = [ 'Console' ];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laradic.idea.php', 'laradic.idea');
    }

    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/laradic.idea.php'=>config_path('laradic/idea.php')]);
    }


}
