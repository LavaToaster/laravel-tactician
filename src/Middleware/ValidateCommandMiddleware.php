<?php

namespace Lavoaster\LaravelTactician\Middleware;

use Illuminate\Container\Container;
use League\Tactician\Middleware;

class ValidateCommandMiddleware implements Middleware
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Executes a command and optionally returns a value.
     *
     * @param object   $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        $validatorClass = $this->getValidatorClass($command);

        if (! $validatorClass) {
            return $next($command);
        }

        $validatorClass = $this->container->make($validatorClass);
        $validatorClass->handle($command);

        return $next($command);
    }

    /**
     * Gets the validator class for a given command.
     *
     * @param $command
     * @return bool|object
     */
    private function getValidatorClass($command)
    {
        // Replace Directory
        $className = str_replace('Commands', 'Validators', get_class($command));
        // Replace Name
        $className = str_replace('Command', 'Validator', $className);

        if (class_exists($className)) {
            return $className;
        }

        return false;
    }
}
