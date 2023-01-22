<?php

declare(strict_types=1);

namespace Chat;

use Chat\Renderers\Errors\HtmlErrorRenderer;
use DI\ContainerBuilder;
use Ilex\SwoolePsr7\SwooleResponseConverter;
use Ilex\SwoolePsr7\SwooleServerRequestConverter;
use InvalidArgumentException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Slim\App as Slim;
use Slim\Handlers\ErrorHandler;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Server;

class App
{
    public const VERSION = '1.0.0';

    private Server $server;
    public Slim $slim;

    public function __construct(array $settings, callable $routes, array $dependencies = [])
    {
        if (!$settings) {
            throw new InvalidArgumentException('Settings not found.');
        }

        $psr17Factory = new Psr17Factory();
        $this->server = $this->initializeServer($settings);
        $container = $this->buildContainer($dependencies, $settings);
        $slim = $this->initializeApp($psr17Factory, $container);

        $routes($slim);

        $this->configureErrorHandler($slim, $settings);
        $request = $this->convertSwooleRequestToPsr7($psr17Factory);
        $this->emitResponseToClient($slim, $request);
    }

    public function events(array $events): void
    {
        if (!$events) {
            throw new InvalidArgumentException('There are no events logged.');
        }

        foreach ($events as $name => $handler) {
            if (!class_exists($handler)) {
                throw new InvalidArgumentException("Event handler class {$handler} not found.");
            }

            print("[Event]: {$handler}\n");
            $this->server->on($name, new $handler());
        }
    }

    public function start(): void
    {
        $this->server->start();
    }

    private function buildContainer(array $dependencies, array $settings): ContainerInterface
    {
        $container = new ContainerBuilder();
        $container->useAutowiring(true);

        if ($settings['app']['cache']) {
            $container->enableCompilation($settings['cache']['compilation']);
            $container->writeProxiesToFile(true, $settings['cache']['proxies']);
        }

        $container->addDefinitions($dependencies, [
            'settings' => $settings,
        ]);

        return $container->build();
    }

    public function configureErrorHandler(Slim $slim, array $settings): void
    {
        $errorHandler = new ErrorHandler($slim->getCallableResolver(), $slim->getResponseFactory());
        $errorHandler->registerErrorRenderer('text/html', HtmlErrorRenderer::class);
        $errorHandler->forceContentType('text/html');

        $errorMiddleware = $slim->addErrorMiddleware($settings['app']['debug'] ?: false, true, true);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);
    }

    private function initializeServer(array $settings): Server
    {
        $server = new Server(
            host: $settings['server']['host'],
            port: (int) $settings['server']['port'],
            mode: $settings['server']['mode'],
            sock_type: $settings['server']['sock_type']
        );

        $server->set($settings['server']['options']);

        return $server;
    }

    private function initializeApp(Psr17Factory $psr17Factory, ContainerInterface $container): Slim
    {
        $app = new Slim($psr17Factory, $container);
        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();

        return $app;
    }

    private function convertSwooleRequestToPsr7(Psr17Factory $psr17Factory): SwooleServerRequestConverter
    {
        return new SwooleServerRequestConverter(
            serverRequestFactory: $psr17Factory,
            uriFactory: $psr17Factory,
            uploadedFileFactory: $psr17Factory,
            streamFactory: $psr17Factory
        );
    }

    private function emitResponseToClient(Slim $slim, SwooleServerRequestConverter $swooleServerRequestConverter): void
    {
        $this->server->on('request', function (Request $request, Response $response) use ($swooleServerRequestConverter, $slim) {
            $psr7Request = $swooleServerRequestConverter->createFromSwoole($request);
            $psr7Response = $slim->handle($psr7Request);
            $converter = new SwooleResponseConverter($response);

            $converter->send($psr7Response);
        });
    }
}
