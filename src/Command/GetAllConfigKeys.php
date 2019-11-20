<?php

namespace Laradic\Idea\Command;

use Illuminate\Contracts\Config\Repository;

class GetAllConfigKeys
{
    /** @var array|\Illuminate\Support\Collection */
    protected $options = [
        'skip_array_keys' => true,
        'skip_list_keys'  => true,
        'repository'      => null,
    ];

    /**
     * GetAllConfigKeys constructor.
     *
     * @param bool $skipArrayKeys
     * @param bool $skipListKeys
     */
    public function __construct(array $options = [])
    {
        $this->options = collect($this->options);
        $this->options = $this->options->merge($options);
    }

    public function handle(Repository $config)
    {
        $repository = $this->options->get('repository',  $config);
        $keys   = $this->recurse($repository->all());
        return $keys;
    }

    protected function recurse(array $items, string $prefix = '', array $result = [])
    {
        foreach ($items as $key => $value) {
            if (is_numeric($key) && $this->options[ 'skip_list_keys' ]) {
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