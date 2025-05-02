<?php

use Slim\Factory\AppFactory;
use Slim\Handlers\Strategies\RequestHandler;

/** @var \DI\Container $container */
$container = require __DIR__ . '/../config/bootstrap.php';

// Create app
AppFactory::setContainer($container);
$app = AppFactory::create();

// Assign matched route arguments to Request attributes for PSR-15 handlers
$app->getRouteCollector()->setDefaultInvocationStrategy(new RequestHandler(true));

$settings = $container->get('settings');

// Register middleware
(require __DIR__ . '/../config/middleware.php')($app, $settings);

// Register routes
(require __DIR__ . '/../config/routes.php')($app);

// Set basePath
if ($settings['base_path_manage']) {
    $app->setBasePath($settings['base_path_manage']);
}

// Run app
$app->run();
