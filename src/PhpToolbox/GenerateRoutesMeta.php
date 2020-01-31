<?php

namespace Laradic\Idea\PhpToolbox;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Bus\DispatchesJobs;

class GenerateRoutesMeta
{
    use DispatchesJobs;
    protected $path;
    /** @var \Illuminate\Filesystem\Filesystem */
    protected $fs;
    /** @var array */
    protected $extensions;

    public function __construct($path = null)
    {
        if ($path === null) {
            $path = path_join(config('laradic.idea.toolbox.path'), 'laravel/routes/.ide-toolbox.metadata.json');
        }
        $this->path = $path;
    }

    public function handle(Router $router, Filesystem $fs)
    {
        $routes = collect($router->getRoutes())
            ->filter(function ($route) {
                return $route instanceof Route && $route->getName() !== null;
            })
            ->map(function (Route $route) {
                return [
                    'method' => implode('|', $route->methods()),
                    'uri'    => $route->uri(),
                    'name'   => $route->getName(),
                    'action' => ltrim($route->getActionName(), '\\'),
                ];
            });

        $fs->ensureDirectory(path_get_directory($this->path));
        $meta = Metadata::create($this->path);

        $meta->push('providers', [
            'name'  => 'laravel_routes',
            'items' => $routes->map(function ($route) {
                $actionText  = last(explode('\\', $route[ 'action' ]));
                return [
                    'lookup_string' => $route[ 'name' ],
                    'tail_text'     => ' ' . $actionText,
                    'type_text'     => $route[ 'uri' ],
                    'icon'          => 'de.espend.idea.laravel.LaravelIcons.LARAVEL',
                    'target'        => str_replace('@','::',$route[ 'action' ]),
                ];
            })->values()->toArray(),
        ]);
        $meta->push('registrar', [
            'provider'  => 'laravel_routes',
            'language'  => 'php',
            'signature' => [
                'route',
                'route:1',
                'Illuminate\Contracts\Routing\UrlGenerator::route',
            ],
        ]);
        $meta->save();
        return;
    }

}