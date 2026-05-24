<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Redis;
use Throwable;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Isolate tests from any live driver geo-index / cached locations.
        try {
            Redis::connection()->flushdb();
        } catch (Throwable) {
            // Redis is optional for the test suite; ride matching falls back to the DB.
        }
    }
}
