<?php


namespace Laradic\Idea\Metas;


use Illuminate\Support\Collection;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ConfigMeta implements MetaInterface
{

    protected $lines = [];
    /** @var Repository */
    protected $config;
    /**
     * @var \Laradic\Idea\Metas\MetaOptions
     */
    protected $options;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function name()
    {
        return 'config';
    }

    public function generate(MetaOptions $options)
    {
        $this->options = $options;
        $keys      = $this->recurse($this->config->all());
        $arguments = implode("', '", $keys);
        $this->line("registerArgumentsSet('config_keys', '{$arguments}');");
        $this->line("
    expectedArguments(\config(), 0, argumentsSet(\"config_keys\"));
    expectedArguments(\config()->get(), 0, argumentsSet(\"config_keys\"));
    expectedArguments(\config()->set(), 0, argumentsSet(\"config_keys\"));
    expectedArguments(\config()->has(), 0, argumentsSet(\"config_keys\"));
        ");
        return implode("\n", $this->lines);
    }

    protected function line($content)
    {
        $this->lines[] = $content;
    }

    protected function recurse(array $items, string $prefix = '', array $result = [])
    {
        foreach ($items as $key => $value) {
            if (is_numeric($key) && $this->options['skip_lists']) {
                continue;
            }
            $result[] = $prefix . $key;
            if (is_array($value)) {
                $result = $this->recurse($value, $prefix . $key . '.', $result);
            }
        }
        return $result;
    }
}