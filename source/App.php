<?php

declare(strict_types=1);

namespace Chat;

use InvalidArgumentException;
use Swoole\WebSocket\Server;

class App
{
    public const VERSION = '0.7.1';

    private Server $server;

    public function __construct(array $settings)
    {
        if (!$settings) {
            throw new InvalidArgumentException('Settings not found.');
        }

        $this->server = new Server(
            host: $settings['server']['host'],
            port: $settings['server']['port'],
            mode: $settings['server']['mode'],
            sock_type: $settings['server']['sock_type']
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

    public function start(): void
    {
        $this->server->start();
    }
}
