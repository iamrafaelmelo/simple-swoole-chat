<?php

declare(strict_types=1);

use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

$settings = require __DIR__ . '/../config/settings.php';
$server = new Server(
    host: $settings['server']['host'],
    port: $settings['server']['port'],
    mode: $settings['server']['mode'],
    sock_type: $settings['server']['sock_type']
);

$server->on('managerStart', function(Server $server) use ($settings) {
    $server->tick($settings['server']['keep_alive'], function () use ($server) {
        foreach ($server->connections as $id) {
            if ($server->isEstablished($id)) {
                $server->push($id, 'ping', WEBSOCKET_OPCODE_PING);
            }
        }
    });
});

$server->on('start', function (Server $server) {
    print("Websocket server is listen on http://{$server->host}:{$server->port}\n");
});

$server->on('open', function (Server $server, Request $request) {
    print("Client connected: {$request->fd}\n");
});

$server->on('message', function (Server $server, Frame $frame) {
    print("Client: {$frame->fd} | Message: {$frame->data}\n");

    foreach ($server->connections as $connection) {
        if ($server->isEstablished($connection) && $connection === $frame->fd) {
            continue;
        }

        $data = json_encode([
            'id' => $frame->fd,
            'text' => $frame->data,
        ]);

        $server->push($connection, $data);
    }
});

$server->on('close', function (Server $server, int $fd) {
    print("Client disconnected: {$fd}\n");
});

$server->start();
