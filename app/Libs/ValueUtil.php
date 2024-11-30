<?php

namespace App\Libs;

class ValueUtil
{
    /**
     * Get value list from yml config file
     *
     * @param string $keys
     * @param array $options
     * @return array|string|null
     */
    public static function get($keys, $options = []) {
        return ConfigUtil::getValueList($keys, $options);
    }

    /**
     * Get value list contain japanese and english
     *
     * @param string $keys
     * @param array $options
     * @return array|null
     */
    public static function getList($keys, $options = []) {
        $options['getList'] = true;

        return ConfigUtil::getValueList($keys, $options);
    }

    /**
     * Convert from value into text in view
     *
     * @param string|int $value property value Ex: 1
     * @param string $listKey list defined in yml Ex: web.type
     * @return string|null text if exists else blank
     */
    public static function valueToText($value, $listKey) {
        // check params
        if (! isset($value) || ! isset($listKey)) {
            return null;
        }
        // get list options
        $list = ValueUtil::get($listKey);
        if (empty($list)) {
            $list = ValueUtil::getList($listKey);
        }
        if (is_array($list) && isset($list[$value])) {
            return $list[$value];
        }

        // can't get value
        return null;
    }

    /**
     * Get value from const (in Yml config file)
     *
     * @param string $keys
     * @return int|string|null
     */
    public static function constToValue($keys) {
        return ConfigUtil::getValue($keys);
    }

    /**
     * Get text from const (in Yml config file)
     *
     * @param string $keys
     * @return int|string|null
     */
    public static function constToText($keys) {
        return ConfigUtil::getValue($keys, true);
    }

    /**
     * Get value from test i
     *
     * @param string $searchText
     * @param string $keys
     * @return int|string|null
     */
    public static function textToValue($searchText, $keys) {
        $valueList = ValueUtil::get($keys);
        foreach ($valueList as $key => $text) {
            if ($searchText == $text) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Remove decimal trailing zeros
     *
     * Ex: removeDecimalTrailingZeros(1000.32900) => 1000.329
     * Ex: removeDecimalTrailingZeros(1000.00322000) => 1000.00322
     *
     * @param string $number The number to remove trailing zeros from.
     * @return string The number without trailing zeros.
     */
    public static function removeDecimalTrailingZeros($number) {
        if (empty($number)) {
            return $number;
        }
        $integerPath = number_format($number);
        $decimalPath = explode('.', $number);
        $decimalPath = isset($decimalPath[1]) ? $decimalPath[1] : '';
        $decimalPath = rtrim($decimalPath, '0');
        $decimalPath = ! empty($decimalPath) ? '.' . $decimalPath : '';

        return $integerPath . $decimalPath;
    }

    /**
     * Format String (YYYY/MM) to Date.YearMonth
     *
     * Ex: formatStringToDate('201906') => 2019/06
     * Ex: formatStringToDate('20190601') => 2019/06/01
     *
     * @param string $str The string to format.
     * @param string $charParse The format to use for the date.
     * @param bool $incudeDay The formatted date.
     * @return string The formatted date.
     */
    public static function formatStringToDate($str, $charParse = '/', $incudeDay = false) {
        $result = '';
        if (! empty($str)) {
            $year = substr($str, 0, 4);
            $month = substr($str, 4, 2);
            $result = $year . $charParse . $month;
            if ($incudeDay == true) {
                $day = substr($str, 6, 2);
                $result = $result . $charParse . $day;
            }
        }

        return $result;
    }

    /**
     * Format Date.YearMonth to String (YYYY/MM)
     *
     * Ex: formatDateToString('2019/06') => 201906
     * Ex: formatDateToString('2019/06/01') => 20190601
     *
     * @param string $date The date to format.
     * @param string $charParse The format to use for the date.
     * @return string The formatted date.
     */
    public static function formatDateToString($date, $charParse = '/') {
        $result = '';
        if (! empty($date)) {
            $year = explode($charParse, $date)[0];
            $month = explode($charParse, $date)[1];
            $result = $year . $month;
        }

        return $result;
    }

    /**
     * Convert string to array
     *
     * Ex: stringToArray('1,2,3') => [1,2,3]
     *
     * @param string $string The string to convert.
     * @return array The converted array.
     */
    public static function stringToArray($string) {
        if (empty($string)) {
            return [];
        }
        $replaceSearch = ['[', ']', ' ', '"', "'"];
        $string = str_replace($replaceSearch, '', $string);

        return explode(',', $string);
    }

    /**
     * Format date
     *
     * Ex: textareaToArray('2019/06/01\n2019/06/02\n2019/06/03') => ['2019/06/01', '2019/06/02', '2019/06/03']
     *
     * @param string $textareaContent The textarea content to format.
     * @return array The formatted array.
     */
    public static function textareaToArray($textareaContent) {
        // Split the textarea content by newline character
        $lines = explode("\n", $textareaContent);

        // Trim whitespace from each line
        $lines = array_map('trim', $lines);

        return $lines;
    }

    /**
     * Rounding unit_price
     *
     * Ex: roundingUnitPrice(1000.323) => 1000
     * Ex: roundingUnitPrice(1000.00) => 1000
     * Ex: roundingUnitPrice(1000.62) => 1001
     *
     * @param int|float $unitPrice The unit price to round.
     * @return int
     */
    public static function roundingUnitPrice($unitPrice) {
        return intval(floor($unitPrice));
    }

    /**
     * Convert <br> tag to break line char
     *
     * Ex: br2nl('<br>') => "\n"
     *
     * @param string $string The string to convert.
     * @return string The converted string.
     */
    public static function br2nl($string) {
        return preg_replace('#<br\s*/?>#i', "\n", $string);
    }

    /**
     * Round a number to a specified number of decimal places.
     *
     * Ex: roundToNDecimalPlaces(1000.323, 2) => 1000.32
     * Ex: roundToNDecimalPlaces(1000.323, 4) => 1000.3230
     *
     * @param float $num The number to round.
     * @param int $decimalPlaces The number of decimal places to round to (default is 10).
     * @return float The rounded number.
     */
    public static function roundToNDecimalPlaces($num, $decimalPlaces = 10) {
        $multiplier = pow(10, $decimalPlaces);

        return round($num * $multiplier) / $multiplier;
    }

    /**
     * format number without rounding decimals.
     *
     * Ex: formatNumberWithCommas(1000.32) => 1,000.32
     * Ex: formatNumberWithCommas(1000.00) => 1,000
     * Ex: formatNumberWithCommas(1000) => 1,000
     *
     * @param float $number The number to round.
     * @return string The rounded number with commas in string.
     */
    public static function formatNumberWithCommas($number) {
        if (empty($number)) {
            return $number;
        }

        // Divide number into integer and decimal parts
        $parts = explode('.', $number);

        // Format the integer part with commas
        $formatted_integer = number_format($parts[0], 0, '.', ',');

        // If there is a decimal part, add it back
        $formatted_number = isset($parts[1]) ? $formatted_integer . '.' . $parts[1] : $formatted_integer;

        return $formatted_number;
    }
}
