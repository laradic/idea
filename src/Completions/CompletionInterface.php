<?php


namespace Laradic\Idea\Completions;


use Laradic\Generators\DocBlock\DocBlockGenerator;

interface CompletionInterface
{
    /**
     * @param \Laradic\Generators\DocBlock\DocBlockGenerator $generator
     * @param                                                $next
     * @return mixed
     */
    public function generate(DocBlockGenerator $generator,$next);

}