<?php

namespace App\Services;

use App\Libs\{
    DateUtil,
    EncryptUtil,
};

class EventTypeService
{
    /**
     * Convert data for event type detail
     *
     * @param object $eventType
     * @return mixed
     */
    public function convertDataEventTypeDetail($eventType) {
        return [
            'event_type_id' => $eventType->event_type_id,
            'type_name' => $eventType->type_name,
            'description' => $eventType->description,
            'last_updated_by' => EncryptUtil::decryptAes256($eventType->last_updated_by),
            'last_updated_at' => DateUtil::formatDefaultDateTime($eventType->last_updated_at),
        ];
    }

    /**
     * Convert data for event type list
     *
     * @param object $eventTypes
     * @return mixed
     */
    public function convertDataEventTypeList($eventTypes) {
        $result = [];
        foreach ($eventTypes as $eventType) {
            $result[] = $this->convertDataEventTypeDetail($eventType);
        }

        return $result;
    }
}
