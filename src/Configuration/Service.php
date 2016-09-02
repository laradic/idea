<?php
namespace Laradic\Idea\Configuration;

class Service extends \Sabre\Xml\Service
{
    public function setValueObjectMap($className, $elementName)
    {
        $this->valueObjectMap[ $className ] = $elementName;
    }
}