<?php

namespace Laradic\Idea\Completions;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laradic\Generators\Completion\CompletionInterface;
use Laradic\Generators\DocBlock\Definition\Definition;
use Laradic\Generators\DocBlock\DocBlockGenerator;
use Laradic\Idea\Command\GetBindings;
use ReflectionClass;

class AddApplicationGetters implements CompletionInterface
{
    use DispatchesJobs;

    protected array $config = [];

    /**
     * @param array $config
     */
    public function __construct(?array $config = [])
    {
        $this->config = array_replace_recursive([
            'addToAppProperties' => true,
            'propertySyntax'     => 'deep-assoc', // 'psalm'|'deep-assoc'
        ], $config);
    }

    public function generate(DocBlockGenerator $generator)
    {
        /** @var Collection $bindings */
        $bindings = $this->dispatchNow(new GetBindings());

        $filtered = $bindings
            ->filter(function ($value, $key) {
                if (Str::containsAny($key, [ '.' ])) {
                    return false;
                }
                if (strtolower($key) !== $key) {
                    return false;
                }
                return true;
            })
            ->transform(function ($value, $key) {
                return Str::ensureLeft($value, '\\');
            });

        $classes[]              = $generator->class(\Illuminate\Contracts\Foundation\Application::class);
        $classes[]              = $generator->class(\Illuminate\Foundation\Application::class);
        $arrayKVTypeDefinitions = [];
        foreach ($filtered as $name => $type) {
            $arrayKVTypeDefinitions[] = $name . ': ' . Definition::resolveType($type);
            foreach ($classes as $class) {
                $class->ensureProperty($name, $type);
            }
        }

        if ($this->config[ 'addToAppProperties' ]) {
            $ref     = new ReflectionClass(ServiceProvider::class);
            $replaced=$content = File::get($ref->getFileName());
            //https://regex101.com/r/yLlaSv/1

            if ($this->config[ 'propertySyntax' ] === 'psalm') {
                $arrayString = implode(",", $arrayKVTypeDefinitions);
                $replaced    = preg_replace("/\@var \\\Illuminate\\\Contracts\\\Foundation\\\Application.*?\n/", "@var \Illuminate\Contracts\Foundation\Application|array{{$arrayString}}\n", $content);
            } else {
                $arrayDefinition = $bindings
                    ->filter(function ($value, $key) {
                        if (strtolower($key) !== $key) {
                            return false;
                        }
                        return true;
                    })
                    ->transform(function ($value, $key) {
                        return Str::ensureLeft($value, '\\');
                    })
                    ->transform(function ($value, $key) {
                        if (class_exists($value)) {
                            $value = 'new ' . $value;
                        }
                        return "'$key' => $value";
                    })->implode(', ');
                $replaced= preg_replace("/\@var \\\Illuminate\\\Contracts\\\Foundation\\\Application.*?\n/", "@var \Illuminate\Contracts\Foundation\Application|array = [{$arrayDefinition}]\n", $content);
            }
            $content = File::put($ref->getFileName(), $replaced);
        }
    }
}
