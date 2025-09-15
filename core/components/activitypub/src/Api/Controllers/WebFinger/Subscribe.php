<?php

namespace MatDave\ActivityPub\Api\Controllers\WebFinger;

use MatDave\ActivityPub\Api\Controllers\Restful;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class Subscribe extends Restful
{
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $params = $this->getParams($request, [], [], []);
        $uri = $params['uri'];
        /** Not really sure what this is supposed to do? */
        $this->modx->log(1, "ActivityPub Subscribe URI: " . $uri);

        return $this->respondWithItem($request, []);
    }
}