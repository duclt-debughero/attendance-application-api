<?php

namespace App\Services;

use App\Libs\{
    ConfigUtil,
    DateUtil,
    EncryptUtil,
    ValueUtil,
};
use App\Repositories\EventTypeRepository;
use Illuminate\Support\Facades\{Auth, DB, Log};

class EventTypeService
{
    public function __construct(
        private EventTypeRepository $eventTypeRepository,
        private CsvFileImportService $csvImportService,
    ) {
    }

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
        $configCsvDirectory = 'Import.EventType.configs.' . $importCSVKey;
        $configurations = ConfigUtil::getCSV($configCsvDirectory);
        $dataFields = array_keys($configurations);

        // Initialize error array
        $errorArray = [];
        $currentRow = 0;
        $hasHeader = true;
        $checkCreateOrUpdateSuccessfully = true;

        // Get Import CSV Mappings
        $importCSVMappings = [
            'import_csv_event_type' => [
                'repository' => $this->eventTypeRepository,
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
        } catch(\Exception $e) {
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
