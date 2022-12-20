<?php

declare(strict_types=1);

return [
    'server' => [
        'host'       => 'localhost',
        'port'       => 8000,
        'mode'       => SWOOLE_BASE,
        'sock_type'  => SWOOLE_SOCK_TCP,
        'keep_alive' => 30000,
    ],
];
