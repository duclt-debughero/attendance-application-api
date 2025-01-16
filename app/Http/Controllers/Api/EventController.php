<?php

namespace App\Http\Controllers\Api;

use App\Enums\{
    ApiCodeNo,
    ApiStatusCode,
};
use App\Libs\{
    ApiBusUtil,
    ConfigUtil,
};
use App\Repositories\EventRepository;
use App\Requests\Api\Event\{
    AddRequest,
    EditRequest,
    ImportCsvRequest,
    ListRequest,
};
use App\Services\{
    CsvFileExportService,
    EventService,
};
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, Response};

class EventController extends ApiBaseController
{
    public function __construct(
        private EventRepository $eventRepository,
        private CsvFileExportService $csvFileExportService,
        private EventService $eventService,
    ) {
    }

    /**
     * Event List
     * GET /api/v1/event/list
     *
     * @param ListRequest $request
     */
    public function list(ListRequest $request) {
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
     * @param AddRequest $request
     */
    public function create(AddRequest $request) {
        try {
            $params = $request->only([
                'event_name',
                'event_start_time',
                'event_end_time',
                'location',
                'description',
                'event_type_id',
            ]);

            // Create event
            $event = $this->eventRepository->create($params);
            if (empty($event)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Convert data for event detail
            $event = $this->eventRepository->getEventByEventId($event->event_id);
            $event = $this->eventService->convertDataEventDetail($event);

            return ApiBusUtil::successResponse($event);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Event Update
     * POST /api/v1/event/update
     *
     * @param EditRequest $request
     * @param string|int $eventId
     */
    public function update(EditRequest $request, $eventId) {
        try {
            $params = $request->only([
                'event_name',
                'event_start_time',
                'event_end_time',
                'location',
                'description',
                'event_type_id',
            ]);

            // Get event by event id
            $event = $this->eventRepository->getEventByEventId($eventId);
            if (empty($event)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Update event
            $event = $this->eventRepository->update($eventId, $params);
            if (empty($event)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Convert data for event detail
            $event = $this->eventRepository->getEventByEventId($event->event_id);
            $event = $this->eventService->convertDataEventDetail($event);

            return ApiBusUtil::successResponse($event);
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
            // Get event by event id
            $event = $this->eventRepository->getEventByEventId($eventId);
            if (empty($event)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Delete event
            $event = $this->eventRepository->deleteById($eventId);
            if (empty($event)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

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
     * @param ImportCsvRequest $request
     */
    public function importCsv(ImportCsvRequest $request) {
        try {
            $importCSVFile = $request->import_csv_file;
            $importCSVKey = 'import_csv_event';

            // Import CSV
            $importResult = $this->eventService->importCsv($importCSVKey, $importCSVFile);
            if (! empty($importResult['errorArray'])) {
                return ApiBusUtil::errorResponse(ApiCodeNo::VALIDATE_PARAMETER, ApiStatusCode::NG400, $importResult['errorArray']);
            }

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
            $exportCSVKey = 'export_csv_event';

            // Define the directory path for retrieving CSV export configurations using the provided export key
            $configCsvDirectory = 'Export.Event.configs.' . $exportCSVKey;

            // Define the directory path for retrieving CSV export templates using the provided export key
            $templateCsvDirectory = 'Export.Event.templates.' . $exportCSVKey;

            // Retrieve CSV configuration from the specified configuration directory
            $csvConfiguration = ConfigUtil::getCSV($configCsvDirectory);

            // Extract export parameters from the request based on the configuration
            $exportParams = $csvConfiguration['params'] ?? [];
            $params = $request->only($exportParams);

            // Get the query for exporting CSV data
            $csvQuery = $this->csvFileExportService->getExportCsvQuery($configCsvDirectory, $params);

            // Perform the CSV export and get the file details
            $exportResult = $this->csvFileExportService->exportCsv($csvQuery, $templateCsvDirectory);

            // Extract the file name and file path from the export result
            $fileName = $exportResult['fileName'];
            $filePath = $exportResult['filePath'];

            // Stream the CSV file for download
            return Response::streamCsvDownload($filePath, $fileName);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }
}
