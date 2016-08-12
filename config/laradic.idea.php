<?php
/*
 * Check the documentation for a complete overview rundown!
 */

return [
    'disable' => [], //['metadata', 'vcs', '...']

    // PHPStorm Advanced Metadata generation (.phpstorm.meta.php)
    'metadata'       => [
        'enabled' => true,
        // relative to base_path
        'output' => '.phpstorm.meta.php',
        'view'   => 'laradic-idea::metadata.meta',
        'metas'  => [
            'bindings'     => 'Laradic\\Idea\\Metadata\\Metas\\BindingsMeta',
            'config'       => 'Laradic\\Idea\\Metadata\\Metas\\ConfigMeta',
            'routes'       => 'Laradic\\Idea\\Metadata\\Metas\\RoutesMeta',
            'translations' => 'Laradic\\Idea\\Metadata\\Metas\\TransMeta',
        ],
    ],

    // Version Control Directories
    'vcs'            => [
        'enabled' => true,
        'scan' => [ 'app', 'workbench/**' ],
    ],

    // Source/Test Folders with namespace configuration
    'folders'        => [
        'scan' => [ 'composer.json', 'workbench/*/*/composer.json' ],
    ],

    // Laravel Plugin view namespaces
    'laravel-plugin' => [
        'scan' => [ 'workbench/*/*' ],
        'for'  => 'resources/views',
        // sets workbench/laradic/admin-theme/resources/views as "laradic-admin-theme", makes autocompletion show stuff like "laradic-admin-theme::layouts.default"
        // available 'variables': vendor, package, composer, git
        'set'  => '{vendor}-{package}',
        // 'set' => '{vendor}-{package}-{composer.extra.branch-alias.dev-master}-{git.branch}-{git.ref}',
        // or custom callback handler
        //'set' => function($path, $composer, array $vars){
        // path like workbench/laradic/admin-theme/resources/views
        // composer is a instance of Laradic/Idea/Composer. works a bit like Illuminate\Config\Repository but with Illuminate\Support\Collection extras in it
        // so: $composer['autoload.psr4'] or $composer->has('autoload.psr4') or $composer->get('require.php', '>=5.6.0') etc
        // $vars is the
        //}
    ],

    // PHP Settings (composer, phing, php interpeters, etc)
    'php-settings'   => [
        'run'   => [ 'composer', 'phing', 'interperter' ],
        'phars' => [
            // will look in:
            // /usr/local/bin
            // {projectRootPath}
            // {projectRootPath}/vendor/bin
            // {projectRootPath}/bin
            // {projectRootPath}/build
            'paths' => [ '/usr/local/bin', './', './vendor/bin', './bin', './build' ],
            // composer, composer.phar, phing, phing.phar, etc
            'for'   => [ '$name$', '$name$.phar' ],
        ],
        'interpeter' => [
            
        ]
    ],
];