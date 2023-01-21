<?php

use Chat\Actions\HomeAction;
use FastRoute\RouteCollector;

return function (RouteCollector $route) {
    $route->get('/', HomeAction::class);
};
