<?php

namespace MatDave\ActivityPub\Api\Controllers\WebFinger;

use MatDave\ActivityPub\Api\Controllers\Restful;
use MatDave\ActivityPub\Api\Exceptions\RestfulException;
use MatDave\ActivityPub\Model\Actor;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Resource extends Restful
{
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $basePath = rtrim($this->modx->config['site_url'], '/');
        $apiPath = $basePath . $this->config->get('base_path_manage', '/activitypub');

        $params = $this->getParams($request, [], [], []);

        $resource = $params['resource'];

        if (empty($resource)) {
            throw RestfulException::badRequest(['error' => 'Resource is required']);
        }

        // check if $resource starts with acct:
        if (preg_match('/^acct:/', $resource)) {
            $username = substr($resource, 5);
            $parts = explode('@', $username);
            $host = array_pop($parts);
            $actor = $parts[0];
        } elseif (preg_match('/^http(s|)?:\/\//', $resource)) {
            // check if $resource starts with https:// or http://
            $url = parse_url($resource);
            $parts = explode('/', $url['path']);
            $actor = array_pop($parts);
            if (preg_match('/^@/', $actor)) {
                $actor = substr($actor, 1);
            }
            $host = $url['host'];
            $username = $actor . '@' . $host;
        } else {
            throw RestfulException::badRequest(['error' => 'Invalid resource format']);
        }

        if (empty($host) || $host !== parse_url($basePath)['host']) {
            throw RestfulException::badRequest(['error' => 'Invalid host']);
        }

        $actorObj = $this->modx->getObject(Actor::class, ['username' => $actor]);
        if (!$actorObj) {
            throw RestfulException::notFound(['error' => 'Actor not found']);
        }

        $response = [
            "subject" => "acct:" . $username,
            "aliases" => [
                $apiPath . "/users/" . $actor,
            ],
            "links" => [
                [
                    "rel" => "self",
                    "type" => "application/activity+json",
                    "href" => $apiPath . "/users/" . $actorObj->get('username')
                ],
            ]
        ];

        if ($actorObj->get('icon')) {
            $response['links'][] = [
                "rel" => "http://webfinger.net/rel/avatar",
                "type" => $actorObj->getIconMime(),
                "href" => $actorObj->get('icon'),
            ];
        }

        if ($actorObj->get('profile')) {
            $response['links'][] = [
                "rel" => "http://webfinger.net/rel/profile-page",
                "type" => "text/html",
                "href" => $actorObj->get('profile'),
            ];
        }

        return $this->respondWithItem($request, $response);
    }
}