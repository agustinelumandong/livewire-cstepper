<?php

namespace agustinelumandong\LivewireCstepper\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Simple test case that doesn't require full Laravel setup
 * for testing individual traits and components
 */
abstract class SimpleTestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }
}