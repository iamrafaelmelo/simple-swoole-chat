<?php

declare(strict_types=1);

namespace Chat\Events;

use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class OnMessage
{
    public function __invoke(Server $server): void
    {
        $server->on('message', function (Server $server, Frame $frame) {
            $clientId = $frame->fd;
            $message = $frame->data;

            foreach ($server->connections as $connection) {
                if ($connection === $clientId) {
                    continue;
                }

                if ($message === 'typing') {
                    $data = json_encode([
                        'id' => $clientId,
                        'text' => "User {$clientId} is typing...",
                    ]);

                    return $server->push($connection, $data);
                }

                $data = json_encode([
                    'id' => $clientId,
                    'text' => $message,
                ]);

                return $server->push($connection, $data);
            }
        });
    }
}
