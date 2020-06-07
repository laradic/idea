<?php

namespace Laradic\Idea\PhpToolbox;

use Laradic\Support\Dot;
use Illuminate\Filesystem\Filesystem;

class Metadata extends Dot
{
    protected $path;

    public static function create($path, $data = [])
    {
        return static::wrap($data)->setPath($path);
    }

    public static function loadFrom($path)
    {
        return static::create($path, json_decode(file_get_contents($path), true));
    }

    public function saveTo($path)
    {
        with(new Filesystem())->ensureDirectory(dirname($path));
        $json = json_encode($this->items, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        file_put_contents($path, $json);
        return $this;
    }

    public function save()
    {
        return $this->saveTo($this->path);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param \Adbar\Dot|array|string $key = array (
     *                                     'registrar' =>
     *                                     array (
     *                                     0 =>
     *                                     array (
     *                                     'provider' => '',
     *                                     'language' => 'php',
     *                                     'parameters' =>
     *                                     array (
     *                                     ),
     *                                     'signatures' =>
     *                                     array (
     *                                     0 =>
     *                                     array (
     *                                     'function' => '',
     *                                     'type' => 'type',
     *                                     'class' => '',
     *                                     'field' => '',
     *                                     'array' => '',
     *                                     'index' => '',
     *                                     'method' => '',
     *                                     ),
     *                                     1 =>
     *                                     array (
     *                                     'type' => 'array_key',
     *                                     ),
     *                                     2 =>
     *                                     array (
     *                                     'type' => 'annotation_array',
     *                                     ),
     *                                     3 =>
     *                                     array (
     *                                     'type' => 'annotation',
     *                                     ),
     *                                     4 =>
     *                                     array (
     *                                     'type' => 'return',
     *                                     ),
     *                                     5 =>
     *                                     array (
     *                                     'type' => 'default',
     *                                     ),
     *                                     ),
     *                                     'signature' =>
     *                                     array (
     *                                     0 => '',
     *                                     ),
     *                                     ),
     *                                     1 =>
     *                                     array (
     *                                     'language' => 'twig',
     *                                     ),
     *                                     ),
     *                                     'providers' =>
     *                                     array (
     *                                     0 =>
     *                                     array (
     *                                     'name' => 'route_names',
     *                                     'lookup_strings' =>
     *                                     array (
     *                                     0 => 'crvs.module.departments::lick',
     *                                     ),
     *                                     ),
     *                                     1 =>
     *                                     array (
     *                                     'name' => '',
     *                                     'items' =>
     *                                     array (
     *                                     0 =>
     *                                     array (
     *                                     'lookup_string' => 'firstname',
     *                                     'type' => 'annotation',
     *                                     'icon' => 'com.jetbrains.php.PhpIcons.FINAL_MARK',
     *                                     'target' => 'Crvs\\ClientsModule\\Client\\ClientModel',
     *                                     'type_text' => 'string',
     *                                     'tail_text' => 'required|min:2|max:30',
     *                                     ),
     *                                     1 =>
     *                                     array (
     *                                     'type' => 'array_key',
     *                                     ),
     *                                     2 =>
     *                                     array (
     *                                     'type' => 'annotation',
     *                                     ),
     *                                     3 =>
     *                                     array (
     *                                     'type' => 'type',
     *                                     ),
     *                                     4 =>
     *                                     array (
     *                                     'type' => 'return',
     *                                     ),
     *                                     5 =>
     *                                     array (
     *                                     'type' => 'default',
     *                                     ),
     *                                     ),
     *                                     'lookup_strings' =>
     *                                     array (
     *                                     0 => '',
     *                                     ),
     *                                     'source' =>
     *                                     array (
     *                                     0 =>
     *                                     array (
     *                                     'parameter' => '',
     *                                     'contributor' => 'return_array',
     *                                     ),
     *                                     1 =>
     *                                     array (
     *                                     'contributor' => 'sub_classes',
     *                                     ),
     *                                     2 =>
     *                                     array (
     *                                     'contributor' => 'return',
     *                                     ),
     *                                     ),
     *                                     'defaults' =>
     *                                     array (
     *                                     0 =>
     *                                     array (
     *                                     'lookup_string' => 'firstname',
     *                                     'type' => 'annotation',
     *                                     'icon' => 'com.jetbrains.php.PhpIcons.FINAL_MARK',
     *                                     'target' => 'Crvs\\ClientsModule\\Client\\ClientModel',
     *                                     'type_text' => 'string',
     *                                     'tail_text' => 'required|min:2|max:30',
     *                                     ),
     *                                     1 =>
     *                                     array (
     *                                     'type' => 'array_key',
     *                                     ),
     *                                     2 =>
     *                                     array (
     *                                     'type' => 'annotation',
     *                                     ),
     *                                     3 =>
     *                                     array (
     *                                     'type' => 'type',
     *                                     ),
     *                                     4 =>
     *                                     array (
     *                                     'type' => 'return',
     *                                     ),
     *                                     5 =>
     *                                     array (
     *                                     'type' => 'default',
     *                                     ),
     *                                     ),
     *                                     ),
     *                                     2 =>
     *                                     array (
     *                                     'name' => 'pyro_views',
     *                                     'items' =>
     *                                     array (
     *                                     0 =>
     *                                     array (
     *                                     'lookup_string' => 'pyro.theme.admin::partials/assets',
     *                                     'icon' => 'icons.TwigIcons.TwigFileIcon',
     *                                     'target' => 'file://addons/shared/pyro/admin-theme/resources/views/partials/assets.twig',
     *                                     ),
     *                                     1 =>
     *                                     array (
     *                                     'lookup_string' => 'pyro.theme.admin::partials/assets2',
     *                                     'icon' => 'de.espend.idea.laravel.LaravelIcons.LARAVEL',
     *                                     'target' => 'file://addons/shared/pyro/admin-theme/resources/views/partials/assets.twig',
     *                                     ),
     *                                     ),
     *                                     ),
     *                                     ),
     *                                     )
     * @param array                   $value
     * @return $this
     */
    public function merge($key, $value = [])
    {
        parent::merge($key, $value);
        return $this;
    }

    /**
     * @param      $keys
     * @param null $value
     * @return \Laradic\Support\Dot|void
     */
    public function set($keys, $value = null)
    {
        return parent::set($keys, $value);
    }

    public function push($key, $value = null)
    {
        parent::push($key, $value);
        return $this;
    }


}