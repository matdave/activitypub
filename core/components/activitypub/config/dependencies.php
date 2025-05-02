<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;
use MatDave\ActivityPub\Api\Configuration;
use MODX\Revolution\modX;

return static function (ContainerBuilder $containerBuilder, array $settings) {
    $containerBuilder->addDefinitions([
        'settings' => $settings,

        Configuration::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            return new Configuration($settings);
        },

        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        modX::class => function (ContainerInterface $c) {
            $modx = new modX();
            $modx->initialize('web');
            return $modx;
        },

        ClientInterface::class => function (ContainerInterface $c) {
            return new Client(['timeout' => 10]);
        },

        RequestFactoryInterface::class => function (ContainerInterface $c) {
            $modx = $c->get(modX::class);
            return $modx->services->get(RequestFactoryInterface::class);
        }
    ]);
};
