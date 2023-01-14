<?php

declare(strict_types=1);

return [
    'server' => [
        'host'       => 'localhost',
        'port'       => 8000,
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
    'views' => [
        'path' => dirname(__DIR__) . '/render',
    ],
];
