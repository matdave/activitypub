<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

define('APP_ENV', $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'DEVELOPMENT');
$settings = (require __DIR__ . '/settings.php')(APP_ENV);

require_once(dirname(__FILE__, 5) . '/config.core.php');

define('MODX_API_MODE', true);
if (!defined('MODX_CORE_PATH')) {
    define('MODX_CORE_PATH', '/www/core/');
}
if (!defined('MODX_CONFIG_KEY')) {
    define('MODX_CONFIG_KEY', 'config');
}

require_once MODX_CORE_PATH . "vendor/autoload.php";

$containerBuilder = new \DI\ContainerBuilder();

if ($settings['di_compilation_path']) {
    $containerBuilder->enableCompilation($settings['di_compilation_path']);
}

(require __DIR__ . '/dependencies.php')($containerBuilder, $settings);

return $containerBuilder->build();
