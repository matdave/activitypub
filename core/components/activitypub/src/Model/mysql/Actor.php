<?php
namespace MatDave\ActivityPub\Model\mysql;

use xPDO\xPDO;

class Actor extends \MatDave\ActivityPub\Model\Actor
{

    public static $metaMap = array (
        'package' => 'MatDave\\ActivityPub\\Model\\',
        'version' => '3.0',
        'table' => 'ap_actor',
        'tableMeta' => 
        array (
            'engine' => 'InnoDB',
        ),
        'fields' => 
        array (
            'type' => NULL,
            'user' => 0,
            'manuallyApprovesFollowers' => 0,
            'username' => '',
            'fullname' => '',
            'profile' => '',
            'icon' => '',
            'createdon' => 0,
            'privatekey' => NULL,
        ),
        'fieldMeta' => 
        array (
            'type' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '12',
                'phptype' => 'string',
                'null' => false,
            ),
            'user' => 
            array (
                'dbtype' => 'int',
                'attributes' => 'unsigned',
                'precision' => '10',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
            ),
            'manuallyApprovesFollowers' => 
            array (
                'dbtype' => 'tinyint',
                'attributes' => 'unsigned',
                'precision' => '1',
                'phptype' => 'boolean',
                'null' => false,
                'default' => 0,
            ),
            'username' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '100',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
                'index' => 'unique',
            ),
            'fullname' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '100',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
            'profile' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
            'icon' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
            'createdon' => 
            array (
                'dbtype' => 'int',
                'precision' => '20',
                'phptype' => 'timestamp',
                'null' => false,
                'default' => 0,
            ),
            'privatekey' => 
            array (
                'dbtype' => 'text',
                'phptype' => 'string',
            ),
        ),
        'indexes' => 
        array (
            'type' => 
            array (
                'alias' => 'type',
                'primary' => false,
                'unique' => false,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'type' => 
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => true,
                    ),
                ),
            ),
            'user' => 
            array (
                'alias' => 'user',
                'primary' => false,
                'unique' => true,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'user' => 
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
            'username' => 
            array (
                'alias' => 'username',
                'primary' => false,
                'unique' => true,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'username' => 
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
        ),
        'composites' => 
        array (
            'Activities' => 
            array (
                'class' => 'Activity',
                'local' => 'id',
                'foreign' => 'actor',
                'cardinality' => 'many',
                'owner' => 'local',
            ),
            'Followers' => 
            array (
                'class' => 'Follower',
                'local' => 'id',
                'foreign' => 'actor',
                'cardinality' => 'many',
                'owner' => 'local',
            ),
        ),
        'aggregates' => 
        array (
            'User' => 
            array (
                'class' => 'APUser',
                'local' => 'user',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
        ),
    );

}
