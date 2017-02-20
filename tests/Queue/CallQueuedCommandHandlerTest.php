<?php

namespace Lavoaster\LaravelTactician\Tests\middleware;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use League\Tactician\CommandBus;
use Mockery;
use Lavoaster\LaravelTactician\Queue\CallQueuedCommandHandler;
use Lavoaster\LaravelTactician\Queue\Queue;
use Lavoaster\LaravelTactician\Tests\TestCase;

class CallQueuedCommandHandlerTest extends TestCase
{
    public function testItCallsQueuedCommandAndDeletesFromQueue()
    {
        $data = [
            'commandName' => QueueableTestCommand::class,
            'command' => serialize(new QueueableTestCommand()), // To simulate what the queue actually does
        ];

        $bus = Mockery::mock(CommandBus::class);
        $bus->shouldReceive('handle')->with(Mockery::type('object'))->once();

        $job = Mockery::mock(Job::class);
        $job->shouldReceive('isDeletedOrReleased')->once()->andReturn(false);
        $job->shouldReceive('delete')->once();

        $commandHandler = new CallQueuedCommandHandler($bus);
        $commandHandler->call($job, $data);

        $command = $commandHandler->getLastCalledCommand();

        $this->assertSame($job, $command->getJob());
        $this->assertTrue($command->inQueue);
    }

    public function testItCallsQueuedCommandAndDoesntDeleteFromQueue()
    {
        $data = [
            'commandName' => QueueableTestCommand::class,
            'command' => serialize(new QueueableTestCommand()), // To simulate what the queue actually does
        ];

        $bus = Mockery::mock(CommandBus::class);
        $bus->shouldReceive('handle')->with(Mockery::type('object'))->once();

        $job = Mockery::mock(Job::class);
        $job->shouldReceive('isDeletedOrReleased')->once()->andReturn(true);
        $job->shouldNotHaveReceived('delete');

        $commandHandler = new CallQueuedCommandHandler($bus);
        $commandHandler->call($job, $data);

        $command = $commandHandler->getLastCalledCommand();

        $this->assertSame($job, $command->getJob());
        $this->assertTrue($command->inQueue);
    }
}

class QueueableTestCommand implements ShouldQueue
{
    use Queue, InteractsWithQueue;

    public function getJob()
    {
        return $this->job;
    }
}
