<?php

namespace MatDave\ActivityPub\Api\Controllers\NodeInfo;

use MatDave\ActivityPub\{Api\Controllers\Restful, Model\Activity, Model\Actor};
use MODX\Revolution\modResource;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class NodeInfo extends Restful
{
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->modx->version) {
            $this->modx->getVersionData();
        }
        $version = $this->modx->version['full_version'];
        $nodeName = $this->modx->config['activitypub.nodeName'];
        if (empty($nodeName)) {
            $nodeName = $this->modx->config['site_name'];
        }
        $nodeDescription = $this->modx->config['activitypub.nodeDescription'];
        if (empty($nodeDescription)) {
            $homepage = $this->modx->getObject(
                modResource::class,
                ['id' => $this->modx->config['site_start']]
            );
            if ($homepage) {
                $nodeDescription = $homepage->get('description');
            }
        }
        $localPosts = $this->modx->getCount(
            Activity::class,
            ['action:IN' => ['Create']]
        );
        // active month
        $c = $this->modx->newQuery(Activity::class);
        $c->where(
            [
                'actor:>' => 0,
                'createdon:>' => strtotime('-1 month'),
            ]
        );
        $c->groupby('actor');

        $activeMonth = $this->modx->getCount(
            Activity::class,
            $c
        );
        // active half year
        $c = $this->modx->newQuery(Activity::class);
        $c->where(
            [
                'actor:>' => 0,
                'createdon:>' => strtotime('-6 month'),
            ]
        );
        $c->groupby('actor');
        $activeHalfyear =  $this->modx->getCount(
            Activity::class,
            $c
        );
        $nodeInfo = [
            "version" => "2.0",
            "software" => [
                "name" => "modx",
                "version" => $version,
            ],
            "protocols" => [
                "activitypub"
            ],
            "services" => [
                "outbound" => [],
                "inbound" => []
            ],
            "usage" => [
                "users" => [
                    "total" => $this->modx->getCount(Actor::class),
                    "activeMonth" => $activeMonth,
                    "activeHalfyear" => $activeHalfyear
                ],
                "localPosts" => $localPosts
            ],
            "openRegistration" => false,
            "metadata" => [
                "nodeName" => $nodeName,
                "nodeDescription" => $nodeDescription,
            ]
        ];
        return $this->respondWithItem($request, $nodeInfo);
    }

}