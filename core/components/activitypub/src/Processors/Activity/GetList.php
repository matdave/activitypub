<?php

namespace MatDave\ActivityPub\Processors\Activity;

use MatDave\ActivityPub\Model\Activity;
use MatDave\ActivityPub\Model\Actor;
use MODX\Revolution\modResource;
use MODX\Revolution\Processors\Model\GetListProcessor;
use xPDO\Om\xPDOObject;

class GetList extends GetListProcessor
{
    use \MatDave\MODXPackage\Traits\Processors\GetList;

    public $classKey = Activity::class;
    public string $alias = 'Activity';
    public $languageTopics = ['activitypub'];
    public $defaultSortField = 'createdon';
    public $defaultSortDirection = 'DESC';

    public $leftJoin = [
        Actor::class => 'Actor',
        modResource::class => 'Resource'
    ];

    public function prepareRow(xPDOObject $object)
    {
        $object = parent::prepareRow($object);
        unset($object['actor_privatekey']);
        return $object;
    }
}