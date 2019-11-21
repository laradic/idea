<?php

namespace Laradic\Idea\PhpToolbox;

use Illuminate\View\Factory;
use Illuminate\Filesystem\Filesystem;
use Laradic\Idea\Command\FindAllViews;
use Illuminate\Foundation\Bus\DispatchesJobs;

class GenerateViewsMeta
{
    use DispatchesJobs;
    protected $path;
    /** @var \Illuminate\Filesystem\Filesystem */
    protected $fs;
    /** @var array */
    protected $extensions;
    protected $excludeNamespaces = [ 'storage', 'root' ];

    public function __construct($path = null)
    {
        if ($path === null) {
            $path = path_join(config('laradic.idea.toolbox.path'), 'laravel/views/.ide-toolbox.metadata.json');
        }
        $this->path = $path;
    }

    public function handle(Factory $factory, Filesystem $fs)
    {
        $fs->ensureDirectory(path_get_directory($this->path));
        $meta = Metadata::create($this->path);

        /** @var \Illuminate\Support\Collection $views */
        $views = $this->dispatchNow(new FindAllViews([ 'root', 'storage' ]));

        $meta->push('providers', [
            'name'  => 'laravel_views',
            'items' => $views->map(function ($view) {
                return [
                    'lookup_string' => $view[ 'view' ],
                    'icon'          => $view[ 'type' ] === 'twig' ? 'icons.TwigIcons.TwigFileIcon' : 'de.espend.idea.laravel.LaravelIcons.LARAVEL',
                    'target'        => "file://{$view[ 'relativePath' ]}",
                ];
            })->toArray(),
        ]);
        $meta->push('registrar', [
            'provider'  => 'laravel_views',
            'language'  => 'php',
            'signature' => [
                'view',
                'view:1',
                'Illuminate\View\Factory::make',
            ],
        ]);
        $meta->save();
        return;
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