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
    print("\033[33mWebsocket server is listen on \033[0m\033[32;4m{$server->host}:{$server->port}\033[0m\n");
});

$server->on('open', function (Server $server, Request $request) {
    print("\033[32mClient connected: {$request->fd} \033[0m\n");
});

$server->on('message', function (Server $server, Frame $frame) {
    print("Client: {$frame->fd} \nMessage: {$frame->data} \n");

    foreach ($server->connections as $connection) {
        if ($connection === $frame->fd) {
            continue;
        }

        $data = json_decode($frame->data, true);
        $data['id'] = $frame->fd;
        $encode = json_encode($data);

        $server->push($connection, $encode);
    }
});

$server->on('close', function (Server $server, int $fd) {
    print("\033[31mClient disconnected: {$fd} \033[0m\n");
});

$server->start();
