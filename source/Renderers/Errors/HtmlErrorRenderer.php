<?php

namespace Chat\Renderers\Errors;

use League\Plates\Engine as Plates;
use Slim\Interfaces\ErrorRendererInterface;
use Throwable;

class HtmlErrorRenderer implements ErrorRendererInterface
{
    public function __construct(
        private Plates $plates
    ) {
    }

    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
        $code = $exception->getCode();
        $view = '';

        switch ($code) {
            case 400:
            case 401:
            case 403:
            case 405:
            case 429:
            case 422:
            case 404:
                $view = 'http/404';
                break;
        }

        return $this->plates->render($view, [
            'code' => $code,
            'message' => $exception->getMessage(),
        ]);
    }
}
