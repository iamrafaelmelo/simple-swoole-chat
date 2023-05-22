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
    public const VERSION = '1.4.0';

    private Server $server;
    private ContainerInterface $container;
    private Slim $slim;
    private Logger $logger;

    public function __construct()
    {
        Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
        $definitions = $this->loadDefinitions();

        $this->server = $this->configureServer($definitions);
        $this->container = $this->buildContainer($definitions);
        $this->slim = $this->container->get(Slim::class);
        $this->logger = new Logger();

        $this->configureAppErrorHandler($definitions);
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

    private function loadDefinitions(): array
    {
        $definitions = [
            'configurations' => [],
            'dependencies' => [],
        ];

        $files = new DirectoryIterator(dirname(__DIR__) . '/config/autoload');

        foreach ($files as $file) {
            $filename = $file->getBasename('.php');

            if ($file->isDot() || $file->isDir()) {
                continue;
            }

            if ($filename !== 'dependencies') {
                $definitions['configurations'][$filename] = require $file->getPathname();

                continue;
            }

            $definitions['dependencies'] = require $file->getPathname();
        }

        return array_merge($definitions['configurations'], $definitions['dependencies']);
    }

    private function configureServer(array $definitions): Server
    {
        $server = new Server(
            host: $definitions['server']['host'],
            port: (int) $definitions['server']['port'],
            mode: $definitions['server']['mode'],
            sock_type: $definitions['server']['sock_type']
        );

        $definitions['server']['options']['document_root'] = $definitions['view']['paths']['assets'];
        $server->set($definitions['server']['options']);

        return $server;
    }

    private function buildContainer(array $definitions): ContainerInterface
    {
        $container = new ContainerBuilder();
        $container->useAutowiring(true);

        if ($definitions['app']['env'] === Env::PRODUCTION) {
            $container->enableCompilation($definitions['cache']['container']['compilation']);
            $container->writeProxiesToFile(true, $definitions['cache']['container']['proxies']);
        }

        $container->addDefinitions($definitions);

        return $container->build();
    }

    private function configureAppErrorHandler(array $definitions): void
    {
        $errorHandler = new ErrorHandler(
            callableResolver: $this->slim->getCallableResolver(),
            responseFactory: $this->slim->getResponseFactory()
        );

        $errorHandler->registerErrorRenderer('text/html', HtmlErrorRenderer::class);
        $errorHandler->forceContentType('text/html');

        $errorMiddleware = $this->slim->addErrorMiddleware(
            displayErrorDetails: $definitions['app']['debug'] ?: false,
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
