<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Numeric implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (! preg_match('/^[0-9]*$/', $value)) {
            $fail(ConfigUtil::getMessage('ECL004', [':attribute']));
        }
    }
}
