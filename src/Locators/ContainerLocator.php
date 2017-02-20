<?php

namespace Lavoaster\LaravelTactician\Locators;

use Illuminate\Contracts\Container\Container;
use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\Locator\HandlerLocator;

class ContainerLocator implements HandlerLocator
{
    /**
     * @var string[]
     */
    private $mapping;

    /**
     * @var Container
     */
    private $container;

    /**
     * ContainerLocator constructor.
     *
     * @param Container $container
     * @param string[] $mapping
     */
    public function __construct(Container $container, array $mapping)
    {
        $this->container = $container;
        $this->mapping = $mapping;
    }

    /**
     * Retrieves the handler for a specified command.
     *
     * @param string $commandName
     *
     * @return object
     *
     * @throws MissingHandlerException
     */
    public function getHandlerForCommand($commandName)
    {
        if (! isset($this->mapping[$commandName])) {
            throw MissingHandlerException::forCommand($commandName);
        }

        return $this->container->make($this->mapping[$commandName]);
    }
}
