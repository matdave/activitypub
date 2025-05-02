<?php

namespace MatDave\ActivityPub\Processors\Combo;

use MODX\Revolution\Processors\Processor;

class ObjectType extends Processor
{
    public function getLanguageTopics()
    {
        return ['activitypub:default'];
    }

    public function process()
    {
        $availableTypes = [
            "Article",
            "Audio",
            "Document",
            "Event",
            "Image",
            "Note",
            "Page",
            "Place",
            "Profile",
            "Relationship",
            "Tombstone",
            "Video",
            "Mention",
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