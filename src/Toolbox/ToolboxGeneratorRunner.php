<?php

namespace Laradic\Idea\Toolbox;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class ToolboxGeneratorRunner extends Collection
{
    /** @var array|string[] generator fqcn's */
    protected $items;

    /** @var array|Closure[] */
    protected $after = [];

    /** @var array|Closure[] */
    protected $before = [];

    public function run()
    {
        foreach ($this->items as $class => $options) {
            $instance = App::build($class);
            if ($instance instanceof ToolboxGenerator === false) {
                throw new \RuntimeException("Class [$class] should implement " . ToolboxGenerator::class);
            }
            foreach ($options as $k => $v) {
                $instance->{$k} = $v;
            }
            $path           = path_join(config('laradic.idea.toolbox.path'), $options[ 'directory' ], '.ide-toolbox.metadata.json');
            $instance->path = $path;
            $this->callCallbacks($this->before, compact('instance', 'path'));
            $instance->generate($path);
            $this->callCallbacks($this->after, compact('instance', 'path'));
        }
    }


    protected function callCallbacks(array $callbacks, array $params = [])
    {
        foreach ($callbacks as $callback) {
            /** @noinspection DisconnectedForeachInstructionInspection */
            App::call($callback, $params);
        }
    }

    public function before(Closure $callback)
    {
        $this->before[] = $callback;
    }

    public function after(Closure $callback)
    {
        $this->after[] = $callback;
    }

}