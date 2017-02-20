<?php

namespace Lavoaster\LaravelTactician\Tests\Locators;

use Illuminate\Contracts\Container\Container;
use League\Tactician\Exception\MissingHandlerException;
use Mockery;
use Lavoaster\LaravelTactician\Locators\ContainerLocator;
use Lavoaster\LaravelTactician\Tests\TestCase;

class ContainerLocatorTest extends TestCase
{
    public function testItSuccessfullyLocatesMappedCommand()
    {
        $container = Mockery::mock(Container::class);
        $container->shouldReceive('make')->with('Handler1')->once()->andReturn('Handler1');

        $mapping = [
            'Command1' => 'Handler1',
        ];

        $locator = new ContainerLocator($container, $mapping);
        $handler = $locator->getHandlerForCommand('Command1');

        $this->assertSame($mapping['Command1'], $handler);
    }

    public function testItThrowsErrorForUnmappedCommand()
    {
        $this->expectException(MissingHandlerException::class);

        $container = Mockery::mock(Container::class);
        $container->shouldNotHaveReceived('make');

        $mapping = [
            'Command1' => 'Handler1',
        ];

        $locator = new ContainerLocator($container, $mapping);
        $locator->getHandlerForCommand('Command2');
    }
}
