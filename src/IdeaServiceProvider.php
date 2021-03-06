<?php

namespace Laradic\Idea;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Laradic\Idea\Console\IdeaMetaCommand;
use Illuminate\Contracts\Config\Repository;
use Laradic\Idea\Console\IdeaFoldersCommand;
use Laradic\Idea\Console\IdeaToolboxCommand;
use Laradic\Idea\Console\IdeaCompletionCommand;
use Laradic\Idea\Toolbox\ToolboxGeneratorRunner;

class IdeaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/config/laradic.idea.php', 'laradic.idea');

        $this->app->singleton('commands.laradic.idea.completion', IdeaCompletionCommand::class);
        $this->app->singleton('commands.laradic.idea.meta', IdeaMetaCommand::class);
        $this->app->singleton('commands.laradic.idea.toolbox', IdeaToolboxCommand::class);
        $this->app->singleton('commands.laradic.idea.folders', IdeaFoldersCommand::class);

        $this->app->events->listen('laradic.idea.bindings', function (Collection $bindings) {
            $bindings->put('config', Repository::class);
        });

        $this->app->bind(ToolboxGeneratorRunner::class, function () {
            return new ToolboxGeneratorRunner(config('laradic.idea.toolbox.generators', []));
        });

        $this->commands([
            'commands.laradic.idea.completion',
            'commands.laradic.idea.meta',
            'commands.laradic.idea.toolbox',
            'commands.laradic.idea.folders',
        ]);
    }

    public function boot()
    {
        $this->publishes([ dirname(__DIR__) . '/config/laradic.idea.php' => config_path('laradic/idea.php') ], 'config');
        $this->app->view->addNamespace('laradic/idea', dirname(__DIR__) . '/resources/views');

        if ($this->app->config->get('laradic.idea.meta.integrate_ide_helper')) {
            if ($this->app->config->has('ide-helper.meta_filename') && $this->app->config->get('ide-helper.meta_filename') === '.phpstorm.meta.php') {
                $this->app->config->set('ide-helper.meta_filename', '.phpstorm.meta.php/ide-helper.meta.php');
            }
            if ($this->app->config->has('ide-helper.include_factory_builders')) {
                $this->app->config->set('ide-helper.include_factory_builders', true);
            }
        }

        $fs       = $this->app->files;
        $metaPath = base_path('.phpstorm.meta.php');
        if ($fs->isFile($metaPath)) {
            $fs->delete($metaPath);
        }
        if ( ! $fs->exists($metaPath)) {
            $fs->makeDirectory($metaPath, 0755, true);
        }
    }


}
