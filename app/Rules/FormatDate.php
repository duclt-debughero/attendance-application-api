<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class FormatDate implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param string $format
     */
    public function __construct(
        private string $format,
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
        if (! empty($value) && ! $this->isValidDate($value)) {
            $fail(ConfigUtil::getMessage('ECL010', [':attribute']));
        }
    }

    /**
     * Check if the value is a valid date in the specified format.
     *
     * @param mixed $value
     * @return bool
     */
    private function isValidDate(mixed $value): bool {
        try {
            $date = Carbon::createFromFormat($this->format, $value);

            return $date->format($this->format) == $value;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }
}
