<?php

namespace Chat\Concerns;

use League\Plates\Engine;
use League\Plates\Extension\Asset;
use Swoole\Http\Response;

trait Renderable
{
    protected Engine $engine;

    public function setup(): void
    {
        $settings = require dirname(__DIR__) . '/../config/settings.php';

        $this->engine = new Engine();
        $this->engine->setDirectory($settings['views']['path']);
        $this->engine->loadExtension(new Asset($settings['server']['options']['document_root']));
    }

    protected function render(Response $response, string $filename, array $data = []): bool
    {
        $this->setup();
        $response->header('Content-Type', 'text/html');
        $content = $this->engine->render($filename, $data);

        return $response->end($content);
    }
}