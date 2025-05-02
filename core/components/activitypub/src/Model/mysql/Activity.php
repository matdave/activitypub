<?php
namespace MatDave\ActivityPub\Model\mysql;

use xPDO\xPDO;

class Activity extends \MatDave\ActivityPub\Model\Activity
{

    public static $metaMap = array (
        'package' => 'MatDave\\ActivityPub\\Model\\',
        'version' => '3.0',
        'table' => 'ap_activity',
        'tableMeta' => 
        array (
            'engine' => 'InnoDB',
        ),
        'fields' => 
        array (
            'type' => NULL,
            'resource' => 0,
            'actor' => 0,
            'sensitive' => 0,
            'public' => 0,
            'summary' => '',
            'content' => NULL,
            'createdon' => 0,
        ),
        'fieldMeta' => 
        array (
            'type' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '15',
                'phptype' => 'string',
                'null' => false,
            ),
            'resource' => 
            array (
                'dbtype' => 'int',
                'attributes' => 'unsigned',
                'precision' => '10',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
            ),
            'actor' => 
            array (
                'dbtype' => 'int',
                'attributes' => 'unsigned',
                'precision' => '10',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
            ),
            'sensitive' => 
            array (
                'dbtype' => 'tinyint',
                'attributes' => 'unsigned',
                'precision' => '1',
                'phptype' => 'boolean',
                'null' => false,
                'default' => 0,
            ),
            'public' => 
            array (
                'dbtype' => 'tinyint',
                'attributes' => 'unsigned',
                'precision' => '1',
                'phptype' => 'boolean',
                'null' => false,
                'default' => 0,
            ),
            'summary' => 
            array (
                'dbtype' => 'text',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
                'index' => 'fulltext',
                'indexgrp' => 'content_ft_idx',
            ),
            'content' => 
            array (
                'dbtype' => 'mediumtext',
                'phptype' => 'string',
                'index' => 'fulltext',
                'indexgrp' => 'content_ft_idx',
            ),
            'createdon' => 
            array (
                'dbtype' => 'int',
                'precision' => '20',
                'phptype' => 'timestamp',
                'null' => false,
                'default' => 0,
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
            'resource' => 
            array (
                'alias' => 'resource',
                'primary' => false,
                'unique' => false,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'resource' => 
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => true,
                    ),
                ),
            ),
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
            'public' => 
            array (
                'alias' => 'public',
                'primary' => false,
                'unique' => false,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'public' => 
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => true,
                    ),
                ),
            ),
            'content_ft_idx' => 
            array (
                'alias' => 'content_ft_idx',
                'primary' => false,
                'unique' => false,
                'type' => 'FULLTEXT',
                'columns' => 
                array (
                    'summary' => 
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => true,
                    ),
                    'content' => 
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
            'Resource' => 
            array (
                'class' => 'MODX\\Revolution\\modResource',
                'local' => 'resource',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
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
