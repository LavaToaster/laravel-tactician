<?php

namespace Lavoaster\LaravelTactician\Tests\Middleware {
    use Illuminate\Container\Container;
    use Mockery;
    use Lavoaster\LaravelTactician\Domain\Commands\TestValidateFailCommand;
    use Lavoaster\LaravelTactician\Domain\Validators\SpecialException;
    use Lavoaster\LaravelTactician\Middleware\ValidateCommandMiddleware;
    use Lavoaster\LaravelTactician\Tests\TestCase;
    use Lavoaster\LaravelTactician\Domain\Commands\TestValCommand;
    use Lavoaster\LaravelTactician\Domain\Commands\TestValidateCommand;
    use Lavoaster\LaravelTactician\Domain\Validators\TestValidateValidator;
    use Lavoaster\LaravelTactician\Domain\Validators\TestValidateFailValidator;

    class ValidateCommandMiddlewareTest extends TestCase
    {
        public function testItSkipsValidatesCommandWhenNoValidatorIsFound()
        {
            $command = new TestValCommand();

            $container = Mockery::mock(Container::class);
            $container->shouldNotReceive('make');

            $middleware = new ValidateCommandMiddleware($container);

            $return = $middleware->execute($command, function () {
                return true;
            });

            $this->assertTrue($return);
        }

        public function testItValidatesCommandWhenValidatorIsFound()
        {
            $command = new TestValidateCommand();

            $container = Mockery::mock(Container::class);
            $container->shouldReceive('make')->with(TestValidateValidator::class)->andReturn(new TestValidateValidator())->once();

            $middleware = new ValidateCommandMiddleware($container);

            $return = $middleware->execute($command, function () {
                return true;
            });

            $this->assertTrue($return);
        }

        public function testItValidatesCommandWhenValidatorIsFoundAndExceptionIsThrown()
        {
            $this->expectException(SpecialException::class);

            $command = new TestValidateFailCommand();

            $container = Mockery::mock(Container::class);
            $container->shouldReceive('make')->with(TestValidateFailValidator::class)->andReturn(new TestValidateFailValidator())->once();

            $middleware = new ValidateCommandMiddleware($container);

            $return = $middleware->execute($command, function () {
                $this->fail('Expected middleware to not call next middleware, next middleware was called.');
            });
        }
    }
}

namespace Lavoaster\LaravelTactician\Domain\Commands {
    class TestValidateCommand
    {
        // ¯\_(ツ)_/¯
    }

    class TestValidateFailCommand
    {
        // ¯\_(ツ)_/¯
    }

    class TestValCommand
    {
        // ¯\_(ツ)_/¯
    }
}

namespace Lavoaster\LaravelTactician\Domain\Validators {
    class TestValidateValidator
    {
        public function handle($command)
        {
            // ¯\_(ツ)_/¯
        }
    }

    class TestValidateFailValidator
    {
        public function handle($command)
        {
            throw new SpecialException();
            // ¯\_(ツ)_/¯
        }
    }

    class SpecialException extends \Exception {

    }
}
