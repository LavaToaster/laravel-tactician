<?php

namespace Lavoaster\LaravelTactician\Tests\Formatters;

use Psr\Log\LoggerInterface;
use Lavoaster\LaravelTactician\Formatters\LogFormatter;
use Lavoaster\LaravelTactician\Tests\TestCase;

class LogFormatterTest extends TestCase
{
    public function testLogCommandReceived()
    {
        $logFormatter = new LogFormatter();

        $testClass = new TestClass();

        $logger = \Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('debug')->with('Command Received: '.get_class($testClass));

        $logFormatter->logCommandReceived($logger, $testClass);
    }

    public function testLogCommandSucceeded()
    {
        $logFormatter = new LogFormatter();

        $testClass = new TestClass();

        $logger = \Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('debug')->with('Command Succeeded: '.get_class($testClass));

        $logFormatter->logCommandSucceeded($logger, $testClass, true);
    }

    public function testLogCommandFailed()
    {
        $logFormatter = new LogFormatter();

        $testClass = new TestClass();
        $exception = new \Exception('Trace');

        $logger = \Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('error')->with('Command Failed: '.get_class($testClass), \Mockery::type('array'));

        $logFormatter->logCommandFailed($logger, $testClass, $exception);
    }
}

class TestClass
{
}
