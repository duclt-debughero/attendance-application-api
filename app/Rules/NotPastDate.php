<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

class NotPastDate implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        try {
            $date = Carbon::createFromFormat('Y/m/d', $value);
            if (! ($date->isFuture() || $date->isToday())) {
                $fail(ConfigUtil::getMessage('ECL027', [':attribute']));
            }
        } catch (Exception $e) {
            $fail(ConfigUtil::getMessage('ECL010', [':attribute']));
        }
    }
}
