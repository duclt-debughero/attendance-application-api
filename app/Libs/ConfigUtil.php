<?php

namespace App\Libs;

use Symfony\Component\Yaml\Yaml;

class ConfigUtil
{
    public const PATH = 'config/Constant/';

    public const VALUE_LIST_DIR = 'Values';

    public const MESSAGE_DIR = 'Messages';

    public const CSV_DIR = 'Csv';

    /**
     * Get root path
     *
     * @return string
     */
    public static function rootPath() {
        return __DIR__ . '/../../';
    }

    /**
     * Get message from message_file, params is optional
     *
     * @param string $key
     * @param array $paramArray
     * @return mixed|null
     */
    public static function getMessage($key, $paramArray = []) {
        $message = self::getConfig(self::MESSAGE_DIR, $key);
        if ($message && is_string($message)) {
            foreach ($paramArray as $param => $value) {
                $message = str_replace(sprintf('<%d>', $param), $value, $message);
            }
        }

        return $message;
    }

    /**
     * Get $key value from value_list_file
     *
     * @param string $keys
     * @param array $options
     * @return array|null
     */
    public static function getValueList($keys, $options = []) {
        $keys = explode('.', $keys);
        if (! is_array($keys) || count($keys) != 2) {
            return null;
        }

        [$fileName, $param] = $keys;
        $valueList = self::loadValueList($fileName, $param);
        if ($valueList && is_array($valueList)) {
            $resultList = [];
            foreach ($valueList as $key => $value) {
                if (! is_array($value)) {
                    $value = explode('|', $value);
                    if (! isset($value[1])) {
                        $resultList[$key] = $value[0];
                    } elseif (isset($options['getList']) && $options['getList']) {
                        $resultList[$key] = $value[0];
                    }
                } else {
                    $resultList[$key] = $value;
                }
            }

            return $resultList;
        }

        return $valueList;
    }

    /**
     * Get value/text from const
     *
     * @param string $keys
     * @param bool $getText
     * @return int|string|null
     */
    public static function getValue($keys, $getText = false) {
        $keys = explode('.', $keys);
        if (! is_array($keys) || count($keys) != 3) {
            return null;
        }

        [$fileName, $key, $const] = $keys;
        $valueList = self::loadValueList($fileName, $key);
        if ($valueList && is_array($valueList)) {
            foreach ($valueList as $key => $value) {
                $value = explode('|', $value);
                if (isset($value[1]) && $value[1] == $const) {
                    if ($getText) {
                        return $value[0];
                    }
                    return $key;
                }
            }
        }

        return null;
    }

    /**
     * Load $key value from specific value_list_file
     *
     * @param $fileName
     * @param $key
     * @return mixed
     */
    public static function loadValueList($fileName, $key) {
        global $cacheYaml;
        global $cacheValueList;

        if (! isset($cacheYaml)) {
            $cacheYaml = [];
        }
        if (! isset($cacheValueList)) {
            $cacheValueList = [];
        }

        $valueListKey = $fileName . '.' . $key;
        if (isset($cacheValueList[$valueListKey])) {
            // retreiving from local static cache
            return $cacheValueList[$valueListKey];
        }

        if (isset($cacheYaml[$fileName])) {
            // retreiving from local static cache
            $paramValue = $cacheYaml[$fileName];
        } else {
            $filePath = self::rootPath() . self::PATH . self::VALUE_LIST_DIR . '/' . $fileName . '.yml';
            $paramValue = Yaml::parse(file_get_contents($filePath));
            $cacheYaml[$fileName] = $paramValue; // cache
        }

        $cacheValueList[$valueListKey] = $paramValue[$key]; // cache

        return $paramValue[$key];
    }

    /**
     * Get config params from DemoBundle/Reosurce/config/folder_name
     *
     * @param string $folderName
     * @param string $paramKey
     * @return string|array|null
     */
    private static function getConfig($folderName, $paramKey) {
        global $cacheConfig;
        global $cacheConfigFile;

        if (! isset($cacheConfig)) {
            $cacheConfig = [];
        }
        if (! isset($cacheConfigFile)) {
            $cacheConfigFile = [];
        }
        if (isset($cacheConfig[$paramKey])) {
            return $cacheConfig[$paramKey];
        }

        $folderPath = self::rootPath() . self::PATH . $folderName;
        $paramKeyArr = explode('.', $paramKey);
        foreach (glob($folderPath . '/*.yml') as $yamlSrc) {
            if (isset($cacheConfigFile[basename($yamlSrc)])) {
                $paramValue = $cacheConfigFile[basename($yamlSrc)];
            } else {
                $paramValue = Yaml::parse(file_get_contents($yamlSrc));
                $cacheConfigFile[basename($yamlSrc)] = $paramValue;
            }

            $found = true;
            foreach ($paramKeyArr as $key) {
                if (! isset($paramValue[$key])) {
                    $found = false;
                    break;
                }
                $paramValue = $paramValue[$key];
            }
            if ($found) {
                $cacheConfig[$paramKey] = $paramValue;

                return $paramValue;
            }
        }

        return null;
    }

    /**
     * Load Csv data from a YAML file
     *
     * @param string $typeFolder
     * @param string $folderName
     * @param string $fileName
     * @param string $key
     * @return array|null
     */
    public static function loadCsv($typeFolder, $folderName, $fileName, $key) {
        global $yamlCache;
        global $csvKeyCache;

        if (!isset($yamlCache)) {
            $yamlCache = [];
        }

        if (!isset($csvKeyCache)) {
            $csvKeyCache = [];
        }

        $csvKey = $typeFolder . '.' . $folderName . '.' . $fileName;
        if (isset($csvKeyCache[$csvKey])) {
            // Retrieving from local static cache
            return $csvKeyCache[$csvKey];
        }

        if (isset($yamlCache[$fileName])) {
            // Retrieving from local static cache
            $yamlData = $yamlCache[$fileName];
        } else {
            $filePath = self::rootPath() . self::PATH . self::CSV_DIR . '/' . $typeFolder . '/' . $folderName . '/' . $fileName . '.yml';
            $yamlData = Yaml::parse(file_get_contents($filePath));
            $yamlCache[$fileName] = $yamlData; // cache
        }

        $csvKeyCache[$csvKey] = $yamlData[$key]; // cache

        return $yamlData[$key];
    }


    /**
     * Get value list from Csv
     *
     * @param string $keys
     * @return array|null
     */
    public static function getCsv($keys) {
        // Split the key string into parts
        $keys = explode('.', $keys);

        // Ensure we have exactly 4 parts
        if (! is_array($keys) || count($keys) != 4) {
            return null;
        }

        // Assign variables from key parts
        [$typeFolder, $folderName, $fileName, $key] = $keys;

        // Load the CSV value using the provided parts
        $csvValue = self::loadCSV($typeFolder, $folderName, $fileName, $key);
        if (empty($csvValue)) {
            return null;
        }

        return $csvValue;
    }
}
