<?php

declare(strict_types=1);

namespace Chat\Events;

use Chat\Concerns\Renderable;
use Swoole\Http\Request;
use Swoole\Http\Response;

class OnRequest
{
    use Renderable;

    public function __invoke(Request $request, Response $response): bool
    {
        return $this->render($response, 'home');
    }
}
