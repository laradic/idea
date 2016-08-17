<?php
/**
 * Part of the Laradic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */


namespace Laradic\Idea\Console;

use Laradic\Console\Command;
use Laradic\Idea\Metadata\MetaRepository;
use Laradic\Idea\Metadata\Seeder;

class MetaCommand extends Command
{
    protected $signature = 'laradic:idea:meta
                                     {--list : Lists all meta}
                                     {--exclude : Excludes meta}';

    protected $description = 'Generates a .phpstorm.meta.php file with the configured metas';

    public function handle()
    {
        /** @var MetaRepository $metas */
        $metas = app('laradic.idea.meta');
        if ( $this->option('list') ) {
            return $this->listing(array_values($metas->all()));
        }
        app()->singleton('seeder', Seeder::class);

        $metas->create();
        $this->info('Created [.phpstorm.meta.php]');

    }
}
