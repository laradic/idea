<?php
/*
 * Check the documentation for a complete overview rundown!
 */

return [
    'completion'  => [
        'view'        => 'laradic/idea::completion',
        'path'        => '.idea.completion.php',
        'completions' => [
            Laradic\Idea\Completions\AddApplicationGetters::class,
        ],
    ],
    'completions' => [
        'application' => [
            Laradic\Idea\Completions\AddApplicationGetters::class,
        ],
    ],
    'meta'        => [
        'integrate_ide_helper' => true,
        'view'                 => 'laradic/idea::meta',
        'path'                 => '.phpstorm.meta.php',
        'metas'                => [
            Laradic\Idea\Metas\ConfigMeta::class => [
                'skip_lists' => true,
            ],
            Laradic\Idea\Metas\ViewMeta::class   => [
                'exclude_namespaces' => [ 'root', 'storage' ],
            ],
        ],
    ],
    'folders'     => [
        // globs
    ],
    'toolbox' => [
        'path' => base_path('php-toolbox')
    ]
];