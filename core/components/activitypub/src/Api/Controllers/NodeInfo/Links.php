<?php

namespace MatDave\ActivityPub\Api\Controllers\NodeInfo;

use MatDave\ActivityPub\Api\Controllers\Restful;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class Links extends Restful
{
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $basePath = rtrim($this->modx->config['site_url'], '/');
        $apiPath = $basePath . $this->config->get('base_path_manage', '/activitypub');
        $links = [
            [
                'rel' => 'http://nodeinfo.diaspora.software/ns/schema/2.0',
                'href' => $apiPath . '/nodeinfo/2.0',
            ]
        ];
        return $this->respondWithItem($request, ['links' => $links]);
    }

}