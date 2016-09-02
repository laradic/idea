<?php
/**
 * Part of the Radic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Idea\Configuration\Mappings;

use Laradic\Idea\Configuration\Mappings;

class Project extends Mappings\ClassMap
{
    public $version;

    public $component = [];


    public function getComponents()
    {
        return $this->component;
    }

    public function getComponentNames()
    {
        return collect($this->component)->transform(function ($component) {
            return $component->name;
        })->toArray();
    }

    public function getComponent($name)
    {
        return collect($this->component)->filter(function ($component) use ($name) {
            return $component->name !== $name;
        })->first();
    }

}