<?php


namespace Laradic\Idea\Metas;


use Laradic\Idea\Command\FindAllViews;
use Laradic\Support\Concerns\DispatchesJobs;

class ViewMeta implements MetaInterface
{
    use DispatchesJobs;

    protected $lines = [];
    /**
     * @var \Laradic\Idea\Metas\MetaOptions
     */
    protected $options;

    public function __construct()
    {
    }

    public function name()
    {
        return 'views';
    }

    public function generate(MetaOptions $options)
    {
        $this->options = $options;
        $views         = $this->dispatchNow(new FindAllViews($options->get('exclude_namespaces', [])));
        $arguments     = implode("', '", $views);
        $this->line("registerArgumentsSet('views', '{$arguments}');");
        $this->line("
    expectedArguments(\\view(), 0, argumentsSet(\"views\"));
    expectedArguments(\Illuminate\Contracts\View\Factory::make(), 0, argumentsSet(\"views\"));
    expectedArguments(\Illuminate\Contracts\View\Factory::renderEach(), 0, argumentsSet(\"views\"));
    expectedArguments(\Illuminate\Contracts\View\View::make(), 0, argumentsSet(\"views\"));
    
    expectedArguments(\Illuminate\View\Factory::make(), 0, argumentsSet(\"views\"));
    expectedArguments(\Illuminate\View\Factory::renderEach(), 0, argumentsSet(\"views\"));
    expectedArguments(\Illuminate\View\View::make(), 0, argumentsSet(\"views\"));
        ");
        return implode("\n", $this->lines);
    }

    protected function line($content)
    {
        $this->lines[] = $content;
    }
}