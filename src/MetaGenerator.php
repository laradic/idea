<?php


namespace Laradic\Idea;


use Laradic\Idea\Metas\MetaOptions;
use Laradic\Idea\Metas\MetaInterface;
use Illuminate\Contracts\View\Factory;

class MetaGenerator
{
    protected $metas = [];

    /** @var string */
    protected $view;

    /** @var \Illuminate\Contracts\View\Factory */
    protected $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function generate($path)
    {
        if (path_is_relative($path)) {
            $path = base_path($path);
        }
        foreach ($this->metas as $class => $options) {
            if (is_int($class) && is_string($options)) {
                $class   = $options;
                $options = [];
            }
            $options = new MetaOptions($options);
            $meta = app()->make($class);
            if ( ! $meta instanceof MetaInterface) {
                throw new \RuntimeException('not implemented meta');
            };
            $rendered = $this->render([ 'meta' => $meta->generate($options) ]);
            $filePath = path_join($path, $meta->name() . '.meta.php');
            file_put_contents($filePath, $rendered);
        }
    }

    protected function render(array $data = [])
    {
        $defaults = [
            'open' => '<?php' . PHP_EOL,
        ];
        $view     = $this->factory->make($this->view, array_replace_recursive($defaults, $data));
        $rendered = $view->render();
        return $rendered;
    }

    /**
     * @param string $view
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * @param array $metas
     * @return $this
     */
    public function setMetas($metas)
    {
        $this->metas = $metas;
        return $this;
    }
}