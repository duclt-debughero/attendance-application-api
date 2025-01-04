<?php

namespace App\Libs;

use App\Extensions\CarbonExtension;
use Carbon\{
    Carbon,
    CarbonInterval,
    CarbonPeriod,
};
use DatePeriod;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;

class DateUtil
{
    /**
     * Format date time
     *
     * Ex: formatDateTime('2024-06-01 12:00:00', 'Y/m/d') => 2024/06/01
     *
     * @param string $string The date to format.
     * @param string $format The format to use for the date.
     * @return string|null The formatted date, or null if the conversion fails.
     */
    public static function formatDateTime($string, $format = 'Y/m/d') {
        $creator = Carbon::parse($string);
        if ($creator) {
            return $creator->format($format);
        }

        return null;
    }

    /**
     * Get list range year month
     *
     * Ex: getListRangeYearMonth(3) => ['2024/05', '2024/06', '2024/07']
     *
     * @param int $subMonth The number of months to subtract.
     * @param string $format The format of the date.
     * @return array The list of years and months.
     */
    public static function getListRangeYearMonth($subMonth, $format = 'Y/m') {
        if (empty($subMonth) && ! is_numeric($subMonth)) {
            return [];
        }

        $now = Carbon::now()->format('Y-m-d');
        $dateOfSubMonth = Carbon::now()->subMonthsNoOverflow(intval($subMonth) - 1)->format('Y-m-d');
        $dateRange = CarbonPeriod::create($dateOfSubMonth, $now);
        $result = [];
        foreach ($dateRange as $key => $date) {
            $result[$date->format('Ym')] = $date->format($format);
        }

        return $result;
    }

    /**
     * Default format for DateTime
     *
     * Ex: formatDefaultDateTime('2024-06-01 12:00:00') => 2024/06/01 12:00
     *
     * @param string $string The date to format.
     * @return string|null The formatted date, or null if the conversion fails.
     */
    public static function formatDefaultDateTime($string) {
        if (! empty($string)) {
            $creator = CarbonExtension::parse($string);
            if ($creator) {
                return $creator->formatDefaultDateTime();
            }
        }

        return null;
    }

    /**
     * Default format for Date
     *
     * Ex: formatDefaultDate('2024-06-01') => 2024/06/01
     *
     * @param string $string The date to format.
     * @return string|null The formatted date, or null if the conversion fails.
     */
    public static function formatDefaultDate($string) {
        if (! empty($string)) {
            $creator = CarbonExtension::parse($string);
            if ($creator) {
                return $creator->formatDefaultDate();
            }
        }

        return null;
    }

    /**
     * Generate array of Carbon objects of each day between 2 days
     *
     * Ex: dateRange('2024-06-01', '2024-06-30', 'Y-m-d') => [2024-06-01, 2024-06-02, 2024-06-03, ... 2024-06-30]
     *
     * @param Carbon $from The start date.
     * @param Carbon $to The end date.
     * @param string|null $format The format of the date.
     * @param string[] $exceptDates The dates to exclude.
     * @param bool $inclusive Whether to include the end date in the range.
     * @return array|null The array of Carbon objects, or null if the conversion fails.
     */
    public static function dateRange($from, $to, $format = null, $exceptDates = [], $inclusive = true) {
        try {
            if ($from->gt($to)) {
                return null;
            }

            $from = $from->copy()->startOfDay();
            $to = $to->copy()->startOfDay();
            if ($inclusive) {
                $to->addDay();
            }

            $step = CarbonInterval::day();
            $period = new DatePeriod($from, $step, $to);
            $range = [];
            foreach ($period as $day) {
                $day = new Carbon($day);
                if (in_array($day->format('Ymd'), $exceptDates)) {
                    continue;
                }
                if (isset($format)) {
                    $range[] = $day->format($format);
                } else {
                    $range[] = new Carbon($day);
                }
            }

            return ! empty($range) ? $range : null;
        } catch (Exception $e) {
            Log::error($e);

            return null;
        }
    }

    /**
     * Check valid date
     *
     * Ex: isValidDate('2024-06-01') => true
     *
     * @param string $dataString The date to check.
     * @return boolean True if the date is valid, false otherwise.
     */
    public static function isValidDate($dataString) {
        try {
            $date = Carbon::parse($dataString);
            if (! is_null($date) && $date instanceof DateTime) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * convert date format to japanese date
     *
     * Ex: convertDateFormat('2024-06-01', 'Y-m-d', 'Y年m月d日') => 2024年6月1日
     *
     * @param string $dataString The date to convert.
     * @param string $fromFormat The format of the date to convert.
     * @param string $toFormat The format to convert the date to.
     * @return string|null The converted date in the specified format, or null if the conversion fails.
     */
    public static function convertDateFormat($dataString, $fromFormat, $toFormat) {
        try {
            $date = Carbon::createFromFormat($fromFormat, $dataString);
            $formattedDate = $date->format($toFormat);

            return $formattedDate;
        } catch (Exception $e) {
            Log::error($e);

            return null;
        }
    }

    /**
     * Get timestamp
     *
     * Ex: getTimestamp() => 202406011200000
     *
     * @return string The current timestamp in the format 'yyyyMMddHHmmssSSS'.
     */
    public static function getTimestamp() {
        $microtime = floatval(substr((string) microtime(), 1, 8));
        $rounded = round($microtime, 3);
        $milisecond = substr((string) $rounded, 2, strlen($rounded));

        return Carbon::now()->format('YmdHis') . $milisecond;
    }

    /**
     * Combine date(yyyy/mm/dd) & time(hh:mm)
     *
     * Ex: combineDateTime('2024/06/01', '12:00') => 2024-06-01 12:00:00
     *
     * @param string|object $date The date in format 'yyyy/mm/dd' or a Carbon instance.
     * @param string|object $time The time in format 'hh:mm' or a Carbon instance.
     * @return string; The combined date and time in format 'yyyy-mm-dd hh:mm:ss'.
     */
    public static function combineDateTime($date, $time) {
        [$hour, $minute] = explode(':', $time);

        return Carbon::parse($date)->addHours($hour)->addMinutes($minute)->format('Y-m-d H:i:s');
    }
}
