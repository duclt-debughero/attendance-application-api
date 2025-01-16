<?php

namespace App\Services;

use App\Libs\{
    ConfigUtil,
    DateUtil,
    ValueUtil,
};
use App\Repositories\EventRepository;
use Exception;
use Illuminate\Support\Facades\{Auth, DB, Log};

class EventService
{
    public function __construct(
        private EventRepository $eventRepository,
        private CsvFileImportService $csvImportService,
    ) {
    }

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

    /**
     * Import CSV
     *
     * @param string $importCSVKey
     * @param object $importCSVFile
     * @return mixed
     */
    public function importCSV($importCSVKey, $importCSVFile) {
        DB::beginTransaction();

        $userId = Auth::user()->user_id;
        $delFlgValid = ValueUtil::constToValue('common.del_flg.VALID');

        // Get CSV configurations
        $configCsvDirectory = 'Import.Event.configs.' . $importCSVKey;
        $configurations = ConfigUtil::getCSV($configCsvDirectory);
        $dataFields = array_keys($configurations);

        // Initialize error array
        $errorArray = [];
        $currentRow = 0;
        $hasHeader = true;
        $checkCreateOrUpdateSuccessfully = true;

        // Get Import CSV Mappings
        $importCSVMappings = [
            'import_csv_event' => [
                'repository' => $this->eventRepository,
            ],
        ];

        try {
            // Open file for reading
            $filePath = $importCSVFile->getRealPath();
            $fileTemp = fopen($filePath, 'r');

            while (! feof($fileTemp)) {
                $processedData = $this->csvImportService->importCSV($configurations, $fileTemp, $hasHeader, $currentRow);
                $errorArray = array_merge($errorArray, $processedData['errorArray']);

                // Check error header
                if ($processedData['errorHeader']) {
                    break;
                }

                // Create or Update data
                if (empty($errorArray)) {
                    foreach ($processedData['dataArray'] as &$data) {
                        $data['del_flg'] = $delFlgValid;
                        $data['created_by'] = $userId;
                        $data['updated_by'] = $userId;
                        $data['deleted_at'] = null;
                        $data['deleted_by'] = null;
                    }

                    if (isset($importCSVMappings[$importCSVKey])) {
                        $exceptUpdate = ['created_at', 'created_by', 'updated_at'];
                        $repository = $importCSVMappings[$importCSVKey]['repository'];

                        // Create or Update data
                        $checkCreateOrUpdateSuccessfully = call_user_func_array([$repository, 'upsert'], [
                            'data' => $processedData['dataArray'],
                            'exceptUpdate' => $exceptUpdate,
                        ]);

                        // Check create or update successfully
                        if (! $checkCreateOrUpdateSuccessfully) {
                            break;
                        }
                    }
                }
            }

            fclose($fileTemp);

            // Check create or update successfully
            $checkCreateOrUpdateSuccessfully = empty($errorArray) && $checkCreateOrUpdateSuccessfully;
        } catch(Exception $e) {
            DB::rollBack();
            Log::error($e);

            // Check create or update successfully
            $checkCreateOrUpdateSuccessfully = false;
        }

        // Commit or Rollback transaction
        $checkCreateOrUpdateSuccessfully ? DB::commit() : DB::rollBack();

        return compact('errorArray', 'checkCreateOrUpdateSuccessfully');
    }
}
