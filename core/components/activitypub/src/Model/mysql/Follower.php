<?php
namespace MatDave\ActivityPub\Model\mysql;

use xPDO\xPDO;

class Follower extends \MatDave\ActivityPub\Model\Follower
{

    public static $metaMap = array (
        'package' => 'MatDave\\ActivityPub\\Model\\',
        'version' => '3.0',
        'table' => 'ap_follower',
        'tableMeta' => 
        array (
            'engine' => 'InnoDB',
        ),
        'fields' => 
        array (
            'actor' => 0,
            'user' => '',
        ),
        'fieldMeta' => 
        array (
            'actor' => 
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
        ),
        'indexes' => 
        array (
            'actor' => 
            array (
                'alias' => 'actor',
                'primary' => false,
                'unique' => false,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'actor' => 
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => true,
                    ),
                ),
            ),
        ),
        'aggregates' => 
        array (
            'Actor' => 
            array (
                'class' => 'Actor',
                'local' => 'actor',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
        ),
    );

}
