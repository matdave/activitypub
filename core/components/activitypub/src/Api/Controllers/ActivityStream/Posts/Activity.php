<?php

namespace MatDave\ActivityPub\Api\Controllers\ActivityStream\Posts;

use MatDave\ActivityPub\{Api\Controllers\Restful,
    Api\Exceptions\RestfulException,
    Model\Activity as ActivityAlias,
    Model\Actor};
use MODX\Revolution\modResource;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class Activity extends Restful
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
            'id' => $owner . '/posts/' . $activity->id . '/activity',
            'type' => $activity->get('action'),
            'actor' => $owner,
            'published' => $activity->formatTime($activity->get('createdon')),
            "to" => [
                "https://www.w3.org/ns/activitystreams#Public"
            ],
            "cc" => [
                $owner . "/followers"
            ]
        ];

        if ($activity->get('resource')) {
            /**
             * @var $resource modResource
             */
            $resource = $activity->getOne('Resource');
            if (!empty($resource) && $resource->get('published') && !$resource->get('deleted')) {
                $noteDate = ($activity->get('action') == 'Create') ?
                    $resource->get('publishedon') :
                    $resource->get('editedon');

                $lang = $this->modx->getOption('cultureKey', [], '');
                $object = [
                    'id' => $owner . '/posts/' . $activity->get('id'),
                    'type' => $activity->get('type'),
                    'url' => $this->modx->makeUrl($activity->get('resource'), $resource->context_key, '', 'full'),
                    'name' => $resource->get('pagetitle'),
                    'summary' => $resource->get('description'),
                    'content' => $activity->parseContent(),
                    'published' =>  $activity->formatTime($noteDate),
                    'sensitive' => $activity->get('sensitive'),
                    'replies' => [
                        'id' => $owner . '/posts/' . $activity->get('id') . '/replies',
                        'type' => 'Collection',
                        'first' => [
                            "type" => "CollectionPage",
                            "next" => $owner . '/posts/' . $activity->get('id') . '/replies?page=true',
                            "partOf" => $owner . '/posts/' . $activity->get('id') . '/replies',
                            "items" => $activity->getReplies()
                        ]
                    ],
                    'likes' => [
                        'id' => $owner . '/posts/' . $activity->get('id') . '/likes',
                        'type' => 'Collection',
                        'totalItems' => $activity->get('likes'),
                    ],
                    'shares' => [
                        'id' => $owner . '/posts/' . $activity->get('id') . '/shares',
                        'type' => 'Collection',
                        'totalItems' => $activity->get('shares'),
                    ]
                ];
                if ($lang) {
                    $object['contentMap'] = [
                        $lang => $activity->parseContent(),
                    ];
                }
            } else {
                $object = [
                    'id' => $owner . '/posts/' . $activity->get('id'),
                    'type' => $activity->get('type'),
                    'published' =>  $activity->formatTime($activity->get('createdon')),
                ];
            }
            $response['object'] = $object;
        }

        return $this->respondWithItem($request, $response);
    }
}