<?php
/**
 * Part of the Laradic PHP packages.
 *
 * MIT License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Idea\Metadata;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Filesystem\Filesystem;

/**
 * This is the MetaRepository.
 *
 * @package        Laradic
 * @author         Laradic Dev Team
 * @copyright      Copyright (c) 2015, Laradic
 * @license        https://tldrlegal.com/license/mit-license MIT License
 *
 */
class MetaRepository implements MetaRepositoryInterface
{
    protected $metas = [ ];

    protected $container;

    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $views;

    /**
     * @var \Laradic\Support\Filesystem
     */
    protected $files;

    protected $generator;

    /**
     * MetaRepository constructor.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @param \Illuminate\Contracts\View\Factory        $views
     * @param \Laradic\Support\Filesystem               $files
     * @param \Laradic\Support\StubGenerator            $generator
     */
    public function __construct(Container $container, Factory $views, Filesystem $files)
    {
        $this->container = $container;
        $this->views     = $views;
        $this->files     = $files;
        $this->generator = new StubGenerator();


    }

    public function add($name, $class)
    {
        if ( false === $this->exists($class) ) {
            throw new FileNotFoundException("Could not find class $class");
        }
        $this->metas[ $name ] = $class;
    }

    public function has($name)
    {
        return array_key_exists($name, $this->metas);
    }

    public function all()
    {
        return $this->metas;
    }

    public function create($path = null, $viewFile = null)
    {
        app()->register(Translation\TranslationServiceProvider::class);
        $path     = null === $path ? config('laradic.idea.meta.output') : $path;
        $viewFile = null === $viewFile ? config('laradic.idea.meta.view') : $viewFile;

        try {
            $metas = [ ];

            foreach ( $this->all() as $name => $class ) {
                if ( $this->exists($class) !== true || $class::canRun() === false ) {
                    continue;
                }

                $meta    = $this->createMetaClass($class);
                $methods = $meta->getMethods();
                $data    = $meta->getData();
                $metas[] = $this->generator->render($meta->getTemplate(), compact('methods', 'data'));
            }

            $open    = '<?php';
            $content = $this->views->make($viewFile, compact('open', 'metas'))->render();

            $this->files->put($path, $content);
        }
        catch (\Exception $e) {
            throw $e;
        }
    }


    /** @return Metas\MetaInterface $meta */
    protected function createMetaClass($className)
    {
        return $this->container->make($className);
    }

    protected function exists($class)
    {
        try {
            $exists = class_exists($class);
        }
        catch (\Exception $e) {
            return false;
        }
        return $exists;
    }
}
