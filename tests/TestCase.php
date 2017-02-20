<?php

namespace Lavoaster\LaravelTactician\Tests;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        \Mockery::close();
    }
}
