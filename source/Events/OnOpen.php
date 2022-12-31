<?php

declare(strict_types=1);

namespace Chat\Events;

use Swoole\Http\Request;
use Swoole\WebSocket\Server;

class OnOpen
{
    public function __invoke(Server $server): void
    {
        $server->on('open', function (Server $server, Request $request) {
            print("Client connected: {$request->fd}\n");
        });
    }
}
