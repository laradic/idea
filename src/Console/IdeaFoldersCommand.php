<?php


namespace Laradic\Idea\Console;


use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Laradic\Idea\MetaGenerator;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Laradic\Idea\Command\ResolveSourceFolders;

class IdeaFoldersCommand extends Command
{
    use DispatchesJobs;

    protected $signature = 'idea:folders {patterns?*} {--php} {--vcs} {--no-tests}';

    public function handle()
    {
        $patterns = $this->argument('patterns');
        if ($patterns === null) {
            $patterns = config('laradic.idea.folders');
        }

        $folders = collect($this->dispatchNow(new ResolveSourceFolders(Arr::wrap($patterns))));
        if($this->option('no-tests')){
            $folders = $folders->filter(function($folder){
                return $folder['isTestSource'] === 'false';
            });
        }
        $this->addToProjectConfig($folders);
        if($this->option('vcs')) {
            $this->addToVCSConfig($folders);
        }
        if($this->option('php')) {
            $this->removeFromPhpConfig($folders);
        }
    }

    protected function addToProjectConfig(Collection $folders)
    {
        $imlXml  = $folders->map(function ($folder) {
            return "<sourceFolder url=\"{$folder['url']}\" isTestSource=\"{$folder['isTestSource']}\" packagePrefix=\"{$folder['packagePrefix']}\" />";
        })->implode("\n");
        $this->line($imlXml);
    }

    protected function removeFromPhpConfig(Collection $folders)
    {
        $packages = $folders->mapWithKeys(function ($value) {
            return [ $value[ 'package' ][ 'composer' ][ 'name' ] => $value[ 'package' ][ 'composer' ] ];
        });
        $file     = base_path('.idea/php.xml');
        if (file_exists($file)) {
            $xml  = file_get_contents($file);
            $exps = $packages->keys()->map(function ($val) {
                return '/.*' . preg_quote($val, '/') . '.*/';
            })->toArray();
            foreach ($exps as $exp) {
                $before = strlen($xml);
                $xml = preg_replace($exp, '', $xml);
                $after = strlen($xml);
                if($before !== $after){
                    $this->line("Removed line with {$exp}");
                }
            }
            file_put_contents($file, $xml);
        }
    }
    protected function addToVCSConfig(Collection $folders)
    {
        $imlXml  = $folders->map(function ($folder) {
            return "<sourceFolder url='{$folder['url']}' isTestSource='{$folder['isTestSource']}' packagePrefix='{$folder['packagePrefix']}' />";
        })->implode("\n");
    }
}