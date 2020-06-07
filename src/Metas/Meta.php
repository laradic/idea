<?php


namespace Laradic\Idea\Metas;


abstract class Meta implements MetaInterface
{
    abstract public static function getName();

    public function name()
    {
        return static::getName();
    }
}