<?php
namespace MatDave\ActivityPub\Model\Activity\mysql;

use xPDO\xPDO;

class Reply extends \MatDave\ActivityPub\Model\Activity\Reply
{

    public static $metaMap = array (
        'package' => 'MatDave\\ActivityPub\\Model\\',
        'version' => '3.0',
        'table' => 'ap_activity_reply',
        'tableMeta' => 
        array (
            'engine' => 'InnoDB',
        ),
        'fields' => 
        array (
            'activity' => 0,
            'createdon' => 0,
            'likes' => 0,
            'shares' => 0,
            'user' => '',
            'content' => '',
        ),
        'fieldMeta' => 
        array (
            'activity' => 
            array (
                'dbtype' => 'int',
                'attributes' => 'unsigned',
                'precision' => '10',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
            ),
            'createdon' => 
            array (
                'dbtype' => 'int',
                'precision' => '20',
                'phptype' => 'timestamp',
                'null' => false,
                'default' => 0,
            ),
            'likes' => 
            array (
                'dbtype' => 'int',
                'attributes' => 'unsigned',
                'precision' => '10',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
            ),
            'shares' => 
            array (
                'dbtype' => 'int',
                'attributes' => 'unsigned',
                'precision' => '10',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
            ),
            'user' => 
            array (
                'dbtype' => 'text',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
            'content' => 
            array (
                'dbtype' => 'text',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
        ),
        'aggregates' => 
        array (
            'Activity' => 
            array (
                'class' => 'Activity',
                'local' => 'activity',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
        ),
    );

}
