<?php

declare(strict_types=1);

namespace Chat\Events;

use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class OnMessage
{
    public const TYPING_MESSAGE = "{username} is typing...";

    public function __invoke(Server $server, Frame $frame)
    {
        $id = $frame->fd;
        $username = "User {$id}";
        $messages = [
            'default' => $frame->data,
            'typing' =>  str_replace('{username}', $username, self::TYPING_MESSAGE),
        ];

        foreach ($server->connections as $connection) {
            if ($connection !== $id) {
                return $server->push($connection, json_encode([
                    'id'       => $id,
                    'username' => $username,
                    'text'     => $messages['default'] === 'typing' ? $messages['typing'] : $messages['default'],
                ]));
            }
        }
    }
}
