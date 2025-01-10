<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiCodeNo;
use App\Libs\ApiBusUtil;
use App\Repositories\EventRepository;
use App\Services\EventService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends ApiBaseController
{
    public function __construct(
        private EventRepository $eventRepository,
        private EventService $eventService,
    ) {
    }

    /**
     * Event List
     * GET /api/v1/event/list
     *
     * @param Request $request
     */
    public function list(Request $request) {
        try {
            $params = $request->only([
                'event_name',
                'event_start_time_from',
                'event_start_time_to',
                'event_end_time_from',
                'event_end_time_to',
                'event_type_name',
            ]);

            $defaultSystemDate = Carbon::now()->startOfMonth()->format('Y/m/d');
            // Default value of search
            if (empty($params)) {
                $params['event_start_time_from'] = $defaultSystemDate;
            }

            // Get event list by params search
            $events = $this->eventRepository->search($params)->get();
            if ($events->isEmpty()) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::RECORD_NOT_EXISTS);
            }

            // Convert data for event list
            $events = $this->eventService->convertDataEventList($events);
            $events = $this->pagination($events)->toArray();

            return ApiBusUtil::successResponse($events);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Event Detail
     * GET /api/v1/event/detail
     *
     * @param Request $request
     * @param string|int $eventId
     */
    public function detail(Request $request, $eventId) {
        try {
            // Get event by event id
            $event = $this->eventRepository->getEventByEventId($eventId);
            if (empty($event)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::RECORD_NOT_EXISTS);
            }

            // Convert data for event detail
            $event = $this->eventService->convertDataEventDetail($event);

            return ApiBusUtil::successResponse($event);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Event Create
     * POST /api/v1/event/create
     *
     * @param Request $request
     */
    public function create(Request $request) {
        try {
            return ApiBusUtil::successResponse();
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Event Update
     * POST /api/v1/event/update
     *
     * @param Request $request
     * @param string|int $eventId
     */
    public function update(Request $request, $eventId) {
        try {
            return ApiBusUtil::successResponse();
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Event Delete
     * POST /api/v1/event/delete
     *
     * @param Request $request
     * @param string|int $eventId
     */
    public function delete(Request $request, $eventId) {
        try {
            return ApiBusUtil::successResponse();
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Event Import CSV
     * POST /api/v1/event/import/csv
     *
     * @param Request $request
     */
    public function importCsv(Request $request) {
        try {
            return ApiBusUtil::successResponse();
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Event Export CSV
     * POST /api/v1/event/export/csv
     *
     * @param Request $request
     */
    public function exportCsv(Request $request) {
        try {
            return ApiBusUtil::successResponse();
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }
}
