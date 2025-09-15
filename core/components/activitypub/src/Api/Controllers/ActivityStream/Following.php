<?php

namespace MatDave\ActivityPub\Api\Controllers\ActivityStream;

use MatDave\ActivityPub\{Api\Controllers\Restful, Api\Exceptions\RestfulException, Model\Actor as ActorAlias};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class Following extends Restful
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
        if ($params['page'] > 0) {
            $total = 0;
            $response = [
                "@context" => "https://www.w3.org/ns/activitystreams",
                "id" => $owner . "/following?page=" . $params['page'],
                "type" => "OrderedCollectionPage",
                "totalItems" => $total,
                "partOf" => $owner . "/following",
                "orderedItems" => []
            ];
        } else {
            $response = [
                "@context" => "https://www.w3.org/ns/activitystreams",
                "id" => $owner . "/following",
                "type" => "OrderedCollection",
                "totalItems" => 0,
                "first" => $owner . "/following?page=1"
            ];
        }

        return $this->respondWithItem($request, $response);
    }
}