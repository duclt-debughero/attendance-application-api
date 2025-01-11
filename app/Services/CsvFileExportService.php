<?php

namespace App\Services;

use App\Libs\{
    ConfigUtil,
    DateUtil,
    EncryptUtil,
    FileUtil,
    ValueUtil,
};
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\{Log, Storage};

class CsvFileExportService
{
    /**
     * Handle export to CSV
     *
     * @param mixed $csvQuery
     * @param string $templateCsvDirectory
     * @param string $encoding
     * @return array
     */
    public function exportCsv($csvQuery, $templateCsvDirectory, $encoding = 'UTF-8') {
        // Retrieve CSV configuration from the specified template directory
        $csvConfiguration = ConfigUtil::getCSV($templateCsvDirectory);
        $fieldConfigurations = $csvConfiguration['fieldConfig'];

        // Set the page size for chunking the data
        $chunkSize = ValueUtil::get('common.batch_page_size') ?? 10000;

        // Generate the file name with a timestamp and a unique identifier
        $fileName = $csvConfiguration['fileName'] . '_' . Carbon::now()->format('YmdHis') . '.csv';
        $uniqueFileName = uniqid() . '.csv';
        $filePath = storage_path('app/private/' . $uniqueFileName);

        // Create a temporary file for writing CSV data
        $temporaryFile = tmpfile();

        // Variables to track the processing status
        $isFirstChunk = true;
        $totalRecordCount = $csvQuery->count();
        $processedRecordCount = 0;

        try {
            // Prepare the CSV header using field titles from the configuration
            $csvHeader = array_column($fieldConfigurations, 'title');
            fwrite($temporaryFile, FileUtil::convertDataCsv([$csvHeader], $encoding));

            // Process the data in chunks to avoid memory overload
            $csvQuery->chunk($chunkSize, function ($rawDataChunk) use (
                $encoding,
                $fieldConfigurations,
                $temporaryFile,
                $totalRecordCount,
                &$processedRecordCount
            ) {
                // If not the last chunk, add a new line before writing data
                if ($processedRecordCount < $totalRecordCount) {
                    fwrite($temporaryFile, PHP_EOL);
                }

                // Format the raw data for CSV output
                $formattedData = $this->formatRawData($fieldConfigurations, $rawDataChunk);
                fwrite($temporaryFile, FileUtil::convertDataCsv($formattedData, $encoding));

                // Update the count of processed records
                $processedRecordCount += count($rawDataChunk);
            });

            // Save the temporary file to permanent storage
            Storage::put($uniqueFileName, $temporaryFile);

            // Close the file after writing all data
            fclose($temporaryFile);
        } catch (Exception $e) {
            // Log any errors that occur during the export process
            Log::error($e);

            // Close the file if an error occurs
            fclose($temporaryFile);
        }

        // Return the generated file name and file path
        return compact('fileName', 'filePath');
    }

    /**
     * Get query for exporting CSV

     * @param string $configCsvDirectory
     * @param array $inputParams
     * @return mixed
     */
    public function getExportCsvQuery($configCsvDirectory, $inputParams = []) {
        // Retrieve CSV configuration from the specified configuration directory
        $configurations = ConfigUtil::getCSV($configCsvDirectory);

        // Initialize an array to hold parsed parameters
        $parsedParams = [];

        // If there are any specific parameters defined in the configuration, parse them
        if (! empty($configurations['params'])) {
            foreach ($configurations['params'] as $paramKey) {
                // Set the value of each parameter from inputParams or fallback to default values in configurations
                $parsedParams[$paramKey] = $inputParams[$paramKey] ?? $configurations['default'][$paramKey] ?? null;
            }
        }

        // Construct the repository class name using the repository specified in the configuration
        $repositoryClass = "App\\Repositories\\{$configurations['repository']}";

        // Check if the repository class exists
        if (class_exists($repositoryClass)) {
            // Create an instance of the repository
            $repositoryInstance = new $repositoryClass();
            return $repositoryInstance->{$configurations['action']}($parsedParams);
        }

        return null;
    }

    /**
     * Process data with format
     *
     * @param array $configs
     * @param array $records
     * @return array
     */
    private function formatRawData($configs, $records) {
        $formattedData = [];

        foreach ($records as $record) {
            $recordArray = method_exists($record, 'toArray') ? $record->toArray() : (array) $record;
            $configItems = $configs;
            $formattedRow = [];

            foreach ($configItems as $key => $configItem) {
                $formattedItem = null;

                if (array_key_exists($key, $recordArray)) {
                    if (isset($configItem['format_date_time'])) {
                        $originalDate = $record->{$key} ?? null;
                        $formattedItem = DateUtil::formatDateTime($originalDate, $configItem['format_date_time']);
                    } else if (isset($configItem['format_type'])) {
                        $formattedItem = getValueToText($recordArray[$key], $configItem['format_type']);
                    } else if (isset($configItem['decrypt_aes_256'])) {
                        $originalItem = $record->{$key} ?? null;
                        $formattedItem = EncryptUtil::decryptAes256($originalItem);
                    } else {
                        $formattedItem = $recordArray[$key];
                    }
                }

                $formattedRow[$key] = $formattedItem;
            }

            $formattedData[] = $formattedRow;
        }

        return $formattedData;
    }
}
