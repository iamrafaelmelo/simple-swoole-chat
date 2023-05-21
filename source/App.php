<?php

declare(strict_types=1);

namespace Chat;

use Chat\Renderers\Errors\HtmlErrorRenderer;
use DI\ContainerBuilder;
use DirectoryIterator;
use Dotenv\Dotenv;
use Ilex\SwoolePsr7\SwooleResponseConverter as ResponseConverter;
use Ilex\SwoolePsr7\SwooleServerRequestConverter as RequestConverter;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App as Slim;
use Slim\Handlers\ErrorHandler;
use Slim\Logger;
use Swoole\Constant;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Server;

class App
{
    public const VERSION = '1.3.0';

    private Server $server;
    private ContainerInterface $container;
    private Slim $slim;
    private Logger $logger;

    public function __construct(array $dependencies = [])
    {
        Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
        $settings = $this->loadSettings();

        $this->server = $this->configureServer($settings);
        $this->container = $this->buildContainer($dependencies, $settings);
        $this->slim = $this->container->get(Slim::class);
        $this->logger = new Logger();

        $this->configureAppErrorHandler($settings);
        $this->sendResponseToClient();
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

            $this->logger->debug("[EVENT]: {$handler}");
            $this->server->on($name, new $handler());
        }
    }

    public function start(): void
    {
        $this->server->start();
    }

    private function loadSettings(): array
    {
        $definitions = [];
        $configurations = new DirectoryIterator(dirname(__DIR__) . '/config/autoload');

        foreach ($configurations as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $definitions[$file->getBasename('.php')] = require $file->getPathname();
            }
        }

        return $definitions;
    }

    private function configureServer(array $settings): Server
    {
        $server = new Server(
            host: $settings['server']['host'],
            port: (int) $settings['server']['port'],
            mode: $settings['server']['mode'],
            sock_type: $settings['server']['sock_type']
        );

        $settings['server']['options']['document_root'] = $settings['view']['paths']['assets'];
        $server->set($settings['server']['options']);

        return $server;
    }

    private function buildContainer(array $dependencies, array $settings): ContainerInterface
    {
        $container = new ContainerBuilder();
        $container->useAutowiring(true);

        if ($settings['app']['env'] === Env::PRODUCTION) {
            $container->enableCompilation($settings['cache']['container']['compilation']);
            $container->writeProxiesToFile(true, $settings['cache']['container']['proxies']);
        }

        $container->addDefinitions($dependencies, $settings);

        return $container->build();
    }

    private function configureAppErrorHandler(array $settings): void
    {
        $errorHandler = new ErrorHandler(
            callableResolver: $this->slim->getCallableResolver(),
            responseFactory: $this->slim->getResponseFactory()
        );

        $errorHandler->registerErrorRenderer('text/html', HtmlErrorRenderer::class);
        $errorHandler->forceContentType('text/html');

        $errorMiddleware = $this->slim->addErrorMiddleware(
            displayErrorDetails: $settings['app']['debug'] ?: false,
            logErrors: true,
            logErrorDetails: true
        );

        $errorMiddleware->setDefaultErrorHandler($errorHandler);
    }

    private function convertRequest(Request $swooleRequest): ResponseInterface
    {
        /** @var RequestConverter $requestConverter */
        $requestConverter = $this->container->get(RequestConverter::class);
        $requestConverted = $requestConverter->createFromSwoole($swooleRequest);

        return $this->slim->handle($requestConverted);
    }

    private function sendResponseToClient(): void
    {
        $this->server->on(Constant::EVENT_REQUEST, function (Request $request, Response $response): void {
            $responseConverted = $this->convertRequest($request);
            $responseConverter = new ResponseConverter($response);
            $responseConverter->send($responseConverted);
        });
    }
}
