<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Required implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!isset($value) || (is_array($value) && count($value) === 0) || (!is_array($value) && trim((string)$value) === '')) {
            $fail(ConfigUtil::getMessage('ECL001', [':attribute']));
        }
    }
}
