<?php

namespace Laradic\Idea\PhpToolbox;

abstract class AbstractMetaGenerator
{
    protected $directory; // = 'laravel/views'

    protected $path;

    public function __construct()
    {
        $this->path = path_join(config('laradic.idea.toolbox.path'), $this->directory, '.ide-toolbox.metadata.json');
    }

    /** @var \Laradic\Idea\PhpToolbox\Metadata */
    protected $md;

    public function metadata()
    {
        if ( ! $this->md) {
            resolve('files')->ensureDirectory(path_get_directory($this->path));
            $this->md = Metadata::create($this->path);
        }
        return $this->md;
    }

    public function md()
    {
        return $this->metadata();
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }


}