<?php
namespace MatDave\ActivityPub\Model;

use xPDO\xPDO;

/**
 * Class Activity
 *
 * @property string $type
 * @property integer $resource
 * @property integer $actor
 * @property boolean $sensitive
 * @property boolean $public
 * @property string $summary
 * @property string $content
 * @property string $createdon
 *
 * @package MatDave\ActivityPub\Model
 */
class Activity extends \xPDO\Om\xPDOSimpleObject
{
}
