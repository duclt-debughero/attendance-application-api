<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DateTimeComparison implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * The operator can be one of the following:
     * - 'gt': Greater Than (startDateTime > endDateTime)
     * - 'lt': Less Than (startDateTime < endDateTime)
     * - 'gte': Greater Than or Equal (startDateTime >= endDateTime)
     * - 'lte': Less Than or Equal (startDateTime <= endDateTime)
     * - 'eq': Equal (startDateTime == endDateTime)
     * - 'neq': Not Equal (startDateTime != endDateTime)
     *
     * @param string $startDateTimeLabel
     * @param string $endDateTimeLabel
     * @param string|null $startDateTimeValue
     * @param string|null $endDateTimeValue
     * @param string $format
     * @param string $operator
     */
    public function __construct(
        private $startDateTimeLabel,
        private $endDateTimeLabel,
        private $startDateTimeValue = null,
        private $endDateTimeValue = null,
        private $format,
        private $operator,
    ) {
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (isset($this->startDateTimeLabel) &&
            isset($this->endDateTimeLabel) &&
            isset($this->startDateTimeValue) &&
            isset($this->endDateTimeValue) &&
            isset($this->format) &&
            isset($this->operator)
        ) {
            $startDateTime = Carbon::createFromFormat($this->format, $this->startDateTimeValue);
            $endDateTime = Carbon::createFromFormat($this->format, $this->endDateTimeValue);

            if (! $this->compare($startDateTime, $endDateTime)) {
                $fail(ConfigUtil::getMessage('ECL026', [$this->endDateTimeLabel, $this->startDateTimeLabel]));
            }
        }
    }

    /**
     * Compare two dates based on the operator.
     *
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @return bool
     */
    private function compare(Carbon $startDateTime, Carbon $endDateTime): bool {
        // Check if the operator is one of the valid Carbon comparison methods
        if (in_array($this->operator, ['gt', 'lt', 'gte', 'lte', 'eq'], true)) {
            return $startDateTime->{$this->operator}($endDateTime);
        }

        // Check if the operator is 'neq'
        if ($this->operator === 'neq') {
            return ! $startDateTime->eq($endDateTime);
        }

        // Return false if the operator is invalid or unsupported
        return false;
    }
}
