<?php

declare(strict_types=1);

namespace Chat\Events;

use Swoole\WebSocket\Server;

class OnClose
{
    public function __invoke(Server $server): void
    {
        $server->on('close', function (Server $server, int $fd) {
            print("Client disconnected: {$fd}\n");
        });
    }
}
