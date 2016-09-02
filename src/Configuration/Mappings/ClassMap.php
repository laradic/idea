<?php
/**
 * Part of the Radic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Idea\Configuration\Mappings;

use Illuminate\Contracts\Support\Arrayable;

abstract class ClassMap implements Arrayable
{
    public $_attributes = [];


    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_attributes;
    }


}