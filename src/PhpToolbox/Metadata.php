<?php

namespace Laradic\Idea\PhpToolbox;

use Laradic\Support\Dot;

class Metadata extends Dot
{
    protected $path;

    public static function create($path, $data = [])
    {
        return static::wrap($data)->setPath($path);
    }

    public static function loadFrom($path)
    {
        return static::create($path, json_decode(file_get_contents($path), true));
    }

    public function saveTo($path)
    {
        file_put_contents($path, json_encode($this->items, JSON_UNESCAPED_SLASHES));
        return $this;
    }

    public function save()
    {
        return $this->saveTo($this->path);
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