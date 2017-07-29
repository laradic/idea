<?php
/**
 * Part of the Laradic PHP Packages.
 *
 * Copyright (c) 2017. Robin Radic.
 *
 * The license can be found in the package and online at https://laradic.mit-license.org.
 *
 * @copyright Copyright 2017 (c) Robin Radic
 * @license https://laradic.mit-license.org The MIT License
 */
namespace Laradic\Idea\Metadata\Metas;

/**
 * This is the ConfigMeta.
 *
 * @package        Laradic
 * @author         Laradic Dev Team
 * @copyright      Copyright (c) 2015, Laradic
 * @license        https://tldrlegal.com/license/mit-license MIT License
 */
class
BindingsMeta extends BaseMeta implements CustomMetaInterface
{
    use CustomMetaTrait;

    protected $methods = [
        'new \\Illuminate\\Contracts\\Container\\Container',
        '\\Illuminate\\Contracts\\Container\\Container::make(\'\')',
        '\\App::make(\'\')',
        '\\app(\'\')',
    ];


    public function getData()
    {
        spl_autoload_register(function ($class) {

            //throw new \Exception("Class '$class' not found.");
        });

        $bindings             = static::getCustom();
        $bindings[ 'config' ] = get_class($this->app->make('config'));
        $abstracts            = $this->getBindingsAbstracts();

        foreach ($abstracts as $abstract) {
            try {
                $concrete = $this->app->make($abstract);
                if (is_object($concrete)) {
                    $bindings[ $abstract ] = get_class($concrete);
                }
            } catch (\Throwable $e) {
                //throw $e;
            }
        }

        return $bindings;
    }

    /**
     * Get a list of abstracts from the Laravel Application.
     *
     * @return array
     */
    protected function getBindingsAbstracts()
    {
        $bindings  = app()->getBindings();
        $abstracts = array_keys($bindings);
        $filtered  = array_except($abstracts, [ 'Illuminate\Database\Seeder' ]);
        $filtered  = array_except($abstracts, static::$excludeClasses);

        return $filtered;
    }

    public static $excludeClasses = [];
}
