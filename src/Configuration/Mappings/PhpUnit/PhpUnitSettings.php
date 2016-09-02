<?php
namespace Laradic\Idea\Configuration\Mappings\PhpUnit;

use Laradic\Idea\Configuration\Mappings\Component;

class PhpUnitSettings extends Component
{
    public $load_method;

    public $bootstrap_file_path;

    public $configuration_file_path;

    public $custom_loader_path;

    public $phpunit_phar_path;

    public $use_bootstrap_file;

    public $use_configuration_file;
}