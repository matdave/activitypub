<?php

namespace MatDave\ActivityPub\Api\Controllers\ActivityStream\Posts;

use MatDave\ActivityPub\{Api\Controllers\Restful,
    Api\Exceptions\RestfulException,
    Model\Actor,
    Model\Activity as ActivityAlias};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class Likes extends Restful
{
    /**
     * @throws RestfulException
     */
    public function get(ServerRequestInterface $request): ResponseInterface
    {

        $basePath = rtrim($this->modx->config['site_url'], '/');
        $apiPath = $basePath . $this->config->get('base_path_manage', '/activitypub');

        $condition = ['username' => $request->getAttribute('alias')];

        $actor = $this->modx->getObject(Actor::class, $condition);
        if (!$actor) {
            throw RestfulException::notFound();
        }
        $owner = $apiPath . '/users/' . $actor->get('username');
        $id = $request->getAttribute('id');
        $activity = $this->modx->getObject(ActivityAlias::class, ['id' => $id, 'actor' => $actor->id]);

        if (empty($activity)) {
            throw RestfulException::notFound();
        }

        $response = [
            "@context" => [
                "https://www.w3.org/ns/activitystreams"
            ],
            'id' => $owner . '/posts/' . $activity->get('id') . '/likes',
            'type' => 'Collection',
            'totalItems' => $activity->get('likes'),
        ];

        return $this->respondWithItem($request, $response);
    }

}