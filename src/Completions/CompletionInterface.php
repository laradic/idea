<?php


namespace Laradic\Idea\Completions;


use Laradic\Generators\DocBlock\DocBlockGenerator;

interface CompletionInterface
{
    public function generate(DocBlockGenerator $generator,$next);

}