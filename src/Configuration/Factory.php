<?php
namespace Laradic\Idea\Configuration;

use Laradic\Filesystem\Filesystem;
use Laradic\Idea\Configuration\Mappings\Component;
use Laradic\Idea\Configuration\Mappings\Project;
use Sabre\Xml\Reader;
use Sabre\Xml\Writer;
use Symfony\Component\Finder\SplFileInfo;

class Factory
{
    protected $componentMappings = [
        'VcsDirectoryMappings' => Mappings\VcsDirectoryMappings\VcsDirectoryMappings::class,
        'PhpUnit'              => Mappings\PhpUnit\PhpUnit::class,
    ];

    protected $globalMappings = [
        'project'   => Project::class,
        'component' => Component::class,
    ];

    protected $mappings = [
        'vcs' => [
            'mapping' => Mappings\VcsDirectoryMappings\Mapping::class,
        ],
    ];

    protected $components = [];

    protected $files = [];

    public function build()
    {

        $fs    = Filesystem::create();
        $files = \Symfony\Component\Finder\Finder::create()->in(base_path('.idea'))->name('*.xml')->files();

        foreach ( $files as $filePath ) {
            $this->files[ path_get_filename_without_extension($filePath) ] = $this->parseFile($filePath);
        }
    }

    public function parseFile($file)
    {
        if ( $file instanceof SplFileInfo ) {
            $xml = $file->getContents();
        } elseif ( $file instanceof \SplFileInfo ) {
            $xml = file_get($file->getPathname());
        } else {
            $xml = file_get($file);
        }
        $fileName = path_get_filename_without_extension($file);
        $s        = new Service();

        if ( array_key_exists($fileName, $this->mappings) ) {
            foreach ( $this->mappings[ $fileName ] as $elementName => $className ) {
                $this->map($s, str_ensure_left($elementName, '{}'), $className);
            }
        }


        return $s->parse($xml);
    }


    protected function map(Service $s, $elementName, $className)
    {
        list($namespace) = Service::parseClarkNotation($elementName);

        $s->elementMap[ $elementName ] = function (Reader $reader) use ($className, $namespace) {
            return $this->deserializeValueObject($reader, $className, $namespace);
        };
        $s->classMap[ $className ]     = function (Writer $writer, $valueObject) use ($namespace) {
            return $this->serializeValueObject($writer, $valueObject, $namespace);
        };
        $s->setValueObjectMap($className, $elementName);
    }

    public function serializeValueObject(Writer $writer, $valueObject, $namespace)
    {
        foreach ( get_object_vars($valueObject) as $key => $val ) {
            if ( is_array($val) ) {
                // If $val is an array, it has a special meaning. We need to
                // generate one child element for each item in $val
                foreach ( $val as $child ) {
                    $writer->writeElement('{' . $namespace . '}' . $key, $child);
                }
            } elseif ( $val !== null ) {
                $writer->writeElement('{' . $namespace . '}' . $key, $val);
            }
        }
    }

    protected function deserializeValueObject(Reader $reader, $className, $namespace)
    {
        $attributes = $reader->parseAttributes();

        // if component, check if its registered by its name. if so, give the specialised class
        if ( $className === Component::class ) {
            if ( array_key_exists($attributes[ 'name' ], $this->componentMappings) ) {
                $className = $this->componentMappings[ $attributes[ 'name' ] ];
            }
        }

        $valueObject              = new $className();
        $valueObject->_attributes = $attributes;

        // assign attributes to properties if exist
        foreach ( $valueObject->_attributes as $key => $val ) {
            if ( property_exists($valueObject, $key) ) {
                $valueObject->{$key} = $val;
            }
        }
        if ( $reader->isEmptyElement ) {
            $reader->next();
            return $valueObject;
        }

        $defaultProperties = get_class_vars($className);

        $reader->read();
        do {

            if ( $reader->nodeType === Reader::ELEMENT && $reader->namespaceURI == $namespace ) {

                if ( property_exists($valueObject, $reader->localName) ) {

                    if ( is_array($defaultProperties[ $reader->localName ]) ) {
                        $valueObject->{$reader->localName}[] = $reader->parseCurrentElement()[ 'value' ];
                    } else {
                        $valueObject->{$reader->localName} = $reader->parseCurrentElement()[ 'value' ];
                    }
                } else {
                    // Ignore property
                    $reader->next();
                }
            } else {
                $reader->read();
            }
        }
        while ( $reader->nodeType !== Reader::END_ELEMENT );

        $reader->read();
        return $valueObject;
    }

}