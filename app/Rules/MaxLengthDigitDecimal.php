<?php

namespace App\Rules;

use App\Libs\ConfigUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxLengthDigitDecimal implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param int $max
     */
    public function __construct(
        private int $max,
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
        $parts = explode(".", $value);

        // Check if there is a decimal part
        if (count($parts) > 1) {
            $currentLength = strlen($parts[1]);
            if (! ($currentLength <= $this->max)) {
                $fail(ConfigUtil::getMessage('ECL002', [':attribute'.'decimal value', $this->max, $currentLength]));
            }
        }
    }
}
