<?php

declare(strict_types=1);

use function DI\env;

return [
    'app' => [
        'name' => 'Simple Swoole Chat',
        'debug' => true,
        'cache' => true,
    ],
    'server' => [
        'host'       => 'localhost',
        'port'       => 8000,
        'mode'       => SWOOLE_BASE,
        'sock_type'  => SWOOLE_SOCK_TCP,
        'keep_alive' => 30000,
        'options'    => [
            'document_root' => dirname(__DIR__) . '/public',
            'enable_static_handler' => true,
        ],
    ],
    'cache' => [
        'compilation' => dirname(__DIR__) . '/storage/cache/compilation',
        'proxies' => dirname(__DIR__) . '/storage/cache/compilation/proxies',
    ],
    'views' => [
        'path' => dirname(__DIR__) . '/render',
    ],
];
