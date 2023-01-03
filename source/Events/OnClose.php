<?php

declare(strict_types=1);

namespace Chat\Events;

use Swoole\WebSocket\Server;

class OnClose
{
    public function __invoke(Server $server, int $fd): void
    {
        print("Client disconnected: {$fd}\n");
    }
}
