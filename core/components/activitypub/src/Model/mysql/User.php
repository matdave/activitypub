<?php
namespace MatDave\ActivityPub\Model\mysql;

use xPDO\xPDO;

class User extends \MatDave\ActivityPub\Model\User
{

    public static $metaMap = array (
        'package' => 'MatDave\\ActivityPub\\Model\\',
        'version' => '3.0',
        'extends' => 'MODX\\Revolution\\modUser',
        'tableMeta' => 
        array (
            'engine' => 'InnoDB',
        ),
        'fields' => 
        array (
        ),
        'fieldMeta' => 
        array (
        ),
        'composites' => 
        array (
            'Actor' => 
            array (
                'class' => 'APUser',
                'local' => 'id',
                'foreign' => 'user',
                'cardinality' => 'one',
                'owner' => 'local',
            ),
        ),
    );

}
