<?php

declare(strict_types=1);

use Monolog\Logger;

return static function (string $appEnv) {
    $settings = [
        'app_env' => $appEnv,
        'di_compilation_path' => __DIR__ . '/../var/compiled',
        'display_error_details' => false,
        'log_errors' => true,
        'base_path_app' => '',
        'base_path_manage' => '/activitypub',
        'logger' => [
            'name' => 'api',
            'path' => dirname(__FILE__, 2) . '/var/log/app.log',
            'level' => Logger::ERROR
        ],
    ];

    if (is_readable(__DIR__ . '/local.php')) {
        $localSettings = require __DIR__ . '/local.php';
        $settings = array_merge($settings, $localSettings);
    }

    if ($settings['app_env'] === 'DEVELOPMENT' || $settings['app_env'] === 'TEST') {
        $settings['di_compilation_path'] = '';
        $settings['display_error_details'] = true;
        $settings['logger']['level'] = Logger::DEBUG;
    }

    return $settings;
};
