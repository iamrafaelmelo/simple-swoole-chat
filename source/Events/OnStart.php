<?php

declare(strict_types=1);

namespace Chat\Events;

use Swoole\WebSocket\Server;

class OnStart
{
    public function __invoke(Server $server): void
    {
        $server->on('start', function (Server $server) {
            print("Websocket server is listen on http://{$server->host}:{$server->port}\n");
        });
    }
}
