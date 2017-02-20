<?php

namespace Lavoaster\LaravelTactician\Queue;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\InteractsWithQueue;
use League\Tactician\CommandBus;

class CallQueuedCommandHandler
{
    /**
     * @var CommandBus
     */
    private $bus;

    private $lastCalledCommand;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function call(Job $job, array $data)
    {
        $command = unserialize($data['command']);
        $command->setInQueue();

        if (in_array(InteractsWithQueue::class, class_uses_recursive(get_class($command)))) {
            $command->setJob($job);
        }

        $this->lastCalledCommand = $command;

        $this->bus->handle($command);

        if (! $job->isDeletedOrReleased()) {
            $job->delete();
        }
    }

    public function getLastCalledCommand()
    {
        return $this->lastCalledCommand;
    }
}
