<?php

namespace Raw\Webshop\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMix();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}