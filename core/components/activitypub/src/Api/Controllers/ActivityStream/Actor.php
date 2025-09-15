<?php

namespace MatDave\ActivityPub\Api\Controllers\ActivityStream;

use MatDave\ActivityPub\{Api\Controllers\Restful, Api\Exceptions\RestfulException, Model\Actor as ActorAlias};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class Actor extends Restful
{
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $basePath = rtrim($this->modx->config['site_url'], '/');
        $apiPath = $basePath . $this->config->get('base_path_manage', '/activitypub');

        $condition = ['username' => $request->getAttribute('alias')];

        $actor = $this->modx->getObject(ActorAlias::class, $condition);
        if (!$actor) {
            throw RestfulException::notFound();
        }
        $owner = $apiPath . '/users/' . $actor->get('username');
        $response = [
            '@context' => [
                'https://www.w3.org/ns/activitystreams',
                'http://activitypub.kr/api/v1',
                [
                    'manuallyApprovesFollowers' => 'as:manuallyApprovesFollowers'
                ]
            ],
            'id' => $owner,
            'type' => $actor->get('type'),
            'following' => $owner.'/following',
            'followers' => $owner.'/followers',
            'inbox' => $owner.'/inbox',
            'outbox' => $owner.'/outbox',
            'preferredUsername' => $actor->get('username'),
            'name' => $actor->get('fullname'),
            'manuallyApprovesFollowers' => (bool) $actor->get('manuallyApprovesFollowers'),
            'published' => date('c', strtotime($actor->get('createdon'))),
            'publicKey' => [
                'id' => $owner.'#main-key',
                'owner' => $owner,
                'publicKeyPem' => $actor->getPublicKey()
            ],
        ];
        $profile = $actor->get('profile');
        if ($profile) {
            $response['url'] = $profile;
        }
        $icon = $actor->get('icon');
        if ($icon) {
            $response['icon'] = [
                'type' => 'Image',
                'mediaType' => $actor->getIconMime(),
                'url' => $icon
            ];
        }

        return $this->respondWithItem($request, $response);
    }
}