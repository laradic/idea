<?php
/**
 * Part of the Laradic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */


namespace Laradic\Idea\Console;

use Illuminate\Console\Command;
use Laradic\Idea\Metadata\MetaRepository;
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
                                     {--exclude : Excludes meta}';

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
        if ( $this->option('list') ) {
            return $this->listing(array_values($metas->all()));
        }
        app()->singleton('seeder', Seeder::class);

        $metas->create();
        $this->info('Created [.phpstorm.meta.php]');

    }
}
