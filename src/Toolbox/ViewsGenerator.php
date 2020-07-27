<?php

namespace Laradic\Idea\Toolbox;

use Laradic\Idea\Command\FindAllViews;

class ViewsGenerator extends AbstractToolboxGenerator
{
    /** @var \Illuminate\Filesystem\Filesystem */
    protected $fs;

    /** @var array */
    public $extensions;

    /** @var array|string[] */
    public $excludeNamespaces = [];

    public function handle()
    {
        /** @var \Illuminate\Support\Collection $views */
        $views = dispatch_now(new FindAllViews($this->excludeNamespaces));

        $this->metadata()
            ->push('providers', [
                'name'  => 'laravel_views',
                'items' => $views->map(function ($view) {
                    return [
                        'lookup_string' => $view[ 'view' ],
                        'icon'          => $view[ 'type' ] === 'twig' ? 'icons.TwigIcons.TwigFileIcon' : 'de.espend.idea.laravel.LaravelIcons.LARAVEL',
                        'target'        => "file://{$view[ 'relativePath' ]}",
                    ];
                })->toArray(),
            ])
            ->push('registrar', [
                'provider'  => 'laravel_views',
                'language'  => 'php',
                'signature' => [
                    'view',
                    'view:1',
                    'Illuminate\View\Factory::make',
                ],
            ])
            ->save();
    }

    protected function getViewsInPath($path)
    {
        $views = [];
        if ( ! $this->fs->exists($path)) {
            return $views;
        }
        $files = $this->fs->allFiles($path);
        foreach ($files as $file) {
            if (in_array($file->getExtension(), $this->extensions)) {
                $pathName = $file->getRelativePathname();
                $path     = $file->getRelativePath();
                $pathName = preg_replace('/\.' . preg_quote($file->getExtension(), '/') . '$/', '', $pathName);
                $views[]  = $pathName;
            }
        }
        return $views;
    }
}