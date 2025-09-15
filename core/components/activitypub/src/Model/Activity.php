<?php
namespace MatDave\ActivityPub\Model;

use DateTime;
use DateTimeZone;
use MODX\Revolution\modResource;

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

    public function parseContent(): string
    {
        $content = '';
        $resource = $this->xpdo->getObject(modResource::class, $this->resource);
        if (!empty($resource)) {
            $this->xpdo->resource = $resource;
            $this->xpdo->resourceIdentifier = $resource->get('id');
            $this->xpdo->elementCache = [];
            $content = $resource->parseContent();
        }
        return $content;
    }

    public function formatTime(string $date): string
    {
        $timeZone = new DateTimeZone($this->xpdo->getOption('date_timezone', [], 'UTC'));
        $dateTime = new DateTime();
        $dateTime->setTimestamp(strtotime($date));
        $dateTime->setTimezone($timeZone);
        return $dateTime->format($dateTime::ATOM);
    }

    public function getReplies($total = 10, $start = 0)
    {
        $c = $this->xpdo->newQuery(Activity\Reply::class);
        $c->where([
            'activity' => $this->id,
        ]);
        $c->sortby('createdon', 'DESC');
        $c->limit($total, $start);
        $collection = $this->xpdo->getCollection(Activity\Reply::class, $c);
        $response = [];
        foreach ($collection as $reply) {
            $response[] = $reply->toArray();
        }
        return $response;
    }
}
