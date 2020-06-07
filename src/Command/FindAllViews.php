<?php

namespace Laradic\Idea\Command;

use Illuminate\Support\Str;
use Illuminate\View\Factory;

class FindAllViews
{

    /** @var array */
    protected $excludeNamespaces = [ 'storage', 'root' ];

    public function __construct($excludeNamespaces = [])
    {
        $this->excludeNamespaces = $excludeNamespaces;
    }

    public function handle(Factory $factory)
    {

        /** @var \Illuminate\View\FileViewFinder $finder */
        $finder     = $factory->getFinder();
        $fs         = $finder->getFilesystem();
        $extensions = $finder->getExtensions();
        $hints      = $finder->getHints();
        $paths      = $finder->getPaths();

        $views = collect();
        foreach ($hints as $namespace => $paths) {
            if (in_array($namespace, $this->excludeNamespaces, true)) {
                continue;
            }

            foreach ($paths as $path) {
                if ( ! $fs->exists($path)) {
                    continue;
                }
                $files = $fs->allFiles($path);
                foreach ($files as $file) {
                    if ( ! in_array($file->getExtension(), $extensions)) {
                        continue;
                    }
                    $pathName = $file->getRelativePathname();
                    $pathName = preg_replace('/\.blade.php$/', '', $pathName);
                    $pathName = preg_replace('/\.' . preg_quote($file->getExtension(), '/') . '$/', '', $pathName);

                    $type = $file->getExtension();
                    if (Str::endsWith($file->getPathname(), '.blade.php')) {
                        $type     = 'blade';
                        $pathName = str_replace('/', '.', $pathName);
                    }
                    $view = $namespace . '::' . $pathName;

                    $views->push([
                        'view'         => $view,
                        'namespace'    => $namespace,
                        'file'         => $file,
                        'path'         => $file->getPathname(),
                        'relativePath' => path_make_relative($file->getPathname(), base_path()),
                        'directory'    => $path,
                        'pathName'     => $pathName,
                        'type'         => $type,
                    ]);
                }
            }
        }
        return $views;
    }
}