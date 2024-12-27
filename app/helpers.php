<?php

use App\Libs\{ConfigUtil, DateUtil, EncryptUtil, ValueUtil};
use Carbon\Carbon;

/**
 * **************************************************************************************
 * ConfigUtil function helper
 * @see \App\Libs\ConfigUtil
 * **************************************************************************************
 */

 if (! function_exists('getMessage')) {
    /**
     * Get message from key
     *
     * @param string $messId
     * @param array $options
     * @param mixed $paramArray
     * @return mixed|string|null
     */
    function getMessage($messId, $paramArray = []) {
        return ConfigUtil::getMessage($messId, $paramArray);
    }
}

/**
 * **************************************************************************************
 * EncryptUtil function helper
 * @see \App\Libs\EncryptUtil
 * **************************************************************************************
 */

 if (! function_exists('encryptUrlBase64')) {
    /**
     * Encrypt string use urlencode and base64
     *
     * @param string $str
     * @return string
     */
    function encryptUrlBase64($str) {
        return EncryptUtil::encryptUrlBase64($str);
    }
}

if (! function_exists('decryptUrlBase64')) {
    /**
     * Decrypt string use urlencode and base64
     *
     * @param string $str
     * @return string
     */
    function decryptUrlBase64($str) {
        return EncryptUtil::decryptUrlBase64($str);
    }
}

if (! function_exists('encryptAes256')) {
    /**
     * Encrypt string use AES 256
     *
     * @param string $str
     * @return string|null
     */
    function encryptAes256($str) {
        return EncryptUtil::encryptAes256($str);
    }
}

if (! function_exists('decryptAes256')) {
    /**
     * Decrypt string use AES256
     *
     * @param string $str
     * @return string
     */
    function decryptAes256($str) {
        return EncryptUtil::decryptAes256($str);
    }
}

/**
 * **************************************************************************************
 * ValueUtil function helper
 * @see \App\Libs\ValueUtil
 * **************************************************************************************
 */

if (! function_exists('getValue')) {
    /**
     * Get value from constant
     *
     * @param string $key
     * @return int|string|null
     */
    function getValue($key) {
        return ValueUtil::get($key);
    }
}

if (! function_exists('getConstToValue')) {
    /**
     * Get value from constant
     *
     * @param string $key
     * @return int|string|null
     */
    function getConstToValue($key) {
        return ValueUtil::constToValue($key);
    }
}

if (! function_exists('getConstToText')) {
    /**
     * Get text from const (in Yml config file)
     *
     * @param $key
     * @return int|string|null
     */
    function getConstToText($key) {
        return ValueUtil::constToText($key);
    }
}

if (! function_exists('getList')) {
    /**
     * Get value for select/checkbox/radio option from key
     *
     * @param string $key
     * @return array|string|null
     */
    function getList($key) {
        return ValueUtil::getList($key);
    }
}

if (! function_exists('getValueToText')) {
    /**
     * Convert from value into text in view
     *
     * @param string|int $value property value Ex: 1
     * @param string $listKey list defined in yml Ex: web.type
     * @return string|null text if exists else blank
     */
    function getValueToText($value, $listKey) {
        return ValueUtil::valueToText($value, $listKey);
    }
}

if (! function_exists('removeDecimalTrailingZeros')) {
    /**
     * Remove decimal trailing zeros
     *
     * Ex: removeDecimalTrailingZeros(1000.32900) => 1000.329
     * Ex: removeDecimalTrailingZeros(1000.00322000) => 1000.00322
     *
     * @param string $number The number to remove trailing zeros from.
     * @return string The number without trailing zeros.
     */
    function removeDecimalTrailingZeros($number) {
        return ValueUtil::removeDecimalTrailingZeros($number);
    }
}

if (! function_exists('formatStringToDate')) {
    /**
     * Format String (YYYY/MM) to Date.YearMonth
     *
     * Ex: formatStringToDate('201906') => 2019/06
     * Ex: formatStringToDate('20190601') => 2019/06/01
     *
     * @param string $str The string to format.
     * @param string $charParse The format to use for the date.
     * @param bool $includeDay The formatted date.
     * @return string The formatted date.
     */
    function formatStringToDate($str, $charParse = '/', $includeDay = false) {
        return ValueUtil::formatStringToDate($str, $charParse, $includeDay);
    }
}

if (! function_exists('formatDateToString')) {
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
    function formatDateToString($date, $charParse = '/') {
        return ValueUtil::formatDateToString($date, $charParse);
    }
}

if (! function_exists('stringToArray')) {
    /**
     * Convert string to array
     *
     * Ex: stringToArray('1,2,3') => [1,2,3]
     *
     * @param string $string The string to convert.
     * @return array The converted array.
     */
    function stringToArray($string) {
        return ValueUtil::stringToArray($string);
    }
}

if (! function_exists('roundingUnitPrice')) {
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
    function roundingUnitPrice($unitPrice) {
        return ValueUtil::roundingUnitPrice($unitPrice);
    }
}

if (! function_exists('br2nl')) {
    /**
     * Convert <br> tag to break line char
     *
     * Ex: br2nl('<br>') => "\n"
     *
     * @param string $string The string to convert.
     * @return string The converted string.
     */
    function br2nl($string) {
        return ValueUtil::br2nl($string);
    }
}

if (! function_exists('roundToNDecimalPlaces')) {
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
    function roundToNDecimalPlaces($num, $decimalPlaces = 10) {
        return ValueUtil::roundToNDecimalPlaces($num, $decimalPlaces);
    }
}

if (! function_exists('formatNumberWithCommas')) {
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
    function formatNumberWithCommas($number) {
        return ValueUtil::formatNumberWithCommas($number);
    }
}

/**
 * **************************************************************************************
 * DateUtil function helper
 * @see \App\Libs\DateUtil
 * **************************************************************************************
 */

if (! function_exists('formatDateTime')) {
    /**
     * Format date time
     *
     * Ex: formatDateTime('2024-06-01 12:00:00', 'Y/m/d') => 2024/06/01
     *
     * @param string $string The date to format.
     * @param string $format The format to use for the date.
     * @return string|null The formatted date, or null if the conversion fails.
     */
    function formatDateTime($string, $format = 'Y/m/d') {
        return DateUtil::formatDateTime($string, $format);
    }
}

if (! function_exists('getListRangeYearMonth')) {
    /**
     * Get list range year month
     *
     * Ex: getListRangeYearMonth(3) => ['2024/05', '2024/06', '2024/07']
     *
     * @param int $subMonth The number of months to subtract.
     * @param string $format The format of the date.
     * @return array The list of years and months.
     */
    function getListRangeYearMonth($string, $format = 'Y/m/d') {
        return DateUtil::getListRangeYearMonth($string, $format);
    }
}

if (! function_exists('textareaToArray')) {
    /**
     * Format date
     *
     * Ex: textareaToArray('2019/06/01\n2019/06/02\n2019/06/03') => ['2019/06/01', '2019/06/02', '2019/06/03']
     *
     * @param string $textareaContent The textarea content to format.
     * @return array The formatted array.
     */
    function textareaToArray($string) {
        return ValueUtil::textareaToArray($string);
    }
}

if (! function_exists('formatDefaultDateTime')) {
    /**
     * Default format for DateTime
     *
     * Ex: formatDefaultDateTime('2024-06-01 12:00:00') => 2024/06/01 12:00
     *
     * @param string $string The date to format.
     * @return string|null The formatted date, or null if the conversion fails.
     */
    function formatDefaultDateTime($string) {
        return DateUtil::formatDefaultDateTime($string);
    }
}

if (! function_exists('formatDefaultDate')) {
    /**
     * Default format for Date
     *
     * Ex: formatDefaultDate('2024-06-01') => 2024/06/01
     *
     * @param string $string The date to format.
     * @return string|null The formatted date, or null if the conversion fails.
     */
    function formatDefaultDate($string) {
        return DateUtil::formatDefaultDate($string);
    }
}

if (! function_exists('dateRange')) {
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
    function dateRange($from, $to, $format = null, $exceptDates = [], $inclusive = true) {
        return DateUtil::dateRange($from, $to, $format, $exceptDates, $inclusive);
    }
}

if (! function_exists('isValidDate')) {
    /**
     * Check valid date
     *
     * Ex: isValidDate('2024-06-01') => true
     *
     * @param string $dataString The date to check.
     * @return boolean True if the date is valid, false otherwise.
     */
    function isValidDate($dataString) {
        return DateUtil::isValidDate($dataString);
    }
}

if (! function_exists('convertDateFormat')) {
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
    function convertDateFormat($dataString, $fromFormat, $toFormat) {
        return DateUtil::convertDateFormat($dataString, $fromFormat, $toFormat);
    }
}
