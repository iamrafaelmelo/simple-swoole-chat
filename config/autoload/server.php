<?php

return [
    'host' => 'localhost',
    'port' => 8000,
    'mode' => SWOOLE_BASE,
    'sock_type' => SWOOLE_SOCK_TCP,
    'keep_alive' => 30000,
    'options' => [
        'enable_static_handler' => true,
    ],
];
