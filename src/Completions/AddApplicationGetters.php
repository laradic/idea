<?php


namespace Laradic\Idea\Completions;


use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Laradic\Idea\Command\GetBindings;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Foundation\Application;
use Laradic\Generators\DocBlock\DocBlockGenerator;

class AddApplicationGetters implements CompletionInterface
{
    use DispatchesJobs;

    public function generate(DocBlockGenerator $generator, $next)
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

        $class = $generator->class(Application::class);
        foreach ($filtered as $name => $type) {
            $class->ensureProperty($name, $type);
        }

        $next($generator);
    }
}