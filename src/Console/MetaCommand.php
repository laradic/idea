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


namespace Laradic\Idea\Console;

use Illuminate\Console\Command;
use Laradic\Idea\Metadata\Metas\BindingsMeta;
use Laradic\Idea\Metadata\Seeder;

/**
 * This is the class MetaCommand.
 *
 * @package Laradic\Idea\Console
 * @author  Robin Radic
 */
class MetaCommand extends Command
{
    /** @var string */
    protected $signature = 'idea:meta
                                     {--list : Lists all meta}
                                     {--ignore-errors : Ignores meta related exceptions} 
                                     {--without= : Excludes 1 or more meta by name, comma seprated}
                                     {--exclude= : Excludes 1 or more classes, comma seprated}';

    /** @var string */
    protected $description = 'Generates a .phpstorm.meta.php file with the configured metas';

    /**
     * handle method
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var \Laradic\Idea\Metadata\MetaRepository $metas */
        $metas = app('laradic.idea.meta');
        $metas->setIgnoreExceptions(!!$this->option('ignore-errors'));
        if ($this->option('list')) {
            foreach ($metas->all() as $name => $class) {
                $this->line("- $name :: $class");
            }
            return;
        }

        app()->singleton('seeder', Seeder::class);

        if ($this->option('without')) {
            foreach (preg_split('/,/', $this->option('without')) as $name) {
                if (!$metas->has($name)) {
                    $this->warn("meta $name cannot be exluded ");
                    continue;
                }
                $metas->remove($name);
            }
        }

        if ($this->option('exclude')) {
            BindingsMeta::$excludeClasses = preg_split('/,/', $this->option('exclude'));
        }

        $this->info("Created [{$metas->create()}]");
    }
}
