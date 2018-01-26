<?php
/**
 * Part of the Laradic PHP Packages.
 *
 * Copyright (c) 2018. Robin Radic.
 *
 * The license can be found in the package and online at https://laradic.mit-license.org.
 *
 * @copyright Copyright 2018 (c) Robin Radic
 * @license https://laradic.mit-license.org The MIT License
 */

/**
 * Created by IntelliJ IDEA.
 * User: radic
 * Date: 8/6/16
 * Time: 10:11 PM
 */

namespace Laradic\Idea\Metadata\Translation;


interface LoaderInterface extends \Illuminate\Contracts\Translation\Loader
{
    /**
     * @return array
     */
    public function getHints();

    /** @return string */
    public function getPath();
}
