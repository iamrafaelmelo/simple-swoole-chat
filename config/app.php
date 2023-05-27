<?php

declare(strict_types=1);

use Chat\Env;

return [
    'name' => Env::get('APP_NAME', 'Chat'),
    'env' => Env::get('APP_ENV', 'production'),
    'debug' => Env::get('APP_DEBUG', false),
    'timezone' => Env::get('APP_TIMEZONE', 'UTC'),
];
