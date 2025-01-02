<?php

namespace App\Repositories;

use App\Models\EventType;
use Exception;
use Illuminate\Support\Facades\Log;

class EventTypeRepository extends BaseRepository
{
    public function getModel() {
        return EventType::class;
    }

    /**
     * Get query event type
     *
     * @param array $columns
     * @return mixed
     */
    public function getQueryEventType($columns = []) {
        $defaultColumns = [
            'event_type.event_type_id',
            'event_type.type_name',
            'event_type.description',
            'mst_user.user_name as last_updated_by',
            'event_type.updated_at as last_updated_at',
        ];

        $query = EventType::query()
            ->select(array_merge($defaultColumns, $columns))
            ->leftJoin('mst_user', function ($query) {
                $query
                    ->on('event_type.updated_by', '=', 'mst_user.user_id')
                    ->whereValidDelFlg();
            })
            ->whereValidDelFlg();

        return $query;
    }

    /**
     * Search for event type
     *
     * @param array $params
     * @return mixed
     */
    public function search($params = []) {
        try {
            $query = $this->getQueryEventType();

            // Search for event type name
            if (isset($params['type_name'])) {
                $query->where('event_type.type_name', 'like', "%{$params['type_name']}%");
            }

            return $query->get();
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Get event type by event type id
     *
     * @param string|int $eventTypeId
     * @return mixed
     */
    public function getEventTypeByEventTypeId($eventTypeId) {
        try {
            $query = $this->getQueryEventType()->where('event_type.event_type_id', $eventTypeId);

            return $query->first();
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }
}
