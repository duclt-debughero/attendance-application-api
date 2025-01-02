<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiCodeNo;
use App\Libs\ApiBusUtil;
use App\Repositories\EventTypeRepository;
use App\Requests\Api\EventType\AddRequest;
use App\Services\EventTypeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventTypeController extends ApiBaseController
{
    public function __construct(
        private EventTypeRepository $eventTypeRepository,
        private EventTypeService $eventTypeService
    ) {
    }

    /**
     * Event Type List
     * GET /api/v1/event-type/list
     *
     * @param Request $request
     */
    public function list(Request $request) {
        try {
            $params = $request->only(['type_name']);

            // Get event type list by params search
            $eventTypes = $this->eventTypeRepository->search($params);
            if ($eventTypes->isEmpty()) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::RECORD_NOT_EXISTS);
            }

            // Convert data for event type list
            $eventTypes = $this->eventTypeService->convertDataEventTypeList($eventTypes);
            $eventTypes = $this->pagination($eventTypes)->toArray();

            return ApiBusUtil::successResponse($eventTypes);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Event Type Detail
     * GET /api/v1/event-type/detail
     *
     * @param Request $request
     * @param string|int $eventTypeId
     */
    public function detail(Request $request, $eventTypeId) {
        try {
            // Get event type by event type id
            $eventType = $this->eventTypeRepository->getEventTypeByEventTypeId($eventTypeId);
            if (empty($eventType)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::URL_NOT_EXISTS);
            }

            // Convert data for event type detail
            $eventType = $this->eventTypeService->convertDataEventTypeDetail($eventType);

            return ApiBusUtil::successResponse($eventType);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Event Type Create
     * POST /api/v1/event-type/create
     *
     * @param AddRequest $request
     */
    public function create(AddRequest $request) {
        try {
            $params = $request->only(['type_name', 'description']);

            // Create event type
            $eventType = $this->eventTypeRepository->create($params);
            if (empty($eventType)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Convert data for event type detail
            $eventType = $this->eventTypeRepository->getEventTypeByEventTypeId($eventType->event_type_id);
            $eventType = $this->eventTypeService->convertDataEventTypeDetail($eventType);

            return ApiBusUtil::successResponse($eventType);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Event Type Update
     * POST /api/v1/event-type/update
     *
     * @param Request $request
     * @param string|int $eventTypeId
     */
    public function update(Request $request, $eventTypeId) {
        try {
            return ApiBusUtil::successResponse();
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Event Type Delete
     * POST /api/v1/event-type/delete
     *
     * @param Request $request
     * @param string|int $eventTypeId
     */
    public function delete(Request $request, $eventTypeId) {
        try {
            return ApiBusUtil::successResponse();
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Event Type Import CSV
     * POST /api/v1/event-type/import/csv
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
     * Event Type Export CSV
     * POST /api/v1/event-type/export/csv
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
