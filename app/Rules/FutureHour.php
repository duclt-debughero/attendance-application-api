<?php

namespace App\Rules;

use App\Libs\{
    ConfigUtil,
    DateUtil,
};
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

class FutureHour implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param string $label
     * @return void
     */
    public function __construct(
        private string $label,
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
        try {
            $inputDate = DateUtil::formatDateTime($value, 'H:i');
            $nowDate = Carbon::now()->format('H:i');

            if ($inputDate < $nowDate) {
                $fail(ConfigUtil::getMessage('ECL026', [$this->label, 'Current time']));
            }
        } catch (Exception $e) {
            $fail(ConfigUtil::getMessage('ECL026', [$this->label, 'Current time']));
        }
    }
}
