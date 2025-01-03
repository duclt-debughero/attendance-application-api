<?php

namespace App\Libs;

use Exception;
use Illuminate\Support\Facades\{Log, Storage};

class FileUtil
{
    /**
     * Write a file to S3 disk.
     * Note: existing file will be overwritten.
     *
     * @param string $path file full path
     * @param string|resource $content the file content
     * @return boolean
     */
    public static function writeToS3($path, $content) {
        try {
            return Storage::disk('s3')->put($path, $content);
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Check file exists on S3 disk.
     *
     * @param string $path file full path
     * @return boolean
     */
    public static function existsOnS3($path) {
        try {
            return Storage::disk('s3')->exists($path);
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Get file string content from S3 disk.
     *
     * @param string $path file full path
     * @return string|boolean return file string content if success, return false otherwise
     */
    public static function getFromS3($path) {
        try {
            return Storage::disk('s3')->get($path);
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Get all files that has prefix in a directory on S3
     *
     * @param string $path path to the directory
     * @param mixed $prefix
     * @return array files matches pattern in this directory
     */
    public static function searchS3ByPrefix($path, $prefix) {
        try {
            $client = Storage::disk('s3')->getClient();
            $command = $client->getCommand('ListObjectsV2');
            $command['Bucket'] = env('AWS_BUCKET');
            $command['Prefix'] = $path . $prefix;
            $result = $client->execute($command);

            return array_column($result['Contents'] ?? [], 'Key');
        } catch (Exception $e) {
            Log::error($e);

            return [];
        }
    }

    /**
     * Get file url from S3 disk.
     *
     * @param string $path file full path
     * @return string|boolean return file url if success, return false otherwise
     */
    public static function getFileUrlFromS3($path) {
        try {
            return Storage::disk('s3')->temporaryUrl($path, now()->addMinute(5));
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Convert data to csv
     *
     * @param array $lstData
     * @param string $encode
     * @return string
     */
    public static function convertDataCsv($lstData, $encode = 'UTF-8') {
        $rowContent = 0;
        $contents = null;

        foreach ($lstData as $index => $data) {
            $temp = [];
            foreach ($data as $row) {
                $row = $row ?? '';
                $temp[] = '"' . preg_replace('/"/', '""', $row) . '"';
            }

            if ($rowContent > 0) {
                $contents .= "\r\n";
            }

            $contents .= mb_convert_encoding(implode(',', $temp), $encode, 'auto');
            $rowContent++;
        }

        return $contents;
    }

    /**
     * Delete files from S3 disk.
     *
     * @param array $path file full path
     * @param mixed $fulPaths
     * @return boolean
     */
    public static function removeFileFromS3($fulPaths) {
        return Storage::disk('s3')->delete($fulPaths);
    }

    /**
     * Get cloudFront url.
     *
     * @param string $path file full path
     * @return string|boolean return file url if success, return false otherwise
     */
    public static function getCloudFrontUrl($path) {
        if ($path != null) {
            return config('attendance-application.cloud_front_url') . $path;
        }

        return null;
    }
}
