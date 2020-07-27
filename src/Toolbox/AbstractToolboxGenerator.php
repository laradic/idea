<?php

namespace Laradic\Idea\Toolbox;

abstract class AbstractToolboxGenerator implements ToolboxGenerator
{
    public $directory; // = 'laravel/views'

    public $path;

    /** @var \Laradic\Idea\Toolbox\Metadata */
    protected $md;

    public function generate($path)
    {
        $this->path = $path;
        dispatch_now($this);
    }

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