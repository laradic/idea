<?php

namespace Laradic\Idea\Command;

use Illuminate\View\Factory;

class FindAllViews
{
    protected $excludeNamespaces = [];

    /** @var \Illuminate\Filesystem\Filesystem */
    protected $fs;

    /** @var array|string[] */
    protected $extensions;


    public function __construct(array $excludeNamespaces = [])
    {
        $this->excludeNamespaces = $excludeNamespaces;
    }


    public function handle(Factory $factory)
    {
        /** @var \Illuminate\View\FileViewFinder $finder */
        $finder           = $factory->getFinder();
        $this->fs         = $finder->getFilesystem();
        $this->extensions = $finder->getExtensions();
        $hints            = $finder->getHints();
        $paths            = $finder->getPaths();

        $views = [];
        foreach ($hints as $namespace => $paths) {
            if (in_array($namespace, $this->excludeNamespaces, true)) {
                continue;
            }

            foreach ($paths as $path) {
                $viewsInPath = $this->getViewsInPath($path);
                $viewsInPath = array_map(function ($view) use ($namespace) {
                    return $namespace . '::' . $view;
                }, $viewsInPath);
                $views       = array_unique(array_merge($views, $viewsInPath));
            }
        }
        return $views;
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
