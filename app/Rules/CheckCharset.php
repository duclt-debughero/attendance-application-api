<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckCharset implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (! preg_match('/^[\x{0000}-\x{FFFF}]*$/u', $value)) {
            $fail(ConfigUtil::getMessage('ECL049'));
        }
    }
}
