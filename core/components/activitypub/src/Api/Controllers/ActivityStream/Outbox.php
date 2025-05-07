<?php

namespace MatDave\ActivityPub\Api\Controllers\ActivityStream;

use MatDave\ActivityPub\Api\Controllers\Restful;
use MatDave\ActivityPub\Api\Exceptions\RestfulException;
use MatDave\ActivityPub\Model\Activity;
use MODX\Revolution\modResource;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Outbox extends Restful
{
    /**
     * @throws \DateInvalidTimeZoneException
     * @throws RestfulException
     */
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

        $actor = $this->modx->getObject(\MatDave\ActivityPub\Model\Actor::class, $condition);
        if (!$actor) {
            throw RestfulException::notFound();
        }
        $owner = $apiPath . '/users/' . $actor->get('username');
        $c = $this->modx->newQuery(Activity::class);
        $c->where([
            'actor' => $actor->id,
            'public' => true,
            'action:IN' => [
                'Create',
                'Update',
                'Delete'
            ]
        ]);
        $total = $this->modx->getCount(Activity::class, $c);
        if ($params['page'] > 0) {
            $limit = 10;
            $response = [
                "@context" => [
                    "https://www.w3.org/ns/activitystreams",
                    [
                        "ostatus" => "http://ostatus.org#",
                        "atomUri" => "ostatus:atomUri",
                        "sensitive" => "as:sensitive",
                    ]
                ],
                "id" => $owner . "/outbox?page=" . $params['page'],
                "type" => "OrderedCollectionPage",
                "totalItems" => $total,
                "partOf" => $owner . "/outbox",
            ];
            if ($total < ($limit * $params['page'])) {
                $response['next'] = $owner . "/outbox?page=" . ($params['page'] + 1);
            }
            if ($params['page'] > 1) {
                $response['prev'] = $owner . "/outbox?page=" . ($params['page'] - 1);
            }
            $response['orderedItems'] = [];
            $c->sortby('createdon', 'ASC');
            $c->limit($limit, $limit * ($params['page'] - 1));
            $collection = $this->modx->getIterator(Activity::class, $c);
            foreach ($collection as $activity) {
                $status = [
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
                    $status['object'] = $object;
                }

                $response['orderedItems'][] = $status;
            }
        } else {
            $response = [
                "@context" => "https://www.w3.org/ns/activitystreams",
                "id" => $owner . "/outbox",
                "type" => "OrderedCollection",
                "totalItems" => $total,
                "first" => $owner . "/outbox?page=1"
            ];
        }

        return $this->respondWithItem($request, $response);
    }
}