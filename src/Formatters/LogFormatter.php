<?php

namespace Lavoaster\LaravelTactician\Formatters;

use Exception;
use League\Tactician\Logger\Formatter\Formatter;
use Psr\Log\LoggerInterface;

class LogFormatter implements Formatter
{
    /**
     * @param LoggerInterface $logger
     * @param object $command
     * @return void
     */
    public function logCommandReceived(LoggerInterface $logger, $command)
    {
        $logger->debug('Command Received: '.get_class($command));
    }

    /**
     * @param LoggerInterface $logger
     * @param object $command
     * @param mixed $returnValue
     * @return void
     */
    public function logCommandSucceeded(LoggerInterface $logger, $command, $returnValue)
    {
        $logger->debug('Command Succeeded: '.get_class($command));
    }

    /**
     * @param LoggerInterface $logger
     * @param object $command
     * @param Exception $e
     * @return void
     */
    public function logCommandFailed(LoggerInterface $logger, $command, Exception $e)
    {
        $logger->error('Command Failed: '.get_class($command), [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'trace' => $e->getTrace(),
        ]);
    }
}
