<?php

declare(strict_types=1);

namespace App\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeAction
{
    use Htmlable;

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->toHtml($response, 'home');
    }
}
