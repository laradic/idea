<?php


namespace Laradic\Idea\Command;


use Laradic\Support\FS;
use Laradic\Support\Dot;
use Laradic\Support\Wrap;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class ResolveSourceFolders
{
    protected $patterns = [];

    public function __construct($patterns = [])
    {
        $this->patterns = Arr::wrap($patterns);
    }


    /**
     * @return \Illuminate\Support\Collection|array = [ $i => ['composer' => [], 'composerPath' => '', 'packagePath' => ''] ]
     */
    protected function getComposerFiles()
    {
        $composerFiles = [];
        foreach ($this->patterns as $pattern) {
            $composerFiles = array_merge($composerFiles, rglob($pattern));
        }
        return collect($composerFiles)->map(function ($path) {
            return [
                'composer'             => json_decode(file_get_contents($path), true),
                'composerPath'         => $path,
                'packagePath'          => dirname($path),
                'relativeComposerPath' => Str::removeLeft($path, base_path('/')),
                'relativePackagePath'  => Str::removeLeft(dirname($path), base_path('/')),
                'packageJsonPath'      => path_join(dirname($path), 'package.json'),
                'viewsPath'            => path_join(dirname($path), 'resources/views'),
            ];
        });
    }

    /** @var array = [ $i=> [ 'url' => '', 'isTestSource' => 'false', 'packagePrefix' => '' ] ] */
    protected $folders = [

    ];

    public function handle()
    {
        foreach ($this->getComposerFiles() as $package) {
            $c = Wrap::dot($package[ 'composer' ]);
            foreach ($c->get('autoload.psr-4', []) as $prefix => $directory) {
                $this->addFolder(path_join($package[ 'packagePath' ], $directory), $prefix, false, $package);
            }
            foreach ($c->get('autoload-dev.psr-4', []) as $prefix => $directory) {
                $this->addFolder(path_join($package[ 'packagePath' ], $directory), $prefix, true, $package);
            }


            if (FS::exists($package[ 'packageJsonPath' ])) {
                $pkg = Dot::wrap(json_decode(file_get_contents($package[ 'packageJsonPath' ]), true));
                $pkgName = str_replace('/','\\',$pkg[ 'name' ]);
                if ($pkg->has('pyro')) {
                    $this->addFolder(path_join($package[ 'packagePath' ], $pkg[ 'pyro.srcPath' ]), $pkgName, false, $package);
                }

                if (FS::isDirectory($package[ 'viewsPath' ])) {
                    $this->addFolder($package[ 'viewsPath' ], $pkgName . '.views', false, $package);
                }
            }
        }

        return $this->folders;
    }

    protected function addFolder(string $path, string $prefix, bool $test = false, ?array $package = null)
    {
        if (path_is_absolute($path)) {
            $path = Str::removeLeft($path, base_path('/'));
        }
        $prefix          = rtrim($prefix, '\\');
        $path            = 'file://$MODULE_DIR$/' . $path;
        $this->folders[] = [
            'url'           => $path,
            'packagePrefix' => Str::startsWith($prefix, '@') ? $prefix : Str::ensureRight($prefix,'\\'),
            'isTestSource'  => $test ? 'true' : 'false',
            'package'       => $package,
        ];
    }
}