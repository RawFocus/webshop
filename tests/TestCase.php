<?php

namespace Raw\Webshop\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected $loadEnvironmentVariables = true;
    protected $enablesPackageDiscoveries = true;
}