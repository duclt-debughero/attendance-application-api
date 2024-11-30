<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Alphanumeric implements ValidationRule
{
    private string $chars;

    /**
     * Create a new rule instance.
     *
     * @param string|null $chars
     * @return void
     */
    public function __construct(string $chars = "") {
        $this->chars = $chars;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $pattern = '/^[a-zA-Z0-9]*$/';

        if (! empty($this->chars)) {
            // Escape any special regex characters in chars
            $escapedChars = preg_quote($this->chars, '/');

            // Combine alphanumeric with allowed special characters
            $pattern = '/^[a-zA-Z0-9' . $escapedChars . ']*$/';
        }

        if (! preg_match($pattern, $value)) {
            $fail(ConfigUtil::getMessage('ECL006', [':attribute']));
        }
    }
}
