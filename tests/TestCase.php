<?php

namespace Laradic\Tests\Idea;

use Laradic\Idea\IdeaServiceProvider;
use Laradic\Support\Commands\AddMixins;
use Laradic\Support\SupportServiceProvider;
use Laradic\Testing\Laravel\AbstractTestCase;

class TestCase extends AbstractTestCase
{
    public function start()
    {
        $this->app->register(SupportServiceProvider::class);
    }

    protected function getPackageRootPath()
    {
        return dirname(__DIR__);
    }

    protected function getServiceProviderClass()
    {
        return IdeaServiceProvider::class;
    }
}
