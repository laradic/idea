<?php
/**
 * Part of the Laradic PHP Packages.
 *
 * Copyright (c) 2017. Robin Radic.
 *
 * The license can be found in the package and online at https://laradic.mit-license.org.
 *
 * @copyright Copyright 2017 (c) Robin Radic
 * @license https://laradic.mit-license.org The MIT License
 */
namespace Laradic\Idea\Metadata\Metas;

use Illuminate\Contracts\Foundation\Application;
use Laradic\Idea\Metadata\Metas\MetaInterface as MetaContract;

/**
 * This is the ConfigMeta.
 *
 * @package        Laradic
 * @author         Laradic Dev Team
 * @copyright      Copyright (c) 2015, Laradic
 * @license        https://tldrlegal.com/license/mit-license MIT License
 */
abstract class BaseMeta implements MetaContract
{
    protected $template = <<<'EOF'
@foreach($methods as $method)
    {!! $method !!} => [
        '' == '@',
        
        @foreach($data as $k => $v)
            '{!! $k !!}' instanceof {!! \Laradic\Support\Str::ensureLeft(is_string($v) && class_exists($v) ? $v : 'null', '\\') !!},
        @endforeach
    ],
@endforeach
EOF;

    protected $methods = [ ];


    protected $app;

    /**
     * BindingsMeta constructor.
     *
     * @param $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public static function canRun()
    {
        return true;
    }
}
