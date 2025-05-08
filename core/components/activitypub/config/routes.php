<?php

declare(strict_types=1);

use MatDave\ActivityPub\Api\Controllers\ActivityStream\Actor;
use MatDave\ActivityPub\Api\Controllers\ActivityStream\Followers;
use MatDave\ActivityPub\Api\Controllers\ActivityStream\Following;
use MatDave\ActivityPub\Api\Controllers\ActivityStream\Inbox;
use MatDave\ActivityPub\Api\Controllers\ActivityStream\Outbox;
use MatDave\ActivityPub\Api\Controllers\ActivityStream\Posts\Activity;
use MatDave\ActivityPub\Api\Controllers\ActivityStream\Posts\Post;
use MatDave\ActivityPub\Api\Controllers\NodeInfo\Links as NodeLinks;
use MatDave\ActivityPub\Api\Controllers\NodeInfo\NodeInfo;
use MatDave\ActivityPub\Api\Controllers\WebFinger\Resource as WebFinger;
use MatDave\ActivityPub\Api\Controllers\WebFinger\Subscribe;
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
        $app->get('/authorize_interactions[/'.self::PARAMS.']',
            Subscribe::class
        );
        $app->any('/inbox', Inbox::class)->add(
            $restful->withAllowedMethods(['GET', 'POST'])
        );

        $app->group(
            '/users',
            function (RouteCollectorProxy $group) use ($restful): void {
                $group->any('/' . self::ALIAS . '/inbox', Inbox::class)->add(
                    $restful->withAllowedMethods(['GET', 'POST'])
                );
                $group->get('/' . self::ALIAS. '/following[/' . self::PARAMS.']', Following::class);
                $group->get('/' . self::ALIAS . '/followers[/' . self::PARAMS.']', Followers::class);
                $group->get('/' . self::ALIAS . '/posts/' . self::ID.'/activity', Activity::class);
                $group->get('/' . self::ALIAS . '/posts[/' . self::ID.']', Post::class);
                $group->get('/' . self::ALIAS . '/outbox[/' . self::PARAMS.']', Outbox::class);
                $group->get('/' . self::ALIAS, Actor::class);
            }
        );
    }
};
