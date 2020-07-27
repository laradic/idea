<?php /** @noinspection SlowArrayOperationsInLoopInspection */

namespace Laradic\Idea\Toolbox;

use PhpParser\Node;
use ReflectionClass;
use Illuminate\Support\Str;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use Illuminate\Support\Collection;
use PhpParser\NodeVisitorAbstract;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Config\Repository;


class ConfigGenerator extends AbstractToolboxGenerator
{
    /** @var array */
    protected $excludes;

    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    /**
     * @return \ReflectionClass[]
     */
    protected function getServiceProviderClasses()
    {
        return collect(get_declared_classes())->filter(function ($class) {
            return in_array(ServiceProvider::class, class_parents($class));
        })->map(function ($class) {
            return new ReflectionClass($class);
        })->toArray();
    }

    public function handle()
    {
        // gather all config files
        /** @var array $configFiles = [ $i => ['namespace' => '', 'path' => ''] ] */
        $configFiles = [];
        $configFiles = array_merge($configFiles, $this->extractConfigFilesFromPath(config_path()));

        foreach ($this->getServiceProviderClasses() as $class) {
            $configFiles = array_merge($configFiles, $this->extractConfigFilesFromServiceProvider($class));
        }

        // transform config files to provider items
        $providerItems = collect();
        foreach ($configFiles as $configFile) {
            $providerItems[] = $this->transformConfigFileToProviderItems($configFile);
        }
        $providerItems = $providerItems->flatten(1)->toArray();

        // create the metadata json file
        $md = $this->createMetadata($providerItems);
        $md->save();
    }

    protected function transformConfigFileToProviderItems($config)
    {
        $relativePath = path_make_relative($config[ 'path' ], base_path());
        $data         = require $config[ 'path' ];
        $resolved     = array_merge([
            $config[ 'namespace' ] => [ 'key' => $config[ 'namespace' ], 'type' => 'file', 'value' => $data ],
        ], $this->resolveConfig($data, $config[ 'namespace' ]));
        return $this->resolvedToItems($resolved, $relativePath);
    }

    protected function extractConfigFilesFromPath($directory)
    {
        $configs = [];
        foreach (rglob(path_join($directory, '**')) as $path) {
            $namespace = path_get_filename_without_extension($path);
            $configs[] = compact('path', 'namespace');
        }
        return $configs;
    }

    protected function createMetadata($providerItems)
    {

        $signature = function ($method) {
            return [
                'class'  => Repository::class,
                'method' => $method,
                'type'   => 'type',
            ];
        };
        $md        = $this->metadata();
        $md->merge([
            'registrar' => [
                [
                    'provider'   => 'laravel_config',
                    'language'   => 'php',
                    'signatures' => [
                        $signature('get'),
                        $signature('has'),
                        $signature('set'),
                        $signature('forget'),
                        [ 'function' => 'config', 'type' => 'type' ],
                    ],
                    'signature'  => [
                        Repository::class . ':get',
                        Repository::class . ':has',
                        Repository::class . ':set',
                        Repository::class . ':forget',
                        'config',
                        'config:1',
                    ],
                ],
            ],
            'providers' => [
                [
                    'name'  => 'laravel_config',
                    'items' => $providerItems,
                ],
            ],
        ]);
        return $md;
    }

    protected function extractConfigFilesFromServiceProvider(ReflectionClass $class)
    {
        $configs      = [];
        $filePath     = $class->getFileName();
        $dirPath      = dirname($filePath);
        $composerPath = null;
        while ($composerPath === null) {
            if (file_exists($currentPath = path_join($dirPath, 'composer.json'))) {
                $composerPath = $currentPath;
            } else {
                $dirPath = dirname($dirPath);
                if ($dirPath === base_path()) {
                    $composerPath = false;
                }
            }
        }
        if ($composerPath !== false) {
            $paths = array_unique(array_merge(
                rglob(path_join(dirname($composerPath), '**', 'config.php')),
                rglob(path_join(dirname($composerPath), '**', 'config', '*.php'))
            ));
            if (count($paths) > 0) {
                $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
                $parsed = $parser->parse(file_get_contents($filePath));

                $traverser = new NodeTraverser();
                $traverser->addVisitor($visitor = new class extends NodeVisitorAbstract {
                    public $configs = [];

                    public function enterNode(Node $node)
                    {
                        if (isset($node->name, $node->name->name) && $node instanceof Node\Expr\MethodCall && $node->name->name === 'mergeConfigFrom') {
                            if (
                                $node->args[ 0 ] instanceof Node\Arg
                                && $node->args[ 0 ]->value instanceof Node\Expr\BinaryOp\Concat
                                && $node->args[ 0 ]->value->right instanceof Node\Scalar\String_
                                && $node->args[ 1 ] instanceof Node\Arg
                                && $node->args[ 1 ]->value instanceof Node\Scalar\String_
                            ) {

                                $path            = $node->args[ 0 ]->value->right->value;
                                $namespace       = $node->args[ 1 ]->value->value;
                                $this->configs[] = compact('path', 'namespace');
                            }
                        }
                    }
                });

                $traverser->traverse($parsed);
                foreach ($visitor->configs as $config) {
                    $config[ 'path' ] = head(rglob(path_join(dirname($composerPath), '**', $config[ 'path' ])));
                    if ( ! file_exists($config[ 'path' ])) {
                        continue;
                    }
                    $configs[] = $config;
                }
            }
        }
        return array_filter($configs);
    }

    protected function resolvedToItems($resolves, $relativeConfigPath)
    {
        $items = [];
        foreach ($resolves as $resolved) {
            $isFile = $resolved[ 'type' ] === 'file';
            if ($isFile) {
                $resolved[ 'type' ] = 'array';
            }
            $item = [
                'lookup_string' => $resolved[ 'key' ],
                'type_text'     => $resolved[ 'type' ],
                'icon'          => 'com.jetbrains.php.PhpIcons.',
                'target'        => 'file:///' . $relativeConfigPath,
            ];
            if ($isFile) {
                $item[ 'icon' ]      .= 'PHP_FILE';
                $item[ 'tail_text' ] = ' file';
            } elseif ($resolved[ 'type' ] === 'array') {
                $item[ 'icon' ]      .= 'FUNCTION';
                $item[ 'tail_text' ] = ' => [...]';
            } else {
                $item[ 'icon' ] .= 'VARIABLE';
                if ($resolved[ 'type' ] === 'bool') {
                    $item[ 'tail_text' ] = ' => ' . ($resolved[ 'value' ] === true ? 'true' : 'false');
                } elseif ($resolved[ 'type' ] === 'int') {
                    $item[ 'tail_text' ] = ' => ' . $resolved[ 'value' ];
                } elseif ($resolved[ 'type' ] === 'string') {
                    $item[ 'tail_text' ] = ' => \'' . Str::truncate($resolved[ 'value' ], 40, '..') . '\'';
                }
            }

            $items[ $item[ 'lookup_string' ] ] = $item;
        }
        return $items;
    }

    protected function resolveConfig($config, $prefix = '')
    {
        $resolved = [];
        foreach ($config as $key => $value) {
            if (is_int($key)) {
                continue;
            }
            $type = $this->getType($value);
            if (Str::endsWith($prefix, '::')) {
                $key = $prefix . $key;
            } else {
                $key = $prefix . '.' . $key;
            }

            $resolved[ $key ] = [
                'key'   => $key,
                'type'  => $type,
                'value' => $value,
            ];

            if (is_array($value)) {
                $resolved = array_merge($resolved, $this->resolveConfig($value, $key));
            }
        }
        return $resolved;
    }

    protected function getType($value)
    {
        if (is_array($value)) {
            return 'array';
        }
        if (is_string($value)) {
            return 'string';
        }
        if (is_bool($value)) {
            return 'bool';
        }
        if (is_int($value)) {
            return 'int';
        }
        return 'mixed';
    }

}
