<?php

namespace RawFocus\Webshop\Tests;

use Stripe\ApiRequestor;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected $loadEnvironmentVariables = true;
    protected $enablesPackageDiscoveries = true;

    public $stripeClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stripeClient = $this->mockStripe();
    }

    public function mockStripe()
    {
        $client = new MockStripeClient();
        ApiRequestor::setHttpClient($client);
        return $client;
    }
}