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
     * @param string $startDateTimeLabel
     * @param string $endDateTimeLabel
     * @param string $startDateTimeValue
     * @param string $endDateTimeValue
     * @param string $format
     * @param string $operator
     */
    public function __construct(
        private string $startDateTimeLabel,
        private string $endDateTimeLabel,
        private string $startDateTimeValue,
        private string $endDateTimeValue,
        private string $format,
        private string $operator,
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
        switch ($this->operator) {
            case '>':
                return $startDateTime->gt($endDateTime);
            case '<':
                return $startDateTime->lt($endDateTime);
            case '>=':
                return $startDateTime->gte($endDateTime);
            case '<=':
                return $startDateTime->lte($endDateTime);
            case '=':
                return $startDateTime->eq($endDateTime);
            case '!=':
                return !$startDateTime->eq($endDateTime);
            default:
                return false;
        }
    }
}
