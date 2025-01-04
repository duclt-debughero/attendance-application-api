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
use App\Repositories\EventTypeRepository;
use App\Requests\Api\EventType\{
    AddRequest,
    EditRequest,
    ImportCsvRequest,
};
use App\Services\{
    CsvFileExportService,
    EventTypeService,
};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, Response};

class EventTypeController extends ApiBaseController
{
    public function __construct(
        private EventTypeRepository $eventTypeRepository,
        private CsvFileExportService $csvFileExportService,
        private EventTypeService $eventTypeService,
    ) {
    }

    /**
     * Event Type List
     * GET /api/v1/event/type/list
     *
     * @param Request $request
     */
    public function list(Request $request) {
        try {
            $params = $request->only(['type_name']);

            // Get event type list by params search
            $eventTypes = $this->eventTypeRepository->search($params)->get();
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
     * GET /api/v1/event/type/detail
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
     * POST /api/v1/event/type/create
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
     * POST /api/v1/event/type/update
     *
     * @param EditRequest $request
     * @param string|int $eventTypeId
     */
    public function update(EditRequest $request, $eventTypeId) {
        try {
            $params = $request->only(['type_name', 'description']);

            // Get event type by event type id
            $eventType = $this->eventTypeRepository->getEventTypeByEventTypeId($eventTypeId);
            if (empty($eventType)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Update event type
            $eventType = $this->eventTypeRepository->update($eventTypeId, $params);
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
     * Event Type Delete
     * POST /api/v1/event/type/delete
     *
     * @param Request $request
     * @param string|int $eventTypeId
     */
    public function delete(Request $request, $eventTypeId) {
        try {
            // Get event type by event type id
            $eventType = $this->eventTypeRepository->getEventTypeByEventTypeId($eventTypeId);
            if (empty($eventType)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Delete event type
            $eventType = $this->eventTypeRepository->deleteById($eventTypeId);
            if (empty($eventType)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            return ApiBusUtil::successResponse();
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Event Type Import CSV
     * POST /api/v1/event/type/import/csv
     *
     * @param ImportCsvRequest $request
     */
    public function importCsv(ImportCsvRequest $request) {
        try {
            $importCSVFile = $request->import_csv_file;
            $importCSVKey = 'import_csv_event_type';

            // Import CSV
            $importResult = $this->eventTypeService->importCsv($importCSVKey, $importCSVFile);
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
     * Event Type Export CSV
     * POST /api/v1/event/type/export/csv
     *
     * @param Request $request
     */
    public function exportCsv(Request $request) {
        try {
            $exportCSVKey = 'export_csv_event_type';

            // Define the directory path for retrieving CSV export configurations using the provided export key
            $configCsvDirectory = 'Export.EventType.configs.' . $exportCSVKey;

            // Define the directory path for retrieving CSV export templates using the provided export key
            $templateCsvDirectory = 'Export.EventType.templates.' . $exportCSVKey;

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
