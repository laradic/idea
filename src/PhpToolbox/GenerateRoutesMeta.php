<?php

namespace Laradic\Idea\PhpToolbox;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Filesystem\Filesystem;

class GenerateRoutesMeta extends AbstractMetaGenerator
{
    protected $directory = 'laravel/routes';

    /** @var \Illuminate\Filesystem\Filesystem */
    protected $fs;

    /** @var array */
    protected $extensions;

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

        $this->metadata()
            ->push('providers', [
                'name'  => 'laravel_routes',
                'items' => $routes->map(function ($route) {
                    $actionText = last(explode('\\', $route[ 'action' ]));
                    return [
                        'lookup_string' => $route[ 'name' ],
                        'tail_text'     => ' ' . $actionText,
                        'type_text'     => $route[ 'uri' ],
                        'icon'          => 'de.espend.idea.laravel.LaravelIcons.LARAVEL',
                        'target'        => str_replace('@', '::', $route[ 'action' ]),
                    ];
                })->values()->toArray(),
            ])
            ->push('registrar', [
                'provider'  => 'laravel_routes',
                'language'  => 'php',
                'signature' => [
                    'route',
                    'route:1',
                    'Illuminate\Contracts\Routing\UrlGenerator::route',
                ],
            ])
            ->save();
    }

}