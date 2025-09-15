<?php

namespace MatDave\ActivityPub\Processors\Actor;

use MatDave\ActivityPub\Model\Actor;
use MatDave\ActivityPub\Model\User;
use MODX\Revolution\Processors\Model\GetListProcessor;
use xPDO\Om\xPDOObject;

class GetList extends GetListProcessor
{
    use \MatDave\MODXPackage\Traits\Processors\GetList;

    public $classKey = Actor::class;
    public string $alias = 'Actor';
    public $languageTopics = ['activitypub'];
    public $defaultSortField = 'username';
    public $defaultSortDirection = 'ASC';

    public $leftJoin = [
        User::class => 'User'
    ];

    public function prepareRow(xPDOObject $object)
    {
        $object = parent::prepareRow($object);
        unset($object['privatekey']);
        return $object;
    }
}