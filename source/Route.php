<?php

declare(strict_types=1);

namespace Chat;

use Slim\App as Slim;
use Slim\Interfaces\RouteInterface;

class Route
{
    public static function get(string $pattern, $callable): RouteInterface
    {
        return self::createRoute('GET', $pattern, $callable);
    }

    public static function post(string $pattern, $callable): RouteInterface
    {
        return self::createRoute('POST', $pattern, $callable);
    }

    public static function put(string $pattern, $callable): RouteInterface
    {
        return self::createRoute('PUT', $pattern, $callable);
    }

    public static function patch(string $pattern, $callable): RouteInterface
    {
        return self::createRoute('PATCH', $pattern, $callable);
    }

    public static function delete(string $pattern, $callable): RouteInterface
    {
        return self::createRoute('DELETE', $pattern, $callable);
    }

    public static function options(string $pattern, $callable): RouteInterface
    {
        return self::createRoute('OPTIONS', $pattern, $callable);
    }

    public static function any(string $pattern, $callable): RouteInterface
    {
        return self::createRoute('ANY', $pattern, $callable);
    }

    public static function group(string $pattern, callable $callable): void
    {
        $app = App::getSlimInstance();
        $app->group($pattern, $callable);
    }

    private static function createRoute(string $method, string $pattern, $callable): RouteInterface
    {
        $app = App::getSlimInstance();
        $route = $app->map([$method], $pattern, $callable);

        return $route;
    }
}
