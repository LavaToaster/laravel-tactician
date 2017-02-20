<?php

namespace Lavoaster\LaravelTactician;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use League\Tactician\Logger\LoggerMiddleware;
use Psr\Log\LoggerInterface;
use Lavoaster\LaravelTactician\Formatters\LogFormatter;
use Lavoaster\LaravelTactician\Locators\ContainerLocator;

class TacticianServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerCommandBus();
        $this->registerLocators();
        $this->registerCommandHandler();
        $this->registerLogger();

        if (! str_contains($this->app->version(), 'Lumen')) {
            $this->publishes([
                $this->getConfigPath() => config_path('tactician.php'),
            ], 'config');
        }
    }

    public function registerCommandBus()
    {
        $this->app->bind(CommandBus::class, function (Application $app) {
            $middleware = array_map(function ($class) use ($app) {
                return $app->make($class);
            }, $app['config']->get('tactician.middleware', []));

            return new CommandBus($middleware);
        });
    }

    public function registerLocators()
    {
        $this->app->bind(ContainerLocator::class, function (Application $app) {
            $mapping = $app['config']->get('tactician.mapping', []);

            return new ContainerLocator($app, $mapping);
        });
    }

    public function registerCommandHandler()
    {
        $this->app->bind(CommandHandlerMiddleware::class, function (Application $app) {
            $locator = $app['config']->get('tactician.locator');

            $nameExtractor = new ClassNameExtractor();
            $inflector = new HandleInflector();
            $locator = $app->make($locator);

            return new CommandHandlerMiddleware($nameExtractor, $locator, $inflector);
        });
    }

    public function registerLogger()
    {
        $this->app->bind(LoggerMiddleware::class, function (Application $app) {
            return new LoggerMiddleware(
                $app->make(LogFormatter::class),
                $app->make(LoggerInterface::class)
            );
        });
    }

    /**
     * @return string
     */
    protected function getConfigPath()
    {
        return __DIR__.'/../config/tactician.php';
    }
}
