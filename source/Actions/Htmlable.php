<?php

declare(strict_types=1);

namespace Chat\Actions;

use League\Plates\Engine as Plates;
use Psr\Http\Message\ResponseInterface as Response;

abstract class Htmlable
{
    public function __construct(
        private readonly Plates $plates
    ) {
    }

    public function toHtml(Response $response, string $filename, array $data = []): Response
    {
        $response->withHeader('Content-Type', 'text/html');
        $content = $this->plates->render($filename, $data);
        $response->getBody()->write($content);

        return $response;
    }
}
