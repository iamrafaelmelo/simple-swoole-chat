<?php

namespace Chat\Actions;
use Chat\Concerns\Renderable;
use Swoole\Http\Request;
use Swoole\Http\Response;

class HomeAction
{
    use Renderable;

    public function __invoke(Request $request, Response $response): string
    {
        return $this->render($response, 'home');
    }
}
