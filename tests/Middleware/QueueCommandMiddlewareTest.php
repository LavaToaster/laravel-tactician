<?php

namespace Lavoaster\LaravelTactician\Tests\Middleware;

use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mockery;
use Lavoaster\LaravelTactician\Middleware\QueueCommandMiddleware;
use Lavoaster\LaravelTactician\Queue\CallQueuedCommandHandler;
use Lavoaster\LaravelTactician\Queue\Queue as QueueTrait;
use Lavoaster\LaravelTactician\Tests\TestCase;

class QueueCommandMiddlewareTest extends TestCase
{
    public function testItQueuesQueueableCommand()
    {
        $command = new TestQueuableCommand();

        $queue = Mockery::mock(Queue::class);
        $queue->shouldReceive('push')->with(CallQueuedCommandHandler::class.'@call', Mockery::type('array'))->once();

        $middleware = new QueueCommandMiddleware($queue);

        $return = $middleware->execute($command, function () {
            $this->fail('Expected middleware to not call next middleware, next middleware was called.');
        });

        $this->assertNull($return);
    }

    public function testItIgnoresNonQueueableCommand()
    {
        $command = new TestCommand();

        $queue = Mockery::mock(Queue::class);
        $queue->shouldNotReceive('push');

        $middleware = new QueueCommandMiddleware($queue);

        $return = $middleware->execute($command, function () {
            return true;
        });

        $this->assertTrue($return);
    }

    public function testItIgnoresQueueableCommandWhenInQueue()
    {
        $command = new TestQueuableCommand();
        $command->inQueue = true;

        $queue = Mockery::mock(Queue::class);
        $queue->shouldNotReceive('push');

        $middleware = new QueueCommandMiddleware($queue);

        $return = $middleware->execute($command, function () {
            return true;
        });

        $this->assertTrue($return);
    }
}

class TestQueuableCommand implements ShouldQueue
{
    use QueueTrait;
}

class TestCommand
{
}
