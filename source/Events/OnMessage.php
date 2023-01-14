<?php

declare(strict_types=1);

namespace Chat\Events;

use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class OnMessage
{
    public function __invoke(Server $server, Frame $frame): void
    {
        $id = $frame->fd;
        $data = json_decode($frame->data);

        foreach ($server->connections as $connection) {
            if ($server->isEstablished($connection) && $connection !== $id) {
                $server->push($connection, json_encode([
                    'id'       => $id,
                    'type'     => $data->type,
                    'message'  => $data->message ?? "User {$id} is typing...",
                ]));
            }
        }
    }
}
