<?php
/*
 * Check the documentation for a complete overview rundown!
 */

use Laradic\Idea\Metas;
use Laradic\Idea\Completions;

return [
    'completion' => [
        'view'        => 'laradic/idea::completion',
        'path'        => '.idea.completion.php',
        'completions' => [
            Completions\AddApplicationGetters::class,
        ],
    ],
    'meta'       => [
        'integrate_ide_helper' => true,
        'view'                 => 'laradic/idea::meta',
        'path'                 => '.phpstorm.meta.php',
        'metas'                => [
            Metas\ConfigMeta::class => [
                'skip_lists' => true
            ],
        ],
    ],
];