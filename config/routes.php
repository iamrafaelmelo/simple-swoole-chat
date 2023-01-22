<?php

use Chat\Actions\HomeAction;
use Slim\App as Slim;

return function (Slim $route) {
    $route->get('/', HomeAction::class);
};
