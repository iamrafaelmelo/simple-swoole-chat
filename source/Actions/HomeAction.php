<?php

declare(strict_types=1);

namespace Chat\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeAction extends Htmlable
{
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->toHtml($response, 'home');
    }
}
