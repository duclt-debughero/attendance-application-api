<?php

namespace App\Repositories;

use App\Libs\DateUtil;
use App\Models\Event;
use Exception;
use Illuminate\Support\Facades\Log;

class EventRepository extends BaseRepository
{
    public function getModel() {
        return Event::class;
    }

    /**
     * Get query event
     *
     * @param array $columns
     * @return mixed
     */
    public function getQueryEvent($columns = []) {
        $defaultColumns = [
            'event.event_id',
            'event.event_name',
            'event.event_start_time',
            'event.event_end_time',
            'event.location',
            'event.description',
            'event_type.event_type_id',
            'event_type.type_name',
        ];

        $query = Event::query()
            ->select(array_merge($defaultColumns, $columns))
            ->leftJoin('event_type', function ($join) {
                $join
                    ->on('event.event_type_id', '=', 'event_type.event_type_id')
                    ->whereValidDelFlg();
            })
            ->whereValidDelFlg();

        return $query;
    }

    /**
     * Search for event
     *
     * @param array $params
     * @return mixed
     */
    public function search($params = []) {
        try {
            $query = $this->getQueryEvent();

            // Search for event name
            if (isset($params['event_name'])) {
                $query->where('event.event_name', 'like', "%{$params['event_name']}%");
            }

            // Search for event start time from
            if (isset($params['event_start_time_from'])) {
                $eventStartTimeFrom = DateUtil::formatDateTime($params['event_start_time_from'], 'y-m-d');
                $query->whereDate('event.event_start_time', '>=', $eventStartTimeFrom);
            }

            // Search for event start time to
            if (isset($params['event_start_time_to'])) {
                $eventStartTimeTo = DateUtil::formatDateTime($params['event_start_time_to'], 'y-m-d');
                $query->whereDate('event.event_start_time', '<=', $eventStartTimeTo);
            }

            // Search for event end time from
            if (isset($params['event_end_time_from'])) {
                $eventEndTimeFrom = DateUtil::formatDateTime($params['event_end_time_from'], 'y-m-d');
                $query->whereDate('event.event_end_time', '>=', $eventEndTimeFrom);
            }

            // Search for event end time to
            if (isset($params['event_end_time_to'])) {
                $eventEndTimeTo = DateUtil::formatDateTime($params['event_end_time_to'], 'y-m-d');
                $query->whereDate('event.event_end_time', '<=', $eventEndTimeTo);
            }

            // Search for event type name
            if (isset($params['type_name'])) {
                $query->where('event_type.type_name', 'like', "%{$params['type_name']}%");
            }

            return $query;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Get event by event id
     *
     * @param string|int $eventId
     * @return mixed
     */
    public function getEventByEventId($eventId) {
        try {
            $query = $this->getQueryEvent()->where('event.event_id', $eventId);

            return $query->first();
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }
}
