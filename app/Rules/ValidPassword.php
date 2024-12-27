<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (
            ! (
                preg_match('/^[a-zA-Z0-9\#\$\%\(\)\*\+\-\.\:\;\?\@\[\]\_\{\}\~\/]*$/', $value)
            )
        ) {
            $fail(ConfigUtil::getMessage('ECL025', [':attribute']));
        }
    }
}
