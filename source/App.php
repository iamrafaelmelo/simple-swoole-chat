<?php

declare(strict_types=1);

namespace Chat;

use DI\ContainerBuilder;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Swoole\WebSocket\Server;

class App
{
    public const VERSION = '0.10.0';

    private Server $server;
    private static mixed $routes;

    private static ContainerInterface $container;

    public function __construct(array $settings, callable $routes, array $dependencies = [])
    {
        if (!$settings) {
            throw new InvalidArgumentException('Settings not found.');
        }

        $builder = new ContainerBuilder();
        $builder->addDefinitions($dependencies, ['settings' => $settings]);

        self::$container = $builder->build();
        self::$routes = $routes;

        $this->server = new Server(
            host: self::$container->get('settings')['server']['host'],
            port: (int) self::$container->get('settings')['server']['port'],
            mode: self::$container->get('settings')['server']['mode'],
            sock_type: self::$container->get('settings')['server']['sock_type']
        );

        $this->server->set($settings['server']['options']);
    }

    public function events(array $events): void
    {
        if (!$events) {
            throw new InvalidArgumentException('There are no events logged.');
        }

        foreach ($events as $name => $handler) {
            if (!class_exists($handler)) {
                throw new InvalidArgumentException("Event handler class {$handler} not found.");
            }

            print("[Event]: {$handler}\n");
            $this->server->on($name, new $handler());
        }
    }

    public static function routes(): callable
    {
        return self::$routes;
    }

    public static function container(): ContainerInterface
    {
        return self::$container;
    }

    public function start(): void
    {
        $this->server->start();
    }
}
