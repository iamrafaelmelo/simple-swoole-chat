<?php

declare(strict_types=1);

namespace Chat\Events;

use Chat\App;
use Chat\Concerns\Renderable;
use FastRoute\Dispatcher;
use Swoole\Http\Request;
use Swoole\Http\Response;
use function FastRoute\cachedDispatcher;

class OnRequest
{
    use Renderable;

    public function __invoke(Request $request, Response $response): void
    {
        $dispatcher = cachedDispatcher(
            routeDefinitionCallback: App::routes(),
            options: App::container()->get('settings')['router']
        );

        $method = $request->getMethod();
        $uri = $request->server['request_uri'];
        $position = strpos($uri, '?');

        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }

        $uri = rawurldecode($uri);
        $router = $dispatcher->dispatch($method, $uri);

        switch ($router[0]) {
            case Dispatcher::NOT_FOUND:
                $response->status(404);
                $this->render($response, 'http/404');
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $response->status(405);
                $response->end('Method not allowed');
                break;
            case Dispatcher::FOUND:
                $handler = $router[1];
                $arguments = $router[2];
                call_user_func(new $handler(), $request, $response, $arguments);
                break;
        }
    }
}
