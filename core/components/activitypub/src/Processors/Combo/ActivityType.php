<?php

namespace MatDave\ActivityPub\Processors\Combo;

use MODX\Revolution\Processors\Processor;

class ActivityType extends Processor
{
    public function getLanguageTopics()
    {
        return ['activitypub:default'];
    }

    public function process()
    {
        $availableTypes = [
            "Accept",
            "Add",
            "Announce",
            "Arrive",
            "Block",
            "Create",
            "Delete",
            "Dislike",
            "Flag",
            "Follow",
            "Ignore",
            "Invite",
            "Join",
            "Leave",
            "Like",
            "Move",
            "Offer",
            "Question",
            "Reject",
            "Read",
            "Remove",
            "TentativeReject",
            "TentativeAccept",
            "Travel",
            "Undo",
            "Update",
            "View",
        ];
        
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $availableTypes = array_filter($availableTypes, function ($directive) use ($query) {
                return stripos($directive, $query) !== false;
            });
        }
        $start = $this->getProperty('start', 0);
        $limit = $this->getProperty('limit', 0);
        $total = count($availableTypes);
        if ($limit > 0) {
            $availableTypes = array_slice($availableTypes, $start, $limit);
        }
        $typeFormat = [];

        foreach ($availableTypes as $directive) {
            $typeFormat[] = [
                'value' => $directive,
            ];
        }

        return $this->outputArray(array_values($typeFormat), $total);
    }
}