<?php

declare(strict_types=1);

use MatDave\ActivityPub\Api\Controllers\ActivityStream\Actor;
use MatDave\ActivityPub\Api\Controllers\NodeInfo\Links as NodeLinks;
use MatDave\ActivityPub\Api\Controllers\NodeInfo\NodeInfo;
use MatDave\ActivityPub\Api\Controllers\WebFinger\Resource as WebFinger;
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

        $app->group(
            '/nodeinfo',
            function (RouteCollectorProxy $group) use ($restful): void {
                $group->get('/2.0',
                    NodeInfo::class
                );
                $group->get('[/]',
                    NodeLinks::class
                );
            }
        );

        $app->get('/webfinger[/'.self::PARAMS.']',
            WebFinger::class
        );

        $app->group(
            '/users',
            function (RouteCollectorProxy $group) use ($restful): void {
                $group->get('/' . self::ALIAS, Actor::class);
            }
        );
    }
};
