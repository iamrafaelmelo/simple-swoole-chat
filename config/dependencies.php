<?php

use League\Plates\Engine as Plates;
use League\Plates\Extension\Asset;
use Psr\Container\ContainerInterface as Container;

return [
    Plates::class => function (Container $container): Plates {
        $publicPath = $container->get('settings')['server']['options']['document_root'];
        $viewsPath = $container->get('settings')['views']['path'];

        $plates = new Plates();
        $plates->setDirectory($viewsPath);
        $plates->loadExtension(new Asset($publicPath));

        return $plates;
    },
];
