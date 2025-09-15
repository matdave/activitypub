<?php

namespace MatDave\ActivityPub\Api\Controllers\ActivityStream;

use MatDave\ActivityPub\{Api\Controllers\Restful,
    Api\Exceptions\RestfulException,
    Model\Actor as ActorAlias,
    Model\Follower};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class Followers extends Restful
{
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $defaultParams = [
            'page' => 0,
        ];
        $paramsCast = [
            'page' => 'int'
        ];
        $params = $this->getParams($request, $defaultParams, $paramsCast, []);
        $basePath = rtrim($this->modx->config['site_url'], '/');
        $apiPath = $basePath . $this->config->get('base_path_manage', '/activitypub');

        $condition = ['username' => $request->getAttribute('alias')];

        $actor = $this->modx->getObject(ActorAlias::class, $condition);
        if (!$actor) {
            throw RestfulException::notFound();
        }
        $owner = $apiPath . '/users/' . $actor->get('username');
        $c = $this->modx->newQuery(Follower::class);
        $c->where([
            'actor' => $actor->id,
            'approved' => 1
        ]);
        $total = $this->modx->getCount(Follower::class, $c);
        if ($params['page'] > 0) {
            $limit = 10;
            $response = [
                "@context" => "https://www.w3.org/ns/activitystreams",
                "id" => $owner . "/followers",
                "type" => "OrderedCollectionPage",
                "totalItems" => $total,
                "partOf" => $owner . "/followers",
            ];
            if ($total < ($limit * $params['page'])) {
                $response['next'] = $owner . "/following?page=" . ($params['page'] + 1);
            }
            if ($params['page'] > 1) {
                $response['prev'] = $owner . "/following?page=" . ($params['page'] - 1);
            }
            $response['orderedItems'] = [];
            $c->sortby('createdon', 'ASC');
            $c->limit($limit, $limit * ($params['page'] - 1));
            $collection = $this->modx->getIterator(Follower::class, $c);
            foreach ($collection as $follower) {
                $response['orderedItems'][] = $follower->get('user');
            }
        } else {
            $response = [
                "@context" => "https://www.w3.org/ns/activitystreams",
                "id" => $owner . "/followers",
                "type" => "OrderedCollection",
                "totalItems" => $total,
                "first" => $owner . "/followers?page=1"
            ];
        }

        return $this->respondWithItem($request, $response);
    }
}