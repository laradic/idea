<?php


namespace Laradic\Idea\Command;


class GetBindings
{

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->registerClassAutoloadExceptions();

        $bindings = [];
        foreach ($this->getAbstracts() as $abstract) {
            // Validator and seeder cause problems
            if (in_array($abstract, [ 'validator', 'seeder' ])) {
                continue;
            }

            try {
                $concrete        = app()->make($abstract);
                $reflectionClass = new \ReflectionClass($concrete);
                if (is_object($concrete) && ! $reflectionClass->isAnonymous()) {
                    $bindings[ $abstract ] = get_class($concrete);
                }
            }
            catch (\Throwable $e) {
//                echo("Cannot make '$abstract': " . $e->getMessage());
            }
        }

        $bindings = collect($bindings);

        event('laradic.idea.bindings', $bindings);

        return $bindings;
    }

    /**
     * Get a list of abstracts from the Laravel Application.
     *
     * @return array
     */
    protected function getAbstracts()
    {
        $abstracts = app()->getBindings();
        // Return the abstract names only
        $keys = array_keys($abstracts);

        sort($keys);

        return $keys;
    }

    /**
     * Register an autoloader the throws exceptions when a class is not found.
     */
    protected function registerClassAutoloadExceptions()
    {
        spl_autoload_register(function ($class) {
            throw new \ReflectionException("Class '$class' not found.");
        });
    }
}