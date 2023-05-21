<?php

declare(strict_types=1);

use Chat\App;
use Chat\Events\OnClose;
use Chat\Events\OnManagerStart;
use Chat\Events\OnMessage;
use Chat\Events\OnOpen;
use Chat\Events\OnStart;
use Swoole\Constant;

require __DIR__ . '/../vendor/autoload.php';

$dependencies = require __DIR__ . '/../config/dependencies.php';

$app = new App($dependencies);
$app->events([
    Constant::EVENT_MANAGER_START => OnManagerStart::class,
    Constant::EVENT_START => OnStart::class,
    Constant::EVENT_OPEN => OnOpen::class,
    Constant::EVENT_MESSAGE => OnMessage::class,
    Constant::EVENT_CLOSE => OnClose::class,
]);

$app->start();
