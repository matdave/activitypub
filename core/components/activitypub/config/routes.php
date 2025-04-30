<?php

declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use MatDave\ActivityPub\Api\Middleware\Restful;
use MatDave\ActivityPub\Api\Middleware\Secure;

return new class {
    const PARAMS = '{params:.*}';
    const ID = '{id:[0-9]+}';
    const ALIAS = '{alias:[a-zA-Z\-_0-9]+}';

    public function __invoke(App $app)
    {
        /** @var Restful $restful */
        $restful = $app->getContainer()->get(Restful::class);

    }
};
