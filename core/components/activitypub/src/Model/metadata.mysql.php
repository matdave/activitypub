<?php
$xpdo_meta_map = array (
    'version' => '3.0',
    'namespace' => 'MatDave\\ActivityPub\\Model',
    'namespacePrefix' => 'MatDave\\ActivityPub',
    'class_map' => 
    array (
        'MODX\\Revolution\\modUser' => 
        array (
            0 => 'MatDave\\ActivityPub\\Model\\User',
        ),
        'xPDO\\Om\\xPDOSimpleObject' => 
        array (
            0 => 'MatDave\\ActivityPub\\Model\\Actor',
            1 => 'MatDave\\ActivityPub\\Model\\Activity',
            2 => 'MatDave\\ActivityPub\\Model\\Follower',
        ),
    ),
);