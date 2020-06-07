<?php


namespace Laradic\Idea\Metas;


interface MetaInterface
{
    public function name();

    public function generate(MetaOptions $options);

}