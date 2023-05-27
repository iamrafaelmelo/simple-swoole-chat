<?php

declare(strict_types=1);

use Chat\Env;

return [
    'host' => Env::get('SERVER_HOST', 'localhost'),
    'port' => Env::get('SERVER_PORT', 8000),
    'mode' => Env::get('SERVER_MODE', SWOOLE_BASE),
    'sock_type' => Env::get('SERVER_SOCK_TYPE', SWOOLE_SOCK_TCP),
    'keep_alive' => Env::get('SERVER_KEEP_ALIVE', 3000),
    'options' => [
        'enable_static_handler' => true,
    ],
];
