<?php

namespace Lavoaster\LaravelTactician\Middleware;

use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use League\Tactician\Middleware;
use Lavoaster\LaravelTactician\Queue\CallQueuedCommandHandler;

class QueueCommandMiddleware implements Middleware
{
    /**
     * @var Queue
     */
    private $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        if ($command instanceof ShouldQueue && ! $command->inQueue) {
            $this->queue->push(
                CallQueuedCommandHandler::class.'@call',
                [
                    'commandName' => get_class($command),
                    'command' => serialize(clone $command),
                ]
            );

            return;
        }

        return $next($command);
    }
}
