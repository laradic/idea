<?php

namespace Laradic\Idea\Toolbox;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;

class RoutesGenerator extends AbstractToolboxGenerator
{
    /** @var \Illuminate\Filesystem\Filesystem */
    protected $fs;

    /** @var array */
    public $extensions;

    public function handle(Router $router)
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