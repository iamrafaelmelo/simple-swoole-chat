<?php

declare(strict_types=1);

use Chat\App;
use Chat\Events\OnClose;
use Chat\Events\OnManagerStart;
use Chat\Events\OnMessage;
use Chat\Events\OnOpen;
use Chat\Events\OnStart;

require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/../config/settings.php';

$app = new App($settings);
$app->events([
    OnManagerStart::class,
    OnStart::class,
    OnOpen::class,
    OnMessage::class,
    OnClose::class,
]);

$app->start();
