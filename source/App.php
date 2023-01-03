<?php

declare(strict_types=1);

namespace Chat;

use RuntimeException;
use Swoole\WebSocket\Server;

class App
{
    public const VERSION = '0.4.0';

    private Server $server;
    private array $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;

        if (!$this->settings) {
            throw new RuntimeException('Settings not found.');
        }

        $this->server = new Server(
            host: $settings['server']['host'],
            port: $settings['server']['port'],
            mode: $settings['server']['mode'],
            sock_type: $settings['server']['sock_type']
        );
    }

    public function events(array $events)
    {
        if (!$events) {
            throw new RuntimeException('There are no events logged.');
        }

        foreach ($events as $name => $handler) {
            print("[Event]: {$handler}\n");
            $this->server->on($name, new $handler());
        }
    }

    public function start()
    {
        $this->server->start();
    }
}
