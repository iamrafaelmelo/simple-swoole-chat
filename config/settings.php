<?php

declare(strict_types=1);

use function DI\env;

return [
    'app' => [
        'name' => env('APP_NAME', 'Chat'),
    ],
    'server' => [
        'host'       => env('APP_HOST', 'localhost'),
        'port'       => env('APP_PORT', 9501),
        'mode'       => SWOOLE_BASE,
        'sock_type'  => SWOOLE_SOCK_TCP,
        'keep_alive' => 30000,
        'options'    => [
            'document_root' => dirname(__DIR__) . '/public',
            'enable_static_handler' => true,
            'static_handler_locations' => ['/assets'],
            'http_index_files' => ['index.php'],
        ],
    ],
    'router' => [
        'cacheFile' => dirname(__DIR__) . '/storage/cache/route.cache'
    ],
    'views' => [
        'path' => dirname(__DIR__) . '/render',
    ],
];
