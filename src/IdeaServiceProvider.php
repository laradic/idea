<?php
namespace Laradic\Idea;

use Illuminate\Contracts\Foundation\Application;
use Laradic\Idea\Metadata\MetaRepository;
use Laradic\ServiceProvider\ServiceProvider;

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
        $app = parent::register();

        $app->singleton('laradic.idea.meta', function (Application $app) {
            $repo = $app->build(MetaRepository::class);
            foreach ( $app[ 'config' ]->get('laradic.idea.meta.metas', [ ]) as $name => $class ) {
                $repo->add($name, $class);
            }
            return $repo;
        });

        return $app;
    }


}