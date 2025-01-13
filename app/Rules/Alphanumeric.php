<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Alphanumeric implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param string|null $chars
     */
    public function __construct(
        private $chars = null,
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
        $escapedChars = isset($this->chars) ? preg_quote($this->chars, '/') : '';
        $pattern = '/^[a-zA-Z0-9' . $escapedChars . ']*$/';

        if (! preg_match($pattern, $value)) {
            $fail(ConfigUtil::getMessage('ECL006', [':attribute']));
        }
    }
}
