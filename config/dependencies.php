<?php

use Ilex\SwoolePsr7\SwooleServerRequestConverter as RequestConverter;
use League\Plates\Engine as Plates;
use League\Plates\Extension\Asset;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface as Container;
use Slim\App as Slim;
use Slim\Factory\AppFactory;

return [
    Slim::class => function (Container $container): Slim {
        $app = AppFactory::create(container: $container);
        $routes = require $container->get('settings')['routes']['path'];
        $routes($app);

        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();

        return $app;
    },
    RequestConverter::class => function (): RequestConverter {
        $psr17Factory = new Psr17Factory();

        return new RequestConverter(
            serverRequestFactory: $psr17Factory,
            uriFactory: $psr17Factory,
            uploadedFileFactory: $psr17Factory,
            streamFactory: $psr17Factory,
        );
    },
    Plates::class => function (Container $container): Plates {
        $publicPath = $container->get('settings')['server']['options']['document_root'];
        $viewsPath = $container->get('settings')['views']['path'];

        $plates = new Plates();
        $plates->setDirectory($viewsPath);
        $plates->loadExtension(new Asset($publicPath));

        return $plates;
    },
];
