<?php

namespace App\Services;

use App\Libs\ConfigUtil;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CsvFileImportService
{
    const CHUNK = 100;

    private $errors = [];

    /**
     *  Validate Import Csv data.
     *
     * @param $configs
     * @param $fileTemp
     * @param $hasHeader
     * @param &$currentRow
     * @return mixed
     */
    public function importCsv($configs, $fileTemp, $hasHeader, &$currentRow) {
        try {
            $errorArray = [];
            $dataArray = [];
            $errorHeader = false;
            $chunkCount = 0;

            while (!feof($fileTemp) && $chunkCount < self::CHUNK) {
                $rowData = fgetcsv($fileTemp);
                if (! empty($rowData)) {
                    // Check CSV Header
                    if ($hasHeader && $currentRow === 0) {
                        $expectedHeader = array_column($configs, 'header');
                        if ($rowData !== $expectedHeader) {
                            $this->commonErrorForCsv([ConfigUtil::getMessage('ECL019')]);
                            $errorHeader = true;
                            break;
                        }
                        $chunkCount++;
                        $currentRow++;
                        continue;
                    }

                    $lineNumber = $currentRow + 1;
                    $idx = $hasHeader ? $currentRow - 1 : $currentRow;
                    $dataArray[$idx] = [];
                    $validationRules = [];
                    $dataFields = [];
                    $attributes = [];

                    // Add values from rowData to the corresponding fields
                    foreach ($rowData as $index => $value) {
                        $fieldNames = array_keys($configs);
                        $valueDefault = $configs[$fieldNames[$index]]['default'] ?? null;
                        $value = $value !== '' ? $value : ($valueDefault ?? null);
                        $dataFields[$fieldNames[$index]] = $value;
                    }

                    // Process each configuration to set up validation rules and headers
                    foreach ($configs as $fieldName => $config) {
                        $headerName = $config['header'] ?? null;
                        $validationRules = $config['validate'] ?? [];
                        $attributes[$fieldName] = $headerName;

                        if (!empty($validationRules)) {
                            foreach ($validationRules as $validation) {
                                $ruleName = is_array($validation) ? array_key_first($validation) : $validation;
                                $rules[$fieldName][$ruleName] = is_array($validation) ? [$validation[$ruleName]] : null;
                            }
                        }
                    }

                    // Check CSV Validate
                    $errorMessages = $this->applyValidationRuleForCsv($rules, $dataFields, $attributes);
                    foreach ($configs as $fieldName => $config) {
                        $headerName = $config['header'] ?? null;
                        $validationErrors = $errorMessages[$fieldName] ?? [];
                        if (!empty($validationErrors)) {
                            $options = ['lineNumber' => $lineNumber, 'header' => $headerName];
                            $this->commonErrorForCsv($validationErrors, $options);
                        }
                    }

                    $dataArray[$idx] = $dataFields;
                }
                $chunkCount++;
                $currentRow++;
            }

            $errorArray = $this->errors;

            return compact('errorArray', 'errorHeader', 'dataArray');
        } catch(Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Apply the validation rule for Import CSV.
     *
     * @param array $validationRules
     * @param array $dataFields
     * @param array $attributes
     * @return mixed
     */
    function applyValidationRuleForCsv($validationRules, &$dataFields, $attributes) {
        try {
            $errorMessages = null;
            $compiledRules = [];

            // Create validator object based on the rule and parameters
            foreach ($validationRules as $field => $ruleSet) {
                foreach ($ruleSet as $ruleName => $params) {
                    switch ($ruleName) {
                        case 'RequiredIf':
                            $dependentValue = reset($params)[1] ?? null;
                            $expectedValue = $dataFields[reset($params)[0]] ?? null;
                            if (isset($dependentValue) && isset($expectedValue)) {
                                $params = [$dependentValue, $expectedValue];
                                break;
                            }
                            // If parameters are not correctly set, skip this rule
                            continue 2;
                        case 'NullIf':
                            $dependentValue = reset($params)[1] ?? null;
                            $expectedValue = $dataFields[reset($params)[0]] ?? null;
                            if (isset($dependentValue) && isset($expectedValue) && $dependentValue == $expectedValue) {
                                $dataFields[$field] = null;
                            }
                            // NullIf doesn't need to instantiate a rule class
                            continue 2;
                        case 'MinLengthIf':
                            $dependentValue = reset($params)[1] ?? null;
                            $expectedValue = $dataFields[reset($params)[0]] ?? null;
                            $minValue = reset($params)[2] ?? null;
                            if (isset($dependentValue) && isset($expectedValue)) {
                                $params = [$dependentValue, $expectedValue, $minValue];
                                break;
                            }
                            // If parameters are not correctly set, skip this rule
                            continue 2;
                        case 'MaxLengthIf':
                            $dependentValue = reset($params)[1] ?? null;
                            $expectedValue = $dataFields[reset($params)[0]] ?? null;
                            $maxValue = reset($params)[2] ?? null;
                            if (isset($dependentValue) && isset($expectedValue)) {
                                $params = [$dependentValue, $expectedValue, $maxValue];
                                break;
                            }
                            // If parameters are not correctly set, skip this rule
                            continue 2;
                    }

                    // Create rule instance
                    $ruleClass = "App\\Rules\\{$ruleName}";
                    if (class_exists($ruleClass)) {
                        $ruleInstance = new $ruleClass(...($params ?: []));
                        $compiledRules[$field][] = $ruleInstance;
                    }
                }
            }

            if (!empty($compiledRules)) {
                $validator = Validator::make($dataFields, $compiledRules, [], $attributes);
                if ($validator->fails()) {
                    $errorMessages = $validator->errors()->toArray();
                }
            }

            return $errorMessages;
        } catch(Exception $e) {
            Log::error($e);

            return null;
        }
    }

    /**
     * Common error handling function for CSV imports.
     *
     * @param array $validationErrors
     * @param array $options
     * @return array
     */
    function commonErrorForCsv($validationErrors, $options = []) {
        if (!empty($validationErrors)) {
            // Initialize errors with the default message if $errors is empty
            if (empty($this->errors)) {
                $this->errors[] = ConfigUtil::getMessage('ECL059_TITLE');
            }

            // Add line number and header details to errors if provided in options
            if (!empty($options['lineNumber']) && !empty($options['header'])) {
                $this->errors[] = ConfigUtil::getMessage('ECL059_DETAIL', [$options['lineNumber'], $options['header']]);
            }

            // Merge $validationErrors into $errors
            $this->errors = array_merge($this->errors, $validationErrors);
        }

        return $this->errors;
    }
}
