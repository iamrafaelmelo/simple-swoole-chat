<?php

declare(strict_types=1);

namespace Chat\Events;

use Swoole\WebSocket\Server;

class OnManagerStart
{
    public function __invoke(Server $server, array $definitions): void
    {
        $server->tick($definitions['server']['keep_alive'], function () use ($server) {
            foreach ($server->connections as $connection) {
                if ($server->isEstablished($connection)) {
                    $server->push($connection, 'ping', WEBSOCKET_OPCODE_PING);
                }
            }
        });
    }
}
