<?php


namespace Laradic\Idea\Command;


use Closure;
use Laradic\Support\Dot;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class ResolveSourceFolders
{
    protected static $callbacks = [];

    public static function extend(Closure $callback)
    {
        static::$callbacks[] = $callback;
    }

    protected $patterns = [];

    public function __construct($patterns = [])
    {
        $this->patterns = Arr::wrap($patterns);
    }


    /**
     * @return \Illuminate\Support\Collection|array = [ $i => [
     *     'composer' => new \Laradic\Support\Dot,
     *     'composerPath' => '',
     *     'packagePath' => '',
     *     'hasPackageJson' => true,
     *     'pkg' =>  new \Laradic\Support\Dot,
     * ] ]
     */
    protected function getMatches()
    {
        $composerFiles = [];
        foreach ($this->patterns as $pattern) {
            $composerFiles = array_merge($composerFiles, rglob($pattern));
        }
        return collect($composerFiles)->map(function ($path) {
            $composer        = Dot::make(json_decode(file_get_contents($path), true));
            $packageJsonPath = path_join(dirname($path), 'package.json');
            $hasPackageJson  = file_exists($packageJsonPath);
            /** @var \Laradic\Support\Dot $pkg */
            $pkg = null;
            if ($hasPackageJson) {
                $pkg = Dot::make(json_decode(file_get_contents($packageJsonPath), true));
            }
            return [
                'composer'             => $composer,
                'composerPath'         => $path,
                'packagePath'          => dirname($path),
                'relativeComposerPath' => Str::removeLeft($path, base_path('/')),
                'relativePackagePath'  => Str::removeLeft(dirname($path), base_path('/')),
                'packageJsonPath'      => $packageJsonPath,
                'hasPackageJson'       => $hasPackageJson,
                'pkg'                  => $pkg,
                'viewsPath'            => path_join(dirname($path), 'resources/views'),
            ];
        });
    }

    /** @var array = [ $i=> [ 'url' => '', 'isTestSource' => 'false', 'packagePrefix' => '' ] ] */
    protected $folders = [

    ];

    public function handle()
    {
        foreach ($this->getMatches() as $match) {

            foreach ($match[ 'composer' ]->get('autoload.psr-4', []) as $prefix => $directory) {
                $this->addFolder(path_join($match[ 'packagePath' ], $directory), $prefix, false, $match);
            }
            foreach ($match[ 'composer' ]->get('autoload-dev.psr-4', []) as $prefix => $directory) {
                $this->addFolder(path_join($match[ 'packagePath' ], $directory), $prefix, true, $match);
            }

            foreach (static::$callbacks as $callback) {
                $callback->call($this, $match);
            }
        }

        return $this->folders;
    }

    public function addFolder(string $path, string $prefix, bool $test = false, ?array $package = null)
    {
        if (path_is_absolute($path)) {
            $path = Str::removeLeft($path, base_path('/'));
        }
        $prefix          = rtrim($prefix, '\\');
        $path            = 'file://$MODULE_DIR$/' . $path;
        $this->folders[] = [
            'url'           => $path,
            'packagePrefix' => Str::startsWith($prefix, '@') ? $prefix : Str::ensureRight($prefix, '\\'),
            'isTestSource'  => $test ? 'true' : 'false',
            'package'       => $package,
        ];
    }
}