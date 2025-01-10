<?php

namespace App\Services;

use App\Libs\DateUtil;

class EventService
{
    /**
     * Convert data for event detail
     *
     * @param object $event
     * @return mixed
     */
    public function convertDataEventDetail($event) {
        $result = [
            'event_id' => $event->event_id,
            'event_name' => $event->event_name,
            'event_start_time' => DateUtil::formatDefaultDateTime($event->event_start_time),
            'event_end_time' => DateUtil::formatDefaultDateTime($event->event_end_time),
            'location' => $event->location,
            'description' => $event->description,
            'event_type' => [],
        ];

        // Add event information if available
        if (isset($event->event_type_id)) {
            $result['event_type'] = [
                'event_type_id' => $event->event_type_id,
                'type_name' => $event->type_name,
            ];
        }

        return $result;
    }

    /**
     * Convert data for event list
     *
     * @param object $events
     * @return mixed
     */
    public function convertDataEventList($events) {
        $result = [];
        foreach ($events as $event) {
            $result[] = $this->convertDataEventDetail($event);
        }

        return $result;
    }
}
