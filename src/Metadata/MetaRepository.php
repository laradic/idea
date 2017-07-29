<?php
/**
 * Part of the Laradic PHP Packages.
 *
 * Copyright (c) 2017. Robin Radic.
 *
 * The license can be found in the package and online at https://laradic.mit-license.org.
 *
 * @copyright Copyright 2017 (c) Robin Radic
 * @license https://laradic.mit-license.org The MIT License
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
    protected $metas = [];

    protected $container;

    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $views;

    /**
     * @var \Laradic\Support\Filesystem
     */
    protected $files;

    /**
     * @var \Laradic\Idea\Metadata\StubGenerator
     */
    protected $generator;

    /** @var boolean */
    protected $ignoreExceptions = true;

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
        if (false === $this->exists($class)) {
            throw new FileNotFoundException("Could not find class $class");
        }
        $this->metas[ $name ] = $class;
        return $this;
    }

    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->metas[ $name ]);
        }
        return $this;
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
        $path     = base_path(null === $path ? config('laradic.idea.meta.output') : $path);
        $viewFile = null === $viewFile ? config('laradic.idea.meta.view') : $viewFile;

        $metas = [];
        $__env = $this->views;

        foreach ($this->all() as $name => $class) {
            try {
                if ($this->exists($class) !== true || $class::canRun() === false) {
                    continue;
                }

                $meta    = $this->createMetaClass($class);
                $methods = $meta->getMethods();
                $data    = $meta->getData();
                $metas[] = $this->generator->render($meta->getTemplate(), compact('methods', 'data', '__env'));

                $open    = '<?php';
                $content = $this->views->make($viewFile, compact('open', 'metas'))->render();

                $this->files->put($path, $content);
            }
            catch (\Exception $e) {
                if ($this->ignoreExceptions) {
                    continue;
                }
                throw $e;
            }
        }

        return $path;
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

    /**
     * @return bool
     */
    public function isIgnoreExceptions(): bool
    {
        return $this->ignoreExceptions;
    }

    /**
     * Set the ignoreExceptions value
     *
     * @param bool $ignoreExceptions
     *
     * @return MetaRepository
     */
    public function setIgnoreExceptions($ignoreExceptions)
    {
        $this->ignoreExceptions = $ignoreExceptions;
        return $this;
    }


}
